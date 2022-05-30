<?php

namespace Tests\Feature\Models;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\TestHelpers;
use Tests\TestCase;

class CommentModelTest extends TestCase
{
    // select, insert, update, delete

    static $once = false;

    public function setUp(): void
    {
        parent::setUp();
        // TestHelpers::truncateTable(['users', 'posts', 'comments']);
        // for faster tests
        TestHelpers::truncateTable('comments');
    }

    public function tearDown(): void
    {
        // TestHelpers::truncateTable(['users', 'posts', 'comments']);
        // for faster tests
        TestHelpers::truncateTable('comments');
        parent::tearDown();
    }

    public function test_select_comments()
    {
        Comment::factory()->create();
        $comments = Comment::all();
        $this->assertTrue($comments->count() >= 1);
    }

    public function test_insert_comment()
    {
        $comment = Comment::factory()->create();
        $this->assertDatabaseHas('comments', $comment->toArray());
    }

    public function test_update_comment()
    {
        $comment = Comment::factory()->create();
        $oldBody = $comment->body;
        $comment->body = 'new body';
        $comment->save();
        $newBody = $comment->body;

        $this->assertNotEquals($oldBody, $newBody);

        return $comment->id;
    }

    public function test_update_comment_with_another_method()
    {
        $comment = Comment::factory()->create();
        $comment = Comment::find($comment->id);
        $oldBody = $comment->body;
        $comment->update(['body' => 'new body']);
        $newBody = $comment->body;

        $this->assertNotEquals($oldBody, $newBody);
    }

    public function test_delete_comment()
    {
        $comment = Comment::factory()->create();
        $comment->forceDelete();
        $this->assertDatabaseMissing('comments', $comment->toArray());
    }

    public function test_soft_delete_comment()
    {
        $comment = Comment::factory()->create();
        $comment->delete();
        $this->assertDatabaseHas('comments', $comment->toArray());
        $this->assertNotNull($comment->deleted_at);
    }

    public function test_restore_comment()
    {
        $comment = Comment::factory()->create();
        $comment->delete();
        $comment->restore();
        $this->assertDatabaseHas('comments', $comment->toArray());
        $this->assertNull($comment->deleted_at);
    }

    public function test_comment_belongs_to_user()
    {
        // $comment = Comment::factory()->create();
        $comment = Comment::factory()->forUser()->create();
        $user = $comment->user;
        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', $user->toArray());
    }

    public function test_comment_commentable_for_post()
    {
        $comment = Comment::factory()->for(
            Post::factory()->create(),
            'commentable'
        )->create();
        $post = $comment->commentable;
        $this->assertInstanceOf(Post::class, $post);
        $this->assertDatabaseHas('posts', $post->toArray());
    }
}
