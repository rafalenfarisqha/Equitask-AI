@extends('layouts.app')

@section('title', 'Pengerjaan Assessment — EquiTask AI')

@push('styles')
<style>
    .qcard {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(246, 250, 255, 0.96) 100%);
        border-radius: var(--radius); padding: 20px;
        box-shadow: 0 18px 32px -22px rgba(15, 23, 42, 0.26);
        border: 1px solid rgba(255, 255, 255, 0.72);
        margin-bottom: 16px;
    }
    .qcard.adaptif { padding: 24px; border-radius: 24px; }
    .qtext {
        font-weight: 700; font-size: 14.5px; line-height: 1.55; color: var(--text);
    }
    .illust-box {
        background: linear-gradient(135deg, var(--secondary-soft), var(--primary-soft));
        border-radius: 16px; padding: 12px; display: flex; align-items: center; justify-content: center;
        margin-bottom: 14px; min-height: 180px;
    }
    .option {
        display: flex; align-items: center; gap: 12px; padding: 14px;
        border: 1.5px solid rgba(148, 163, 184, 0.2); border-radius: 14px;
        margin-top: 10px; cursor: pointer; font-size: 13.5px; font-weight: 600;
        background: rgba(255, 255, 255, 0.84); transition: all 0.2s ease;
    }
    .option.sel {
        border-color: rgba(37, 99, 235, 0.35);
        background: linear-gradient(135deg, var(--primary-soft) 0%, #dfeeff 100%);
    }
    .optkey {
        width: 26px; height: 26px; border-radius: 8px; background: var(--border);
        display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; flex-shrink: 0;
    }
    .option.sel .optkey { background: var(--primary); color: #fff; }
    .progress-bar { height: 8px; background: var(--border); border-radius: 99px; overflow: hidden; flex: 1; }
    .progress-bar > div { height: 100%; background: var(--secondary); border-radius: 99px; transition: width 0.3s; }
</style>
@endpush

@section('content')
<!-- Topbar Ujian dengan Timer -->
<div class="topbar">
    <a href="{{ route('siswa.dashboard') }}" class="iconbtn" onclick="return confirm('Keluar dari assessment?')">
        <span class="material-symbols-outlined">close</span>
    </a>
    <div style="flex:1; padding: 0 12px;">
        <div class="progress-bar"><div style="width: 40%;"></div></div>
    </div>
    <div class="row" style="gap:4px; font-weight:800; font-size:13px; color:var(--primary);">
        <span class="material-symbols-outlined" style="font-size:17px;">timer</span>24:12
    </div>
</div>

<div class="px" style="padding-top: 14px;">

    <!-- Informasi Nomor Soal & Tombol Bookmark -->
    <div class="row" style="justify-content:space-between; margin-bottom:10px;">
        <span class="muted">Soal 2 dari 10</span>
        <button class="iconbtn" style="width:32px;height:32px;">
            <span class="material-symbols-outlined" style="font-size:18px; color:var(--muted);">bookmark_border</span>
        </button>
    </div>

    <!-- Kartu Soal (Mode Adaptif / Ilustrasi) -->
    <div class="qcard adaptif">
        <div class="illust-box">
            <!-- Ilustrasi / Gambar Pendukung Soal Adaptif -->
            <span class="material-symbols-outlined" style="font-size: 64px; color: var(--primary);">eco</span>
        </div>

        <div class="qtext">
            Di sebuah padang rumput, penggunaan pestisida menyebabkan populasi belalang menurun secara drastis. Perubahan tersebut memengaruhi makhluk hidup lain. Apa yang terjadi pada katak?
        </div>

        <div style="margin-top: 16px;">
            <!-- Opsi Jawaban A -->
            <div class="option sel" onclick="selectOption(this)">
                <div class="optkey">A</div>
                <div>Populasi katak menurun karena sumber makanannya berkurang.</div>
            </div>
            <!-- Opsi Jawaban B -->
            <div class="option" onclick="selectOption(this)">
                <div class="optkey">B</div>
                <div>Populasi ular meningkat karena lebih mudah mendapatkan makanan.</div>
            </div>
            <!-- Opsi Jawaban C -->
            <div class="option" onclick="selectOption(this)">
                <div class="optkey">C</div>
                <div>Populasi rumput menurun karena tidak dimakan belalang.</div>
            </div>
        </div>
    </div>

    <div style="height: 80px;"></div>
</div>

<!-- Tombol Navigasi Bawah Ujian -->
<div style="position: fixed; bottom: 0; left: 0; right: 0; background: rgba(255,255,255,0.9); padding: 14px 20px; border-top: 1px solid var(--border); display: flex; gap: 10px; backdrop-filter: blur(10px);">
    <button class="btn btn-outline" style="flex:1;">Sebelumnya</button>
    <button class="btn btn-primary" style="flex:1;">Selanjutnya</button>
</div>
@endsection

@push('scripts')
<script>
    function selectOption(element) {
        // Menghapus kelas 'sel' dari semua opsi di soal ini
        const options = element.parentElement.querySelectorAll('.option');
        options.forEach(opt => opt.classList.remove('sel'));

        // Menambahkan kelas 'sel' ke opsi yang diklik
        element.classList.add('sel');
    }
</script>
@endpush
