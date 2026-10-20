
@extends('layouts.app')

@section('title', 'Detail Asesmen')

@section('content')

<style>
    .assessment-page {
        max-width: 1000px;
    }

    .assessment-card {
        border: 0;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 4px 16px rgba(15, 23, 42, .055);
    }

    .assessment-heading {
        color: #0f172a;
        font-weight: 700;
    }

    .assessment-subtitle {
        color: #64748b;
        font-size: 14px;
    }

    .assessment-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .assessment-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 10px;
        padding: 11px 18px;
        font-weight: 600;
        text-decoration: none;
    }

    .assessment-panel {
        min-height: 240px;
    }

    .question-card {
        display: none;
    }

    .question-card.active {
        display: block;
    }

    .question-text {
        white-space: pre-line;
        line-height: 1.75;
        overflow-wrap: anywhere;
    }

    .question-choice {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 16px;
        margin-bottom: 10px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
    }

    .question-label {
        display: inline-flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 9px;
        background: #dbeafe;
        color: #1d4ed8;
        font-weight: 700;
    }

    .scaffolding-box {
        padding: 18px;
        border-radius: 12px;
        border-left: 4px solid #2563eb;
        background: #eff6ff;
    }

    .answer-box {
        padding: 18px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
    }

    .answer-box summary {
        cursor: pointer;
        color: #1e40af;
    }

    .evaluation-item {
        padding: 18px;
        margin-bottom: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    /* Gambar sekarang berada pada masing-masing soal Adaptif */
    .assessment-image-frame {
        padding: 14px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
    }

    .assessment-image-frame img {
        display: block;
        width: 100%;
        max-height: 350px;
        object-fit: contain;
        border-radius: 10px;
        background: #ffffff;
    }

    .assessment-image-caption {
        font-size: 13px;
        color: #64748b;
        margin-top: 10px;
    }

    @media (max-width: 575px) {
        .assessment-actions {
            width: 100%;
        }

        .assessment-action-btn {
            flex: 1;
        }

        .assessment-card {
            border-radius: 14px;
        }
    }
</style>

<div class="container py-4 py-md-5 assessment-page">

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert alert-success rounded-3 mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger rounded-3 mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- HEADER --}}
    <div class="assessment-card p-4 mb-4">

        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">

            <div class="d-flex align-items-start gap-3">

                <a href="{{ route('guru.assessments.index') }}"
                   class="btn btn-light border rounded-3"
                   title="Kembali ke daftar asesmen">
                    <span class="material-symbols-outlined">
                        arrow_back
                    </span>
                </a>

                <div>
                    <h1 class="h3 assessment-heading mb-2">
                        {{ $assessment->nama }}
                    </h1>

                    <div class="assessment-subtitle d-flex flex-wrap gap-2">
                        <span>{{ $assessment->mapel }}</span>
                        <span>•</span>
                        <span>{{ $assessment->kelas ?? 'Kelas Umum' }}</span>
                        <span>•</span>
                        <span>{{ $assessment->versi_asesmen ?? 'Asesmen' }}</span>
                    </div>
                </div>

            </div>

            <span class="badge rounded-pill px-3 py-2
                {{ $assessment->status === 'Published'
                    ? 'bg-success'
                    : 'bg-warning text-dark' }}">

                {{ $assessment->status === 'Published'
                    ? 'Dipublikasikan'
                    : 'Draft' }}
            </span>
        </div>

        <div class="border-top mt-4 pt-4">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                <div>
                    <h6 class="fw-bold mb-1">
                        Kelola Paket Asesmen
                    </h6>

                    <p class="assessment-subtitle mb-0">
                        Periksa soal, ilustrasi, dan jawaban
                        sebelum dibagikan kepada kelas.
                    </p>
                </div>

                <div class="assessment-actions">

                    <a href="{{ route('guru.assessments.edit', $assessment->id) }}"
                       class="btn btn-outline-primary assessment-action-btn">

                        <span class="material-symbols-outlined">
                            edit
                        </span>

                        {{ $assessment->status === 'Published'
                            ? 'Lihat Review'
                            : 'Edit Soal' }}
                    </a>

                    @if($assessment->status === 'Published')

                        <button type="button"
                                class="btn btn-success assessment-action-btn"
                                disabled>
                            <span class="material-symbols-outlined">
                                check_circle
                            </span>
                            Sudah Dipublikasikan
                        </button>

                    @else

                        <a href="{{ route('guru.assessments.edit', $assessment->id) }}#publikasi"
                           class="btn btn-success assessment-action-btn">

                            <span class="material-symbols-outlined">
                                publish
                            </span>

                            Publikasikan
                        </a>

                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- TUJUAN PEMBELAJARAN --}}
    <div class="assessment-card p-4 mb-4">

        <h5 class="assessment-heading mb-3">
            Tujuan Pembelajaran
        </h5>

        <p class="text-secondary question-text mb-0">
            {{ $assessment->tujuan_pembelajaran ?: 'Belum diisi.' }}
        </p>
    </div>

    {{-- PERINGATAN VALIDASI --}}
    @if(
        ($statusKesetaraan ?? null) === 'perlu_revisi'
        || (
            is_numeric($jumlahPerluRevisi ?? null)
            && $jumlahPerluRevisi > 0
        )
        || in_array(
            strtoupper((string) ($hasilValidasi ?? '')),
            ['TIDAK_VALID', 'TIDAK VALID'],
            true
        )
    )

        <div class="alert alert-warning rounded-3 mb-4">

            <strong>Asesmen memerlukan tinjauan guru.</strong>

            <p class="small mt-2 mb-0">
                Periksa soal, hasil validasi, kunci jawaban,
                scaffolding, dan gambar sebelum publikasi.
            </p>

        </div>

    @elseif(($statusKesetaraan ?? null) === 'setara')

        <div class="alert alert-success rounded-3 mb-4">

            <strong>
                Hasil evaluasi AI menyatakan kedua versi setara.
            </strong>

            <p class="small mt-1 mb-0">
                Guru tetap perlu memeriksa seluruh soal
                dan ilustrasi sebelum publikasi.
            </p>

        </div>

    @endif

    {{-- HASIL ASESMEN --}}
    <div class="assessment-card p-4 mb-4">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">

            <div>
                <h5 class="assessment-heading mb-1">
                    Hasil Asesmen AI
                </h5>

                <p class="assessment-subtitle mb-0">
                    Pilih versi asesmen untuk melihat soal satu per satu.
                </p>
            </div>

            <div class="btn-group" role="group">

                <button type="button"
                        id="tabReguler"
                        class="btn btn-primary"
                        aria-pressed="true">
                    Reguler
                </button>

                <button type="button"
                        id="tabAdaptif"
                        class="btn btn-outline-primary"
                        aria-pressed="false">
                    Adaptif
                </button>

            </div>
        </div>

        {{-- ===================== --}}
        {{-- PANEL SOAL REGULER --}}
        {{-- ===================== --}}

        <div id="panelReguler" class="assessment-panel">

            @if(!empty($catatanReguler))
                <p class="text-muted small question-text mb-4">
                    {{ $catatanReguler }}
                </p>
            @endif

            @forelse($soalReguler as $index => $soal)

                <article
                    class="question-card {{ $index === 0 ? 'active' : '' }}"
                    data-version="reguler"
                    data-index="{{ $index }}">

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

                        <span class="badge bg-primary px-3 py-2">
                            Soal {{ $index + 1 }}
                        </span>

                        <span class="text-muted small">
                            {{ ($soal['jenis'] ?? '') === 'pilihan_ganda'
                                ? 'Pilihan Ganda'
                                : 'Esai' }}

                            • {{ $soal['level_bloom'] ?? '-' }}
                        </span>
                    </div>

                    <h5 class="fw-semibold question-text mb-4">
                        {{ $soal['pertanyaan'] ?? 'Pertanyaan belum tersedia.' }}
                    </h5>

                    @if(!empty($soal['deskripsi_gambar']))

                        <div class="alert alert-light border rounded-3 small">
                            <strong>Deskripsi Ilustrasi:</strong>
                            {{ $soal['deskripsi_gambar'] }}
                        </div>

                    @endif

                    {{-- PILIHAN JAWABAN REGULER --}}
                    @if(!empty($soal['opsi']) && is_array($soal['opsi']))

                        <div class="mb-4">

                            @foreach($soal['opsi'] as $opsi)

                                <div class="question-choice">

                                    <span class="question-label">
                                        {{ $opsi['label'] ?? '-' }}
                                    </span>

                                    <span class="question-text">
                                        {{ $opsi['teks'] ?? '' }}
                                    </span>

                                </div>

                            @endforeach
                        </div>

                    @endif

                    {{-- KUNCI JAWABAN REGULER --}}
                    <details class="answer-box mt-4">

                        <summary class="fw-semibold">
                            Lihat Kunci Jawaban dan Pembahasan Guru
                        </summary>

                        <div class="mt-3">

                            <strong>Kunci Jawaban</strong>

                            <p class="question-text mt-2">
                                {{ $soal['kunci_jawaban'] ?? '-' }}
                            </p>

                            <strong>Pembahasan</strong>

                            <p class="question-text mt-2">
                                {{ $soal['pembahasan'] ?? '-' }}
                            </p>

                            @if(!empty($soal['pedoman_penilaian']))

                                <hr>

                                <strong>Pedoman Penilaian</strong>

                                <p class="question-text mt-2 mb-0">
                                    {{ $soal['pedoman_penilaian'] }}
                                </p>

                            @endif

                        </div>
                    </details>

                </article>

            @empty

                <div class="alert alert-secondary">
                    Belum ada soal Reguler yang tersimpan.
                </div>

            @endforelse

        </div>

        {{-- ===================== --}}
        {{-- PANEL SOAL ADAPTIF --}}
        {{-- ===================== --}}

        <div id="panelAdaptif"
             class="assessment-panel"
             style="display:none;">

            @if(!empty($catatanAdaptif))

                <p class="text-muted small question-text mb-4">
                    {{ str_ireplace('inklusif', 'adaptif', $catatanAdaptif) }}
                </p>

            @endif

            @forelse($soalAdaptif as $index => $soal)

                @php
                    /*
                     * Ambil ID soal Adaptif.
                     * Contoh: PG1, ES1, PG2.
                     */
                    $idSoalAdaptif = trim(
                        (string) (
                            $soal['id_soal_asal']
                            ?? $soal['id']
                            ?? ''
                        )
                    );

                    /*
                     * Ambil HANYA gambar yang memiliki
                     * id_soal_asal sama dengan soal ini.
                     */
                    $ilustrasiSoal = $idSoalAdaptif !== ''
                        ? ($gambarPerSoal[$idSoalAdaptif] ?? [])
                        : [];

                    /*
                     * Ambil teks alternatif dari rencana_visual.
                     * Cocokkan berdasarkan id_visual.
                     */
                    $rencanaVisual = is_array(
                        $soal['rencana_visual'] ?? null
                    )
                        ? $soal['rencana_visual']
                        : [];

                    $altPerVisual = [];

                    foreach ($rencanaVisual as $visual) {
                        if (!is_array($visual)) {
                            continue;
                        }

                        $idVisual = (string) (
                            $visual['id_visual'] ?? ''
                        );

                        if ($idVisual !== '') {
                            $altPerVisual[$idVisual] =
                                $visual['teks_alternatif']
                                ?? $visual['tujuan_visual']
                                ?? '';
                        }
                    }
                @endphp

                <article
                    class="question-card {{ $index === 0 ? 'active' : '' }}"
                    data-version="adaptif"
                    data-index="{{ $index }}">

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

                        <span class="badge bg-primary px-3 py-2">
                            Soal {{ $index + 1 }}
                        </span>

                        <span class="text-muted small">
                            {{ ($soal['jenis'] ?? '') === 'pilihan_ganda'
                                ? 'Pilihan Ganda'
                                : 'Esai' }}

                            • {{ $soal['level_bloom'] ?? '-' }}
                        </span>

                    </div>

                    {{-- ================================== --}}
                    {{-- GAMBAR OTOMATIS PER SOAL ADAPTIF --}}
                    {{-- ================================== --}}

                    @if(!empty($ilustrasiSoal))

                        <div class="mb-4">

                            <h6 class="fw-bold mb-3">
                                <span class="material-symbols-outlined
                                             align-middle text-primary me-1">
                                    image
                                </span>

                                Ilustrasi Soal
                            </h6>

                            <div class="row g-3">

                                @foreach($ilustrasiSoal as $gambar)

                                    @php
                                        $idVisualGambar = (string) (
                                            $gambar['id_visual'] ?? ''
                                        );

                                        $teksAlternatif =
                                            $gambar['teks_alternatif']
                                            ?? null;

                                        if (empty($teksAlternatif)) {
                                            $teksAlternatif =
                                                $altPerVisual[$idVisualGambar]
                                                ?? 'Ilustrasi untuk soal '
                                                    . $idSoalAdaptif;
                                        }
                                    @endphp

                                    <div class="col-md-6">

                                        <div class="assessment-image-frame h-100">

                                            <img
                                                src="{{ $gambar['url'] }}"
                                                alt="{{ $teksAlternatif }}"
                                                loading="lazy">

                                            <div class="assessment-image-caption">

                                                <strong>
                                                    Soal {{ $idSoalAdaptif }}
                                                </strong>

                                                @if($idVisualGambar !== '')

                                                    <span class="ms-1">
                                                        • {{ $idVisualGambar }}
                                                    </span>

                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        </div>

                    @elseif(!empty($rencanaVisual))

                        <div class="alert alert-light border rounded-3 mb-4">

                            <strong>
                                Ilustrasi untuk soal
                                {{ $idSoalAdaptif ?: $index + 1 }}
                                belum tersedia.
                            </strong>

                            <p class="small text-muted mt-2 mb-0">
                                Dify memiliki rencana visual untuk soal ini,
                                tetapi belum ada gambar dengan ID soal yang
                                sesuai dalam output yang tersimpan.
                            </p>

                        </div>

                    @endif

                    {{-- PERTANYAAN ADAPTIF --}}
                    <h5 class="fw-semibold question-text mb-4">
                        {{ $soal['pertanyaan'] ?? 'Pertanyaan belum tersedia.' }}
                    </h5>

                    {{-- PILIHAN JAWABAN ADAPTIF --}}
                    @if(!empty($soal['opsi']) && is_array($soal['opsi']))

                        <div class="mb-4">

                            @foreach($soal['opsi'] as $opsi)

                                <div class="question-choice">

                                    <span class="question-label">
                                        {{ $opsi['label'] ?? '-' }}
                                    </span>

                                    <span class="question-text">
                                        {{ $opsi['teks'] ?? '' }}
                                    </span>

                                </div>

                            @endforeach

                        </div>

                    @endif

                    {{-- SCAFFOLDING ADAPTIF --}}
                    @if(
                        !empty($soal['dukungan_scaffolding'])
                        && is_array($soal['dukungan_scaffolding'])
                    )

                        <div class="scaffolding-box mb-4">

                            <h6 class="fw-bold mb-3">
                                Dukungan Belajar Adaptif
                            </h6>

                            <ol class="mb-0">

                                @foreach($soal['dukungan_scaffolding'] as $petunjuk)

                                    <li class="mb-2 question-text">
                                        {{ $petunjuk }}
                                    </li>

                                @endforeach

                            </ol>

                        </div>

                    @endif

                    {{-- INFORMASI RENCANA VISUAL --}}
                    @if(!empty($rencanaVisual))

                        <details class="bg-light border rounded-3 p-3 mb-4">

                            <summary class="fw-semibold">
                                Detail Rencana Ilustrasi Dify
                            </summary>

                            <div class="mt-3">

                                @foreach($rencanaVisual as $visual)

                                    <p class="small question-text mb-2">
                                        {{ $visual['teks_alternatif']
                                            ?? $visual['tujuan_visual']
                                            ?? 'Ilustrasi pendukung.' }}
                                    </p>

                                @endforeach

                            </div>

                        </details>

                    @endif

                    {{-- KUNCI JAWABAN ADAPTIF --}}
                    <details class="answer-box mt-4">

                        <summary class="fw-semibold">
                            Lihat Kunci Jawaban dan Pembahasan Guru
                        </summary>

                        <div class="mt-3">

                            <strong>Kunci Jawaban</strong>

                            <p class="question-text mt-2">
                                {{ $soal['kunci_jawaban'] ?? '-' }}
                            </p>

                            <strong>Pembahasan</strong>

                            <p class="question-text mt-2">
                                {{ $soal['pembahasan'] ?? '-' }}
                            </p>

                            @if(!empty($soal['pedoman_penilaian']))

                                <hr>

                                <strong>Pedoman Penilaian</strong>

                                <p class="question-text mt-2 mb-0">
                                    {{ $soal['pedoman_penilaian'] }}
                                </p>

                            @endif

                        </div>

                    </details>

                </article>

            @empty

                <div class="alert alert-secondary">
                    Belum ada soal Adaptif yang tersimpan.
                </div>

            @endforelse

        </div>

        {{-- NAVIGASI SOAL --}}
        <div id="questionNavigation"
             class="d-flex flex-wrap justify-content-between align-items-center gap-3 border-top mt-4 pt-4">

            <button
                type="button"
                id="previousQuestion"
                class="btn btn-outline-secondary rounded-3">
                ← Sebelumnya
            </button>

            <span id="questionCounter"
                  class="text-secondary small fw-semibold">
                Soal 1
            </span>

            <button
                type="button"
                id="nextQuestion"
                class="btn btn-primary rounded-3">
                Berikutnya →
            </button>

        </div>

    </div>

    {{-- =========================== --}}
    {{-- EVALUASI DAN VALIDASI AI --}}
    {{-- =========================== --}}

    <div class="assessment-card p-4 mb-4">

        <div class="d-flex align-items-center gap-2 mb-3">

            <span class="material-symbols-outlined text-primary">
                fact_check
            </span>

            <h5 class="assessment-heading mb-0">
                Evaluasi dan Validasi AI
            </h5>

        </div>

        <div class="row g-3 mb-3">

            <div class="col-md-6">

                <div class="bg-light rounded-3 p-3 h-100">

                    <div class="small text-muted mb-1">
                        Status Kesetaraan
                    </div>

                    <strong>
                        {{ ucfirst(str_replace(
                            '_',
                            ' ',
                            $statusKesetaraan ?? '-'
                        )) }}
                    </strong>

                </div>

            </div>

            <div class="col-md-6">

                <div class="bg-light rounded-3 p-3 h-100">

                    <div class="small text-muted mb-1">
                        Jumlah Soal Perlu Revisi
                    </div>

                    <strong>
                        {{ $jumlahPerluRevisi ?? '-' }}
                    </strong>

                </div>

            </div>

        </div>

        @if(!empty($hasilValidasi))

            <p class="small text-muted mb-3">

                Hasil validasi workflow:

                <strong>
                    {{ $hasilValidasi }}
                </strong>

            </p>

        @endif

        @if(!empty($statusValidasiGambar))

            <p class="small text-muted mb-3">

                Status validasi gambar:

                <strong>
                    {{ $statusValidasiGambar }}
                </strong>

            </p>

        @endif

        @if(!empty($evaluasi['ringkasan_evaluasi']))

            <p class="question-text mb-4">
                {{ str_ireplace(
                    'inklusif',
                    'adaptif',
                    $evaluasi['ringkasan_evaluasi']
                ) }}
            </p>

        @endif

        @if(
            !empty($evaluasi['hasil_per_soal'])
            && is_array($evaluasi['hasil_per_soal'])
        )

            <h6 class="fw-bold mb-3">
                Hasil Evaluasi per Soal
            </h6>

            @foreach($evaluasi['hasil_per_soal'] as $hasil)

                <div class="evaluation-item">

                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">

                        <strong>
                            {{ $hasil['id_soal_asal'] ?? 'Soal' }}
                        </strong>

                        @if(($hasil['status_soal'] ?? '') === 'setara')

                            <span class="badge bg-success">
                                Setara
                            </span>

                        @elseif(
                            ($hasil['status_soal'] ?? '') === 'perlu_revisi'
                        )

                            <span class="badge bg-warning text-dark">
                                Perlu Revisi
                            </span>

                        @else

                            <span class="badge bg-secondary">
                                {{ str_replace(
                                    '_',
                                    ' ',
                                    $hasil['status_soal'] ?? '-'
                                ) }}
                            </span>

                        @endif

                    </div>

                    @if(
                        !empty($hasil['masalah_kritis'])
                        && is_array($hasil['masalah_kritis'])
                    )

                        <div class="small mb-3">

                            <strong>Masalah yang ditemukan:</strong>

                            <ul class="mb-0 mt-2">

                                @foreach($hasil['masalah_kritis'] as $masalah)

                                    <li class="question-text mb-1">
                                        {{ str_ireplace(
                                            'inklusif',
                                            'adaptif',
                                            $masalah
                                        ) }}
                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endif

                    @if(
                        !empty($hasil['rekomendasi_revisi'])
                        && is_array($hasil['rekomendasi_revisi'])
                    )

                        <div class="small">

                            <strong>Rekomendasi revisi:</strong>

                            <ul class="mb-0 mt-2">

                                @foreach($hasil['rekomendasi_revisi'] as $rekomendasi)

                                    <li class="question-text mb-1">
                                        {{ str_ireplace(
                                            'inklusif',
                                            'adaptif',
                                            $rekomendasi
                                        ) }}
                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endif

                </div>

            @endforeach

        @endif

        @if(
            !empty($evaluasi['rekomendasi_umum'])
            && is_array($evaluasi['rekomendasi_umum'])
        )

            <h6 class="fw-bold mt-4 mb-3">
                Rekomendasi Umum
            </h6>

            <ul class="mb-0">

                @foreach($evaluasi['rekomendasi_umum'] as $rekomendasi)

                    <li class="question-text mb-2">
                        {{ str_ireplace(
                            'inklusif',
                            'adaptif',
                            $rekomendasi
                        ) }}
                    </li>

                @endforeach

            </ul>

        @endif

        <p class="small text-muted mt-4 mb-0">
            Evaluasi AI merupakan bahan tinjauan.
            Guru tetap bertanggung jawab memeriksa dan
            menyetujui soal sebelum publikasi.
        </p>

    </div>

    {{-- INFORMASI GAMBAR TANPA ID SOAL --}}
    @php
        $gambarTanpaId = array_values(
            array_filter(
                $gambarAsesmen ?? [],
                fn ($gambar) =>
                    is_array($gambar)
                    && empty($gambar['id_soal_asal'])
            )
        );
    @endphp

    @if(!empty($gambarTanpaId))

        <div class="assessment-card p-4 mb-4">

            <h5 class="assessment-heading mb-3">
                Gambar Lama yang Belum Terhubung ke Soal
            </h5>

            <p class="small text-muted">
                Gambar berikut tidak memiliki ID soal dari Dify.
                Gambar tidak dipasangkan secara otomatis agar
                tidak tertukar.
            </p>

            <div class="row g-3">

                @foreach($gambarTanpaId as $gambar)

                    <div class="col-md-6">

                        <div class="assessment-image-frame">

                            <img
                                src="{{ $gambar['url'] }}"
                                alt="Ilustrasi asesmen tanpa ID soal"
                                loading="lazy">

                            <p class="assessment-image-caption mb-0">
                                {{ $gambar['filename']
                                    ?? 'Gambar asesmen' }}
                            </p>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    @endif

    {{-- NAVIGASI BAWAH --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4">

        <a href="{{ route('guru.assessments.index') }}"
           class="btn btn-light border rounded-3 px-4 py-2">

            <span class="material-symbols-outlined align-middle me-1">
                arrow_back
            </span>

            Kembali ke Daftar
        </a>

        <a href="{{ route('guru.assessments.edit', $assessment->id) }}"
           class="btn btn-outline-primary rounded-3 px-4 py-2">

            <span class="material-symbols-outlined align-middle me-1">
                edit
            </span>

            {{ $assessment->status === 'Published'
                ? 'Lihat Review Guru'
                : 'Tinjau & Edit Soal' }}
        </a>

    </div>

</div>

{{-- SCRIPT TAB DAN NAVIGASI --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const tabReguler =
        document.getElementById('tabReguler');

    const tabAdaptif =
        document.getElementById('tabAdaptif');

    const panelReguler =
        document.getElementById('panelReguler');

    const panelAdaptif =
        document.getElementById('panelAdaptif');

    const previousButton =
        document.getElementById('previousQuestion');

    const nextButton =
        document.getElementById('nextQuestion');

    const counter =
        document.getElementById('questionCounter');

    const navigation =
        document.getElementById('questionNavigation');

    let activeVersion = 'reguler';
    let currentIndex = 0;

    function getQuestions() {

        const panel = activeVersion === 'reguler'
            ? panelReguler
            : panelAdaptif;

        return Array.from(
            panel.querySelectorAll('.question-card')
        );
    }

    function renderQuestions() {

        const questions = getQuestions();

        questions.forEach(function (question, index) {

            question.classList.toggle(
                'active',
                index === currentIndex
            );

        });

        if (questions.length === 0) {

            navigation.style.display = 'none';
            counter.textContent = 'Belum ada soal';

            return;
        }

        navigation.style.display = 'flex';

        counter.textContent =
            'Soal ' +
            (currentIndex + 1) +
            ' dari ' +
            questions.length;

        previousButton.disabled =
            currentIndex === 0;

        nextButton.disabled =
            currentIndex >= questions.length - 1;
    }

    function changeVersion(version) {

        activeVersion = version;
        currentIndex = 0;

        panelReguler.style.display =
            version === 'reguler'
                ? 'block'
                : 'none';

        panelAdaptif.style.display =
            version === 'adaptif'
                ? 'block'
                : 'none';

        tabReguler.className =
            version === 'reguler'
                ? 'btn btn-primary'
                : 'btn btn-outline-primary';

        tabAdaptif.className =
            version === 'adaptif'
                ? 'btn btn-primary'
                : 'btn btn-outline-primary';

        tabReguler.setAttribute(
            'aria-pressed',
            version === 'reguler'
                ? 'true'
                : 'false'
        );

        tabAdaptif.setAttribute(
            'aria-pressed',
            version === 'adaptif'
                ? 'true'
                : 'false'
        );

        renderQuestions();
    }

    tabReguler.addEventListener('click', function () {
        changeVersion('reguler');
    });

    tabAdaptif.addEventListener('click', function () {
        changeVersion('adaptif');
    });

    previousButton.addEventListener('click', function () {

        if (currentIndex > 0) {
            currentIndex--;
            renderQuestions();
        }

    });

    nextButton.addEventListener('click', function () {

        if (currentIndex < getQuestions().length - 1) {
            currentIndex++;
            renderQuestions();
        }

    });

    changeVersion('reguler');
});
</script>

@endsection
