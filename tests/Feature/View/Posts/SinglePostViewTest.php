<?php

namespace Tests\Feature\View\Posts;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\TestHelpers;
use Tests\TestCase;

class SinglePostViewTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        TestHelpers::truncateTable(['posts', 'users']);
    }

    public function tearDown(): void
    {
        TestHelpers::truncateTable(['posts', 'users']);
        parent::tearDown();
    }

    public function test_access_single_post_view()
    {
        $post = Post::factory()->create();
        $response = $this->get(route('posts.show', $post));
        $response->assertStatus(200)
        ->assertViewIs('posts.show');
    }

    public function test_send_comment_form_rendered_if_user_is_login()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $response = $this->actingAs($user)->get(route('posts.show', $post));
        $response->assertStatus(200)
        ->assertViewIs('posts.show')
        ->assertSee('action="' . route('posts.comments.store', $post), false);
    }
}
