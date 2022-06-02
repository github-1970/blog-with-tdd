<?php

namespace Tests\Feature\Http\Controllers\Posts;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\TestHelpers;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        TestHelpers::truncateTable(['posts', 'users', 'tags', 'comments', 'taggables']);
    }

    public function tearDown(): void
    {
        TestHelpers::truncateTable(['posts', 'users', 'tags', 'comments', 'taggables']);
        parent::tearDown();
    }

    public function test_posts_index()
    {
        Post::factory(10)->hasAttached(
            Tag::factory(2)
        )->has(
            Comment::factory(2)
        )->forUser()->create();
        $posts = Post::with(['tags', 'comments'])->get();

        $response = $this->get(route('posts.index'));

        $response->assertStatus(200)
            ->assertViewIs('posts.index')
            ->assertViewHas('posts', $posts);
    }

    public function test_post_show()
    {
        $post = Post::factory()->hasAttached(
            Tag::factory(2)
        )->hasComments(2)->forUser()->create();
        // $post = Post::with(['tags', 'comments'])->first();
        $comments = $post->comments;
        // $tags = $post->tags;


        $response = $this->get(route('posts.show', ['post' => $post]));

        $response->assertStatus(200)
            ->assertViewIs('posts.show')
            ->assertViewHas('post', $post)
            ->assertViewHas('comments', $comments)
            ->assertViewHas('tags');
        // if you want to check tags with value, you should convert to array
    }

    public function test_post_store()
    {
        $user = User::factory()->create();
        $post = Post::factory()->make([
            'user_id' => $user->id,
        ]);
        $tags = Tag::factory(2)->make();
        $tagsData = $tags->pluck('title')->toArray();

        $response = $this->actingAs($user)->post(
            route('posts.store'),
            array_merge($post->toArray(), [
                'tags' => $tagsData,
            ])
        );

        $response->assertStatus(302)
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect(route('home'));

        // $this->assertStringNotContainsString('login', $response->headers->get('Location'));
        $this->assertDatabaseHas('posts', $post->toArray());
        $this->assertDatabaseHas('tags', $tags->first()->toArray());
    }

    public function test_post_update()
    {
        $user = User::factory()->create();
        $post = Post::factory()->hasAttached(
            Tag::factory(2)
        )->create([
            'user_id' => $user->id,
        ]);
        $post['title'] = 'New title';

        $tags = $post->tags;
        $tags->first()['title'] = 'New tag';
        $tagsData = $tags->pluck('title')->toArray();

        $response = $this->actingAs($user)->put(
            route('posts.update', $post->id),
            array_merge($post->toArray(), [
                'tags' => $tagsData,
            ])
        );

        $response->assertStatus(302)
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect(route('home'));

        unset($post['tags']);
        // for time differences
        unset($post['created_at'], $post['updated_at']);
        $this->assertDatabaseHas('posts', $post->toArray())
            ->assertDatabaseHas('tags', ['title' => $tagsData[0]]);

        return ['tag' => $tags->first(), 'post' => $post];
    }

    public function test_user_can_not_post_update_if_user_not_owner()
    {
        $ownerUser = User::factory()->create();
        $anotherUser = User::factory()->create();
        $post = Post::factory()->hasAttached(
            Tag::factory(2)
        )->create([
            'user_id' => $ownerUser->id,
        ]);
        $post['title'] = 'New title';

        $tags = $post->tags;
        $tags->first()['title'] = 'New tag';
        $tagsData = $tags->pluck('title')->toArray();

        $response = $this->actingAs($anotherUser)->put(
            route('posts.update', $post->id),
            array_merge($post->toArray(), [
                'tags' => $tagsData,
            ])
        );

        $response->assertStatus(302)
            ->assertSessionHasErrors('message', null, 'post_error')
            ->assertRedirect(route('home'));

        unset($post['tags']);
        // for time differences
        unset($post['created_at'], $post['updated_at']);
        $this->assertDatabaseMissing('posts', $post->toArray())
            ->assertDatabaseMissing('tags', ['title' => $tagsData[0]]);
    }

    public function test_post_destroy()
    {
        $user = User::factory()->create();
        $post = Post::factory()->hasAttached(
            Tag::factory(2)
        )->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('posts.destroy', $post));

        $response->assertStatus(302)
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect(route('home'));

        $post = Post::onlyTrashed()->find($post->id);
        $this->assertNotNull($post->deleted_at);

        // comment this following line, because use soft delete, so if you want to restore, all tags not restore
        // $this->assertEmpty($post->tags->toArray());
    }

    public function test_user_can_not_post_destroy_if_user_not_owner()
    {
        $ownerUser = User::factory()->create();
        $anotherUser = User::factory()->create();
        $post = Post::factory()->hasAttached(
            Tag::factory(2)
        )->create([
            'user_id' => $ownerUser->id,
        ]);

        $response = $this->actingAs($anotherUser)->delete(route('posts.destroy', $post));

        $response->assertStatus(302)
            ->assertSessionHasErrors('message', null, 'post_error')
            ->assertRedirect(route('home'));

        $post = Post::find($post->id);
        $this->assertNull($post->deleted_at);
    }
}
