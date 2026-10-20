@extends('layouts.app')

@section('title', 'Detail Asesmen')

@section('content')
<div class="container py-5" style="max-width: 900px;">

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 px-2">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('guru.assessments.index') }}" class="btn btn-light border shadow-sm p-2 d-flex align-items-center justify-content-center rounded-3 text-secondary" style="width: 40px; height: 40px;">
                <span class="material-symbols-outlined" style="font-size: 20px;">arrow_back</span>
            </a>
            <div>
                <h1 class="h4 fw-bold text-dark mb-1">{{ $assessment->nama }}</h1>
                <p class="text-secondary small mb-0">
                    {{ $assessment->mapel }} • {{ $assessment->kelas ?? 'Kelas Umum' }} • Versi: {{ $assessment->versi_asesmen ?? 'Standard' }}
                </p>
            </div>
        </div>
        <div>
            <span class="badge bg-{{ $assessment->status == 'Published' ? 'success' : 'warning' }} px-3 py-2 rounded-3">
                {{ $assessment->status }}
            </span>
        </div>
    </div>

    <!-- Informasi Detail & Tujuan Pembelajaran -->
    <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
        <h5 class="fw-bold text-dark mb-2">Tujuan Pembelajaran</h5>
        <p class="text-secondary mb-4">{{ $assessment->tujuan_pembelajaran }}</p>

        <h5 class="fw-bold text-dark mb-2">Hasil Susunan Soal dari Multi-Agent Dify AI</h5>
        <div class="p-4 bg-light rounded-3 border text-secondary" style="white-space: pre-line; line-height: 1.6;">
            {!! $assessment->description ?? '<span class="text-muted fst-italic">Belum ada hasil generate atau proses AI sedang berjalan / gagal.</span>' !!}
        </div>
    </div>

    <!-- Tombol Aksi -->
    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('guru.assessments.index') }}" class="btn btn-secondary px-4 rounded-3 fw-semibold">Kembali ke Daftar</a>
        @if($assessment->visibility == 'private')
            <form action="{{ route('guru.assessments.share', $assessment->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success px-4 rounded-3 fw-semibold d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size: 18px;">share</span> Bagikan ke Bank Soal Publik
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
