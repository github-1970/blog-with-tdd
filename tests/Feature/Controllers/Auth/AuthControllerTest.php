<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\Helpers\TestHelpers;
use Tests\TestCase;

class AuthControllerTest extends TestCase
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

    public function test_register()
    {
        $user = User::factory()->make(['password' => '12345678']);
        $userArray = $user->toArray();
        $userArray['password'] = $user->password;
        $userArray['password_confirmation'] = $user->password;
        $response = $this->post(route('register.send'), $userArray);

        $response->assertStatus(302)
        ->assertRedirect(route('home'));
    }

    public function test_validation_register()
    {
        $password = '123';
        $user = User::factory()->make();
        $userArray = $user->toArray();
        $userArray['password'] = $password;
        $userArray['password_confirmation'] = $password;
        $userArray['email'] = '';
        $response = $this->post(route('register.send'), $userArray);

        $response->assertStatus(302)
        ->assertSessionHasErrors([
            'password' => __('validation.min.string', ['attribute' => 'password', 'min' => 8]),
            'email' => __('validation.required', ['attribute' => 'email']),
        ]);
    }

    public function test_login()
    {
        $userInBeforeLogin = Auth::user();
        $user = User::factory()->create(['password' => Hash::make('12345678')]);
        $response = $this->post(route('login.send'), [
            'email' => $user->email,
            'password' => '12345678'
        ]);

        /** @var User $userInAfterLogin */
        $userInAfterLogin = Auth::user();

        $response->assertStatus(302)
        ->assertRedirect(route('home'))
        ->assertSessionDoesntHaveErrors();

        $this->assertDatabaseHas('users', $userInAfterLogin->toArray())
        ->assertNotEquals($userInBeforeLogin, $userInAfterLogin);
    }

    public function test_validation_login()
    {
        $userInBeforeLogin = Auth::user();
        $user = User::factory()->create(['password' => Hash::make('12345678')]);
        $response = $this->post(route('login.send'), [
            'email' => '',
            'password' => '123'
        ]);

        /** @var User $userInAfterLogin */
        $userInAfterLogin = Auth::user();

        $response->assertStatus(302)
        ->assertSessionHasErrors([
            'password' => __('validation.min.string', ['attribute' => 'password', 'min' => 8]),
            'email' => __('validation.required', ['attribute' => 'email']),
        ]);

        $this->assertNull($userInAfterLogin);
        $this->assertEquals($userInBeforeLogin, $userInAfterLogin);
    }
}
