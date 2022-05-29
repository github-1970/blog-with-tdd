<?php

namespace Tests\Feature\Models;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\Helpers\TestHelpers;
use Tests\TestCase;

class UserTest extends TestCase
{
    // select, insert, update, delete

    static $once = false;

    public function setUp(): void
    {
        parent::setUp();
        TestHelpers::truncateTable(['users', 'posts', 'comments', 'tags']);
    }

    public function tearDown(): void
    {
        TestHelpers::truncateTable(['users', 'posts', 'comments', 'tags']);
        parent::tearDown();
    }

    public function test_select_users()
    {
        User::factory()->create();
        $users = User::all();
        $this->assertTrue($users->count() >= 1);
    }

    public function test_insert_user()
    {
        $user = User::factory()->create();
        $this->assertDatabaseHas('users', $user->toArray());
    }

    public function test_update_user()
    {
        $user = User::factory()->create();
        $oldName = $user->name;
        $user->name = 'new name';
        $user->save();
        $newName = $user->name;

        $this->assertNotEquals($oldName, $newName);

        return $user->id;
    }

    public function test_update_user_with_another_method()
    {
        $user = User::factory()->create();
        $user = User::find($user->id);
        $oldName = $user->name;
        $user->update(['name' => 'new name']);
        $newName = $user->name;

        $this->assertNotEquals($oldName, $newName);
    }

    public function test_delete_user()
    {
        $user = User::factory()->create();
        $user->delete();
        $this->assertDatabaseMissing('users', $user->toArray());
    }

    public function test_user_is_admin()
    {
        $user = User::factory()->admin()->create();
        $this->assertTrue($user->isAdmin());
    }

    public function test_user_has_many_posts()
    {
        $user = User::factory()->hasPosts(5)->create();
        $posts = $user->posts;
        $this->assertInstanceOf(Post::class, $posts->first());
        $this->assertDatabaseHas('posts', $posts->first()->toArray());
    }

    public function test_post_has_many_comments()
    {
        $user = User::factory()->hasComments(5)->create();
        // Comment::factory(5)->create(['user_id' => $user->id]);
        $comments = $user->comments;
        $this->assertInstanceOf(Comment::class, $comments->first());
        $this->assertDatabaseHas('comments', $comments->first()->toArray());
    }

    // get comments in my (user) post, no my (user) comment
    // has many through
    public function test_access_comments_with_post()
    {
        $user = User::factory()->create();
        $post = Post::factory(['user_id' => $user->id])->create();
        $comments = Comment::factory(2, [
            'commentable_id' => $post->id,
            'deleted_at' => null,
        ])->create();
        // set deleted_at for match get comments.

        $postComments = $user->postComments;

        // must use last! because first() created with another test. or delete comment table.
        $postComment = $postComments->last()->toArray();
        unset($postComment['laravel_through_key']);
        $comment = $comments->last()->toArray();

        $this->assertInstanceOf(Comment::class, $postComments->last());
        $this->assertDatabaseHas('comments', $postComment);
        $this->assertEquals($comment, $postComment);
    }
}
