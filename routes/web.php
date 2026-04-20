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

    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.handle.logout');

    Route::prefix('beranda')->group(function () {
        Route::view('/presensi', 'dashboard.presensi')->name('presensi');
        Route::view('/kepegawaian', 'dashboard.pegawai')->name('kepegawaian');
        Route::view('/lembur', 'dashboard.lembur')->name('lembur')->middleware('role:Admin,SDM Yayasan,SDM Universitas,Staff,Tendik');
        Route::view('/cuti', 'dashboard.cuti')->name('cuti');
        Route::view('/surat-menyurat', 'dashboard.surat-menyurat')->name('surat-menyurat');
    });

    Route::prefix('manajemen')->group(function () {
        Route::middleware('role:Admin')->group(function () {
            Route::view('pengguna', 'manajemen.pengguna')->name('manajemen-pengguna');
        });
        Route::middleware('role:Admin,SDM Yayasan,SDM Universitas,Pimpinan')->group(function () {
            Route::view('pegawai', 'manajemen.pegawai')->name('manajemen-pegawai');
            Route::view('presensi', 'manajemen.presensi')->name('manajemen-presensi');
            Route::view('lembur', 'manajemen.lembur')->name('manajemen-lembur');
            Route::view('cuti', 'manajemen.cuti')->name('manajemen-cuti');
        });
    });
});
// Test Error
Route::get('/test-401', function () {
    abort(401);
});
Route::get('/test-403', function () {
    abort(403);
});
Route::get('/test-500', function () {
    abort(500);
});

// Test DB
// Route::get('/test-db', function() {
//     $users = App\Models\User::all();
//     $roles = App\Models\Role::find(4)->users()->get();

//     return $users;
// });