@extends('layouts.app')

@section('title', 'Kelas Saya')

@push('styles')
<style>
    .thumb {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .card-tap { cursor: pointer; transition: transform 0.15s ease; text-decoration: none; display: block; color: inherit; }
    .card-tap:active { transform: scale(0.98); }
</style>
@endpush

@section('content')
<!-- Topbar -->
<div class="topbar">
    <h1>Kelas Saya</h1>
</div>

<div class="px" style="padding-top: 16px;">

    <!-- Simulasi Data Array (Nantinya data ini dikirim dari Controller/Database) -->
    @php
        $kelas = [
            ['id' => '7a', 'nama' => 'Kelas 7A', 'siswa' => 28, 'assessment' => 5],
            ['id' => '7b', 'nama' => 'Kelas 7B', 'siswa' => 26, 'assessment' => 4],
            ['id' => '8a', 'nama' => 'Kelas 8A', 'siswa' => 30, 'assessment' => 6],
        ];
    @endphp

    <!-- Looping Data Kelas -->
    @foreach($kelas as $k)
    <div class="card card-tap" style="margin-bottom:14px;">
        <div class="row">
            <div class="thumb" style="background:var(--primary-soft);">
                <span class="material-symbols-outlined" style="color:var(--primary);">groups_2</span>
            </div>
            <div style="flex:1;">
                <div style="font-weight:800; font-size:14.5px;">{{ $k['nama'] }}</div>
                <div class="muted">{{ $k['siswa'] }} Siswa · {{ $k['assessment'] }} Assessment</div>
            </div>
        </div>

        <!-- Tombol ini nantinya bisa dihubungkan ke route detail kelas -->
        <a href="#" class="btn btn-secondary btn-sm btn-block" style="margin-top:14px; text-decoration:none;">
            Buka Kelas
        </a>
    </div>
    @endforeach

    <!-- Spasi bawah agar tidak tertutup bottom nav -->
    <div style="height:24px;"></div>
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
    <a href="{{ route('guru.detail-kelas', ['id' => $k['id']]) }}" class="btn btn-secondary btn-sm btn-block" style="margin-top:14px; text-decoration:none;">
    Buka Kelas
    </a>
    <a href="{{ route('guru.bank-soal') }}" class="navitem {{ request()->routeIs('guru.bank-soal') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">folder_open</span>Assessment
    </a>
    <a href="{{ route('guru.profil') }}" class="navitem {{ request()->routeIs('guru.profil') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">person</span>Profil
    </a>
</div>
@endsection
