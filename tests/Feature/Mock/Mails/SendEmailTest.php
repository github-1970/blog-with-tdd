<?php

namespace Tests\Feature\Mock\Mails;

use App\Mail\LoginAlertMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\Helpers\TestHelpers;
use Tests\TestCase;

class SendEmailTest extends TestCase
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

    public function test_send_email_in_login() {
        Mail::fake();

        $user = User::factory()->create(['password' => Hash::make('12345678')]);
        $this->post(route('login.send'), [
            'email' => $user->email,
            'password' => '12345678'
        ]);

        Mail::assertSent(LoginAlertMail::class);
    }
}
