
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>PDF Asesmen</title>

    <style>
        @page {
            margin: 25mm 20mm;
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
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 25px;
            font-size: 10pt;
            color: #555;
        }

        .question {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .question-title {
            font-weight: bold;
            margin-bottom: 8px;
        }

        .metadata {
            font-size: 9pt;
            color: #666;
            margin-bottom: 6px;
        }

        .option {
            margin: 5px 0 5px 15px;
        }

        .description {
            font-style: italic;
            margin-top: 8px;
        }

        .answer-space {
            margin-top: 15px;
            border-bottom: 1px solid #999;
            height: 25px;
        }
    </style>
</head>

<body>

    <h1>ASESMEN PEMBELAJARAN</h1>

    <div class="subtitle">
        {{ $format === 'easy_read' ? 'Format Easy Read' : 'Format Reguler' }}
    </div>

    <p>
        <strong>Judul:</strong>
        {{ $assessment->nama ?? $assessment->title ?? $assessment->judul ?? 'Asesmen' }}
    </p>

    <hr>

    @foreach($questions as $index => $soal)
        <div class="question">

            <div class="metadata">
                {{ $soal['level_bloom'] ?? '' }}
            </div>

            <div class="question-title">
                {{ $index + 1 }}.
                {{ $soal['pertanyaan'] ?? 'Pertanyaan tidak tersedia.' }}
            </div>

            @if(!empty($soal['deskripsi_gambar']))
                <div class="description">
                    {{ $soal['deskripsi_gambar'] }}
                </div>
            @endif

            @if(!empty($soal['opsi']) && is_array($soal['opsi']))
                @foreach($soal['opsi'] as $opsi)
                    <div class="option">
                        {{ $opsi['label'] ?? '-' }}.
                        {{ $opsi['teks'] ?? '' }}
                    </div>
                @endforeach
            @else
                <div class="answer-space"></div>
                <div class="answer-space"></div>
            @endif

        </div>
    @endforeach

</body>
</html>
