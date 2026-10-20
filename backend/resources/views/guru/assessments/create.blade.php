@extends('layouts.app')

@section('title', 'Buat Asesmen AI — EquiTask AI')

@section('content')
<div class="px py-4" style="max-width: 800px; margin: 0 auto; padding-bottom: 90px;">

    <!-- Header -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('guru.assessments.index') }}"
           class="btn btn-light border rounded-3 p-2 me-3 d-flex align-items-center justify-content-center"
           style="width: 40px; height: 40px;">
            <span class="material-symbols-outlined fs-5">arrow_back</span>
        </a>

        <div>
            <h1 class="h4 fw-bold text-dark mb-1">
                Buat Asesmen dengan AI
            </h1>

            <p class="text-muted small mb-0">
                Tentukan parameter untuk menghasilkan asesmen reguler
                dan inklusif menggunakan multi-agent AI.
            </p>
        </div>
    </div>

    <!-- Form Create AI Assessment -->
    <div class="card border shadow-sm rounded-4 p-4 bg-white">

        <form
            id="assessmentForm"
            action="{{ route('guru.assessments.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            <!-- Pesan Error dari Controller -->
            @if (session('error'))
                <div class="alert alert-danger rounded-3">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Pesan Validasi Laravel -->
            @if ($errors->any())
                <div class="alert alert-danger rounded-3">
                    <strong>Periksa kembali input berikut:</strong>

                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Nama Asesmen dan Mata Pelajaran -->
            <div class="row">

                <div class="col-md-6 mb-3">
                    <label
                        for="nama"
                        class="form-label fw-bold text-muted small"
                    >
                        Nama / Judul Asesmen
                    </label>

                    <input
                        type="text"
                        name="nama"
                        id="nama"
                        value="{{ old('nama') }}"
                        class="form-control rounded-3 py-2 shadow-none"
                        placeholder="Contoh: Sumatif Bab Sifat Cahaya"
                        maxlength="255"
                        required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label
                        for="mata_pelajaran"
                        class="form-label fw-bold text-muted small"
                    >
                        Mata Pelajaran
                    </label>

                    <input
                        type="text"
                        name="mata_pelajaran"
                        id="mata_pelajaran"
                        value="{{ old('mata_pelajaran') }}"
                        class="form-control rounded-3 py-2 shadow-none"
                        placeholder="Contoh: IPA"
                        maxlength="255"
                        required
                    >
                </div>

            </div>

            <!-- Kelas dan Versi Asesmen -->
            <div class="row">

                <!-- Dropdown Kelas Sesuai Dify -->
                <div class="col-md-6 mb-3">

                    <label
                        for="kelas"
                        class="form-label fw-bold text-muted small"
                    >
                        Kelas
                    </label>

                    <select
                        name="kelas"
                        id="kelas"
                        class="form-select rounded-3 py-2 shadow-none"
                        required
                    >
                        <option
                            value=""
                            disabled
                            @selected(!old('kelas'))
                        >
                            Pilih Kelas
                        </option>

                        @for ($i = 1; $i <= 6; $i++)
                            <option
                                value="Kelas {{ $i }}"
                                @selected(
                                    old('kelas') === 'Kelas ' . $i
                                )
                            >
                                Kelas {{ $i }}
                            </option>
                        @endfor

                    </select>

                    <div class="form-text">
                        Pilih kelas 1 sampai kelas 6.
                    </div>

                </div>

                <!-- Dropdown Versi Asesmen -->
                <div class="col-md-6 mb-3">

                    <label
                        for="versi_asesmen"
                        class="form-label fw-bold text-muted small"
                    >
                        Versi Asesmen
                    </label>

                    <select
                        name="versi_asesmen"
                        id="versi_asesmen"
                        class="form-select rounded-3 py-2 shadow-none"
                        required
                    >
                        <option
                            value=""
                            disabled
                            @selected(!old('versi_asesmen'))
                        >
                            Pilih Versi Asesmen
                        </option>

                        <option
                            value="Asesmen Awal"
                            @selected(
                                old('versi_asesmen') === 'Asesmen Awal'
                            )
                        >
                            Asesmen Awal
                        </option>

                        <option
                            value="Asesmen Utama"
                            @selected(
                                old('versi_asesmen') === 'Asesmen Utama'
                            )
                        >
                            Asesmen Utama
                        </option>

                    </select>

                    <div class="form-text">
                        Pilih Asesmen Awal atau Asesmen Utama
                        sesuai alur workflow Dify.
                    </div>

                </div>
            </div>

            <!-- Tujuan Pembelajaran -->
            <div class="mb-3">

                <label
                    for="tujuan_pembelajaran"
                    class="form-label fw-bold text-muted small"
                >
                    Tujuan Pembelajaran
                </label>

                <textarea
                    name="tujuan_pembelajaran"
                    id="tujuan_pembelajaran"
                    class="form-control rounded-3 py-2 shadow-none"
                    rows="3"
                    placeholder="Tuliskan tujuan pembelajaran..."
                    required
                >{{ old('tujuan_pembelajaran') }}</textarea>

            </div>

            <!-- Upload Materi Ajar PDF -->
            <div class="mb-3">

                <label
                    for="materi_ajar"
                    class="form-label fw-bold text-muted small"
                >
                    Upload Materi Ajar (PDF)
                </label>

                <input
                    type="file"
                    name="materi_ajar"
                    id="materi_ajar"
                    class="form-control rounded-3 py-2 shadow-none"
                    accept=".pdf,application/pdf"
                    required
                >

                <div class="form-text">
                    Format PDF, maksimal 10 MB.
                    AI akan menggunakan dokumen ini sebagai referensi soal.
                </div>

                @error('materi_ajar')
                    <div class="text-danger small mt-1">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            <!-- Jumlah Soal -->
            <div class="row">

                <div class="col-md-6 mb-3">

                    <label
                        for="jumlah_pilihan_ganda"
                        class="form-label fw-bold text-muted small"
                    >
                        Jumlah Pilihan Ganda
                    </label>

                    <input
                        type="number"
                        name="jumlah_pilihan_ganda"
                        id="jumlah_pilihan_ganda"
                        value="{{ old('jumlah_pilihan_ganda', 10) }}"
                        min="0"
                        class="form-control rounded-3 py-2 shadow-none"
                        required
                    >

                </div>

                <div class="col-md-6 mb-3">

                    <label
                        for="jumlah_esai"
                        class="form-label fw-bold text-muted small"
                    >
                        Jumlah Esai
                    </label>

                    <input
                        type="number"
                        name="jumlah_esai"
                        id="jumlah_esai"
                        value="{{ old('jumlah_esai', 2) }}"
                        min="0"
                        class="form-control rounded-3 py-2 shadow-none"
                        required
                    >

                </div>

            </div>

            <!-- Taksonomi Bloom -->
            <div class="mb-4">

                <label class="form-label fw-bold text-muted small mb-2">
                    Level Kognitif Taksonomi Bloom (C1–C6)
                </label>

                <div class="d-flex flex-wrap gap-3 p-3 bg-light rounded-3 border">

                    @php
                        $bloomLevels = [
                            'c1' => 'C1 (Remember)',
                            'c2' => 'C2 (Understand)',
                            'c3' => 'C3 (Apply)',
                            'c4' => 'C4 (Analyze)',
                            'c5' => 'C5 (Evaluate)',
                            'c6' => 'C6 (Create)',
                        ];
                    @endphp

                    @foreach ($bloomLevels as $key => $label)

                        <div class="form-check">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="{{ $key }}"
                                value="1"
                                id="{{ $key }}"
                                @checked(
                                    (bool) old(
                                        $key,
                                        in_array($key, ['c1', 'c2', 'c3'])
                                    )
                                )
                            >

                            <label
                                class="form-check-label fw-semibold text-dark small"
                                for="{{ $key }}"
                            >
                                {{ $label }}
                            </label>

                        </div>

                    @endforeach

                </div>

                <div class="form-text">
                    Pilih level kognitif yang ingin digunakan
                    untuk penyusunan soal.
                </div>

            </div>

            <!-- Status dan Visibility -->
            <div class="row">

                <div class="col-md-6 mb-3">

                    <label
                        for="status"
                        class="form-label fw-bold text-muted small"
                    >
                        Status Awal
                    </label>

                    <select
                        name="status"
                        id="status"
                        class="form-select rounded-3 py-2 shadow-none"
                        required
                    >
                        <option value="Draft" selected>
                            Draft (Ditinjau Guru)
                        </option>
                    </select>

                    <div class="form-text">
                        Hasil AI perlu diperiksa sebelum dipublikasikan.
                    </div>

                </div>

                <div class="col-md-6 mb-4">

                    <label
                        for="visibility"
                        class="form-label fw-bold text-muted small"
                    >
                        Akses (Visibility)
                    </label>

                    <select
                        name="visibility"
                        id="visibility"
                        class="form-select rounded-3 py-2 shadow-none"
                        required
                    >
                        <option value="private" selected>
                            Private (Pribadi)
                        </option>
                    </select>

                </div>

            </div>

            <!-- Tombol -->
            <div class="d-flex gap-2">

                <a
                    href="{{ route('guru.assessments.index') }}"
                    class="btn btn-light w-50 py-2 fw-bold rounded-3 text-secondary border"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    id="generateBtn"
                    class="btn btn-primary w-50 py-2 fw-bold rounded-3"
                >
                    <span id="buttonText">
                        Generate & Simpan Asesmen
                    </span>

                    <span
                        id="buttonLoading"
                        class="d-none"
                    >
                        <span
                            class="spinner-border spinner-border-sm me-2"
                            role="status"
                            aria-hidden="true"
                        ></span>

                        Memproses AI...
                    </span>
                </button>

            </div>

            <!-- Informasi Proses -->
            <div
                id="processingMessage"
                class="alert alert-info mt-3 mb-0 d-none"
                role="status"
            >
                <strong>EquiTask AI sedang bekerja.</strong>

                <div class="small mt-1">
                    Materi sedang dikirim ke Dify dan workflow AI
                    sedang dijalankan. Proses ini mungkin memerlukan
                    beberapa menit. Jangan tutup halaman dahulu.
                </div>
            </div>

        </form>

    </div>

