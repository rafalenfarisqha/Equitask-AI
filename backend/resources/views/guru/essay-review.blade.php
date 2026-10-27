@extends('layouts.app')

@section('title', 'Tinjauan Esai — EquiTask AI')

@section('content')
<div class="review-page">
    <main class="review-container">

        <header class="review-header">
            <div>
                <p class="review-eyebrow">
                    EQUITASK AI · PENILAIAN GURU
                </p>

                <h1>📝 Tinjauan Jawaban Esai</h1>

                <p>
                    Periksa jawaban siswa dan berikan
                    skor berdasarkan bobot soal.
                </p>
            </div>
        </header>

        @if(session('success'))
            <div class="review-success" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="review-error" role="alert">
                <strong>Periksa kembali data penilaian.</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="review-summary">
            <div>
                <span>Nama siswa</span>
                <strong>{{ $attempt->student_name }}</strong>
            </div>

            <div>
                <span>Asesmen</span>
                <strong>{{ $attempt->assessment_name }}</strong>
            </div>

            <div>
                <span>Mata pelajaran</span>
                <strong>{{ $attempt->mapel }}</strong>
            </div>

            <div>
                <span>Skor sementara</span>
                <strong>{{ $attempt->score ?? '—' }}</strong>
            </div>
        </section>

        <section class="review-section">
            <div class="section-heading">
                <h2>Daftar Jawaban Esai</h2>
                <span>{{ $essays->count() }} soal</span>
            </div>

            @forelse($essays as $index => $essay)

                @php
                    $maxPoints = (float) $essay->maximum_points;
                    $currentPoints = (float) $essay->points_obtained;
                    $isPending = $essay->is_correct === null;
                @endphp

                <article class="essay-card">
                    <div class="essay-top">
                        <span class="essay-number">
                            Soal {{ $index + 1 }}
                        </span>

                        <span class="essay-max">
                            Maksimal {{ $maxPoints }} poin
                        </span>
                    </div>

                    <h3>Pertanyaan</h3>

                    <div class="essay-text">
                        {{ $essay->question_text }}
                    </div>

                    <h3>Jawaban siswa</h3>

                    <div class="answer-box">
                        {{ filled($essay->answer)
                            ? $essay->answer
                            : 'Siswa tidak mengisi jawaban.' }}
                    </div>

                    <h3>Referensi jawaban</h3>

                    <div class="reference-box">
                        {{ $essay->correct_answer
                            ?: 'Belum tersedia referensi jawaban.' }}
                    </div>

                    <div class="essay-status">
                        @if($isPending)
                            <span class="status pending">
                                Menunggu penilaian
                            </span>
                        @else
                            <span class="status complete">
                                Sudah memiliki skor
                            </span>
                        @endif

                        @if(isset($essay->grading_source)
                            && $essay->grading_source)
                            <span class="status source">
                                Sumber:
                                {{ $essay->grading_source }}
                            </span>
                        @endif
                    </div>

                    @if(!empty($essay->ai_feedback))
                        <div class="ai-feedback">
                            <strong>✨ Catatan AI</strong>
                            <p>{{ $essay->ai_feedback }}</p>
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('guru.essay-review.update', [
                            'attemptId' => $attempt->id,
                            'questionId' => $essay->question_id
                        ]) }}"
                        class="grading-form"
                    >
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label
                                for="score-{{ $essay->question_id }}"
                            >
                                Skor dari guru
                            </label>

                            <input
                                type="number"
                                id="score-{{ $essay->question_id }}"
                                name="score"
                                min="0"
                                max="{{ $maxPoints }}"
                                step="0.01"
                                value="{{ old(
                                    'score',
                                    $isPending ? '' : $currentPoints
                                ) }}"
                                placeholder="Masukkan skor"
                                required
                            >

                            <small>
                                Masukkan nilai antara 0
                                dan {{ $maxPoints }}.
                            </small>
                        </div>

                        <div class="form-group">
                            <label
                                for="feedback-{{ $essay->question_id }}"
                            >
                                Catatan untuk siswa
                            </label>

                            <textarea
                                id="feedback-{{ $essay->question_id }}"
                                name="feedback"
                                rows="4"
                                placeholder="Tuliskan bagian yang sudah baik dan yang perlu dipelajari..."
                            >{{ old(
                                'feedback',
                                $essay->teacher_feedback ?? ''
                            ) }}</textarea>
                        </div>

                        <button type="submit" class="save-button">
                            Simpan Penilaian
                        </button>
                    </form>
                </article>

            @empty
                <div class="empty-state">
                    <h3>Belum ada jawaban esai</h3>
                    <p>
                        Tidak ditemukan soal esai
                        pada pengerjaan siswa ini.
                    </p>
                </div>
            @endforelse
        </section>

    </main>
