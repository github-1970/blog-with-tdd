<?php

namespace Tests\Feature\Controllers;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\TestHelpers;
use Tests\TestCase;

class PostTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        TestHelpers::truncateTable('posts');
    }

    public function tearDown(): void
    {
        TestHelpers::truncateTable('posts');
        parent::tearDown();
    }

    // public function test_posts_index()
    // {
    //     $posts = Post::factory()->create();
    //     $response = $this->get(route('posts.index'));
    //     $response->assertStatus(200)
    //     ->assertViewIs('posts.index')
    //     ->assertViewHas('posts');
    // }

    // public function test_show()
    // {
    //     $response = $this->get(route('posts.index'));
    //     $response->assertStatus(200);
    // }
}
