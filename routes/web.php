<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::view('/login', 'auth.login')->name('login');

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
});