<?php

use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;

// Users will be redirected to this route if not logged in
Volt::route('/login', 'login')->name('login');
Volt::route('/register', 'register')->name('register');

// Define the logout
Route::get('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
});

// Protected routes here
Route::middleware('auth')->group(function () {
    Volt::route('/', 'index');

    Route::group(['prefix' => 'users'], function () {
        Volt::route('/', 'users.index');
        Volt::route('/create', 'users.create');
        Volt::route('/{user}/edit', 'users.edit');
    });

    Route::group(['prefix' => 'bills'], function () {
        Volt::route('/', 'bills.index')->name('bills');
        Volt::route('/create', 'bills.create');
        Volt::route('/{bill}/edit', 'bills.edit');
    });
});
