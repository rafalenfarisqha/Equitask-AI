@extends('layouts.app')

@section('content')
<div class="screen" style="position: relative; width: 100%; height: auto; background: var(--bg); padding-bottom: 40px;">
    <!-- Topbar Header -->
    <div class="topbar" style="background: transparent; padding: 20px 24px 10px;">
        <div class="row" style="display: flex; align-items: center; gap: 12px; flex: 1;">
            <div class="avatar" style="background: var(--secondary-soft); color: var(--secondary);">DA</div>
            <div>
                <div style="font-size: 15px; font-weight: 800;">Halo, Dinda 👋</div>
                <div class="muted" style="font-size: 12px;">Kelas 7A · Mode Adaptif</div>
            </div>
        </div>
        <button class="iconbtn"><span class="material-symbols-outlined">notifications</span></button>
    </div>

    <div class="px" style="padding: 0 24px;">
        <!-- Student Hero -->
        <div class="card student-dashboard-hero" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 45%, #e0f2fe 100%); border-radius: 20px; padding: 20px; border: 1px solid rgba(37, 99, 235, 0.12);">
            <div class="student-badge" style="display: inline-flex; align-items: center; gap: 6px; background: rgba(37,99,235,0.1); color: var(--primary); padding: 5px 10px; border-radius: 999px; font-size: 10.5px; font-weight: 800; text-transform: uppercase;">
                <span class="material-symbols-outlined" style="font-size: 11px;">spark</span> Fokus Hari Ini
            </div>
            <div style="font-size: 18px; font-weight: 800; margin-top: 8px; line-height: 1.35;">Semangat, Dinda — kamu hampir sampai garis finish!</div>
            <div class="muted" style="font-size: 12px; margin-top: 6px;">Satu assessment IPA siap dikerjakan dan dua materi review menunggu.</div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 14px;">
                <div style="background: rgba(255,255,255,0.7); padding: 10px; border-radius: 12px;"><strong style="font-size: 15px;">4 hari</strong><small style="font-size: 10px; color: var(--muted); display: block;">Streak belajar</small></div>
                <div style="background: rgba(255,255,255,0.7); padding: 10px; border-radius: 12px;"><strong style="font-size: 15px;">180 pts</strong><small style="font-size: 10px; color: var(--muted); display: block;">Poin aktif</small></div>
            </div>
        </div>

        <!-- Action Card -->
        <a href="{{ route('student.assessment') }}" class="card card-tap" style="display: block; margin-top: 14px; text-decoration: none; color: inherit; padding: 16px;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div class="thumb" style="background: var(--primary-soft); width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <span class="material-symbols-outlined" style="color: var(--primary);">play_circle</span>
                    </div>
                    <div>
                        <div style="font-weight: 800; font-size: 13.5px;">Assessment Hari Ini</div>
                        <div class="muted" style="font-size: 12px;">IPA — Ekosistem & Rantai Makanan</div>
                    </div>
                </div>
                <span class="badge badge-blue">Mulai</span>
            </div>
        </a>

        <div class="section-title" style="font-size: 15px; font-weight: 800; margin: 20px 0 10px;">Rencana Belajarmu</div>
        <div class="card" style="padding: 14px;">
            <div style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px;">
                <span class="material-symbols-outlined" style="color: var(--secondary); font-size: 20px;">task_alt</span>
                <div style="flex: 1;">
                    <div style="font-weight: 700; font-size: 13px;">Kerjakan assessment IPA</div>
                    <div class="muted" style="font-size: 11.5px; margin-top: 2px;">Selesai sebelum Jumat agar nilai aman.</div>
                </div>
            </div>
            <div style="display: flex; align-items: flex-start; gap: 10px;">
                <span class="material-symbols-outlined" style="color: var(--primary); font-size: 20px;">menu_book</span>
                <div style="flex: 1;">
                    <div style="font-weight: 700; font-size: 13px;">Review materi rantai makanan</div>
                    <div class="muted" style="font-size: 11.5px; margin-top: 2px;">Cukup 10 menit sebelum istirahat siang.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
