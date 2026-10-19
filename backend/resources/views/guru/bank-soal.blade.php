@extends('layouts.app')

@section('title', 'Bank Soal Komunitas — EquiTask AI')

@section('content')
<div class="px py-4" style="max-width: 1024px; margin: 0 auto; padding-bottom: 90px;">

    <!-- Topbar / Header -->
    <div class="d-flex align-items-center mb-4">
        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <span class="material-symbols-outlined fs-4">library_books</span>
        </div>
        <div>
            <h1 class="h4 fw-bold text-dark mb-1">Bank Soal Komunitas</h1>
            <p class="text-muted small mb-0">Jelajahi & gunakan soal dari guru-guru se-Indonesia</p>
        </div>
    </div>

    <!-- Filter & Search Form (Dibuat bersih tanpa tombol Cari) -->
    <form method="GET" action="{{ route('guru.bank-soal') }}" class="mb-4" id="searchForm">
        <!-- Search Input -->
        <div class="input-group shadow-sm rounded-3 overflow-hidden bg-white border mb-3">
            <span class="input-group-text bg-white border-0 text-muted ps-3">
                <span class="material-symbols-outlined fs-5">search</span>
            </span>
            <input type="text" name="search" id="search-soal" value="{{ request('search') }}" class="form-control border-0 py-2 ps-0 shadow-none" placeholder="Cari soal publik atau mata pelajaran..." onkeyup="handleSearch(event)">
        </div>

        <!-- Filter Pills / Kategori Mapel -->
        @php $currentMapel = request('mapel', ''); @endphp
        <div class="d-flex gap-2 overflow-x-auto pb-2">
            <a href="{{ route('guru.bank-soal', array_merge(request()->except(['mapel', 'page']), ['mapel' => ''])) }}"
               class="btn btn-sm rounded-pill px-3 {{ $currentMapel == '' ? 'btn-primary' : 'btn-light text-dark border bg-white' }}">
                Semua
            </a>
            <a href="{{ route('guru.bank-soal', array_merge(request()->except(['mapel', 'page']), ['mapel' => 'IPA'])) }}"
               class="btn btn-sm rounded-pill px-3 {{ $currentMapel == 'IPA' ? 'btn-primary' : 'btn-light text-dark border bg-white' }}">
                IPA
            </a>
            <a href="{{ route('guru.bank-soal', array_merge(request()->except(['mapel', 'page']), ['mapel' => 'Matematika'])) }}"
               class="btn btn-sm rounded-pill px-3 {{ $currentMapel == 'Matematika' ? 'btn-primary' : 'btn-light text-dark border bg-white' }}">
                Matematika
            </a>
            <a href="{{ route('guru.bank-soal', array_merge(request()->except(['mapel', 'page']), ['mapel' => 'Bahasa Indonesia'])) }}"
               class="btn btn-sm rounded-pill px-3 {{ $currentMapel == 'Bahasa Indonesia' ? 'btn-primary' : 'btn-light text-dark border bg-white' }}">
                B. Indonesia
            </a>
        </div>
    </form>

    <!-- List Soal Publik -->
    <div class="row g-3">
        @forelse($publicAssessments as $item)
        <div class="col-12">
            <div class="card border shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: {{ $item->color ?? '#2563EB' }}15; color: {{ $item->color ?? '#2563EB' }};">
                            <span class="material-symbols-outlined">{{ $item->icon ?? 'eco' }}</span>
                        </div>
                        <div>
                            <h5 class="fw-bold fs-6 mb-1 text-dark">{{ $item->nama }}</h5>
                            <span class="text-muted small">{{ $item->mapel }} · Dibagikan oleh Guru Pengampu</span>
                        </div>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-semibold small">Publik</span>
                </div>

                <div class="d-flex gap-2 pt-2 border-top">
                    <a href="#" class="btn btn-outline-secondary btn-sm flex-fill py-2 fw-semibold">Preview Soal</a>

                    <form action="{{ route('guru.bank-soal.gunakan', $item->id) }}" method="POST" class="flex-fill">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm w-100 py-2 fw-semibold">Gunakan Soal Ini</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 text-center py-5 bg-white">
                <div class="text-muted mb-2">
                    <span class="material-symbols-outlined fs-1 text-secondary">folder_open</span>
                </div>
                <h5 class="fw-bold text-dark">Belum ada soal publik</h5>
                <p class="text-muted small mb-0">Belum ada asesmen publik yang dibagikan oleh guru lain atau sesuai dengan pencarian Anda.</p>
            </div>
        </div>
        @endforelse
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Agar saat menekan Enter di kolom pencarian, form langsung otomatis submit mencari data ke server
    function handleSearch(event) {
        if (event.key === 'Enter') {
            document.getElementById('searchForm').submit();
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
