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
        background: #ffffff;
        border: 1px solid var(--border, #e2e8f0);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .class-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
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
        background: #edf4ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .class-title {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .class-meta {
        font-size: 12.5px;
        color: #64748b;
        margin-top: 2px;
        font-weight: 600;
    }

    .btn-buka-kelas {
        display: block;
        text-align: center;
        background: #edf4ff;
        color: #2563eb;
        font-weight: 700;
        font-size: 13px;
        padding: 10px 16px;
        border-radius: 12px;
        text-decoration: none;
        transition: background 0.2s, color 0.2s;
    }

    .btn-buka-kelas:hover {
        background: #2563eb;
        color: #fff;
    }

    /* Modal Styling */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.5);
        z-index: 1050;
        align-items: center;
        justify-content: center;
        padding: 16px;
        backdrop-filter: blur(4px);
    }
    .modal-content-custom {
        background: #fff;
        width: 100%;
        max-width: 420px;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
</style>
@endpush

@section('content')
<div class="px py-4" style="max-width: 1024px; margin: 0 auto;">

    <!-- Judul Halaman & Tombol Buat Kelas -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="fw-bold fs-4 mb-0 text-dark">Kelas Saya</h2>
        <button onclick="openModal()" class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2 rounded-3 fw-bold shadow-sm" style="font-size: 13px;">
            <span class="material-symbols-outlined fs-5">add</span> Buat Kelas
        </button>
    </div>

    <!-- Kotak Pencarian Kelas (Search Bar ala Bootstrap Clean) -->
    <div class="mb-4">
        <div class="input-group shadow-sm rounded-3 overflow-hidden bg-white border">
            <span class="input-group-text bg-white border-0 text-muted ps-3">
                <span class="material-symbols-outlined fs-5">search</span>
            </span>
            <input type="text" id="search-class" placeholder="Cari nama kelas (misal: 7A, 8B)..." class="form-control border-0 py-2 ps-0 shadow-none" onkeyup="filterClasses()">
        </div>
    </div>

    <!-- Daftar Kelas (Grid Layout) -->
    <div class="classes-container" id="classes-grid">
        @forelse($classes as $kelas)
        <div class="class-card">
            <div>
                <div class="class-header">
                    <div class="class-thumb">
                        <span class="material-symbols-outlined fs-4">groups</span>
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
        <div class="card border-0 shadow-sm rounded-4 text-center py-5 bg-white" style="grid-column: 1 / -1;">
            <div class="text-muted mb-2">
                <span class="material-symbols-outlined fs-1 text-secondary">groups</span>
            </div>
            <h5 class="fw-bold text-dark">Belum ada kelas</h5>
            <p class="text-muted small mb-3">Mulai buat kelas pertama Anda untuk mengelola siswa dan asesmen.</p>
            <button onclick="openModal()" class="btn btn-primary btn-sm px-4 py-2 fw-bold rounded-3">
                <span class="material-symbols-outlined fs-5 align-middle me-1">add</span> Buat Kelas Sekarang
            </button>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Form Buat Kelas Baru -->
<div id="classModal" class="modal-overlay">
    <div class="modal-content-custom">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fs-5 fw-bold mb-0 text-dark">Tambah Kelas Baru</h3>
            <button onclick="closeModal()" class="btn-close shadow-none"></button>
        </div>
        <form action="{{ route('guru.kelas.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold text-muted small">Nama Kelas</label>
                <input type="text" name="name" placeholder="Contoh: Kelas 7A - Informatika" class="form-control rounded-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold text-muted small">Deskripsi (Opsional)</label>
                <textarea name="description" placeholder="Keterangan singkat tentang kelas..." class="form-control rounded-3 py-2" style="height: 80px; resize: none;"></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="button" onclick="closeModal()" class="btn btn-light w-50 py-2 fw-bold rounded-3 text-secondary border">Batal</button>
                <button type="submit" class="btn btn-primary w-50 py-2 fw-bold rounded-3">Simpan Kelas</button>
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
