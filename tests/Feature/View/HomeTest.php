<?php

namespace Tests\Feature\View;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\TestHelpers;
use Tests\TestCase;

class HomeTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        TestHelpers::truncateTable('users');
    }

    public function tearDown(): void
    {
        TestHelpers::truncateTable('users');
        parent::tearDown();
    }

    public function test_access_home()
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200)
        ->assertViewIs('home');
    }

    public function test_admin_panel_link_rendered_if_user_is_admin_()
    {
        $user = User::factory()->create(['type' => 'admin']);
        $response = $this->actingAs($user)->get(route('home'));
        $response->assertStatus(200)
        ->assertViewIs('home')
        ->assertSee('<a href="admin/dashboard">Admin Panel</a>', false);
    }
}
