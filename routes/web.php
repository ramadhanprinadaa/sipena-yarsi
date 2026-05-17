<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\ManajemenPegawaiController;
use App\Http\Controllers\PegawaiController;
use App\Livewire\Manajemen\Pegawai\DetailPegawai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('kepegawaian') : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.handle.login');
});

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.handle.logout');
    Route::view('/profile', 'profile-page')->name('profile');

    Route::get('/upload', function () {
        return view('testing.upload');
    })->name('upload');
    Route::post('/upload', [FileUploadController::class, 'store'])->name('upload.store');
    Route::delete('/upload', [FileUploadController::class, 'destroy'])->name('upload.destroy');

    Route::prefix('beranda')->group(function () {
        Route::view('/presensi', 'dashboard.presensi')->name('presensi');
        Route::get('/kepegawaian', [PegawaiController::class, 'pegawai'])->name('kepegawaian');
        Route::view('/lembur', 'dashboard.lembur')->name('lembur')->middleware('role:Admin,SDM Yayasan,SDM Universitas,Rektor,Staff,Tendik');
        Route::view('/cuti', 'dashboard.cuti')->name('cuti');
        Route::view('/surat-menyurat', 'dashboard.surat-menyurat')->name('surat-menyurat');
    });

    Route::prefix('manajemen')->group(function () {
        Route::middleware('role:Admin')->group(function () {
            Route::view('pengguna', 'manajemen.pengguna')->name('manajemen-pengguna');
        });

        Route::middleware('role:Admin,SDM Yayasan,SDM Universitas,Rektor,Pimpinan')->group(function () {
            Route::get('pegawai', [ManajemenPegawaiController::class, 'index'])->name('manajemen-pegawai');
            Route::livewire('pegawai/{id}', DetailPegawai::class)->name('manajemen-pegawai-detail');

            Route::view('presensi', 'manajemen.presensi')->name('manajemen-presensi');
            Route::view('lembur', 'manajemen.lembur')->name('manajemen-lembur');
            Route::view('cuti', 'manajemen.cuti')->name('manajemen-cuti');
        });
    });

    Route::prefix('konfigurasi')->group(function () {
        Route::middleware('role:Admin, SDM Yayasan')->group(function () {
            Route::view('unit-kerja', 'config.unit-kerja')->name('konfigurasi-unit-kerja');
            Route::view('unit-sdm', 'config.unit-kerja')->name('konfigurasi-unit-sdm');
            Route::view('kalender', 'config.kalender')->name('konfigurasi-kalender');
            Route::view('hari-libur', 'config.hari-libur')->name('konfigurasi-hari-libur');
            Route::view('alur-persetujuan', 'config.alur-persetujuan')->name('konfigurasi-alur-persetujuan');
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