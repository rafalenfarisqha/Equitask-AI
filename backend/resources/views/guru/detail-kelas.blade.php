@extends('layouts.app')

@section('title', 'Detail Kelas ' . $classroom->name)

@push('styles')
<style>
    /* Styling khusus Tab & List Siswa */
    .tabbar {
        display: flex; gap: 8px; padding: 0 20px 10px;
        background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px);
        position: sticky; top: 65px; z-index: 39;
        border-bottom: 1px solid var(--border); margin-bottom: 16px;
    }
    .tabbtn {
        flex: 1; text-align: center; padding: 10px; border-radius: 12px;
        font-weight: 700; font-size: 13px; background: var(--card);
        color: var(--muted); border: 1px solid var(--border); cursor: pointer;
        transition: all 0.2s;
    }
    .tabbtn.active {
        background: var(--primary); color: #fff; border-color: var(--primary);
    }
    .chip-row { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 14px; }
    .avatar {
        width: 46px; height: 46px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 16px; flex-shrink: 0;
    }
    .thumb {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .card-tap { cursor: pointer; transition: transform 0.15s ease; text-decoration: none; display: block; color: inherit; }
    .card-tap:active { transform: scale(0.98); }

    /* Modal Styling Sederhana */
    .modal-overlay {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center;
    }
    .modal-box {
        background: #fff; padding: 24px; border-radius: 16px; width: 90%; max-width: 420px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<!-- Topbar dengan Tombol Back -->
<div class="topbar">
    <a href="{{ route('guru.kelas') }}" class="iconbtn" style="text-decoration: none;">
        <span class="material-symbols-outlined">arrow_back_ios_new</span>
    </a>
    <h1>{{ $classroom->name }}</h1>
</div>

<!-- Notifikasi Flash Message -->
@if(session('success'))
<div style="margin: 0 20px 14px; padding: 12px; background: #D1FAE5; color: #065F46; border-radius: 10px; font-size: 13px; font-weight: 600;">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="margin: 0 20px 14px; padding: 12px; background: #FEE2E2; color: #991B1B; border-radius: 10px; font-size: 13px; font-weight: 600;">
    {{ $errors->first() }}
</div>
@endif

<!-- Tab Navigation -->
<div class="tabbar">
    <button class="tabbtn active" id="btn-tab-siswa" onclick="switchTab('siswa')">Siswa</button>
    <button class="tabbtn" id="btn-tab-assessment" onclick="switchTab('assessment')">Assessment</button>
</div>

<div class="px" id="tab-content">

    <!-- TAB 1: DAFTAR SISWA -->
    <div id="content-siswa">
        <div class="chip-row">
            <button class="btn btn-outline btn-sm" onclick="openModal('modalTambahSiswa')">
                <span class="material-symbols-outlined" style="font-size:16px;">person_add</span>Tambah Siswa
            </button>
            <button class="btn btn-outline btn-sm" onclick="openModal('modalImportExcel')">
                <span class="material-symbols-outlined" style="font-size:16px;">upload_file</span>Import Excel
            </button>
        </div>

        <!-- Daftar Siswa -->
        @forelse($classroom->students as $student)
        <div class="card row" style="margin-bottom:10px; padding: 14px;">
            <div class="avatar" style="background:var(--secondary-soft); color:var(--secondary);">
                {{ strtoupper(substr($student->name, 0, 2)) }}
            </div>
            <div style="flex:1;">
                <div style="font-weight:700; font-size:13.5px;">{{ $student->name }}</div>
                <div class="muted">NISN: {{ $student->nisn ?? 'Tidak ada NISN' }}</div>
            </div>
            <span class="badge badge-blue">Siswa Aktif</span>
        </div>
        @empty
        <div class="card" style="padding: 30px; text-align: center; color: var(--muted);">
            <span class="material-symbols-outlined" style="font-size: 36px; margin-bottom: 8px; opacity: 0.5;">group_off</span>
            <div style="font-weight: 600;">Belum ada siswa di kelas ini.</div>
            <div style="font-size: 12px; margin-top: 4px;">Klik tombol "Tambah Siswa" di atas untuk mendaftarkan siswa via NISN atau Link.</div>
        </div>
        @endforelse
    </div>

    <!-- TAB 2: DAFTAR ASSESSMENT -->
    <div id="content-assessment" style="display: none;">
        <div style="margin-bottom:14px;">
            <a href="{{ route('guru.assessments.create') }}" class="btn btn-primary btn-sm btn-block" style="text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 6px;">
                <span class="material-symbols-outlined" style="font-size:16px;">add</span>Tambah Assessment
            </a>
        </div>

        <div class="card" style="padding: 30px; text-align: center; color: var(--muted);">
            <span class="material-symbols-outlined" style="font-size: 36px; margin-bottom: 8px; opacity: 0.5;">assignment</span>
            <div style="font-weight: 600;">Belum ada assessment untuk kelas ini.</div>
        </div>
    </div>

    <div style="height:24px;"></div>
</div>

<!-- MODAL TAMBAH SISWA (BERISI LINK GABUNG & INPUT NISN) -->
<div id="modalTambahSiswa" class="modal-overlay">
    <div class="modal-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <h3 style="font-size: 16px; font-weight: 700; margin: 0;">Tambah Siswa ke Kelas</h3>
            <button type="button" onclick="closeModal('modalTambahSiswa')" style="background: none; border: none; cursor: pointer; font-size: 18px; color: var(--muted);">✕</button>
        </div>
        <p style="font-size: 12.5px; color: var(--muted); margin-bottom: 16px;">Pilih metode untuk mendaftarkan siswa ke kelas ini.</p>

        <!-- Opsi 1: Bagikan Link -->
        <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: 10px; padding: 12px; margin-bottom: 16px;">
            <div style="font-weight: 700; font-size: 12.5px; margin-bottom: 4px; display: flex; align-items: center; gap: 6px; color: var(--primary);">
                <span class="material-symbols-outlined" style="font-size: 16px;">link</span> Link Bergabung Siswa
            </div>
            <p style="font-size: 11.5px; color: var(--muted); margin-bottom: 8px;">Bagikan tautan ini agar siswa bisa bergabung otomatis.</p>
            <div style="display: flex; gap: 6px;">
                <input type="text" id="joinLink" readonly value="{{ route('siswa.kelas.join', $classroom->id) }}" style="flex: 1; padding: 6px 10px; border: 1px solid var(--border); border-radius: 6px; font-size: 11.5px; background: #fff; color: var(--muted);" />
                <button type="button" class="btn btn-outline btn-sm" onclick="copylink()" style="font-size: 11.5px; padding: 6px 10px;">Salin</button>
            </div>
        </div>

        <div style="border-top: 1px solid var(--border); margin: 16px 0; position: relative; text-align: center;">
            <span style="position: absolute; top: -9px; left: 50%; transform: translateX(-50%); background: #fff; padding: 0 8px; font-size: 10.5px; color: var(--muted); font-weight: 600;">ATAU INPUT NISN</span>
        </div>

        <!-- Opsi 2: Input NISN Manual -->
        <form action="{{ route('guru.kelas.addStudent', $classroom->id) }}" method="POST">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">Nomor Induk Siswa Nasional (NISN)</label>
                <input type="text" name="nisn" required placeholder="Contoh: 2025010" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px;">
            </div>

            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('modalTambahSiswa')">Tutup</button>
                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL IMPORT EXCEL / CSV -->
<div id="modalImportExcel" class="modal-overlay">
    <div class="modal-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <h3 style="font-size: 16px; font-weight: 700; margin: 0;">Import Siswa via Excel / CSV</h3>
            <button type="button" onclick="closeModal('modalImportExcel')" style="background: none; border: none; cursor: pointer; font-size: 18px; color: var(--muted);">✕</button>
        </div>
        <p style="font-size: 13px; color: var(--muted); margin-bottom: 16px;">
            Unggah file berformat <b>.csv</b> atau <b>.xlsx</b> di mana kolom pertama berisi daftar <b>NISN</b> siswa.
        </p>

        <form action="{{ route('guru.kelas.importExcel', $classroom->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px;">Pilih File (.csv / .xlsx)</label>
                <input type="file" name="file" required accept=".csv, .txt, .xlsx, .xls" style="width: 100%; padding: 8px; border: 1px solid var(--border); border-radius: 8px; font-size: 13px;">
            </div>

            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('modalImportExcel')">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Upload & Import</button>
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
    <a href="{{ route('guru.kelas') }}" class="navitem {{ request()->routeIs('guru.kelas', 'guru.detail-kelas') ? 'active' : '' }}" style="text-decoration:none;">
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

@push('scripts')
<script>
    // Fungsi untuk memindahkan tab
    function switchTab(tabName) {
        document.getElementById('btn-tab-siswa').classList.remove('active');
        document.getElementById('btn-tab-assessment').classList.remove('active');

        document.getElementById('content-siswa').style.display = 'none';
        document.getElementById('content-assessment').style.display = 'none';

        document.getElementById('btn-tab-' + tabName).classList.add('active');
        document.getElementById('content-' + tabName).style.display = 'block';
    }

    // Fungsi Modal
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'flex';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // Fungsi Salin Link Join
    function copylink() {
        var copyText = document.getElementById("joinLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        alert("Link berhasil disalin ke clipboard!");
    }
</script>
@endpush
