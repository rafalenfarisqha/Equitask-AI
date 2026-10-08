@extends('layouts.app')

@section('title', 'Detail Kelas 7A')

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
</style>
@endpush

@section('content')
<!-- Topbar dengan Tombol Back -->
<div class="topbar">
    <a href="{{ route('guru.kelas') }}" class="iconbtn" style="text-decoration: none;">
        <span class="material-symbols-outlined">arrow_back_ios_new</span>
    </a>
    <h1>Kelas 7A</h1>
</div>

<!-- Tab Navigation -->
<div class="tabbar">
    <button class="tabbtn active" id="btn-tab-siswa" onclick="switchTab('siswa')">Siswa</button>
    <button class="tabbtn" id="btn-tab-assessment" onclick="switchTab('assessment')">Assessment</button>
</div>

<div class="px" id="tab-content">

    <!-- Simulasi Data -->
    @php
        $siswa = [
            ['nama' => 'Dinda Amelia Putri', 'nis' => '2025010', 'jenis' => 'Adaptif'],
            ['nama' => 'Rafi Ardiansyah', 'nis' => '2025011', 'jenis' => 'Reguler'],
            ['nama' => 'Putri Salsabila', 'nis' => '2025012', 'jenis' => 'Reguler'],
            ['nama' => 'Bagas Nugroho', 'nis' => '2025013', 'jenis' => 'Adaptif'],
            ['nama' => 'Keysha Anindya', 'nis' => '2025014', 'jenis' => 'Reguler'],
        ];

        $assessments = [
            ['nama' => 'Ekosistem & Rantai Makanan', 'mapel' => 'IPA', 'status' => 'Published', 'tanggal' => '10 Jul 2026', 'icon' => 'eco', 'color' => '#10B981'],
            ['nama' => 'Operasi Pecahan', 'mapel' => 'Matematika', 'status' => 'Draft', 'tanggal' => '08 Jul 2026', 'icon' => 'calculate', 'color' => '#2563EB'],
        ];
    @endphp

    <!-- TAB 1: DAFTAR SISWA -->
    <div id="content-siswa">
        <div class="chip-row">
            <button class="btn btn-outline btn-sm">
                <span class="material-symbols-outlined" style="font-size:16px;">person_add</span>Tambah Siswa
            </button>
            <button class="btn btn-outline btn-sm">
                <span class="material-symbols-outlined" style="font-size:16px;">upload_file</span>Import Excel
            </button>
        </div>

        @foreach($siswa as $s)
        <div class="card row" style="margin-bottom:10px; padding: 14px;">
            <div class="avatar" style="background:var(--secondary-soft); color:var(--secondary);">
                {{ substr($s['nama'], 0, 2) }}
            </div>
            <div style="flex:1;">
                <div style="font-weight:700; font-size:13.5px;">{{ $s['nama'] }}</div>
                <div class="muted">NIS {{ $s['nis'] }}</div>
            </div>

            @if($s['jenis'] === 'Adaptif')
                <span class="badge badge-green">Adaptif</span>
            @else
                <span class="badge badge-blue">Reguler</span>
            @endif
        </div>
        @endforeach
    </div>

    <!-- TAB 2: DAFTAR ASSESSMENT (Disembunyikan secara default) -->
    <div id="content-assessment" style="display: none;">
        <div style="margin-bottom:14px;">
            <button class="btn btn-primary btn-sm btn-block">
                <span class="material-symbols-outlined" style="font-size:16px;">add</span>Tambah Assessment
            </button>
        </div>

        @foreach($assessments as $a)
        <a href="#" class="card card-tap row" style="margin-bottom:12px; padding: 14px;">
            <div class="thumb" style="background:{{ $a['color'] }}1A;">
                <span class="material-symbols-outlined" style="color:{{ $a['color'] }};">{{ $a['icon'] }}</span>
            </div>
            <div style="flex:1;">
                <div style="font-weight:700; font-size:13.5px;">{{ $a['nama'] }}</div>
                <div class="muted">{{ $a['mapel'] }} · {{ $a['tanggal'] }}</div>
            </div>

            @if($a['status'] === 'Published')
                <span class="badge badge-green"><span class="material-symbols-outlined" style="font-size:12px;">check_circle</span>{{ $a['status'] }}</span>
            @else
                <span class="badge badge-amber"><span class="material-symbols-outlined" style="font-size:12px;">edit</span>{{ $a['status'] }}</span>
            @endif
        </a>
        @endforeach
    </div>

    <div style="height:24px;"></div>
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
    <a href="{{ route('guru.bank-soal') }}" class="navitem {{ request()->routeIs('guru.bank-soal') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">folder_open</span>Assessment
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
        // Reset tombol
        document.getElementById('btn-tab-siswa').classList.remove('active');
        document.getElementById('btn-tab-assessment').classList.remove('active');

        // Sembunyikan semua konten
        document.getElementById('content-siswa').style.display = 'none';
        document.getElementById('content-assessment').style.display = 'none';

        // Aktifkan tombol & konten yang dipilih
        document.getElementById('btn-tab-' + tabName).classList.add('active');
        document.getElementById('content-' + tabName).style.display = 'block';
    }
</script>
@endpush
