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

class TagTest extends TestCase
{
    // select, insert, update, delete

    static $once = false;

    public function setUp(): void
    {
        parent::setUp();
        // TestHelpers::truncateTable(['users', 'posts', 'comments', 'tags', 'taggables]);
        // for faster tests
        TestHelpers::truncateTable(['tags', 'taggables']);
    }

    public function tearDown(): void
    {
        // TestHelpers::truncateTable(['users', 'posts', 'comments', 'tags', 'taggables]);
        // for faster tests
        TestHelpers::truncateTable(['tags', 'taggables']);
        parent::tearDown();
    }

    public function test_select_tags()
    {
        Tag::factory()->create();
        $tags = Tag::all();
        $this->assertTrue($tags->count() >= 1);
    }

    public function test_insert_tag()
    {
        $tag = Tag::factory()->create();
        $this->assertDatabaseHas('tags', $tag->toArray());
    }

    public function test_update_tag()
    {
        $tag = Tag::factory()->create();
        $oldTitle = $tag->title;
        $tag->title = 'new title';
        $tag->save();
        $newTitle = $tag->title;

        $this->assertNotEquals($oldTitle, $newTitle);

        return $tag->id;
    }

    public function test_update_tag_with_another_method()
    {
        $tag = Tag::factory()->create();
        $tag = Tag::find($tag->id);
        $oldTitle = $tag->title;
        $tag->update(['title' => 'new title']);
        $newTitle = $tag->title;

        $this->assertNotEquals($oldTitle, $newTitle);
    }

    public function test_delete_tag()
    {
        $tag = Tag::factory()->create();
        $tag->forceDelete();
        $this->assertDatabaseMissing('tags', $tag->toArray());
    }

    public function test_soft_delete_tag()
    {
        $tag = Tag::factory()->create();
        $tag->delete();
        $this->assertDatabaseHas('tags', $tag->toArray());
        $this->assertNotNull($tag->deleted_at);
    }

    public function test_restore_tag()
    {
        $tag = Tag::factory()->create();
        $tag->delete();
        $tag->restore();
        $this->assertDatabaseHas('tags', $tag->toArray());
        $this->assertNull($tag->deleted_at);
    }

    public function test_tag_belongs_to_many_posts()
    {
        $tag = Tag::factory()->hasAttached(
            Post::factory(5)->create()
        )->create();
        $post = $tag->posts->first();
        $this->assertInstanceOf(Post::class, $post);
        unset($post['pivot']);
        $this->assertDatabaseHas('posts', $post->toArray());
    }
}
