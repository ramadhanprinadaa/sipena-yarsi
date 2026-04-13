<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use Symfony\Component\Routing\Route as RoutingRoute;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('kepegawaian') : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.handle.login');
});


Route::middleware('auth')->group(function () {
    Route::view('/presensi', 'dashboard.presensi')->name('presensi');
    Route::view('/kepegawaian', 'dashboard.pegawai')->name('kepegawaian');
    Route::view('/lembur', 'dashboard.lembur')->name('lembur')->middleware('role:Super Admin,Admin,SDM Universitas,Pegawai Tendik');
    Route::view('/cuti', 'dashboard.cuti')->name('cuti');
    Route::view('modul/surat-perintah-lembur', 'dashboard.modul.surat-perintah-lembur')->name('modul.surat-perintah-lembur');
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.handle.logout');
});

Route::get('/test', function() {
    return view('layouts_2.app');
});

// Test DB
// Route::get('/test-db', function() {
//     $users = App\Models\User::all();
//     $roles = App\Models\Role::find(4)->users()->get();
    
//     return $users;
// });