@extends('layouts.app')

@section('content')
<div class="screen" style="position: relative; width: 100%; height: auto; background: var(--bg); padding-bottom: 40px;">
    <!-- Topbar -->
    <div class="topbar" style="background: transparent; padding: 20px 24px 10px;">
        <h1 style="font-size: 20px; font-weight: 800; flex: 1;">Bank Materi & Dokumen Acuan</h1>
        <a href="{{ route('materials.create') }}" class="btn btn-primary btn-sm" style="text-decoration: none;">
            <span class="material-symbols-outlined" style="font-size: 16px;">upload_file</span> Upload Modul
        </a>
    </div>

    <!-- Info Banner -->
    <div class="px" style="padding: 0 24px 16px;">
        <div class="card" style="background: linear-gradient(135deg, var(--primary-soft) 0%, #eef5ff 100%); border: 1px solid rgba(37, 99, 235, 0.15);">
            <div class="row" style="display: flex; align-items: flex-start; gap: 12px;">
                <span class="material-symbols-outlined" style="color: var(--primary); font-size: 24px;">lightbulb</span>
                <div style="flex: 1;">
                    <div style="font-weight: 800; font-size: 13.5px; color: var(--primary);">Integrasi AI Dokumen</div>
                    <div class="muted" style="margin-top: 4px; font-size: 12px; color: var(--text);">Modul dan dokumen yang diunggah di sini akan otomatis dianalisis oleh AI untuk menyusun soal ujian dan materi adaptif yang selaras dengan Kurikulum Merdeka.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- List Materi -->
    <div class="px" style="padding: 0 24px;">
        @php
            $materials = [
                ['judul' => 'Modul Ajar IPS Kelas VII - Sistem Pemerintahan', 'mapel' => 'IPS', 'ukuran' => '2.4 MB', 'tanggal' => '02 Jul 2026', 'status' => 'Tergabung dalam 3 Assessment'],
                ['judul' => 'Modul Ekosistem dan Rantai Makanan Kelas VII', 'mapel' => 'IPA', 'ukuran' => '3.1 MB', 'tanggal' => '10 Jun 2026', 'status' => 'Tergabung dalam 5 Assessment'],
                ['judul' => 'Panduan Pecahan dan Aljabar Dasar', 'mapel' => 'Matematika', 'ukuran' => '1.8 MB', 'tanggal' => '25 May 2026', 'status' => 'Tergabung dalam 2 Assessment'],
            ];
        @endphp

        @foreach($materials as $m)
        <div class="card" style="margin-bottom: 14px;">
            <div class="row" style="display: flex; align-items: center; gap: 14px;">
                <div class="thumb" style="background: #EEF2FF; width: 46px; height: 46px; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                    <span class="material-symbols-outlined" style="color: #4F46E5;">description</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 700; font-size: 13.5px;">{{ $m['judul'] }}</div>
                    <div class="muted" style="font-size: 12px; margin-top: 2px;">{{ $m['mapel'] }} · {{ $m['ukuran'] }} · Diunggah {{ $m['tanggal'] }}</div>
                </div>
                <span class="badge badge-slate">PDF</span>
            </div>

            <div class="divider" style="margin: 12px 0;"></div>

            <div class="row" style="display: flex; align-items: center; justify-content: space-between;">
                <span class="muted" style="font-size: 11.5px; font-weight: 600; color: var(--secondary);">
                    <span class="material-symbols-outlined" style="font-size: 13px; vertical-align: middle;">check_circle</span> {{ $m['status'] }}
                </span>
                <div style="display: flex; gap: 8px;">
                    <a href="#" class="btn btn-outline btn-sm" style="text-decoration: none;">Lihat Modul</a>
                    <a href="#" class="btn btn-outline btn-sm" style="text-decoration: none; color: var(--danger); border-color: #fecaca;">Hapus</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
