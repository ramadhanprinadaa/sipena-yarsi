<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::view('/login', 'auth.login')->name('login');

Route::view('/presensi', 'dashboard.presensi')->name('presensi');

// Route::middleware('auth')->group(function () {
//     Route::view('/dashboard', 'dashboard')->name('dashboard');
// });