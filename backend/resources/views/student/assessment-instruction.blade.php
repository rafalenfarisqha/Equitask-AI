
@extends('layouts.app')

@section('title', 'Petunjuk Assessment — EquiTask AI')

@section('content')
<div class="eq-page">
    <div class="eq-container">
        <a href="{{ route('siswa.assessment') }}" class="eq-back">
            <span aria-hidden="true">←</span> Kembali ke daftar asesmen
        </a>

        <header class="eq-heading">
            <div class="eq-label">PERSIAPAN ASESMEN</div>
            <h1>Siap untuk belajar? ✨</h1>
            <p>Pilih format soal yang paling nyaman untukmu.</p>
        </header>

        @if(session('error'))
            <div class="eq-alert">{{ session('error') }}</div>
        @endif

        <section class="eq-summary">
            <div class="eq-subject-icon">📘</div>
            <div class="eq-summary-main">
                <span class="eq-muted">{{ $assessment->mapel }}</span>
                <h2>{{ $assessment->nama }}</h2>
                <p>Pengajar: {{ $assessment->teacher_name ?? 'Guru' }}</p>
            </div>
            <span class="eq-status">Dipublikasikan</span>

            <div class="eq-stats">
                <div>
                    <strong>{{ $questionCount }}</strong>
                    <span>Soal tersedia*</span>
                </div>
                <div>
                    <strong>30 menit</strong>
                    <span>Estimasi waktu</span>
                </div>
                <div>
                    <strong>2 format</strong>
                    <span>Pilihan soal</span>
                </div>
            </div>
            <p class="eq-note">
                *Jumlah di atas merupakan total soal dari kedua versi yang tersedia.
                Jumlah soal yang dikerjakan bisa berbeda sesuai versi pilihanmu.
            </p>
        </section>

        <section class="eq-feature">
            <h3>Fitur yang tersedia</h3>
            <div class="eq-feature-grid">
                <div><span>✓</span> Dukungan teks besar</div>
                <div><span>✓</span> Mode fokus</div>
                <div><span>✓</span> Navigasi soal</div>
                <div><span>✓</span> Penyimpanan jawaban saat dikumpulkan</div>
            </div>
        </section>

        <form method="GET"
              action="{{ route('siswa.assessment.start', $assessment->id) }}"
              id="versionForm">
            @csrf

            <div class="eq-version-heading">
                <div>
                    <h3>Pilih versi soal</h3>
                    <p>Kamu bisa memilih format yang sesuai dengan kebutuhanmu.</p>
                </div>
            </div>

            <div class="eq-version-grid">
                @if($hasRegular)
                    <label class="eq-version-card">
                        <input type="radio" name="version_type"
                               value="reguler" required>
                        <div class="eq-version-inner">
                            <div class="eq-version-icon regular">📚</div>
                            <span class="eq-version-tag">FORMAT 01</span>
                            <h3>Versi Reguler</h3>
                            <p>Soal dengan penyajian standar dan pilihan jawaban.</p>
                            <span class="eq-radio-indicator"></span>
                        </div>
                    </label>
                @endif

                @if($hasAdaptive)
                    <label class="eq-version-card">
                        <input type="radio" name="version_type"
                               value="adaptif" required>
                        <div class="eq-version-inner">
                            <div class="eq-version-icon adaptive">♿</div>
                            <span class="eq-version-tag">FORMAT 02</span>
                            <h3>Versi Adaptif</h3>
                            <p>Penyajian soal yang dirancang lebih aksesibel dan mudah diikuti.</p>
                            <span class="eq-radio-indicator"></span>
                        </div>
                    </label>
                @endif
            </div>

            @error('version_type')
                <p class="eq-error">{{ $message }}</p>
            @enderror

            <div class="eq-reminder">
                <span>💡</span>
                <div>
                    <strong>Tips sebelum mulai</strong>
                    <p>Pastikan kamu sudah membaca instruksi dan memilih versi soal yang diinginkan.</p>
                </div>
            </div>

            <button type="submit" id="startButton" class="eq-start" disabled>
                Mulai Assessment <span aria-hidden="true">→</span>
            </button>
        </form>
    </div>
</div>

