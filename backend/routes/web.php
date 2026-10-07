<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Mengarahkan halaman utama (/) langsung ke halaman login
Route::get('/', function () {
    return redirect('/login');
});

// Menampilkan halaman form login (GET)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// Memproses data login saat tombol masuk ditekan (POST)
Route::post('/login', [AuthController::class, 'login'])->name('login.process');

// Route logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
