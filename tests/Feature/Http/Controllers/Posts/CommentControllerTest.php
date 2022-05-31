<?php

namespace Tests\Feature\Http\Controllers\Posts;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\TestHelpers;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        TestHelpers::truncateTable(['comments', 'users', 'posts']);
    }

    public function tearDown(): void
    {
        TestHelpers::truncateTable(['comments', 'users', 'posts']);
        parent::tearDown();
    }

    public function test_post_comment_store()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $commentData = [
            'body' => $this->faker()->text(100),
        ];

        $response = $this->actingAs($user)->post(route('posts.comments.store', $post), $commentData);

        $response->assertStatus(302)
        ->assertSessionDoesntHaveErrors()
        ->assertRedirect(route('home'));

        $comment = $post->comments()->where([
            ['body', $commentData['body']],
            ['user_id', $user->id],
        ])->first();
        $this->assertDatabaseHas('comments', $comment->toArray());
    }

    public function test_post_comment_update()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => Post::class,
        ]);
        $commentData = [
            'body' => $this->faker()->text(100),
        ];

        $response = $this->actingAs($user)->put(route('posts.comments.update', [$post, $comment]), $commentData);

        $response->assertStatus(302)
        ->assertSessionDoesntHaveErrors()
        ->assertRedirect(route('home'));

        $comment = $post->comments()->where([
            ['body', $commentData['body']],
            ['user_id', $user->id],
        ])->first();
        $this->assertDatabaseHas('comments', $comment->toArray());
    }

    public function test_user_can_not_post_comment_update_if_user_not_owner()
    {
        $ownerUser = User::factory()->create();
        $anotherUser = User::factory()->create();
        $post = Post::factory()->create();

        $comment = Comment::factory()->create([
            'user_id' => $ownerUser->id,
            'commentable_id' => $post->id,
            'commentable_type' => Post::class,
        ]);
        $commentData = [
            'body' => $this->faker()->text(100),
        ];

        $response = $this->actingAs($anotherUser)->put(route('posts.comments.update', [$post, $comment]), $commentData);

        $response->assertStatus(302)
        ->assertSessionHasErrors('comment', null, 'comment_error')
        ->assertRedirect(route('home'));

        $comment = $post->comments()->where([
            ['body', $commentData['body']],
            ['user_id', $anotherUser->id],
        ])->first();
        $this->assertNull($comment);
    }

    public function test_post_comment_destroy()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => Post::class,
        ]);

        $response = $this->actingAs($user)->delete(route('posts.comments.destroy', [$post, $comment]));

        $response->assertStatus(302)
        ->assertSessionDoesntHaveErrors()
        ->assertRedirect(route('home'));

        $comment = Comment::onlyTrashed()->find($comment->id);
        $this->assertNotNull($comment->deleted_at);
    }

    public function test_user_can_not_post_comment_destroy_if_user_not_owner()
    {
        $ownerUser = User::factory()->create();
        $another = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $ownerUser->id,
            'commentable_id' => $post->id,
            'commentable_type' => Post::class,
        ]);

        $response = $this->actingAs($another)->delete(route('posts.comments.destroy', [$post, $comment]));

        $response->assertStatus(302)
        ->assertSessionHasErrors('comment', null, 'comment_error')
        ->assertRedirect(route('home'));

        $comment = Comment::onlyTrashed()->find($comment->id);
        $this->assertNull($comment);
    }
}
