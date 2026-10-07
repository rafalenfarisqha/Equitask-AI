<?php

use Illuminate\Support\Facades\Route;

// Mengarahkan halaman utama (/) langsung ke halaman login
Route::get('/', function () {
    return redirect('/login');
});

// Route Login & Auth
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    return redirect('/dashboard');
});

// Tambahkan rute logout ini agar error hilang
Route::post('/logout', function () {
    return redirect('/login');
})->name('logout');

// Dashboard Guru
Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard');

// Dashboard Siswa
Route::get('/student/dashboard', function () {
    return view('student.dashboard');
})->name('student.dashboard');

Route::get('/student/assessment', function () {
    return view('student.assessment');
})->name('student.assessment');

// Manajemen Assessments
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

// Manajemen Materials / Modul Ajar
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
