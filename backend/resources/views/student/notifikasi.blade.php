@extends('layouts.app')

@section('title', 'Notifikasi Siswa')

@section('content')
<div class="screen" style="position: relative; width: 100%; min-height: 100vh; background: var(--bg); padding-bottom: 90px;">

    <!-- Topbar / Header -->
    <div class="topbar" style="background: transparent; padding: 20px 24px 16px; display: flex; align-items: center; gap: 12px;">
        <a href="{{ route('siswa.dashboard') }}" style="color: var(--text); text-decoration: none; display: flex; align-items: center;">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 style="font-size: 18px; font-weight: 800; color: var(--text);">Notifikasi Saya</h1>
    </div>

    <div class="px" style="padding: 0 24px; display: flex; flex-direction: column; gap: 12px;">

        @forelse($notifications ?? [] as $notif)
            @php
                $borderColor = 'var(--primary)';
                $bgColor = 'rgba(37, 99, 235, 0.1)';
                $icon = 'notifications';

                if($notif->type == 'assessment') {
                    $borderColor = '#10B981';
                    $bgColor = 'rgba(16, 185, 129, 0.1)';
                    $icon = 'assignment';
                } elseif($notif->type == 'security') {
                    $borderColor = '#8B5CF6';
                    $bgColor = 'rgba(139, 92, 246, 0.1)';
                    $icon = 'security';
                }
            @endphp

            <div class="card" style="padding: 16px; display: flex; gap: 14px; align-items: flex-start; background: var(--card-bg); border-left: 4px solid {{ $borderColor }};">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: {{ $bgColor }}; color: {{ $borderColor }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <span class="material-symbols-outlined" style="font-size: 20px;">{{ $icon }}</span>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                        <span style="font-size: 13.5px; font-weight: 800; color: var(--text);">{{ $notif->title }}</span>
                        <span style="font-size: 11px; color: var(--muted);">{{ $notif->created_at->diffForHumans() }}</span>
                    </div>
                    <div style="font-size: 13px; color: var(--text); line-height: 1.4;">
                        {!! $notif->message !!}
                    </div>
                </div>
            </div>
        @empty
            <div class="card" style="padding: 50px 20px; text-align: center; color: var(--muted); background: var(--card-bg); border-radius: 16px;">
                <span class="material-symbols-outlined" style="font-size: 48px; margin-bottom: 12px; opacity: 0.4;">notifications_off</span>
                <div style="font-size: 15px; font-weight: 800; color: var(--text);">Belum ada notifikasi</div>
                <div style="font-size: 12.5px; margin-top: 4px; color: var(--muted);">Informasi asesmen baru dari guru akan muncul di sini secara otomatis.</div>
            </div>
        @endforelse

    </div>
</div>
@endsection

@section('bottom_nav')
<div class="bottomnav">
    <a href="{{ route('siswa.dashboard') }}" class="navitem" style="text-decoration:none;">
        <span class="material-symbols-outlined">space_dashboard</span>Dashboard
    </a>
    <a href="{{ route('siswa.notifikasi') }}" class="navitem active" style="text-decoration:none;">
        <span class="material-symbols-outlined">notifications</span>Notifikasi
    </a>
    <!-- Tambahkan menu siswa lainnya di sini sesuai kebutuhan -->
</div>
@endsection
