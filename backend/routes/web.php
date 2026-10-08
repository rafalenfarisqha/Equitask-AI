<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Halaman utama
Route::get('/', function () {
    return redirect('/login');
});

// =========================
// AUTHENTICATION
// =========================

// Menampilkan halaman login
Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login');

// Memproses login
Route::post('/login', [AuthController::class, 'login']);

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


// =========================
// DASHBOARD
// =========================

// Dashboard Guru
Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard');

// Dashboard Siswa
Route::get('/student/dashboard', function () {
    return view('student.dashboard');
})->name('student.dashboard');


// =========================
// STUDENT ASSESSMENT
// =========================

Route::get('/student/assessment', function () {
    return view('student.assessment');
})->name('student.assessment');


// =========================
// ASSESSMENTS
// =========================

Route::get('/assessments', function () {
    return view('assessments.index');
})->name('assessments.index');

Route::get('/assessments/create', function () {
    return view('assessments.create');
})->name('assessments.create');

Route::get('/assessments/{id}/edit', function ($id) {
    return view('assessments.edit');
})->name('assessments.edit');

Route::get('/assessments/{id}', function ($id) {
    return view('assessments.show');
})->name('assessments.show');


// =========================
// MATERIALS
// =========================

Route::get('/materials', function () {
    return view('materials.index');
})->name('materials.index');

Route::get('/materials/create', function () {
    return view('materials.create');
})->name('materials.create');

Route::get('/materials/{id}/edit', function ($id) {
    return view('materials.edit');
})->name('materials.edit');

Route::get('/materials/{id}', function ($id) {
    return view('materials.show');
})->name('materials.show');