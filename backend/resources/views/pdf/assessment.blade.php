<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>PDF Asesmen - {{ $assessment->nama ?? 'EquiTask AI' }}</title>

    <style>
        @page {
            margin: 22mm 18mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #222;
        }

        h1 {
            text-align: center;
            font-size: 16pt;
            margin: 0 0 5px;
        }

        .subtitle {
            text-align: center;
            font-size: 10pt;
            color: #555;
            margin-bottom: 22px;
        }

        .assessment-info {
            margin-bottom: 16px;
        }

        .assessment-info p {
            margin: 5px 0;
        }

        hr {
            border: 0;
            border-top: 1px solid #777;
            margin: 15px 0 22px;
        }

        .question {
            margin-bottom: 24px;
        }

        .metadata {
            font-size: 9pt;
            color: #666;
            margin-bottom: 7px;
        }

        .question-title {
            font-weight: bold;
            line-height: 1.7;
            margin-bottom: 10px;
            white-space: pre-wrap;
        }

        .description {
            font-style: italic;
            font-size: 10pt;
            color: #475569;
            margin: 8px 0 12px;
        }

        .question-image-wrapper {
            text-align: center;
            margin: 12px 0 18px;
            page-break-inside: avoid;
        }

        .question-image {
            display: block;
            max-width: 100%;
            max-height: 260px;
            width: auto;
            height: auto;
            margin: 0 auto;
        }

        .image-caption {
            text-align: center;
            font-size: 9pt;
            line-height: 1.5;
            color: #475569;
            margin-top: 7px;
        }

        .option {
            margin: 7px 0 7px 15px;
            line-height: 1.7;
        }

        .answer-space {
            height: 26px;
            border-bottom: 1px solid #aaa;
            margin-top: 14px;
        }

        /* Tampilan khusus Easy Read / Adaptif */
        body.easy-read {
            font-size: 13pt;
            line-height: 1.85;
        }

        .easy-read .question-title {
            font-size: 13pt;
            line-height: 1.85;
        }

        .easy-read .option {
            font-size: 12pt;
            line-height: 1.9;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .easy-read .question-image {
            max-height: 300px;
        }
    </style>
</head>

<body class="{{ ($format ?? 'regular') === 'easy_read' ? 'easy-read' : '' }}">

    <h1>ASESMEN PEMBELAJARAN</h1>

    <div class="subtitle">
        {{ ($format ?? 'regular') === 'easy_read'
            ? 'Format Easy Read / Adaptif'
            : 'Format Reguler' }}
    </div>

    <div class="assessment-info">
        <p>
            <strong>Judul:</strong>
            {{ $assessment->nama
                ?? $assessment->title
                ?? $assessment->judul
                ?? 'Asesmen' }}
        </p>

        @if(!empty($assessment->mapel))
            <p>
                <strong>Mata Pelajaran:</strong>
                {{ $assessment->mapel }}
            </p>
        @endif
    </div>

    <hr>

    @foreach($questions as $index => $soal)
        @php
            /*
             * Normalisasi soal dari AssessmentPdfService.
             */
            $soal = is_array($soal)
                ? $soal
                : (array) $soal;

            $questionId = $soal['id']
                ?? $soal['question_id']
                ?? null;

            $sourceQuestionId = $soal['id_soal_asal']
                ?? $soal['original_question_id']
                ?? $soal['source_question_id']
                ?? null;

            /*
             * Prioritaskan indeks soal hasil pemetaan controller.
             * Jika tidak tersedia, gunakan ID soal.
             */
            $gambarSoal = [];

            if (($format ?? 'regular') === 'easy_read') {
                $gambarSoal = $gambarPerSoal['index_' . $index]
                    ?? $gambarPerSoal[$questionId ?? '']
                    ?? $gambarPerSoal[$sourceQuestionId ?? '']
                    ?? [];
            }

            $gambarSoal = is_array($gambarSoal)
                ? $gambarSoal
                : [];

            /*
             * Normalisasi opsi pilihan ganda.
             */
            $opsiSoal = $soal['opsi'] ?? [];

            if (is_string($opsiSoal)) {
                $opsiSoal = json_decode($opsiSoal, true) ?: [];
            }

            if (!is_array($opsiSoal)) {
                $opsiSoal = [];
            }
        @endphp

        <div class="question">

            {{-- LEVEL BLOOM --}}
            @if(!empty($soal['level_bloom']))
                <div class="metadata">
                    {{ $soal['level_bloom'] }}
                </div>
            @endif

            {{-- PERTANYAAN --}}
            <div class="question-title">
                {{ $index + 1 }}.
                {{ $soal['pertanyaan']
                    ?? $soal['question_text']
                    ?? 'Pertanyaan tidak tersedia.' }}
            </div>

            {{-- DESKRIPSI GAMBAR --}}
            @if(!empty($soal['deskripsi_gambar']))
                <div class="description">
                    {{ $soal['deskripsi_gambar'] }}
                </div>
            @endif

            {{-- GAMBAR SOAL ADAPTIF DARI STORAGE LARAVEL --}}
            @if(($format ?? 'regular') === 'easy_read')
                @foreach($gambarSoal as $gambar)
                    @php
                        $gambar = is_array($gambar)
                            ? $gambar
                            : (array) $gambar;

                        $imageSrc = $gambar['url'] ?? '';

                        $imageAlt = $gambar['alt']
                            ?? $gambar['teks_alternatif']
                            ?? 'Ilustrasi pendukung soal';

                        /*
                         * Controller mengirim data URI
                         * dari gambar lokal Laravel.
                         */
                        $validImage = is_string($imageSrc)
                            && (bool) preg_match(
                                '/^data:image\/(?:png|jpeg|gif);base64,/',
                                $imageSrc
                            );
                    @endphp

                    @if($validImage)
                        <div class="question-image-wrapper">
                            <img
                                src="{{ $imageSrc }}"
                                alt="{{ $imageAlt }}"
                                class="question-image"
                            >

                            @if(!empty($imageAlt))
                                <div class="image-caption">
                                    {{ $imageAlt }}
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            @endif

            {{-- PILIHAN GANDA ATAU ESAI --}}
            @if(!empty($opsiSoal))
                @foreach($opsiSoal as $key => $opsi)
                    @php
                        $opsi = is_array($opsi)
                            ? $opsi
                            : ['teks' => (string) $opsi];

                        $label = $opsi['label']
                            ?? (
                                is_numeric($key)
                                    ? chr(65 + (int) $key)
                                    : (string) $key
                            );

                        $teksOpsi = $opsi['teks']
                            ?? $opsi['text']
                            ?? '';
                    @endphp

                    <div class="option">
                        <strong>{{ $label }}.</strong>
                        {{ $teksOpsi }}
                    </div>
                @endforeach
            @else
                {{-- TEMPAT MENULIS JAWABAN ESAI --}}
                <div class="answer-space"></div>
                <div class="answer-space"></div>
                <div class="answer-space"></div>
            @endif

        </div>
    @endforeach

</body>
</html>
