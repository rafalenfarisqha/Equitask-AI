@extends('layouts.app')

@section('title', 'Edit Asesmen')

@section('content')

<style>
    .edit-page {
        max-width: 1000px;
    }

    .edit-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 26px;
        margin-bottom: 24px;
        box-shadow: 0 4px 16px rgba(15, 23, 42, .05);
    }

    .edit-question {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 22px;
        margin-bottom: 20px;
        background: #fff;
    }

    .edit-panel {
        display: none;
    }

    .edit-panel.active {
        display: block;
    }

    .edit-help {
        color: #64748b;
        font-size: 13px;
    }

    .edit-question textarea {
        line-height: 1.65;
    }

    .image-card {
        height: 100%;
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #fff;
    }

    .image-card img {
        display: block;
        width: 100%;
        height: 230px;
        object-fit: contain;
        background: #f8fafc;
        border-radius: 8px;
    }

    .class-option {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px;
        margin-bottom: 10px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
    }

    .class-option:hover {
        background: #f8fafc;
    }

    #publikasi {
        scroll-margin-top: 24px;
    }

    @media (max-width: 575px) {
        .edit-card {
            padding: 18px;
        }

        .edit-question {
            padding: 16px;
        }
    }
</style>

<div class="container py-4 py-md-5 edit-page">

    <!-- HEADER -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">

        <div>
            <h1 class="h3 fw-bold mb-2">
                Kelola Paket Asesmen
            </h1>

            <p class="text-muted mb-0">
                {{ $assessment->nama }}
                • {{ $assessment->mapel }}
                • {{ $assessment->kelas }}
            </p>
        </div>

        <a href="{{ route('guru.assessments.show', $assessment->id) }}"
           class="btn btn-outline-secondary rounded-3">

            <span class="material-symbols-outlined align-middle me-1">
                arrow_back
            </span>

            Kembali ke Detail
        </a>
    </div>

    <!-- PESAN -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Periksa kembali formulir:</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- STATUS -->
    <div class="edit-card">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

            <div>
                <h5 class="fw-bold mb-1">
                    Status Asesmen
                </h5>

                <p class="edit-help mb-0">
                    Satu paket berisi soal Reguler dan Adaptif.
                </p>
            </div>

            <span class="badge px-3 py-2
                {{ $assessment->status === 'Published'
                    ? 'bg-success'
                    : 'bg-warning text-dark' }}">

                {{ $assessment->status === 'Published'
                    ? 'Dipublikasikan'
                    : 'Draft' }}
            </span>
        </div>

        @if($assessment->status === 'Published')
            <div class="alert alert-info mt-3 mb-0">
                Paket sudah dipublikasikan.
                Soal tidak dapat diedit melalui formulir ini.
            </div>
        @else
            <div class="alert alert-warning mt-3 mb-0">
                Pastikan seluruh soal dan jawaban sudah benar.
                Periksa juga scaffolding Adaptif agar tidak
                membocorkan jawaban.
            </div>
        @endif
    </div>

    <!-- FORM EDIT -->
    <form method="POST"
          action="{{ route('guru.assessments.update', $assessment->id) }}">

        @csrf
        @method('PUT')

        <div class="edit-card">

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">

                <div>
                    <h5 class="fw-bold mb-1">
                        Edit Soal dan Jawaban
                    </h5>

                    <p class="edit-help mb-0">
                        Kedua versi disimpan dalam satu paket revisi.
                    </p>
                </div>

                <div class="btn-group" role="group">

                    <button type="button"
                            id="editTabReguler"
                            class="btn btn-primary">
                        Reguler
                    </button>

                    <button type="button"
                            id="editTabAdaptif"
                            class="btn btn-outline-primary">
                        Adaptif
                    </button>
                </div>
            </div>

            @if($assessment->status === 'Published')
                <fieldset disabled>
            @endif

            <!-- SOAL REGULER -->
            <div id="editPanelReguler" class="edit-panel active">

                @forelse($soalReguler as $index => $soal)

                    <div class="edit-question">

                        <div class="d-flex justify-content-between align-items-center mb-3">

                            <h6 class="fw-bold mb-0">
                                Soal Reguler {{ $index + 1 }}
                            </h6>

                            <span class="badge bg-primary">
                                {{ $soal['level_bloom'] ?? '-' }}
                            </span>
                        </div>

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Pertanyaan
                            </label>

                            <textarea
                                name="reguler[{{ $index }}][pertanyaan]"
                                class="form-control"
                                rows="4"
                                required>{{ old("reguler.$index.pertanyaan", $soal['pertanyaan'] ?? '') }}</textarea>
                        </div>

                        @if(!empty($soal['opsi']) && is_array($soal['opsi']))

                            <label class="form-label fw-semibold">
                                Pilihan Jawaban
                            </label>

                            @foreach($soal['opsi'] as $optionIndex => $opsi)

                                <div class="input-group mb-2">

                                    <span class="input-group-text fw-bold">
                                        {{ $opsi['label'] ?? chr(65 + $optionIndex) }}
                                    </span>

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="reguler[{{ $index }}][opsi][{{ $optionIndex }}][teks]"
                                        value="{{ old("reguler.$index.opsi.$optionIndex.teks", $opsi['teks'] ?? '') }}"
                                        required>
                                </div>

                            @endforeach
                        @endif

                        <div class="mt-3 mb-3">

                            <label class="form-label fw-semibold">
                                Kunci Jawaban
                            </label>

                            <textarea
                                name="reguler[{{ $index }}][kunci_jawaban]"
                                class="form-control"
                                rows="2"
                                required>{{ old("reguler.$index.kunci_jawaban", $soal['kunci_jawaban'] ?? '') }}</textarea>
                        </div>

                        <div>
                            <label class="form-label fw-semibold">
                                Pembahasan
                            </label>

                            <textarea
                                name="reguler[{{ $index }}][pembahasan]"
                                class="form-control"
                                rows="3">{{ old("reguler.$index.pembahasan", $soal['pembahasan'] ?? '') }}</textarea>
                        </div>

                    </div>

                @empty
                    <div class="alert alert-secondary">
                        Belum ada soal Reguler yang dapat diedit.
                    </div>
                @endforelse
            </div>

            <!-- SOAL ADAPTIF -->
            <div id="editPanelAdaptif" class="edit-panel">

                {{-- Gambar ditampilkan per soal, bukan sebagai galeri terpisah. --}}

                <!-- FORM SOAL ADAPTIF -->
                @forelse($soalAdaptif as $index => $soal)

                    <div class="edit-question">

                        <div class="d-flex justify-content-between align-items-center mb-3">

                            <h6 class="fw-bold mb-0">
                                Soal Adaptif {{ $index + 1 }}
                            </h6>

                            <span class="badge bg-primary">
                                {{ $soal['level_bloom'] ?? '-' }}
                            </span>
                        </div>

                        {{-- Pasangkan gambar menggunakan ID soal, bukan urutan PNG. --}}
                        @php
                            $idSoalAdaptif = trim((string) (
                                $soal['id_soal_asal'] ?? $soal['id'] ?? ''
                            ));
                            $ilustrasiSoal = $idSoalAdaptif !== ''
                                ? ($gambarPerSoal[$idSoalAdaptif] ?? [])
                                : [];
                        @endphp

                        @if(!empty($ilustrasiSoal))
                            <div class="mb-4">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="material-symbols-outlined text-primary">image</span>
                                    <label class="form-label fw-semibold mb-0">
                                        Ilustrasi untuk soal {{ $idSoalAdaptif }}
                                    </label>
                                </div>

                                <div class="row g-3">
                                    @foreach($ilustrasiSoal as $ilustrasi)
                                        @php
                                            // Gunakan teks alternatif dari rencana visual yang cocok.
                                            $altGambar = null;
                                            foreach (($soal['rencana_visual'] ?? []) as $rencana) {
                                                if (($rencana['id_visual'] ?? null) === ($ilustrasi['id_visual'] ?? null)) {
                                                    $altGambar = $rencana['teks_alternatif'] ?? null;
                                                    break;
                                                }
                                            }
                                            $altGambar = $ilustrasi['teks_alternatif']
                                                ?? $altGambar
                                                ?? ('Ilustrasi soal ' . $idSoalAdaptif);
                                        @endphp

                                        <div class="col-md-8">
                                            <div class="image-card">
                                                <img
                                                    src="{{ $ilustrasi['url'] }}"
                                                    alt="{{ $altGambar }}"
                                                    loading="lazy">
                                                <p class="edit-help mt-2 mb-0">
                                                    {{ $altGambar }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="alert alert-light border small mb-3">
                                Belum ada ilustrasi yang terhubung ke soal
                                {{ $idSoalAdaptif !== '' ? $idSoalAdaptif : $index + 1 }}.
                                @if(!empty($gambarAsesmen))
                                    Gambar dari format lama atau tanpa ID soal tidak
                                    dipasangkan otomatis agar tidak tertukar.
                                @endif
                            </div>
                        @endif

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Pertanyaan
                            </label>

                            <textarea
                                name="adaptif[{{ $index }}][pertanyaan]"
                                class="form-control"
                                rows="4"
                                required>{{ old("adaptif.$index.pertanyaan", $soal['pertanyaan'] ?? '') }}</textarea>
                        </div>

                        @if(!empty($soal['opsi']) && is_array($soal['opsi']))

                            <label class="form-label fw-semibold">
                                Pilihan Jawaban
                            </label>

                            @foreach($soal['opsi'] as $optionIndex => $opsi)

                                <div class="input-group mb-2">

                                    <span class="input-group-text fw-bold">
                                        {{ $opsi['label'] ?? chr(65 + $optionIndex) }}
                                    </span>

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="adaptif[{{ $index }}][opsi][{{ $optionIndex }}][teks]"
                                        value="{{ old("adaptif.$index.opsi.$optionIndex.teks", $opsi['teks'] ?? '') }}"
                                        required>
                                </div>

                            @endforeach
                        @endif

                        <div class="mt-3 mb-3">

                            <label class="form-label fw-semibold">
                                Kunci Jawaban
                            </label>

                            <textarea
                                name="adaptif[{{ $index }}][kunci_jawaban]"
                                class="form-control"
                                rows="2"
                                required>{{ old("adaptif.$index.kunci_jawaban", $soal['kunci_jawaban'] ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Pembahasan
                            </label>

                            <textarea
                                name="adaptif[{{ $index }}][pembahasan]"
                                class="form-control"
                                rows="3">{{ old("adaptif.$index.pembahasan", $soal['pembahasan'] ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Dukungan Belajar / Scaffolding
                            </label>

                            <textarea
                                name="adaptif[{{ $index }}][scaffolding]"
                                class="form-control"
                                rows="5">{{ old("adaptif.$index.scaffolding", implode("\n", $soal['dukungan_scaffolding'] ?? [])) }}</textarea>

                            <p class="edit-help mt-2 mb-0">
                                Satu petunjuk per baris.
                                Jangan menuliskan jawaban secara langsung.
                            </p>
                        </div>

                        @if(!empty($soal['rencana_visual']))

                            <div class="border rounded-3 bg-light p-3">

                                <h6 class="fw-bold">
                                    Rencana Ilustrasi
                                </h6>

                                @foreach($soal['rencana_visual'] as $visual)

                                    <p class="small mb-2">
                                        {{ $visual['teks_alternatif']
                                            ?? $visual['tujuan_visual']
                                            ?? 'Ilustrasi pendukung.' }}
                                    </p>

                                @endforeach
                            </div>

                        @endif

                    </div>

                @empty

                    <div class="alert alert-secondary">
                        Belum ada soal Adaptif yang dapat diedit.
                    </div>

                @endforelse
            </div>

            @if($assessment->status === 'Published')
                </fieldset>
            @endif

            <!-- SIMPAN REVISI -->
            @if($assessment->status !== 'Published')

                <div class="border-top pt-4 d-flex flex-wrap justify-content-between align-items-center gap-3">

                    <span class="edit-help">
                        Simpan perubahan kedua versi sebelum publikasi.
                    </span>

                    <button type="submit"
                            class="btn btn-primary rounded-3 px-4 py-2 fw-semibold">

                        <span class="material-symbols-outlined align-middle me-1">
                            save
                        </span>

                        Simpan Seluruh Revisi
                    </button>
                </div>

            @endif

        </div>

    </form>

    <!-- PUBLIKASI PAKET KE KELAS -->
    <div id="publikasi" class="edit-card">

        <div class="d-flex align-items-center gap-2 mb-3">

            <span class="material-symbols-outlined text-success">
                publish
            </span>

            <h5 class="fw-bold mb-0">
                Publikasikan Paket ke Kelas
            </h5>
        </div>

        <p class="text-muted small mb-4">
            Pilih kelas penerima. Seluruh paket
            Reguler dan Adaptif akan dipublikasikan bersama.
        </p>

        @if($assessment->status === 'Published')

            <div class="alert alert-success">
                Paket ini sudah dipublikasikan ke kelas.
            </div>

        @elseif(!$hasReview)

            <div class="alert alert-warning">
                Tinjau soal dan klik
                <strong>Simpan Seluruh Revisi</strong>
                terlebih dahulu.
            </div>

        @endif

        <form method="POST"
              action="{{ route('guru.assessments.publish-to-class', $assessment->id) }}">

            @csrf

            <label class="form-label fw-semibold">
                Pilih Kelas Tujuan
            </label>

            @forelse($classes as $class)

                <label class="class-option"
                       for="class-{{ $class->id }}">

                    <input
                        type="checkbox"
                        class="form-check-input mt-0"
                        name="class_ids[]"
                        value="{{ $class->id }}"
                        id="class-{{ $class->id }}"
                        {{ in_array((int) $class->id, old('class_ids', $assignedClassIds)) ? 'checked' : '' }}
                        {{ $assessment->status === 'Published' ? 'disabled' : '' }}>

                    <span class="fw-semibold">
                        {{ $class->name }}
                    </span>

                </label>

            @empty

                <div class="alert alert-secondary">
                    Belum ada kelas.
                    Buat kelas terlebih dahulu melalui
                    menu Manajemen Kelas.
                </div>

            @endforelse

            <p class="edit-help mt-2">
                Kamu boleh memilih lebih dari satu kelas.
            </p>

            <hr class="my-4">

            <div class="form-check mb-4">

                <input
                    type="checkbox"
                    class="form-check-input"
                    id="reviewConfirmed"
                    name="review_confirmed"
                    value="1"
                    required
                    {{ $assessment->status === 'Published' ? 'disabled' : '' }}>

                <label for="reviewConfirmed"
                       class="form-check-label">
                    Saya sudah memeriksa pertanyaan,
                    pilihan jawaban, kunci jawaban,
                    scaffolding, serta ilustrasi pada kedua versi asesmen.
                </label>

            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                <span class="edit-help">
                    Publikasi dilakukan setelah persetujuan guru.
                </span>

                <button type="submit"
                        class="btn btn-success rounded-3 px-4 py-2 fw-semibold"
                        {{ !$hasReview || $classes->isEmpty() || $assessment->status === 'Published' ? 'disabled' : '' }}>

                    <span class="material-symbols-outlined align-middle me-1">
                        publish
                    </span>

                    Publikasikan Satu Paket
                </button>
            </div>

        </form>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const btnReguler =
        document.getElementById('editTabReguler');

    const btnAdaptif =
        document.getElementById('editTabAdaptif');

    const panelReguler =
        document.getElementById('editPanelReguler');

    const panelAdaptif =
        document.getElementById('editPanelAdaptif');

    function selectVersion(version) {
        const isReguler = version === 'reguler';

        panelReguler.classList.toggle(
            'active',
            isReguler
        );

        panelAdaptif.classList.toggle(
            'active',
            !isReguler
        );

        btnReguler.className = isReguler
            ? 'btn btn-primary'
            : 'btn btn-outline-primary';

        btnAdaptif.className = isReguler
            ? 'btn btn-outline-primary'
            : 'btn btn-primary';
    }

    btnReguler.addEventListener('click', function () {
        selectVersion('reguler');
    });

    btnAdaptif.addEventListener('click', function () {
        selectVersion('adaptif');
    });

    selectVersion('reguler');
});
</script>

@endsection
