<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// auth
Route::group([], function(){
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');

    Route::post('register', [AuthController::class, 'register'])->name('register.send');
    Route::post('login', [AuthController::class, 'login'])->name('login.send');
});

Route::name('posts.')->prefix('posts')->group(function(){
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::get('{post}', [PostController::class, 'show'])->name('show');

    Route::get('create', [PostController::class, 'create'])->name('create');
    Route::post('store', [PostController::class, 'store'])->name('store')->middleware('auth');

    Route::get('{post}/edit', [PostController::class, 'edit'])->name('edit');
    Route::put('{post}', [PostController::class, 'update'])->name('update')->middleware('auth');

    Route::delete('{post}', [PostController::class, 'destroy'])->name('destroy')->middleware('auth');
});
