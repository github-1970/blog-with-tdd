<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\LoginAlertMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Notifications\LoginAlertNotification;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validData = $request->validated();
        $validData['password'] = Hash::make($validData['password']);
        User::create($validData);

        return redirect()->route('home');
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->get()->first();
        if (!Auth::attempt(['email' => $user->email, 'password' => $request->password])) {
            return redirect()->route('login')->withErrors(['password' => 'Password is incorrect']);
        }

        // just use one service and disable another service in following and in test!
        Mail::send(new LoginAlertMail($user->email));
        $user->notify(new LoginAlertNotification());

        return redirect()->back();
    }
}