<style>
.eq-page {
    min-height: 100vh; padding: 34px 18px 60px;
    background: #f4f7ff; color: #17233e;
    font-family: Inter, ui-sans-serif, system-ui, sans-serif;
}
.eq-container { max-width: 850px; margin: auto; }
.eq-back {
    display: inline-flex; gap: 10px; align-items: center;
    color: #52617d; text-decoration: none; font-size: 14px;
    margin-bottom: 26px;
}
.eq-heading { margin-bottom: 24px; }
.eq-label { color: #2563eb; font-weight: 800; font-size: 11px; letter-spacing: 1.6px; }
.eq-heading h1 { margin: 9px 0; font-size: clamp(25px, 4vw, 34px); letter-spacing: -1px; }
.eq-heading p, .eq-version-heading p { color: #73809a; margin: 0; font-size: 14px; }
.eq-summary, .eq-feature, .eq-version-inner, .eq-reminder {
    background: white; border: 1px solid #e6ebf5;
    border-radius: 20px; box-shadow: 0 8px 28px #1d35570a;
}
.eq-summary { padding: 24px; display: grid; grid-template-columns: auto 1fr auto; gap: 14px; }
.eq-subject-icon {
    width: 48px; height: 48px; display: grid; place-items: center;
    border-radius: 15px; background: #edf3ff; font-size: 23px;
}
.eq-muted { font-size: 12px; color: #71809a; }
.eq-summary h2 { margin: 4px 0; font-size: 20px; }
.eq-summary-main p { color: #71809a; margin: 5px 0 0; font-size: 13px; }
.eq-status {
    align-self: start; padding: 7px 10px; border-radius: 30px;
    color: #087f65; background: #e7f8f2; font-size: 11px; font-weight: 700;
}
.eq-stats {
    grid-column: 1 / -1; display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 12px; border-top: 1px solid #edf0f7; padding-top: 18px; margin-top: 6px;
}
.eq-stats div { display: flex; flex-direction: column; gap: 5px; }
.eq-stats strong { font-size: 17px; }
.eq-stats span { color: #75829a; font-size: 12px; }
.eq-note { grid-column: 1 / -1; color: #8792a7; font-size: 11px; margin: 0; }
.eq-feature { padding: 22px; margin: 18px 0 30px; }
.eq-feature h3, .eq-version-heading h3 { font-size: 16px; margin: 0 0 16px; }
.eq-feature-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 13px; }
.eq-feature-grid span { color: #0d9488; font-weight: 900; margin-right: 8px; }
.eq-version-heading { margin-bottom: 16px; }
.eq-version-heading h3 { margin-bottom: 6px; }
.eq-version-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.eq-version-card { display: block; cursor: pointer; position: relative; }
.eq-version-card input {
    position: absolute; opacity: 0; width: 1px; height: 1px;
}
.eq-version-inner {
    padding: 22px; height: 100%; box-sizing: border-box;
    position: relative; transition: .18s ease; border: 2px solid #e6ebf5;
}
.eq-version-card input:checked + .eq-version-inner {
    border-color: #2563eb; background: #f7f9ff;
    box-shadow: 0 0 0 3px #2563eb13;
}
.eq-version-card:has(input:focus-visible) .eq-version-inner {
    outline: 3px solid #93c5fd; outline-offset: 3px;
}
.eq-version-icon {
    width: 45px; height: 45px; display: grid; place-items: center;
    border-radius: 14px; font-size: 22px; margin-bottom: 17px;
}
.eq-version-icon.regular { background: #edf3ff; }
.eq-version-icon.adaptive { background: #e4f8f3; }
.eq-version-tag { font-size: 10px; font-weight: 800; color: #8390a8; letter-spacing: 1px; }
.eq-version-inner h3 { margin: 8px 0; font-size: 18px; }
.eq-version-inner p { margin: 0; color: #74819a; font-size: 13px; line-height: 1.7; padding-right: 10px; }
.eq-radio-indicator {
    position: absolute; right: 18px; top: 20px;
    width: 17px; height: 17px; border: 1.5px solid #c8d1e0; border-radius: 50%;
}
.eq-version-card input:checked + .eq-version-inner .eq-radio-indicator {
    border: 5px solid #2563eb; background: white;
}
.eq-reminder { display: flex; gap: 12px; padding: 16px; margin: 20px 0; }
.eq-reminder p { color: #71809a; margin: 5px 0 0; font-size: 12px; line-height: 1.6; }
.eq-start {
    border: 0; width: 100%; padding: 16px; border-radius: 14px;
    background: #2563eb; color: white; font-size: 14px; font-weight: 800;
    cursor: pointer; box-shadow: 0 7px 16px #2563eb25;
}
.eq-start:disabled { background: #aebbd3; box-shadow: none; cursor: not-allowed; }
.eq-start span { margin-left: 8px; }
.eq-alert { padding: 14px; border-radius: 12px; background: #fff4e5; color: #8b4b00; margin-bottom: 18px; }
.eq-error { color: #b91c1c; font-size: 13px; }
@media(max-width: 560px) {
    .eq-page { padding: 22px 14px 40px; }
    .eq-summary { padding: 18px; grid-template-columns: auto 1fr; }
    .eq-status { grid-column: 1 / -1; justify-self: start; }
    .eq-stats { gap: 8px; }
    .eq-stats strong { font-size: 15px; }
    .eq-stats span { font-size: 11px; }
    .eq-version-grid { grid-template-columns: 1fr; }
}
</style>

<script>
document.querySelectorAll('input[name="version_type"]').forEach(input => {
    input.addEventListener('change', () => {
        document.getElementById('startButton').disabled = false;
    });
});
</script>
@endsection
