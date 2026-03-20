<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::view('/login', 'auth.login')->name('login');

Route::view('/presensi', 'dashboard.presensi')->name('presensi');
Route::view('/kepegawaian', 'dashboard.pegawai')->name('kepegawaian');
Route::view('/lembur', 'dashboard.lembur')->name('lembur');
Route::view('/cuti', 'dashboard.cuti')->name('cuti');

// Route::middleware('auth')->group(function () {
//     Route::view('/dashboard', 'dashboard')->name('dashboard');
// });