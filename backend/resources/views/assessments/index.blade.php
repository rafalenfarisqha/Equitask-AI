@extends('layouts.app')

@section('content')
<div class="screen" style="position: relative; width: 100%; height: auto; background: var(--bg); padding-bottom: 40px;">
    <!-- Topbar -->
    <div class="topbar" style="background: transparent; padding: 20px 24px 10px;">
        <h1 style="font-size: 20px; font-weight: 800; flex: 1;">Daftar Assessment</h1>
        <a href="{{ route('assessments.create') }}" class="btn btn-primary btn-sm" style="text-decoration: none;">
            <span class="material-symbols-outlined" style="font-size: 16px;">add</span> Buat Baru
        </a>
    </div>

    <!-- Filter & Search -->
    <div class="px" style="padding: 0 24px 16px;">
        <div class="searchbar">
            <span class="material-symbols-outlined" style="color: var(--muted); font-size: 19px;">search</span>
            <input type="text" placeholder="Cari assessment berdasarkan nama atau mapel..." id="searchAssessment">
        </div>
        <div class="filter-row" style="margin-top: 12px; display: flex; gap: 8px; overflow-x: auto;">
            <div class="chip sel" style="padding: 7px 14px; font-size: 12px;">Semua</div>
            <div class="chip" style="padding: 7px 14px; font-size: 12px;">IPA</div>
            <div class="chip" style="padding: 7px 14px; font-size: 12px;">Matematika</div>
            <div class="chip" style="padding: 7px 14px; font-size: 12px;">B. Indonesia</div>
            <div class="chip" style="padding: 7px 14px; font-size: 12px;">IPS</div>
        </div>
    </div>

    <!-- List Assessment -->
    <div class="px" style="padding: 0 24px;">
        @php
            $assessments = [
                ['nama' => 'Ekosistem & Rantai Makanan', 'mapel' => 'IPA', 'status' => 'Published', 'tanggal' => '10 Jul 2026', 'icon' => 'eco', 'color' => '#10B981'],
                ['nama' => 'Operasi Pecahan', 'mapel' => 'Matematika', 'status' => 'Draft', 'tanggal' => '08 Jul 2026', 'icon' => 'calculate', 'color' => '#2563EB'],
                ['nama' => 'Teks Deskripsi', 'mapel' => 'Bahasa Indonesia', 'status' => 'Published', 'tanggal' => '05 Jul 2026', 'icon' => 'menu_book', 'color' => '#F59E0B'],
                ['nama' => 'Sistem Pemerintahan', 'mapel' => 'IPS', 'status' => 'Draft', 'tanggal' => '02 Jul 2026', 'icon' => 'account_balance', 'color' => '#8B5CF6'],
            ];
        @endphp

        @foreach($assessments as $a)
        <div class="card" style="margin-bottom: 14px;">
            <div class="row" style="display: flex; align-items: center; gap: 12px;">
                <div class="thumb" style="background: {{ $a['color'] }}1A; width: 46px; height: 46px; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                    <span class="material-symbols-outlined" style="color: {{ $a['color'] }};">{{ $a['icon'] }}</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 700; font-size: 14px;">{{ $a['nama'] }}</div>
                    <div class="muted" style="font-size: 12px;">{{ $a['mapel'] }} · Dibuat {{ $a['tanggal'] }}</div>
                </div>
                <div>
                    @if($a['status'] === 'Published')
                        <span class="badge badge-green"><span class="material-symbols-outlined" style="font-size: 12px;">check_circle</span> Published</span>
                    @else
                        <span class="badge badge-amber"><span class="material-symbols-outlined" style="font-size: 12px;">edit</span> Draft</span>
                    @endif
                </div>
            </div>

            <div style="margin-top: 14px; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px;">
                <a href="#" class="btn btn-outline btn-sm" style="text-decoration: none; text-align: center; display: block;">Preview</a>
                <a href="#" class="btn btn-outline btn-sm" style="text-decoration: none; text-align: center; display: block;">Edit</a>
                <a href="#" class="btn btn-outline btn-sm" style="text-decoration: none; text-align: center; display: block;">Duplicate</a>
                @if($a['status'] === 'Draft')
                    <button class="btn btn-secondary btn-sm" style="width: 100%;">Publish</button>
                @else
                    <button class="btn btn-outline btn-sm" style="width: 100%; color: var(--danger); border-color: #fecaca;">Arsip</button>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
