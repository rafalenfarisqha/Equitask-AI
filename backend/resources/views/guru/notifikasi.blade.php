@extends('layouts.app')

@section('title', 'Notifikasi Aktivitas — EquiTask AI')

@section('content')
<div class="px py-4" style="max-width: 900px; margin: 0 auto; padding-bottom: 90px;">

    <!-- Header -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('guru.dashboard') }}" class="btn btn-light border rounded-3 p-2 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
            <span class="material-symbols-outlined fs-5">arrow_back</span>
        </a>
        <div>
            <h1 class="h4 fw-bold text-dark mb-1">Notifikasi Aktivitas</h1>
            <p class="text-muted small mb-0">Pantau seluruh aktivitas asesmen, siswa, dan sistem secara real-time.</p>
        </div>
    </div>

    <!-- Daftar Notifikasi -->
    <div class="row g-3">
        @forelse($notifications as $notif)
            @php
                // Tentukan warna dan ikon berdasarkan tipe notifikasi
                $icon = 'notifications';
                $bgClass = 'bg-primary bg-opacity-10 text-primary';
                $borderLeft = '#2563eb';

                if ($notif->type == 'assessment' || $notif->type == 'pengerjaan') {
                    $icon = 'assignment_turned_in';
                    $bgClass = 'bg-success bg-opacity-10 text-success';
                    $borderLeft = '#10b981';
                } elseif ($notif->type == 'bank_soal') {
                    $icon = 'library_books';
                    $bgClass = 'bg-info bg-opacity-10 text-info';
                    $borderLeft = '#0ea5e9';
                } elseif ($notif->type == 'ai_generation' || $notif->type == 'login') {
                    $icon = 'auto_awesome';
                    $bgClass = 'bg-warning bg-opacity-10 text-warning';
                    $borderLeft = '#f59e0b';
                }
            @endphp

            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white position-relative overflow-hidden" style="border-left: 5px solid {{ $borderLeft }} !important;">
                    <div class="d-flex align-items-start gap-3">
                        <!-- Ikon Kotak -->
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 {{ $bgClass }}" style="width: 46px; height: 46px;">
                            <span class="material-symbols-outlined fs-5">{{ $icon }}</span>
                        </div>

                        <!-- Konten Pesan -->
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <h6 class="fw-bold text-dark mb-0 fs-6">{{ $notif->title }}</h6>
                                <span class="text-muted" style="font-size: 11.5px;">{{ $notif->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-muted small mb-0 lh-base">{!! $notif->message !!}</p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 text-center py-5 bg-white">
                    <div class="text-muted mb-2">
                        <span class="material-symbols-outlined fs-1 text-secondary opacity-50">notifications_off</span>
                    </div>
                    <h6 class="fw-bold text-dark">Belum ada notifikasi</h6>
                    <p class="text-muted small mb-0">Semua pemberitahuan aktivitas terbaru akan muncul di sini.</p>
                </div>
            </div>
        @endforelse
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
