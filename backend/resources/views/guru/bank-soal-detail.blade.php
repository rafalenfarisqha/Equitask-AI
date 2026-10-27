@extends('layouts.app')

@section('title', 'Detail Bank Soal')

@section('content')
<div class="container py-4 pb-5" style="max-width: 1200px;">

    {{-- Tombol kembali --}}
    <a href="{{ route('guru.bank-soal') }}"
       class="btn btn-light border rounded-3 mb-4">
        <span class="material-symbols-outlined align-middle me-1">
            arrow_back
        </span>
        Kembali ke Bank Soal
    </a>

    {{-- Informasi asesmen --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 p-lg-5">

            <span class="badge bg-success rounded-pill px-3 py-2 mb-3">
                Asesmen Publik
            </span>

            <h3 class="fw-bold mb-4">
                {{ $assessment->nama }}
            </h3>

            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="small text-secondary mb-1">
                            Mata Pelajaran
                        </div>
                        <div class="fw-semibold">
                            {{ $assessment->mapel ?? '-' }}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="small text-secondary mb-1">
                            Kelas
                        </div>
                        <div class="fw-semibold">
                            {{ $assessment->kelas ?? '-' }}
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="small text-secondary mb-1">
                            Guru Pembuat
                        </div>
                        <div class="fw-semibold">
                            {{ $assessment->teacher?->name ?? 'Guru tidak diketahui' }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Judul paket --}}
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Paket Soal</h4>
        <p class="text-secondary mb-0">
            Lihat soal dan unduh paket asesmen dalam format PDF.
        </p>
    </div>

    @php
        $paketSoal = [
            [
                'judul' => 'Soal Reguler',
                'deskripsi' => 'Paket asesmen standar',
                'format' => 'regular',
                'soal' => $questionsRegular ?? [],
                'warna' => 'primary',
                'ikon' => 'description',
            ],
            [
                'judul' => 'Soal Easy Read',
                'deskripsi' => 'Paket asesmen versi inklusif',
                'format' => 'easy_read',
                'soal' => $questionsEasyRead ?? [],
                'warna' => 'success',
                'ikon' => 'menu_book',
            ],
        ];
    @endphp

    {{-- Dua kartu paket --}}
    <div class="row g-4 align-items-start">

        @foreach ($paketSoal as $paket)

            @php
                $daftarSoal = is_array($paket['soal'])
                    ? array_values($paket['soal'])
                    : [];

                $isEasyRead = $paket['format'] === 'easy_read';
            @endphp

            <div class="col-12 col-lg-6">

                <div class="card border-0 shadow-sm rounded-4 h-100">

                    {{-- Header paket dan tombol download --}}
                    <div class="card-body p-4 border-bottom">

                        <div class="d-flex align-items-center gap-3 mb-3">

                            <div class="rounded-3 d-flex align-items-center justify-content-center
                                {{ $paket['warna'] === 'primary'
                                    ? 'bg-primary-subtle text-primary'
                                    : 'bg-success-subtle text-success' }}"
                                 style="width: 48px; height: 48px; flex-shrink: 0;">

                                <span class="material-symbols-outlined">
                                    {{ $paket['ikon'] }}
                                </span>

                            </div>

                            <div>
                                <h5 class="fw-bold mb-1">
                                    {{ $paket['judul'] }}
                                </h5>

                                <p class="text-secondary small mb-0">
                                    {{ $paket['deskripsi'] }}
                                </p>
                            </div>

                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-secondary small">
                                Jumlah soal
                            </span>

                            <span class="badge bg-light text-dark border">
                                {{ count($daftarSoal) }} soal
                            </span>
                        </div>

                        @if (count($daftarSoal) > 0)

                            <a href="{{ route('guru.bank-soal.download-pdf', [
                                'id' => $assessment->id,
                                'format' => $paket['format'],
                            ]) }}"
                               class="btn {{ $paket['warna'] === 'primary'
                                    ? 'btn-primary'
                                    : 'btn-success' }} w-100 rounded-3">

                                <span class="material-symbols-outlined align-middle me-1">
                                    download
                                </span>

                                Download PDF {{ $paket['judul'] }}

                            </a>

                        @else

                            <button type="button"
                                    class="btn btn-outline-secondary w-100 rounded-3"
                                    disabled>
                                PDF belum tersedia
                            </button>

                        @endif

                    </div>

                    {{-- Pratinjau soal --}}
                    <div class="card-body p-3 p-md-4">

                        @if (count($daftarSoal) > 0)

                            @foreach ($daftarSoal as $index => $item)

                                @php
                                    $item = is_array($item) ? $item : [];

                                    $pertanyaan = $item['pertanyaan']
                                        ?? $item['soal']
                                        ?? $item['question']
                                        ?? $item['teks_soal']
                                        ?? '';

                                    $pilihan = $item['opsi']
                                        ?? $item['pilihan_jawaban']
                                        ?? $item['pilihan']
                                        ?? $item['options']
                                        ?? [];

                                    if (is_string($pilihan)) {
                                        $decodedPilihan = json_decode(
                                            $pilihan,
                                            true
                                        );

                                        $pilihan = is_array($decodedPilihan)
                                            ? $decodedPilihan
                                            : [];
                                    }

                                    $questionId = trim((string) (
                                        $item['id_soal_asal']
                                        ?? $item['id_soal']
                                        ?? $item['kode_soal']
                                        ?? $item['id']
                                        ?? ''
                                    ));

                                    $questionImages = (
                                        $isEasyRead && $questionId !== ''
                                    )
                                        ? ($imagesByQuestion[$questionId] ?? [])
                                        : [];
                                @endphp

                                <article class="border rounded-3 p-3 mb-3">

                                    {{-- Nomor soal --}}
                                    <div class="mb-3">
                                        <span class="badge {{ $paket['warna'] === 'primary'
                                            ? 'bg-primary'
                                            : 'bg-success' }}">
                                            Soal {{ $index + 1 }}
                                        </span>
                                    </div>

                                    {{-- Ilustrasi khusus Easy Read --}}
                                    @if ($isEasyRead && count($questionImages) > 0)

                                        <div class="mb-4">

                                            <h6 class="fw-semibold mb-3">
                                                <span class="material-symbols-outlined align-middle text-success me-1">
                                                    image
                                                </span>
                                                Ilustrasi Soal
                                            </h6>

                                            <div class="row g-3">

                                                @foreach ($questionImages as $image)

                                                    <div class="col-12">

                                                        <div class="border rounded-3 p-3 bg-light text-center">

                                                            <img
                                                                src="{{ $image['url'] }}"
                                                                alt="{{ $image['alt'] ?? 'Ilustrasi soal ' . ($index + 1) }}"
                                                                class="img-fluid rounded-3"
                                                                style="max-height: 320px; width: auto; object-fit: contain;"
                                                                loading="lazy"
                                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">

                                                            <div class="alert alert-warning small mt-2 mb-0"
                                                                 style="display: none;">
                                                                Gambar gagal dimuat.
                                                                Periksa URL atau akses penyimpanan gambar.
                                                            </div>

                                                            @if (!empty($image['id_visual']))
                                                                <div class="small text-secondary mt-2">
                                                                    {{ $image['id_visual'] }}
                                                                </div>
                                                            @endif

                                                        </div>

                                                    </div>

                                                @endforeach

                                            </div>

                                        </div>

                                    @endif

                                    {{-- Pertanyaan --}}
                                    <div class="fw-semibold mb-3"
                                         style="white-space: pre-line; overflow-wrap: anywhere;">
                                        {{ $pertanyaan ?: 'Teks soal belum tersedia.' }}
                                    </div>

                                    {{-- Pilihan jawaban --}}
                                    @if (is_array($pilihan) && count($pilihan) > 0)

                                        <div class="d-grid gap-2">

                                            @foreach ($pilihan as $key => $opsi)

                                                @php
                                                    $teksOpsi = is_array($opsi)
                                                        ? (
                                                            $opsi['teks']
                                                            ?? $opsi['text']
                                                            ?? $opsi['label']
                                                            ?? $opsi['jawaban']
                                                            ?? ''
                                                        )
                                                        : $opsi;

                                                    $labelOpsi = is_string($key)
                                                        ? $key
                                                        : chr(65 + $loop->index);
                                                @endphp

                                                <div class="border rounded-3 p-2"
                                                     style="overflow-wrap: anywhere;">

                                                    <strong>
                                                        {{ $labelOpsi }}.
                                                    </strong>

                                                    {{ $teksOpsi }}

                                                </div>

                                            @endforeach

                                        </div>

                                    @endif

                                </article>

                            @endforeach

                        @else

                            <div class="text-center py-4">

                                <span class="material-symbols-outlined text-secondary"
                                      style="font-size: 40px;">
                                    description
                                </span>

                                <p class="fw-semibold mt-2 mb-1">
                                    Belum ada soal untuk ditampilkan.
                                </p>

                                <p class="text-secondary small mb-0">
                                    Periksa data hasil generate atau revisi asesmen.
                                </p>

                            </div>

                        @endif

                    </div>

                </div>

            </div>

        @endforeach

    </div>

</div>
@endsection
