<?php

namespace Tests\Feature\Models;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\TestHelpers;
use Tests\TestCase;

class PostTest extends TestCase
{
    // select, insert, update, delete

    static $once = false;

    public function setUp(): void
    {
        parent::setUp();
        // TestHelpers::truncateTable(['users', 'posts', 'comments', 'post_tag']);
        // for faster tests
        TestHelpers::truncateTable(['posts', 'post_tag']);
    }

    public function tearDown(): void
    {
        // TestHelpers::truncateTable(['users', 'posts', 'comments', 'post_tag']);
        // for faster tests
        TestHelpers::truncateTable(['posts', 'post_tag']);
        parent::tearDown();
    }

    public function test_select_posts()
    {
        Post::factory()->create();
        $posts = Post::all();
        $this->assertTrue($posts->count() >= 1);
    }

    public function test_insert_post()
    {
        $post = Post::factory()->create();
        $this->assertDatabaseHas('posts', $post->toArray());
    }

    public function test_update_post()
    {
        $post = Post::factory()->create();
        $oldTitle = $post->title;
        $post->title = 'new title';
        $post->save();
        $newTitle = $post->title;

        $this->assertNotEquals($oldTitle, $newTitle);

        return $post->id;
    }

    public function test_update_post_with_another_method()
    {
        $post = Post::factory()->create();
        $post = Post::find($post->id);
        $oldTitle = $post->title;
        $post->update(['title' => 'new title']);
        $newTitle = $post->title;

        $this->assertNotEquals($oldTitle, $newTitle);
    }

    public function test_delete_post()
    {
        $post = Post::factory()->create();
        $post->forceDelete();
        $this->assertDatabaseMissing('posts', $post->toArray());
    }

    public function test_soft_delete_post()
    {
        $post = Post::factory()->create();
        $post->delete();
        $this->assertDatabaseHas('posts', $post->toArray());
        $this->assertNotNull($post->deleted_at);
    }

    public function test_restore_post()
    {
        $post = Post::factory()->create();
        $post->delete();
        $post->restore();
        $this->assertDatabaseHas('posts', $post->toArray());
        $this->assertNull($post->deleted_at);
    }

    public function test_post_belongs_to_user()
    {
        // $post = Post::factory()->create();
        $post = Post::factory()->forUser()->create();
        $user = $post->user;
        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', $user->toArray());
    }

    public function test_post_has_many_comments()
    {
        $post = Post::factory()->hasComments(5)->create();
        // Comment::factory(5)->create(['post_id' => $post->id]);
        $comments = $post->comments;
        $this->assertInstanceOf(Comment::class, $comments->first());
        $this->assertDatabaseHas('comments', $comments->first()->toArray());
    }

    public function test_post_belongs_to_many_tags()
    {
        $post = Post::factory()->hasTags(5)->create();
        $tag = $post->tags->first();
        $this->assertInstanceOf(Tag::class, $tag);
        unset($tag['pivot']);
        $this->assertDatabaseHas('tags', $tag->toArray());
    }
}
