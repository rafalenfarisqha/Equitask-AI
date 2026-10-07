@extends('layouts.app')

@section('content')
<div class="screen" style="position: relative; width: 100%; height: auto; background: var(--bg); padding-bottom: 40px;">
    <!-- Topbar Header -->
    <div class="topbar" style="background: transparent; padding: 20px 24px 10px;">
        <div class="row" style="display: flex; align-items: center; gap: 12px; flex: 1;">
            <div class="avatar" style="background: linear-gradient(135deg, var(--primary-soft), #dfeeff); color: var(--primary);">AW</div>
            <div>
                <div style="font-size: 15px; font-weight: 800;">Halo, Bu Anita 👋</div>
                <div class="muted" style="font-size: 12px;">SMP Inklusif Harapan Bangsa</div>
            </div>
        </div>
        <button class="iconbtn"><span class="material-symbols-outlined">notifications</span></button>
    </div>

    <div class="px" style="padding: 0 24px;">
        <!-- Hero Card -->
        <div class="card dashboard-hero" style="margin-top: 8px; background: linear-gradient(135deg, #173f8f 0%, #2563eb 48%, #0f766e 100%); color: #fff; padding: 20px; border-radius: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div class="hero-pill" style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 10px; border-radius: 999px; background: rgba(255,255,255,0.16); font-size: 10.5px; font-weight: 800; text-transform: uppercase;">
                        <span class="material-symbols-outlined" style="font-size: 12px;">monitoring</span> Live Insights
                    </div>
                    <div style="font-size: 17px; font-weight: 800; margin-top: 8px; line-height: 1.35;">Kinerja kelas Anda terasa solid minggu ini</div>
                    <div style="font-size: 12px; margin-top: 6px; opacity: 0.9;">3 kelas aktif, 15 assessment siap dipantau, dan 9 assessment sudah dipublikasikan.</div>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 16px;">
                <div style="background: rgba(255,255,255,0.14); border-radius: 12px; padding: 10px; text-align: center;"><span style="display:block; font-size:16px; font-weight:800;">3</span><small style="font-size:10px; opacity:0.8;">Kelas Aktif</small></div>
                <div style="background: rgba(255,255,255,0.14); border-radius: 12px; padding: 10px; text-align: center;"><span style="display:block; font-size:16px; font-weight:800;">15</span><small style="font-size:10px; opacity:0.8;">Assessment</small></div>
                <div style="background: rgba(255,255,255,0.14); border-radius: 12px; padding: 10px; text-align: center;"><span style="display:block; font-size:16px; font-weight:800;">92%</span><small style="font-size:10px; opacity:0.8;">Progress</small></div>
            </div>
        </div>

        <!-- Stat Grid -->
        <div class="stat-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-top: 14px;">
            <div class="stat-card" style="background: #fff; padding: 16px; border-radius: 14px; box-shadow: var(--shadow);">
                <span class="material-symbols-outlined" style="background: var(--primary-soft); color: var(--primary); padding: 8px; border-radius: 10px;">groups</span>
                <div style="font-size: 22px; font-weight: 800; margin-top: 10px;">3</div>
                <div class="lbl" style="font-size: 11.5px; color: var(--muted);">Jumlah Kelas</div>
            </div>
            <div class="stat-card" style="background: #fff; padding: 16px; border-radius: 14px; box-shadow: var(--shadow);">
                <span class="material-symbols-outlined" style="background: var(--primary-soft); color: var(--primary); padding: 8px; border-radius: 10px;">assignment</span>
                <div style="font-size: 22px; font-weight: 800; margin-top: 10px;">15</div>
                <div class="lbl" style="font-size: 11.5px; color: var(--muted);">Jumlah Assessment</div>
            </div>
        </div>

        <div class="section-title" style="font-size: 15px; font-weight: 800; margin: 20px 0 10px; display: flex; justify-content: space-between;">
            <span>Assessment Terbaru</span>
            <a href="{{ route('assessments.index') }}" style="font-size: 12.5px; color: var(--primary); text-decoration: none;">Lihat Semua</a>
        </div>

        <!-- Quick Item -->
        <div class="card" style="margin-bottom: 12px; padding: 14px 16px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div class="thumb" style="background: #10B9811A; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <span class="material-symbols-outlined" style="color: #10B981;">eco</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 700; font-size: 13.5px;">Ekosistem & Rantai Makanan</div>
                    <div class="muted" style="font-size: 12px;">IPA · 10 Jul 2026</div>
                </div>
                <span class="badge badge-green">Published</span>
            </div>
        </div>
    </div>
</div>
@endsection
