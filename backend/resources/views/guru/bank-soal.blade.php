@extends('layouts.app')

@section('title', 'Bank Soal Komunitas — EquiTask AI')

@section('content')
<div class="container py-4"
     style="max-width: 1024px; padding-bottom: 90px !important;">

    {{-- HEADER --}}
    <div class="d-flex align-items-center mb-4">
        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3 me-3 d-flex align-items-center justify-content-center"
             style="width: 50px; height: 50px;">
            <span class="material-symbols-outlined fs-4">
                library_books
            </span>
        </div>

        <div>
            <h1 class="h4 fw-bold text-dark mb-1">
                Bank Soal Komunitas
            </h1>
            <p class="text-muted small mb-0">
                Jelajahi dan unduh asesmen yang dibagikan oleh guru lain.
            </p>
        </div>
    </div>

    {{-- PENCARIAN --}}
    <form method="GET"
          action="{{ route('guru.bank-soal') }}"
          class="mb-4"
          id="searchForm">

        <div class="input-group shadow-sm rounded-3 overflow-hidden bg-white border mb-3">
            <span class="input-group-text bg-white border-0 text-muted ps-3">
                <span class="material-symbols-outlined fs-5">
                    search
                </span>
            </span>

            <input
                type="text"
                name="search"
                id="search-soal"
                value="{{ request('search') }}"
                class="form-control border-0 py-2 ps-0 shadow-none"
                placeholder="Cari judul soal atau mata pelajaran..."
                onkeydown="handleSearch(event)">
        </div>

        {{-- FILTER MATA PELAJARAN --}}
        @php
            $currentMapel = request('mapel', '');
        @endphp

        <div class="d-flex gap-2 overflow-x-auto pb-2">

            <a href="{{ route('guru.bank-soal', array_merge(
                    request()->except(['mapel', 'page']),
                    ['mapel' => '']
                )) }}"
               class="btn btn-sm rounded-pill px-3 {{ $currentMapel === '' ? 'btn-primary' : 'btn-light text-dark border bg-white' }}">
                Semua
            </a>

            <a href="{{ route('guru.bank-soal', array_merge(
                    request()->except(['mapel', 'page']),
                    ['mapel' => 'IPA']
                )) }}"
               class="btn btn-sm rounded-pill px-3 {{ $currentMapel === 'IPA' ? 'btn-primary' : 'btn-light text-dark border bg-white' }}">
                IPA
            </a>

            <a href="{{ route('guru.bank-soal', array_merge(
                    request()->except(['mapel', 'page']),
                    ['mapel' => 'Matematika']
                )) }}"
               class="btn btn-sm rounded-pill px-3 {{ $currentMapel === 'Matematika' ? 'btn-primary' : 'btn-light text-dark border bg-white' }}">
                Matematika
            </a>

            <a href="{{ route('guru.bank-soal', array_merge(
                    request()->except(['mapel', 'page']),
                    ['mapel' => 'Bahasa Indonesia']
                )) }}"
               class="btn btn-sm rounded-pill px-3 {{ $currentMapel === 'Bahasa Indonesia' ? 'btn-primary' : 'btn-light text-dark border bg-white' }}">
                B. Indonesia
            </a>

        </div>
    </form>

    {{-- DAFTAR ASESMEN PUBLIK --}}
    <div class="row g-3">

        @forelse ($publicAssessments as $item)

            <div class="col-12">
                <div class="card border shadow-sm rounded-4 p-3 bg-white h-100">

                    {{-- INFORMASI ASESMEN --}}
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-3">

                        <div class="d-flex align-items-center gap-3">

                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width: 45px;
                                        height: 45px;
                                        background-color: {{ $item->color ?? '#2563EB' }}15;
                                        color: {{ $item->color ?? '#2563EB' }};">

                                <span class="material-symbols-outlined">
                                    {{ $item->icon ?? 'eco' }}
                                </span>
                            </div>

                            <div>
                                <h5 class="fw-bold fs-6 mb-1 text-dark">
                                    {{ $item->nama }}
                                </h5>

                                <p class="text-muted small mb-1">
                                    {{ $item->mapel }}
                                    @if ($item->kelas)
                                        · Kelas {{ $item->kelas }}
                                    @endif
                                </p>

                                <p class="text-muted small mb-0">
                                    <span class="material-symbols-outlined align-middle me-1"
                                          style="font-size: 16px;">
                                        person
                                    </span>

                                    Dibuat oleh
                                    <span class="fw-semibold">
                                        {{ $item->teacher?->name ?? 'Guru tidak diketahui' }}
                                    </span>
                                </p>
                            </div>

                        </div>

                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-semibold">
                            Publik
                        </span>

                    </div>

                    {{-- AKSI BANK SOAL --}}
                    <div class="border-top pt-3 mt-auto">

                        <a href="{{ route('guru.bank-soal.detail', $item->id) }}"
                           class="btn btn-primary w-100 py-2 fw-semibold">

                            <span class="material-symbols-outlined align-middle me-1">
                                visibility
                            </span>

                            Lihat Detail Soal
                        </a>

                    </div>

                </div>
            </div>

        @empty

            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 text-center py-5 bg-white">

                    <div class="text-muted mb-2">
                        <span class="material-symbols-outlined fs-1 text-secondary">
                            folder_open
                        </span>
                    </div>

                    <h5 class="fw-bold text-dark">
                        Belum ada soal publik
                    </h5>

                    <p class="text-muted small mb-0 px-3">
                        Belum ada asesmen yang dipublikasikan atau
                        sesuai dengan pencarian Anda.
                    </p>

                </div>
            </div>

        @endforelse

    </div>

</div>
@endsection

{{-- SCRIPT PENCARIAN --}}
@push('scripts')
<script>
    function handleSearch(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            document.getElementById('searchForm').submit();
        }
    }
</script>
@endpush

{{-- NAVIGASI BAWAH --}}
@section('bottom_nav')
<div class="bottomnav">

    <a href="{{ route('guru.dashboard') }}"
       class="navitem {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}"
       style="text-decoration: none;">
        <span class="material-symbols-outlined">space_dashboard</span>
        Dashboard
    </a>

    <a href="{{ route('guru.kelas') }}"
       class="navitem {{ request()->routeIs('guru.kelas') ? 'active' : '' }}"
       style="text-decoration: none;">
        <span class="material-symbols-outlined">groups</span>
        Kelas
    </a>

    <a href="{{ route('guru.assessments.index') }}"
       class="navitem {{ request()->routeIs('guru.assessments.*') ? 'active' : '' }}"
       style="text-decoration: none;">
        <span class="material-symbols-outlined">library_books</span>
        Asesmen
    </a>

    <a href="{{ route('guru.bank-soal') }}"
       class="navitem {{ request()->routeIs('guru.bank-soal', 'guru.bank-soal.detail') ? 'active' : '' }}"
       style="text-decoration: none;">
        <span class="material-symbols-outlined">folder_open</span>
        Bank Soal
    </a>

    <a href="{{ route('guru.profil') }}"
       class="navitem {{ request()->routeIs('guru.profil') ? 'active' : '' }}"
       style="text-decoration: none;">
        <span class="material-symbols-outlined">person</span>
        Profil
    </a>

</div>
@endsection
