@extends('layouts.app')

@section('title', 'Buat Asesmen AI — EquiTask AI')

@section('content')
<div class="px py-4" style="max-width: 800px; margin: 0 auto; padding-bottom: 90px;">

    <!-- Header -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('guru.assessments.index') }}" class="btn btn-light border rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <span class="material-symbols-outlined fs-5">arrow_back</span>
        </a>
        <div>
            <h1 class="h4 fw-bold text-dark mb-1">Buat Asesmen dengan AI</h1>
            <p class="text-muted small mb-0">Tentukan parameter di bawah untuk memicu multi-agent AI menyusun soal otomatis.</p>
        </div>
    </div>

    <!-- Form Create AI Assessment -->
    <div class="card border shadow-sm rounded-4 p-4 bg-white">
        <form action="{{ route('guru.assessments.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Nama / Judul Asesmen</label>
                    <input type="text" name="nama" class="form-control rounded-3 py-2 shadow-none" placeholder="Contoh: Sumatif Bab Jaringan Komputer" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Mata Pelajaran</label>
                    <input type="text" name="mata_pelajaran" class="form-control rounded-3 py-2 shadow-none" placeholder="Contoh: Informatika" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Kelas</label>
                    <input type="text" name="kelas" class="form-control rounded-3 py-2 shadow-none" placeholder="Contoh: Kelas 10 / Fase E" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Versi Asesmen</label>
                    <select name="versi_asesmen" class="form-select rounded-3 py-2 shadow-none">
                        <option value="Standard">Standard</option>
                        <option value="Adaptif AI">Adaptif AI (Diferensiasi)</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-muted small">Tujuan Pembelajaran</label>
                <textarea name="tujuan_pembelajaran" class="form-control rounded-3 py-2 shadow-none" rows="2" placeholder="Tuliskan tujuan pembelajaran..." required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-muted small">Materi Ajar</label>
                <textarea name="materi_ajar" class="form-control rounded-3 py-2 shadow-none" rows="3" placeholder="Ringkasan materi atau referensi..." required></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Jumlah Pilihan Ganda</label>
                    <input type="number" name="jumlah_pilihan_ganda" value="10" min="0" class="form-control rounded-3 py-2 shadow-none" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Jumlah Esai</label>
                    <input type="number" name="jumlah_esai" value="2" min="0" class="form-control rounded-3 py-2 shadow-none" required>
                </div>
            </div>

            <!-- Checkbox Taksonomi Bloom C1 - C6 -->
            <div class="mb-4">
                <label class="form-label fw-bold text-muted small mb-2">Level Kognitif Taksonomi Bloom (C1 - C6)</label>
                <div class="d-flex flex-wrap gap-3 p-3 bg-light rounded-3 border">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="c1" value="1" id="c1" checked>
                        <label class="form-check-label fw-semibold text-dark small" for="c1">C1 (Remember)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="c2" value="1" id="c2" checked>
                        <label class="form-check-label fw-semibold text-dark small" for="c2">C2 (Understand)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="c3" value="1" id="c3" checked>
                        <label class="form-check-label fw-semibold text-dark small" for="c3">C3 (Apply)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="c4" value="1" id="c4">
                        <label class="form-check-label fw-semibold text-dark small" for="c4">C4 (Analyze)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="c5" value="1" id="c5">
                        <label class="form-check-label fw-semibold text-dark small" for="c5">C5 (Evaluate)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="c6" value="1" id="c6">
                        <label class="form-check-label fw-semibold text-dark small" for="c6">C6 (Create)</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-muted small">Status Awal</label>
                    <select name="status" class="form-select rounded-3 py-2 shadow-none">
                        <option value="Draft">Draft</option>
                        <option value="Published">Published</option>
                    </select>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold text-muted small">Akses (Visibility)</label>
                    <select name="visibility" class="form-select rounded-3 py-2 shadow-none">
                        <option value="private">Private (Pribadi)</option>
                        <option value="public">Public (Masuk Bank Soal)</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('guru.assessments.index') }}" class="btn btn-light w-50 py-2 fw-bold rounded-3 text-secondary border">Batal</a>
                <button type="submit" class="btn btn-primary w-50 py-2 fw-bold rounded-3">Generate & Simpan Asesmen</button>
            </div>
        </form>
    </div>

</div>
@endsection

@section('bottom_nav')
<div class="bottomnav">
    <a href="{{ route('guru.dashboard') }}" class="navitem {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">space_dashboard</span>Dashboard
    </a>
    <a href="{{ route('guru.kelas') }}" class="navitem {{ request()->routeIs('guru.kelas') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">groups</span>Kelas
    </a>
    <a href="{{ route('guru.assessments.index') }}" class="navitem {{ request()->routeIs('assessments*') || request()->routeIs('guru.assessments*') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">library_books</span>Asesmen
    </a>
    <a href="{{ route('guru.bank-soal') }}" class="navitem {{ request()->routeIs('guru.bank-soal') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">folder_open</span>Bank Soal
    </a>
    <a href="{{ route('guru.profil') }}" class="navitem {{ request()->routeIs('guru.profil') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">person</span>Profil
    </a>
</div>
@endsection
