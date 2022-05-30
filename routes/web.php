<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// auth
Route::group([], function(){
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');

    Route::post('register', [AuthController::class, 'register'])->name('register.send');
    Route::post('login', [AuthController::class, 'login'])->name('login.send');
});
