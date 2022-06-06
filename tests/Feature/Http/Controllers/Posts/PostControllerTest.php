<?php

namespace Tests\Feature\Http\Controllers\Posts;

use App\Models\Category;
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
        TestHelpers::truncateTable(['posts', 'users', 'tags', 'comments', 'taggables', 'categories']);
    }

    public function tearDown(): void
    {
        TestHelpers::truncateTable(['posts', 'users', 'tags', 'comments', 'taggables', 'categories']);
        parent::tearDown();
    }

    public function test_posts_index()
    {
        Post::factory(5)->state([
            'published_at' => now()->yesterday()
        ])->hasAttached(
            Tag::factory(2)
        )->has(
            Comment::factory(2)
        )->forCategory()->forUser()->create();
        $posts = Post::with(['tags', 'comments', 'category'])->published()->get();
        $response = $this->get(route('posts.index'));

        $response->assertStatus(200)
            ->assertViewIs('posts.index')
            ->assertViewHas('posts', $posts);
    }

    public function test_post_show()
    {
        $post = Post::factory()->hasAttached(
            Tag::factory(2)
        )->hasComments(2)->forCategory()->forUser()->create();
        $post = Post::with(['tags', 'comments'])->where('id', $post->id)->first();
        $comments = $post->comments;
        $category = $post->category;

        $response = $this->get(route('posts.show', ['post' => $post]));

        $response->assertStatus(200)
            ->assertViewIs('posts.show')
            ->assertViewHas('post', $post)
            ->assertViewHas('comments', $comments)
            ->assertViewHas('tags')
            ->assertViewHas('category', $category);
        // if you want to check tags with value, you should convert to array. -what? +i do know!
        // i think it's because get post in controller is automatically and for the same reason some property in tags are null!
    }

    public function test_post_store()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $post = Post::factory()->make([
            'category_id' => $category->id,
        ]);
        unset($post->user_id);

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

        $post = Post::factory()
        ->hasAttached(
            Tag::factory(2)
        )
        ->forCategory()
        ->create([
            'user_id' => $user->id,
        ]);
        $post['title'] = 'New title';

        $tags = $post->tags;
        $tags->first()['title'] = 'New tag';
        $tagsData = $tags->pluck('title')->toArray();

        $newCategoryId = Category::factory()->create()->id;

        $response = $this->actingAs($user)->put(
            route('posts.update', $post->id),
            array_merge($post->toArray(), [
                'tags' => $tagsData,
                'category_id' => $newCategoryId,
            ])
        );

        // $afterUpdatePost = Post::with(['tags', 'category'])->where('id', $post->id)->first();

        $response->assertStatus(302)
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect(route('home'));

        unset($post['tags']);
        unset($post['category']);
        // for time differences
        unset($post['created_at'], $post['updated_at']);
        $this->assertDatabaseHas('posts', $post->refresh()->toArray())
            ->assertDatabaseHas('tags', ['title' => $tagsData[0]])
            ->assertEquals($newCategoryId, $post->category->id);
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

        $response = $this->actingAs($anotherUser)->put(
            route('posts.update', $post->id),
            $post->toArray()
        );

        $response->assertStatus(302)
            ->assertSessionHasErrors('message', null, 'post_error')
            ->assertRedirect(route('home'));

        unset($post['tags']);
        // for time differences
        unset($post['created_at'], $post['updated_at']);
        $this->assertDatabaseMissing('posts', $post->toArray());
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