</div>

<!-- Loading Saat Submit -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('assessmentForm');
    const button = document.getElementById('generateBtn');
    const buttonText = document.getElementById('buttonText');
    const buttonLoading = document.getElementById('buttonLoading');
    const processingMessage = document.getElementById('processingMessage');

    if (!form || !button) {
        return;
    }

    form.addEventListener('submit', function () {
        if (!form.checkValidity()) {
            return;
        }

        button.disabled = true;
        buttonText.classList.add('d-none');
        buttonLoading.classList.remove('d-none');
        processingMessage.classList.remove('d-none');
    });
});
</script>
@endsection

@section('bottom_nav')
<div class="bottomnav">

    <a
        href="{{ route('guru.dashboard') }}"
        class="navitem {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}"
        style="text-decoration:none;"
    >
        <span class="material-symbols-outlined">
            space_dashboard
        </span>
        Dashboard
    </a>

    <a
        href="{{ route('guru.kelas') }}"
        class="navitem {{ request()->routeIs('guru.kelas') ? 'active' : '' }}"
        style="text-decoration:none;"
    >
        <span class="material-symbols-outlined">
            groups
        </span>
        Kelas
    </a>

    <a
        href="{{ route('guru.assessments.index') }}"
        class="navitem {{ request()->routeIs('assessments*') || request()->routeIs('guru.assessments*') ? 'active' : '' }}"
        style="text-decoration:none;"
    >
        <span class="material-symbols-outlined">
            library_books
        </span>
        Asesmen
    </a>

    <a
        href="{{ route('guru.bank-soal') }}"
        class="navitem {{ request()->routeIs('guru.bank-soal') ? 'active' : '' }}"
        style="text-decoration:none;"
    >
        <span class="material-symbols-outlined">
            folder_open
        </span>
        Bank Soal
    </a>

    <a
        href="{{ route('guru.profil') }}"
        class="navitem {{ request()->routeIs('guru.profil') ? 'active' : '' }}"
        style="text-decoration:none;"
    >
        <span class="material-symbols-outlined">
            person
        </span>
        Profil
    </a>

</div>
@endsection
