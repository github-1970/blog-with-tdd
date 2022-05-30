<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        
        return redirect()->back();
    }
}
