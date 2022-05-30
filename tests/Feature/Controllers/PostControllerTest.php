<?php

namespace Tests\Feature\Controllers;

use App\Models\Post;
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
        TestHelpers::truncateTable(['posts', 'users']);
    }

    public function tearDown(): void
    {
        TestHelpers::truncateTable(['posts', 'users']);
        parent::tearDown();
    }

    public function test_posts_index()
    {
        Post::factory(10)->forUser()->create();
        $posts = Post::all();

        $response = $this->get(route('posts.index'));

        $response->assertStatus(200)
        ->assertViewIs('posts.index')
        ->assertViewHas('posts', $posts);
    }

    public function test_post_show()
    {
        Post::factory()->create();
        $post = Post::all()->first();

        $response = $this->get(route('posts.show', ['post' => $post]));

        $response->assertStatus(200)
        ->assertViewIs('posts.show')
        ->assertViewHas('post', $post);
    }

    public function test_post_store()
    {
        $user = User::factory()->create();
        $post = Post::factory()->make([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('posts.store'), $post->toArray());

        $response->assertStatus(302)
        ->assertSessionDoesntHaveErrors()
        ->assertRedirect(route('home'));

        // $this->assertStringNotContainsString('login', $response->headers->get('Location'));
        $this->assertDatabaseHas('posts', $post->toArray());
    }

    public function test_post_update()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);
        $post['title'] = 'New title';

        $response = $this->actingAs($user)->put(route('posts.update', $post), $post->toArray());

        $response->assertStatus(302)
        ->assertSessionDoesntHaveErrors()
        ->assertRedirect(route('home'));

        $this->assertDatabaseHas('posts', $post->toArray());
    }

    public function test_post_destroy()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('posts.destroy', $post));

        $response->assertStatus(302)
        ->assertRedirect(route('home'));

        $post = Post::onlyTrashed()->find($post->id);
        $this->assertNotNull($post->deleted_at);
    }
}
