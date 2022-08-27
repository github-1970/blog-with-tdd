<?php

namespace Tests\Feature\Mail;

use App\Mail\LoginAlertMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LoginAlertMailTest extends TestCase
{
    public function test_mailable_content()
    {
        $user = User::factory()->create();
        Auth::loginUsingId($user->id);

        $mailable = new LoginAlertMail($user->email);

        $mailable->assertSeeInHtml(config('app.url'));
    }
}