</div>

<style>
.review-page {
    min-height: 100vh;
    background: #f4f7ff;
    padding: 36px 18px 70px;
    color: #17233e;
    font-family: Inter, system-ui, sans-serif;
}

.review-container {
    max-width: 950px;
    margin: 0 auto;
}

.review-header {
    margin-bottom: 26px;
}

.review-eyebrow {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1.4px;
    color: #2563eb;
}

.review-header h1 {
    font-size: clamp(24px, 4vw, 32px);
    margin: 10px 0;
}

.review-header p {
    color: #71809a;
    font-size: 14px;
    line-height: 1.7;
}

.review-summary {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    background: white;
    border: 1px solid #e5ebf5;
    padding: 24px;
    border-radius: 20px;
    margin-bottom: 28px;
}

.review-summary div {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.review-summary span {
    color: #75829a;
    font-size: 12px;
}

.review-summary strong {
    font-size: 15px;
}

.section-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 18px;
}

.section-heading h2 {
    font-size: 19px;
    margin: 0;
}

.section-heading span {
    color: #71809a;
    font-size: 13px;
}

.essay-card {
    background: white;
    border: 1px solid #e5ebf5;
    border-radius: 20px;
    padding: 25px;
    margin-bottom: 22px;
    box-shadow: 0 8px 28px #1d355708;
}

.essay-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}

.essay-number {
    color: #2563eb;
    font-weight: 800;
    font-size: 13px;
}

.essay-max {
    background: #edf3ff;
    color: #2455be;
    padding: 8px 12px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 700;
}

.essay-card h3 {
    font-size: 14px;
    margin: 20px 0 10px;
}

.essay-text,
.answer-box,
.reference-box {
    padding: 17px;
    border-radius: 13px;
    white-space: pre-wrap;
    overflow-wrap: anywhere;
    font-size: 14px;
    line-height: 1.8;
}

.essay-text {
    background: #f5f7fc;
}

.answer-box {
    background: #edf7ff;
    border: 1px solid #d6eaff;
}

.reference-box {
    background: #f6fbf6;
    border: 1px solid #d9efde;
}

.essay-status {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 18px;
}

.status {
    display: inline-block;
    padding: 7px 11px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 700;
}

.status.pending {
    background: #fff4dd;
    color: #986000;
}

.status.complete {
    background: #e4f8ef;
    color: #087f65;
}

.status.source {
    background: #edf3ff;
    color: #2563eb;
}

.ai-feedback {
    background: #f3efff;
    border: 1px solid #e4dafa;
    padding: 18px;
    border-radius: 13px;
    margin-top: 18px;
}

.ai-feedback strong {
    color: #6339b6;
    font-size: 13px;
}

.ai-feedback p {
    color: #51486c;
    font-size: 13px;
    line-height: 1.7;
    margin-bottom: 0;
    white-space: pre-wrap;
}

.grading-form {
    margin-top: 23px;
    padding-top: 22px;
    border-top: 1px solid #e8edf6;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    font-weight: 700;
    font-size: 13px;
    margin-bottom: 9px;
}

.form-group input,
.form-group textarea {
    display: block;
    width: 100%;
    box-sizing: border-box;
    padding: 13px 14px;
    border: 1px solid #d6dfef;
    border-radius: 11px;
    background: #fff;
    color: #17233e;
    font: inherit;
    font-size: 14px;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #2563eb;
    outline: 3px solid #dbeafe;
}

.form-group small {
    color: #71809a;
    font-size: 11px;
    display: block;
    margin-top: 8px;
}

.save-button {
    background: #2563eb;
    color: white;
    border: 0;
    border-radius: 12px;
    padding: 13px 22px;
    font-size: 13px;
    font-weight: 800;
    cursor: pointer;
}

.save-button:hover {
    background: #1d4ed8;
}

.review-success,
.review-error {
    padding: 15px 18px;
    margin-bottom: 22px;
    border-radius: 12px;
    font-size: 13px;
}

.review-success {
    background: #e4f8ef;
    color: #087f65;
}

.review-error {
    background: #fff0f0;
    color: #a91d30;
}

.empty-state {
    text-align: center;
    background: white;
    border-radius: 18px;
    padding: 40px 20px;
    color: #71809a;
}

.empty-state h3 {
    color: #17233e;
}

@media(max-width: 600px) {
    .review-page {
        padding: 22px 14px 45px;
    }

    .review-summary {
        grid-template-columns: 1fr;
        padding: 18px;
    }

    .essay-card {
        padding: 18px;
    }
}
</style>
@endsection
