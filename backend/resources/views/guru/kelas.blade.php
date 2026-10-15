@extends('layouts.app')

@section('title', 'Kelas Saya — EquiTask AI')

@push('styles')
<style>
    .classes-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
        margin-top: 16px;
        padding-bottom: 90px;
    }

    .class-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 10px 24px -15px rgba(15, 23, 42, 0.1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .class-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px -15px rgba(15, 23, 42, 0.18);
    }

    .class-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 16px;
    }

    .class-thumb {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: var(--primary-soft);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .class-title {
        font-size: 16px;
        font-weight: 800;
        color: var(--text);
        margin: 0;
    }

    .class-meta {
        font-size: 12.5px;
        color: var(--muted);
        margin-top: 2px;
        font-weight: 600;
    }

    .btn-buka-kelas {
        display: block;
        text-align: center;
        background: var(--primary-soft);
        color: var(--primary);
        font-weight: 700;
        font-size: 13px;
        padding: 10px 16px;
        border-radius: 10px;
        text-decoration: none;
        transition: background 0.2s, color 0.2s;
    }

    .btn-buka-kelas:hover {
        background: var(--primary);
        color: #fff;
    }

    /* Modal Styling */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.5);
        z-index: 100;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .modal-content {
        background: #fff;
        width: 100%;
        max-width: 400px;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
</style>
@endpush

@section('content')
<div class="px" style="padding-bottom: 90px;">
    
    <!-- Judul Halaman & Tombol Buat Kelas -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
        <h2 style="font-size: 20px; font-weight: 800; margin: 0;">Kelas Saya</h2>
        <button onclick="openModal()" class="btn btn-primary" style="display: flex; align-items: center; gap: 6px; padding: 8px 14px; font-size: 13px; border-radius: 10px; font-weight: 700; background: var(--primary); color: #fff; border: none; cursor: pointer;">
            <span class="material-symbols-outlined" style="font-size: 18px;">add</span> Buat Kelas
        </button>
    </div>

    <!-- Kotak Pencarian Kelas (Search Bar) -->
    <div style="margin-bottom: 16px;">
        <div style="position: relative;">
            <span class="material-symbols-outlined" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 20px;">search</span>
            <input type="text" id="search-class" placeholder="Cari nama kelas (misal: 7A, 8B)..." class="input" style="padding-left: 48px;" onkeyup="filterClasses()">
        </div>
    </div>

    <!-- Daftar Kelas (Grid Layout) -->
    <div class="classes-container" id="classes-grid">
        @forelse($classes as $kelas)
        <div class="class-card">
            <div>
                <div class="class-header">
                    <div class="class-thumb">
                        <span class="material-symbols-outlined" style="font-size: 24px;">groups</span>
                    </div>
                    <div>
                        <h3 class="class-title">{{ $kelas->nama ?? $kelas->name }}</h3>
                        <div class="class-meta">
                            {{ $kelas->students_count ?? 0 }} Siswa · {{ $kelas->assessments_count ?? 0 }} Assessment
                        </div>
                    </div>
                </div>
            </div>

            <a href="{{ route('guru.kelas.show', $kelas) }}" class="btn-buka-kelas" style="position: relative; z-index: 10;">
                Buka Kelas
            </a>
        </div>
        @empty
        <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 40px 20px;">
            <span class="material-symbols-outlined" style="font-size: 48px; color: var(--muted); opacity: 0.5; margin-bottom: 8px;">groups</span>
            <div style="font-weight: 700; font-size: 15px; margin-bottom: 4px;">Belum ada kelas</div>
            <div class="muted" style="font-size: 13px; margin-bottom: 16px;">Mulai buat kelas pertama Anda untuk mengelola siswa dan asesmen.</div>
            <button onclick="openModal()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; font-size: 13px; border-radius: 10px; font-weight: 700; background: var(--primary); color: #fff; border: none; cursor: pointer;">
                <span class="material-symbols-outlined" style="font-size: 18px;">add</span> Buat Kelas Sekarang
            </button>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Form Buat Kelas Baru -->
<div id="classModal" class="modal-overlay">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 18px; font-weight: 800; margin: 0;">Tambah Kelas Baru</h3>
            <button onclick="closeModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--muted);">×</button>
        </div>
        <form action="{{ route('guru.kelas.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 14px;">
                <label class="field-label" style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Nama Kelas</label>
                <input type="text" name="name" placeholder="Contoh: Kelas 7A - Informatika" class="input" required>
            </div>
            <div style="margin-bottom: 20px;">
                <label class="field-label" style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 6px;">Deskripsi (Opsional)</label>
                <textarea name="description" placeholder="Keterangan singkat tentang kelas..." class="input" style="height: 80px; resize: none;"></textarea>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="closeModal()" class="btn" style="flex: 1; background: var(--border); color: var(--text); border: none; padding: 10px; border-radius: 10px; font-weight: 700; cursor: pointer;">Batal</button>
                <button type="submit" class="btn btn-primary" style="flex: 1; background: var(--primary); color: #fff; border: none; padding: 10px; border-radius: 10px; font-weight: 700; cursor: pointer;">Simpan Kelas</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openModal() {
        document.getElementById('classModal').style.display = 'flex';
    }
    function closeModal() {
        document.getElementById('classModal').style.display = 'none';
    }

    function filterClasses() {
        let input = document.getElementById('search-class').value.toLowerCase();
        let cards = document.getElementsByClassName('class-card');

        for (let i = 0; i < cards.length; i++) {
            let title = cards[i].getElementsByClassName('class-title')[0].innerText.toLowerCase();
            if (title.includes(input)) {
                cards[i].style.display = "";
            } else {
                cards[i].style.display = "none";
            }
        }
    }
</script>
@endpush

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
