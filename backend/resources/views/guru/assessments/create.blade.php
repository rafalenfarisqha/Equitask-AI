@extends('layouts.app')

@section('title', 'Buat Asesmen dengan EquiTask AI')

@section('content')
<div class="container py-5" style="max-width: 860px;">

    <!-- Header Banner dengan Aksen Gradien AI -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);">
        <div class="position-absolute top-0 end-0 p-3 opacity-10">
            <span class="material-symbols-outlined" style="font-size: 140px;">psychology</span>
        </div>
        <div class="d-flex align-items-center gap-3 position-relative z-1">
            <a href="{{ route('guru.assessments.index') }}" class="btn btn-light bg-white bg-opacity-20 border-0 text-white p-2 d-flex align-items-center justify-content-center rounded-3 transition hover-bg-opacity-30" style="width: 42px; height: 42px;" title="Kembali">
                <span class="material-symbols-outlined" style="font-size: 22px;">arrow_back</span>
            </a>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-white bg-opacity-25 text-white px-2 py-1 rounded-2 small fw-semibold">Multi-Agent AI</span>
                </div>
                <h1 class="h3 fw-bold mb-1" style="letter-spacing: -0.5px;">Buat Asesmen dengan EquiTask AI</h1>
                <p class="text-white text-opacity-75 small mb-0">Hasilkan soal format reguler dan adaptif secara otomatis berdasarkan materi ajar.</p>
            </div>
        </div>
    </div>

    <!-- Card Form Utama -->
    <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-white">
        <form action="{{ route('guru.assessments.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Judul Asesmen & Mata Pelajaran -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark small">Judul Asesmen</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Sumatif Bab Wujud Benda & Perubahannya" class="form-control bg-light bg-opacity-50 rounded-3 py-2.5 text-secondary shadow-none border-light-subtle" style="font-size: 0.9rem;">
                    @error('nama') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark small">Mata Pelajaran</label>
                    <input type="text" name="mata_pelajaran" value="{{ old('mata_pelajaran') }}" required placeholder="Contoh: Ilmu Pengetahuan Alam (IPA)" class="form-control bg-light bg-opacity-50 rounded-3 py-2.5 text-secondary shadow-none border-light-subtle" style="font-size: 0.9rem;">
                    @error('mata_pelajaran') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- Kelas & Versi Asesmen -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark small">Kelas</label>
                    <select name="kelas" required class="form-select bg-light bg-opacity-50 rounded-3 py-2.5 text-secondary shadow-none border-light-subtle" style="font-size: 0.9rem;">
                        <option value="" disabled selected>Pilih Kelas</option>
                        <option value="Kelas 1" {{ old('kelas') == 'Kelas 1' ? 'selected' : '' }}>Kelas 1</option>
                        <option value="Kelas 2" {{ old('kelas') == 'Kelas 2' ? 'selected' : '' }}>Kelas 2</option>
                        <option value="Kelas 3" {{ old('kelas') == 'Kelas 3' ? 'selected' : '' }}>Kelas 3</option>
                        <option value="Kelas 4" {{ old('kelas') == 'Kelas 4' ? 'selected' : '' }}>Kelas 4</option>
                        <option value="Kelas 5" {{ old('kelas') == 'Kelas 5' ? 'selected' : '' }}>Kelas 5</option>
                        <option value="Kelas 6" {{ old('kelas') == 'Kelas 6' ? 'selected' : '' }}>Kelas 6</option>
                    </select>
                    @error('kelas') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark small">Versi & Format Asesmen</label>
                    <select name="versi_asesmen" class="form-select bg-light bg-opacity-50 rounded-3 py-2.5 text-secondary shadow-none border-light-subtle" style="font-size: 0.9rem;">
                        <option value="Asesmen Awal (Reguler & Adaptif)">Asesmen Awal (Reguler & Adaptif)</option>
                        <option value="Asesmen Utama (Reguler & Adaptif)">Asesmen Utama (Reguler & Adaptif)</option>
                    </select>
                </div>
            </div>

            <!-- Tujuan Pembelajaran -->
            <div class="mb-3">
                <label class="form-label fw-semibold text-dark small">Tujuan Pembelajaran</label>
                <textarea name="tujuan_pembelajaran" rows="3" required placeholder="Contoh: Peserta didik dapat mengidentifikasi wujud benda padat, cair, dan gas..." class="form-control bg-light bg-opacity-50 rounded-3 p-3 text-secondary shadow-none border-light-subtle" style="font-size: 0.9rem;">{{ old('tujuan_pembelajaran') }}</textarea>
                @error('tujuan_pembelajaran') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Materi Ajar (Upload File dengan Feedback Nama File) -->
            <div class="mb-4">
                <label class="form-label fw-semibold text-dark small">Materi Ajar (Bahan Bacaan / Buku Guru)</label>
                <div id="drop-zone" class="border border-2 border-dashed rounded-4 p-4 text-center bg-primary bg-opacity-10 border-primary border-opacity-25 transition position-relative">
                    <div class="mx-auto w-12 h-12 rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                        <span class="material-symbols-outlined" style="font-size: 26px;">cloud_upload</span>
                    </div>
                    <div>
                        <label for="materi_ajar" class="btn btn-primary btn-sm fw-semibold px-4 py-2 rounded-3 shadow-sm cursor-pointer">
                            Pilih Berkas Materi
                            <input id="materi_ajar" name="materi_ajar" type="file" class="d-none" accept=".pdf,.txt,.doc,.docx,.ppt,.pptx" required>
                        </label>
                    </div>
                    <div class="text-muted small mt-2">atau seret ke sini</div>
                    <!-- Teks penanda nama file yang terpilih akan muncul di sini -->
                    <div id="file-name-display" class="fw-bold text-success small mt-2"></div>
                    <div class="text-muted opacity-75 mt-1" style="font-size: 11px;">Format yang didukung: PDF, TXT, DOCX, PPTX (Maksimal 10MB)</div>
                </div>
                @error('materi_ajar') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <!-- Script kecil untuk mendeteksi file yang dipilih / diseret -->
            @push('scripts')
            <script>
                const fileInput = document.getElementById('materi_ajar');
                const fileNameDisplay = document.getElementById('file-name-display');
                const dropZone = document.getElementById('drop-zone');

                // Ketika file dipilih lewat tombol
                fileInput.addEventListener('change', function() {
                    if (fileInput.files.length > 0) {
                        fileNameDisplay.textContent = '📄 File terpilih: ' + fileInput.files[0].name;
                    }
                });

                // Mencegah perilaku default browser saat file diseret
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropZone.classList.add('bg-opacity-25');
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        dropZone.classList.remove('bg-opacity-25');
                    }, false);
                });

                // Ketika file dilepas (di-drop) ke dalam kotak
                dropZone.addEventListener('drop', (e) => {
                    let dt = e.dataTransfer;
                    let files = dt.files;
                    if (files.length > 0) {
                        fileInput.files = files;
                        fileNameDisplay.textContent = '📄 File terpilih: ' + files[0].name;
                    }
                }, false);
            </script>
            @endpush

            <!-- Jumlah Pilihan Ganda & Esai -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark small">Jumlah Soal Pilihan Ganda</label>
                    <input type="number" name="jumlah_pilihan_ganda" value="{{ old('jumlah_pilihan_ganda', 10) }}" min="0" max="50" required class="form-control bg-light bg-opacity-50 rounded-3 py-2.5 text-secondary shadow-none border-light-subtle" style="font-size: 0.9rem;">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark small">Jumlah Soal Esai</label>
                    <input type="number" name="jumlah_esai" value="{{ old('jumlah_esai', 2) }}" min="0" max="20" required class="form-control bg-light bg-opacity-50 rounded-3 py-2.5 text-secondary shadow-none border-light-subtle" style="font-size: 0.9rem;">
                </div>
            </div>

            <!-- Taksonomi Bloom (C1 - C6) -->
            <div class="mb-4 p-4 rounded-4 border-0" style="background-color: #f8fafc;">
                <label class="form-label fw-semibold text-dark small mb-2">Level Kognitif Taksonomi Bloom (C1 - C6)</label>
                <div class="row g-2">
                    <div class="col-6 col-md-4">
                        <div class="form-check bg-white p-2.5 rounded-3 border border-light-subtle shadow-sm">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="c1" value="1" checked id="c1">
                            <label class="form-check-label small fw-medium text-secondary" for="c1">C1 (Remember)</label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-check bg-white p-2.5 rounded-3 border border-light-subtle shadow-sm">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="c2" value="1" checked id="c2">
                            <label class="form-check-label small fw-medium text-secondary" for="c2">C2 (Understand)</label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-check bg-white p-2.5 rounded-3 border border-light-subtle shadow-sm">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="c3" value="1" checked id="c3">
                            <label class="form-check-label small fw-medium text-secondary" for="c3">C3 (Apply)</label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-check bg-white p-2.5 rounded-3 border border-light-subtle shadow-sm">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="c4" value="1" id="c4">
                            <label class="form-check-label small fw-medium text-secondary" for="c4">C4 (Analyze)</label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-check bg-white p-2.5 rounded-3 border border-light-subtle shadow-sm">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="c5" value="1" id="c5">
                            <label class="form-check-label small fw-medium text-secondary" for="c5">C5 (Evaluate)</label>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-check bg-white p-2.5 rounded-3 border border-light-subtle shadow-sm">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="c6" value="1" id="c6">
                            <label class="form-check-label small fw-medium text-secondary" for="c6">C6 (Create)</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status & Visibility -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark small">Status Awal</label>
                    <select name="status" class="form-select bg-light bg-opacity-50 rounded-3 py-2.5 text-secondary shadow-none border-light-subtle" style="font-size: 0.9rem;">
                        <option value="Draft">Draft</option>
                        <option value="Published">Published</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark small">Akses (Visibility)</label>
                    <select name="visibility" class="form-select bg-light bg-opacity-50 rounded-3 py-2.5 text-secondary shadow-none border-light-subtle" style="font-size: 0.9rem;">
                        <option value="private">Private (Pribadi)</option>
                        <option value="public">Public (Bagikan ke Bank Soal)</option>
                    </select>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="d-flex align-items-center justify-content-end gap-3 pt-4 border-top">
                <a href="{{ route('guru.assessments.index') }}" class="btn btn-light px-4 py-2.5 rounded-3 fw-semibold text-secondary">Batal</a>
                <button type="submit" class="btn btn-primary px-4 py-2.5 rounded-3 fw-semibold d-flex align-items-center gap-2 shadow">
                    <span class="material-symbols-outlined" style="font-size: 18px;">auto_awesome</span> Generate Asesmen (Reguler & Adaptif)
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
