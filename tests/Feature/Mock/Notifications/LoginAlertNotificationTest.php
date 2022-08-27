<?php

namespace Tests\Feature\Mock\Notifications;

use App\Models\User;
use App\Notifications\LoginAlertNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\Helpers\TestHelpers;
use Tests\TestCase;

class LoginAlertNotificationTest extends TestCase
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

    public function test_send_email_notification_in_login() {
        Notification::fake();

        $user = User::factory()->create(['password' => Hash::make('12345678')]);
        $this->post(route('login.send'), [
            'email' => $user->email,
            'password' => '12345678'
        ]);

        Notification::assertSentTo( [$user], LoginAlertNotification::class );
        Notification::assertCount(1);
    }
}
