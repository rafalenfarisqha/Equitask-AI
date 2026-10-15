@extends('layouts.app')

@section('content')
<div class="screen" style="position: relative; width: 100%; height: auto; background: var(--bg); padding-bottom: 40px;">
    <!-- Topbar -->
    <div class="topbar" style="background: transparent; padding: 20px 24px 10px;">
        <div style="width: 42px; height: 42px; border-radius: 12px; background: rgba(37, 99, 235, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 800; margin-right: 12px;">
            <span class="material-symbols-outlined">library_books</span>
        </div>
        <div style="flex: 1;">
            <h1 style="font-size: 18px; font-weight: 800;">Bank Soal Komunitas</h1>
            <div class="muted" style="font-size: 12px;">Jelajahi & gunakan soal dari guru-guru se-Indonesia</div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="px" style="padding: 0 24px 16px;">
        <div class="searchbar">
            <span class="material-symbols-outlined" style="color: var(--muted); font-size: 19px;">search</span>
            <input type="text" placeholder="Cari soal publik atau mata pelajaran..." id="searchBankSoal">
        </div>
        <div class="filter-row" style="margin-top: 12px; display: flex; gap: 8px; overflow-x: auto;">
            <a href="{{ route('guru.bank-soal') }}" class="chip {{ request('mapel') == '' ? 'sel' : '' }}" style="padding: 7px 14px; font-size: 12px; text-decoration: none;">Semua</a>
            <a href="{{ route('guru.bank-soal', ['mapel' => 'IPA']) }}" class="chip {{ request('mapel') == 'IPA' ? 'sel' : '' }}" style="padding: 7px 14px; font-size: 12px; text-decoration: none;">IPA</a>
            <a href="{{ route('guru.bank-soal', ['mapel' => 'Matematika']) }}" class="chip {{ request('mapel') == 'Matematika' ? 'sel' : '' }}" style="padding: 7px 14px; font-size: 12px; text-decoration: none;">Matematika</a>
            <a href="{{ route('guru.bank-soal', ['mapel' => 'Bahasa Indonesia']) }}" class="chip {{ request('mapel') == 'Bahasa Indonesia' ? 'sel' : '' }}" style="padding: 7px 14px; font-size: 12px; text-decoration: none;">B. Indonesia</a>
        </div>
    </div>

    <!-- List Soal Publik -->
    <div class="px" style="padding: 0 24px;">
        @forelse($publicAssessments as $item)
        <div class="card" style="margin-bottom: 14px;">
            <div class="row" style="display: flex; align-items: center; gap: 12px;">
                <div class="thumb" style="background: {{ $item->color ?? '#2563EB' }}1A; width: 46px; height: 46px; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                    <span class="material-symbols-outlined" style="color: {{ $item->color ?? '#2563EB' }};">{{ $item->icon ?? 'eco' }}</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 700; font-size: 14px;">{{ $item->nama }}</div>
                    <div class="muted" style="font-size: 11.5px;">{{ $item->mapel }} · Dibagikan oleh Guru Pengampu</div>
                </div>
                <span class="badge badge-green" style="font-size: 10.5px;">Publik</span>
            </div>

            <div style="margin-top: 14px; display: flex; gap: 8px;">
                <a href="#" class="btn btn-outline btn-sm" style="flex: 1; text-align: center; text-decoration: none; display: block;">Preview Soal</a>
                <a href="#" class="btn btn-primary btn-sm" style="flex: 1; text-align: center; text-decoration: none; display: block;">Gunakan Soal Ini</a>
            </div>
        </div>
        @empty
        <div class="card" style="text-align: center; padding: 30px;">
            <div class="muted">Belum ada soal publik yang dibagikan oleh guru lain.</div>
        </div>
        @endforelse
    </div>
</div>
@endsection
