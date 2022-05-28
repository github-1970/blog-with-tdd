<?php

namespace Tests\Feature\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\HelperTesting;
use Tests\TestCase;

class UserTest extends TestCase
{
    // select, insert, update, delete

    static $once = false;

    public function setUp(): void
    {
        parent::setUp();
        HelperTesting::truncateTable('users');
    }

    public function tearDown(): void
    {
        HelperTesting::truncateTable('users');
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
}
