@extends('layouts.app')

@section('title', 'Analytics Guru')

@section('content')

<style>
    .analytics-page {
        max-width: 1150px;
        color: #14213d;
    }

    .analytics-card {
        background: #fff;
        border: 1px solid #e5eaf3;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 6px 22px rgba(24, 57, 112, .05);
    }

    .analytics-stat {
        min-height: 170px;
        border: 1px solid #e4edfb;
        background: linear-gradient(
            145deg, #fff 35%, #eaf2ff 100%
        );
        border-radius: 20px;
        padding: 22px;
    }

    .analytics-stat .symbol {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: #e9f1ff;
        color: #2563eb;
        margin-bottom: 18px;
    }

    .analytics-stat .value {
        font-size: 30px;
        line-height: 1.2;
        font-weight: 800;
        color: #15213b;
        overflow-wrap: anywhere;
    }

    .analytics-stat .label {
        color: #64748b;
        font-size: 13px;
        margin-top: 5px;
    }

    .analytics-bar-track {
        height: 14px;
        border-radius: 30px;
        background: #e9eff9;
        overflow: hidden;
    }

    .analytics-bar-fill {
        height: 100%;
        border-radius: 30px;
        background: linear-gradient(90deg, #2563eb, #60a5fa);
    }

    .analytics-table thead th {
        color: #64748b;
        background: #f8faff;
        font-size: 13px;
        white-space: nowrap;
    }

    .analytics-table td,
    .analytics-table th {
        vertical-align: middle;
        padding: 14px;
    }

    .analytics-muted {
        color: #64748b;
    }

    @media (max-width: 575px) {
        .analytics-card {
            padding: 17px;
            border-radius: 15px;
        }
    }
</style>

<div class="container py-4 py-md-5 analytics-page">

    {{-- HEADER --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('guru.dashboard') }}"
           class="btn btn-light border rounded-3"
           aria-label="Kembali">
            <span class="material-symbols-outlined">
                arrow_back
            </span>
        </a>

        <div>
            <h1 class="h3 fw-bold mb-1">
                Analytics Guru
            </h1>

            <p class="analytics-muted mb-0">
                Pantau hasil asesmen dan perkembangan belajar kelas.
            </p>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="analytics-card mb-4">

        <h5 class="fw-bold mb-3">
            Filter Hasil Belajar
        </h5>

        <form method="GET"
              action="{{ route('guru.analytics.index') }}">

            <div class="row g-3">

                <div class="col-md-5">
                    <label for="class_id"
                           class="form-label fw-semibold">
                        Pilih Kelas
                    </label>

                    <select id="class_id"
                            name="class_id"
                            class="form-select">

                        @foreach($classes as $class)
                            <option
                                value="{{ $class->id }}"
                                {{ (int) $selectedClassId ===
                                   (int) $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="col-md-5">
                    <label for="assessment_id"
                           class="form-label fw-semibold">
                        Pilih Asesmen
                    </label>

                    <select id="assessment_id"
                            name="assessment_id"
                            class="form-select">

                        <option value="">
                            Semua Asesmen
                        </option>

                        @foreach($assessmentOptions as $assessment)
                            <option
                                value="{{ $assessment->id }}"
                                {{ (int) $selectedAssessmentId ===
                                   (int) $assessment->id
                                   ? 'selected' : '' }}>
                                {{ $assessment->nama }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit"
                            class="btn btn-primary w-100">
                        Terapkan
                    </button>
                </div>

            </div>
        </form>

        <p class="small analytics-muted mt-3 mb-0">
            Statistik dihitung dari nilai dan pengumpulan
            yang sudah tersimpan di database.
        </p>
    </div>

    @if(!$selectedClass)

        <div class="analytics-card text-center py-5">
            <span class="material-symbols-outlined
                         text-secondary mb-3"
                  style="font-size:48px;">
                school
            </span>

            <h5 class="fw-bold">
                Belum ada kelas
            </h5>

            <p class="analytics-muted mb-0">
                Buat kelas terlebih dahulu untuk melihat analytics.
            </p>
        </div>

    @else

        {{-- KARTU STATISTIK --}}
        <div class="row g-3 mb-4">

            <div class="col-6 col-lg-3">
                <div class="analytics-stat h-100">

                    <div class="symbol">
                        <span class="material-symbols-outlined">
                            star
                        </span>
                    </div>

                    <div class="value">
                        {{ $stats['rata_rata'] !== null
                            ? number_format($stats['rata_rata'], 1, ',', '.')
                            : '—' }}
                    </div>

                    <div class="label">
                        Rata-rata Nilai
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="analytics-stat h-100">

                    <div class="symbol">
                        <span class="material-symbols-outlined">
                            assignment
                        </span>
                    </div>

                    <div class="value">
                        {{ $stats['jumlah_asesmen'] }}
                    </div>

                    <div class="label">
                        Asesmen Diberikan
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="analytics-stat h-100">

                    <div class="symbol">
                        <span class="material-symbols-outlined">
                            task_alt
                        </span>
                    </div>

                    <div class="value">
                        {{ $stats['penyelesaian'] !== null
                            ? number_format($stats['penyelesaian'], 1, ',', '.') . '%'
                            : '—' }}
                    </div>

                    <div class="label">
                        Tingkat Penyelesaian
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="analytics-stat h-100">

                    <div class="symbol">
                        <span class="material-symbols-outlined">
                            groups
                        </span>
                    </div>

                    <div class="value">
                        {{ $stats['jumlah_siswa'] }}
                    </div>

                    <div class="label">
                        Jumlah Siswa
                    </div>
                </div>
            </div>

        </div>

        {{-- INFORMASI RINGKAS --}}
        <div class="analytics-card mb-4">
            <h5 class="fw-bold mb-3">
                Ringkasan {{ $selectedClass->name }}
            </h5>

            <div class="row g-3 text-center">
                <div class="col-6 col-md-3">
                    <div class="analytics-muted small">
                        Pengumpulan
                    </div>
                    <div class="h4 fw-bold mt-1">
                        {{ $stats['selesai'] }}
                        /
                        {{ $stats['total_tugas'] }}
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="analytics-muted small">
                        Sudah Dinilai
                    </div>
                    <div class="h4 fw-bold mt-1">
                        {{ $stats['dinilai'] }}
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="analytics-muted small">
                        Nilai Tertinggi
                    </div>
                    <div class="h4 fw-bold mt-1">
                        {{ $stats['tertinggi'] !== null
                            ? number_format($stats['tertinggi'], 1, ',', '.')
                            : '—' }}
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="analytics-muted small">
                        Nilai Terendah
                    </div>
                    <div class="h4 fw-bold mt-1">
                        {{ $stats['terendah'] !== null
                            ? number_format($stats['terendah'], 1, ',', '.')
                            : '—' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- GRAFIK RATA-RATA ASESMEN --}}
        <div class="analytics-card mb-4">

            <h5 class="fw-bold mb-2">
                Rata-rata Nilai per Asesmen
            </h5>

            <p class="analytics-muted small mb-4">
                Dihitung dari nilai akhir siswa yang
                sudah dinilai pada masing-masing asesmen.
            </p>

            @forelse($assessmentStats as $item)

                <div class="mb-4">

                    <div class="d-flex justify-content-between
                                align-items-center gap-3 mb-2">

                        <div>
                            <strong>
                                {{ $item['nama'] }}
                            </strong>

                            <div class="analytics-muted small">
                                {{ $item['mapel'] }}
                                •
                                {{ $item['submitted'] }}/{{ $item['total'] }}
                                dikumpulkan
                            </div>
                        </div>

                        <strong>
                            {{ $item['average'] !== null
                                ? number_format($item['average'], 1, ',', '.')
                                : 'Belum dinilai' }}
                        </strong>

                    </div>

                    <div class="analytics-bar-track">

                        @if($item['average'] !== null)
                            <div class="analytics-bar-fill"
                                 style="width: {{
                                     max(0, min(100, $item['average']))
                                 }}%;">
                            </div>
                        @endif

                    </div>

                </div>

            @empty

                <p class="analytics-muted mb-0">
                    Belum ada asesmen yang diberikan ke kelas ini.
                </p>

            @endforelse
        </div>

        {{-- RATA-RATA SISWA --}}
        <div class="analytics-card mb-4">

            <h5 class="fw-bold mb-2">
                Ringkasan Hasil per Siswa
            </h5>

            <p class="analytics-muted small mb-3">
                Rata-rata setiap siswa dihitung hanya dari
                asesmen yang sudah memiliki nilai.
            </p>

            <div class="table-responsive">
                <table class="table analytics-table mb-0">

                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Sudah Mengumpulkan</th>
                            <th>Sudah Dinilai</th>
                            <th>Rata-rata</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($studentStats as $student)

                            <tr>
                                <td class="fw-semibold">
                                    {{ $student['name'] }}
                                </td>

                                <td>
                                    {{ $student['submitted'] }}
                                    /
                                    {{ $student['total'] }}
                                </td>

                                <td>
                                    {{ $student['graded'] }}
                                </td>

                                <td class="fw-bold">
                                    {{ $student['average'] !== null
                                        ? number_format(
                                            $student['average'],
                                            1,
                                            ',',
                                            '.'
                                        )
                                        : '—' }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="4"
                                    class="text-center analytics-muted py-4">
                                    Belum ada siswa di kelas ini.
                                </td>
                            </tr>

                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- DETAIL HASIL SETIAP SISWA --}}
        <div class="analytics-card mb-4">

            <h5 class="fw-bold mb-2">
                Detail Nilai Setiap Siswa
            </h5>

            <p class="analytics-muted small mb-3">
                Menampilkan nilai dan status pengerjaan
                untuk setiap asesmen yang diberikan.
            </p>

            <div class="table-responsive">
                <table class="table analytics-table mb-0">

                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Asesmen</th>
                            <th>Mata Pelajaran</th>
                            <th>Status</th>
                            <th>Nilai</th>
                            <th>Dikumpulkan</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($detailRows as $row)

                            <tr>
                                <td class="fw-semibold">
                                    {{ $row['student_name'] }}
                                </td>

                                <td>
                                    {{ $row['assessment_name'] }}
                                </td>

                                <td>
                                    {{ $row['mapel'] }}
                                </td>

                                <td>
                                    @if($row['status'] === 'Dinilai')
                                        <span class="badge bg-success">
                                            Dinilai
                                        </span>
                                    @elseif($row['status'] === 'Menunggu nilai')
                                        <span class="badge bg-info text-dark">
                                            Menunggu nilai
                                        </span>
                                    @elseif($row['status'] === 'Sedang dikerjakan')
                                        <span class="badge bg-warning text-dark">
                                            Sedang dikerjakan
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            Belum dikerjakan
                                        </span>
                                    @endif
                                </td>

                                <td class="fw-bold">
                                    {{ $row['score'] !== null
                                        ? number_format(
                                            $row['score'],
                                            1,
                                            ',',
                                            '.'
                                        )
                                        : '—' }}
                                </td>

                                <td class="small analytics-muted">
                                    {{ $row['submitted_at']
                                        ? \Illuminate\Support\Carbon::parse(
                                            $row['submitted_at']
                                        )->format('d/m/Y H:i')
                                        : '—' }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="6"
                                    class="text-center analytics-muted py-4">
                                    Belum ada hasil pengerjaan untuk ditampilkan.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>

    @endif

</div>

@endsection
