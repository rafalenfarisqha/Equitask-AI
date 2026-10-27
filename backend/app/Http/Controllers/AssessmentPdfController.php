<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AIGeneration;
use App\Models\AIAgentResult;
use App\Services\AssessmentPdfService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssessmentPdfController extends Controller
{
    public function download(
        Request $request,
        Assessment $assessment,
        AssessmentPdfService $pdfService
    ) {
        abort_unless(
            $request->user()
            && (int) $assessment->user_id === (int) $request->user()->id,
            403,
            'Anda tidak memiliki akses ke asesmen ini.'
        );

        return $this->makePdf(
            $assessment,
            $this->getFormat($request),
            $pdfService
        );
    }

    public function downloadPublic(
        Request $request,
        int $id,
        AssessmentPdfService $pdfService
    ) {
        $assessment = Assessment::query()
            ->where('visibility', 'public')
            ->where('status', 'Published')
            ->findOrFail($id);

        return $this->makePdf(
            $assessment,
            $this->getFormat($request),
            $pdfService
        );
    }

    private function getFormat(Request $request): string
    {
        $format = $request->query('format', 'regular');

        abort_unless(
            is_string($format)
            && in_array($format, ['regular', 'easy_read'], true),
            404,
            'Format PDF tidak tersedia.'
        );

        return $format;
    }

    private function makePdf(
        Assessment $assessment,
        string $format,
        AssessmentPdfService $pdfService
    ) {
        $questions = $pdfService->getQuestions(
            $assessment,
            $format
        );

        $gambarPerSoal = $format === 'easy_read'
            ? $this->getGambarAdaptif($assessment, $questions)
            : [];

        $pdf = Pdf::loadView('pdf.assessment', [
            'assessment' => $assessment,
            'questions' => $questions,
            'format' => $format,
            'gambarPerSoal' => $gambarPerSoal,
        ])->setPaper('a4', 'portrait');

        return $pdf->download(
            'asesmen-' . $assessment->id . '-' . $format . '.pdf'
        );
    }

    private function decodeJson(mixed $value): array
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        return is_array($value) ? $value : [];
    }

    /**
     * Mengambil output Dify yang tersimpan.
     * Tidak memanggil workflow Dify ulang.
     */
    private function getDifyOutputs(
        Assessment $assessment
    ): array {
        $generation = AIGeneration::query()
            ->where('assessment_id', $assessment->id)
            ->where('status', 'success')
            ->latest('id')
            ->first();

        if (!$generation) {
            return [];
        }

        $result = AIAgentResult::query()
            ->where('generation_id', $generation->id)
            ->where('status', 'success')
            ->latest('id')
            ->first();

        if (!$result) {
            return [];
        }

        $stored = $this->decodeJson(
            $result->output_data
        );

        return $this->decodeJson(
            $stored['outputs']
            ?? data_get($stored, 'data.outputs')
            ?? []
        );
    }

    /**
     * Mengubah path pada disk public Laravel
     * menjadi data URI untuk DomPDF.
     *
     * Contoh path yang diterima:
     * assessment-images/soal-1.png
     * storage/assessment-images/soal-1.png
     * /storage/assessment-images/soal-1.png
     *
     * URL Dify eksternal tidak digunakan.
     */
    private function localImageToDataUri(
        mixed $value
    ): ?string {
        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        $path = trim($value);

        /*
         * Jika berupa URL aplikasi Laravel,
         * ambil path /storage/ saja.
         */
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $parts = parse_url($path);

            if (!is_array($parts)) {
                return null;
            }

            $scheme = strtolower(
                (string) ($parts['scheme'] ?? '')
            );

            if (!in_array($scheme, ['http', 'https'], true)) {
                return null;
            }

            $appHost = parse_url(
                (string) config('app.url'),
                PHP_URL_HOST
            );

            $imageHost = $parts['host'] ?? null;

            if (
                !$appHost
                || !$imageHost
                || strcasecmp($appHost, $imageHost) !== 0
            ) {
                return null;
            }

            $path = (string) ($parts['path'] ?? '');
        }

        $path = rawurldecode($path);
        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/app/public/')) {
            $path = substr(
                $path,
                strlen('storage/app/public/')
            );
        } elseif (str_starts_with($path, 'public/storage/')) {
            $path = substr(
                $path,
                strlen('public/storage/')
            );
        } elseif (str_starts_with($path, 'storage/')) {
            $path = substr(
                $path,
                strlen('storage/')
            );
        }

        /*
         * Batasi pembacaan ke disk public.
         * Tidak boleh mengakses direktori lain.
         */
        if (
            $path === ''
            || str_contains($path, '..')
            || str_contains($path, "\0")
            || str_contains($path, '?')
            || str_contains($path, '#')
            || str_starts_with($path, '/')
        ) {
            return null;
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($path)) {
            return null;
        }

        $absolutePath = $disk->path($path);
        $publicRoot = realpath(storage_path('app/public'));
        $realFile = realpath($absolutePath);

        if (
            $publicRoot === false
            || $realFile === false
            || !str_starts_with(
                $realFile,
                $publicRoot . DIRECTORY_SEPARATOR
            )
            || !is_file($realFile)
        ) {
            return null;
        }

        /*
         * Hindari file terlalu besar saat
         * dimasukkan sebagai base64 ke PDF.
         */
        $size = filesize($realFile);

        if (
            $size === false
            || $size <= 0
            || $size > 5 * 1024 * 1024
        ) {
            return null;
        }

        $mime = mime_content_type($realFile);

        if (!in_array($mime, [
            'image/png',
            'image/jpeg',
            'image/gif',
        ], true)) {
            return null;
        }

        $content = file_get_contents($realFile);

        if ($content === false) {
            return null;
        }

        return 'data:' . $mime . ';base64,'
            . base64_encode($content);
    }

    /**
     * Ambil path gambar dari struktur yang
     * digunakan saat menyimpan gambar Dify
     * ke Laravel.
     */
    private function getLocalImagePath(
        array $image
    ): ?string {
        $keys = [
            'local_path',
            'storage_path',
            'image_path',
            'gambar_path',
            'path_lokal',
            'file_path',
            'url_lokal',
            'local_url',
            'path',
            'url',
            'gambar_url',
        ];

        foreach ($keys as $key) {
            $value = $image[$key] ?? null;

            if (!is_string($value) || trim($value) === '') {
                continue;
            }

            /*
             * Hanya gunakan path yang benar-benar
             * dapat dibaca dari disk public.
             */
            if ($this->localImageToDataUri($value) !== null) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Baca gambar pada JSON bertingkat.
     *
     * id_soal_asal pada parent diwariskan
     * kepada gambar yang berada di dalamnya.
     */
    private function collectImages(array $raw): array
    {
        $queue = [
            [
                'data' => $raw,
                'source_id' => '',
            ],
        ];

        $images = [];
        $seen = [];

        while ($queue !== []) {
            $entry = array_shift($queue);
            $item = $entry['data'];

            if (!is_array($item)) {
                continue;
            }

            $sourceId = trim((string) (
                $item['id_soal_asal']
                ?? $item['id_soal']
                ?? $entry['source_id']
            ));

            $path = $this->getLocalImagePath($item);

            if ($path !== null && $sourceId !== '') {
                $dataUri = $this->localImageToDataUri($path);

                if ($dataUri !== null) {
                    $key = $sourceId . '|' . $path;

                    if (!isset($seen[$key])) {
                        $seen[$key] = true;

                        $images[] = [
                            'id_soal_asal' => $sourceId,
                            'url' => $dataUri,
                            'alt' => (string) (
                                $item['teks_alternatif']
                                ?? $item['alt']
                                ?? 'Ilustrasi pendukung soal'
                            ),
                        ];
                    }
                }
            }

            foreach ($item as $child) {
                if (is_array($child)) {
                    $queue[] = [
                        'data' => $child,
                        'source_id' => $sourceId,
                    ];
                }
            }
        }

        return $images;
    }

    /**
     * Data soal Adaptif sumber Dify untuk
     * membantu pencocokan berdasarkan teks.
     */
    private function getOriginalAdaptiveQuestions(
        array $outputs
    ): array {
        $adaptive = $this->decodeJson(
            $outputs['asesmen_inklusif_revisi']
            ?? $outputs['asesmen_inklusif']
            ?? []
        );

        $questions = $adaptive['soal_inklusif']
            ?? $adaptive['soal_adaptif']
            ?? $adaptive['soal']
            ?? [];

        return is_array($questions)
            ? array_values($questions)
            : [];
    }

    private function normalizeText(mixed $text): string
    {
        $text = trim((string) $text);

        $text = preg_replace(
            '/\s+/u',
            ' ',
            $text
        ) ?? $text;

        return mb_strtolower($text);
    }

    /**
     * Memasangkan gambar tersimpan ke soal
     * pada PDF Easy Read / Adaptif.
     */
    private function getGambarAdaptif(
        Assessment $assessment,
        iterable $questions
    ): array {
        $outputs = $this->getDifyOutputs($assessment);

        if ($outputs === []) {
            return [];
        }

        /*
         * Baca gambar dari beberapa nama field.
         * Gambar harus sudah memiliki path Laravel.
         */
        $imageSources = [
            $outputs['gambar_asesmen_revisi'] ?? [],
            $outputs['gambar_asesmen'] ?? [],
            $outputs['gambar_per_soal'] ?? [],
        ];

        $images = [];

        foreach ($imageSources as $source) {
            $images = array_merge(
                $images,
                $this->collectImages(
                    $this->decodeJson($source)
                )
            );
        }

        if ($images === []) {
            return [];
        }

        $imagesBySourceId = [];
        $seenBySourceId = [];

        foreach ($images as $image) {
            $sourceId = trim(
                (string) $image['id_soal_asal']
            );

            if ($sourceId === '') {
                continue;
            }

            $uniqueImage = hash('sha256', $image['url']);

            if (isset($seenBySourceId[$sourceId][$uniqueImage])) {
                continue;
            }

            $seenBySourceId[$sourceId][$uniqueImage] = true;

            $imagesBySourceId[$sourceId][] = [
                'url' => $image['url'],
                'alt' => $image['alt'],
            ];
        }

        /*
         * Pemetaan teks soal sumber Dify ke ID.
         * Digunakan bila soal PDF tidak memiliki ID.
         */
        $sourceIdsByText = [];

        foreach (
            $this->getOriginalAdaptiveQuestions($outputs)
            as $original
        ) {
            if (!is_array($original)) {
                continue;
            }

            $text = $this->normalizeText(
                $original['pertanyaan']
                ?? $original['question_text']
                ?? ''
            );

            $sourceId = trim((string) (
                $original['id_soal_asal']
                ?? $original['id_soal']
                ?? $original['id']
                ?? ''
            ));

            if ($text !== '' && $sourceId !== '') {
                $sourceIdsByText[$text][] = $sourceId;
            }
        }

        $gambarPerSoal = [];

        foreach ($questions as $index => $question) {
            $soal = is_array($question)
                ? $question
                : (array) $question;

            $candidateIds = [
                $soal['id_soal_asal'] ?? null,
                $soal['id_soal'] ?? null,
                $soal['original_question_id'] ?? null,
                $soal['source_question_id'] ?? null,
                $soal['question_code'] ?? null,
                $soal['question_id'] ?? null,
                $soal['id'] ?? null,
            ];

            $matchedImages = [];

            foreach ($candidateIds as $candidateId) {
                $sourceId = trim(
                    (string) ($candidateId ?? '')
                );

                if (
                    $sourceId !== ''
                    && isset($imagesBySourceId[$sourceId])
                ) {
                    $matchedImages = $imagesBySourceId[$sourceId];
                    break;
                }
            }

            /*
             * Fallback: teks pertanyaan yang identik.
             * Tidak mencocokkan sembarang soal.
             */
            if ($matchedImages === []) {
                $text = $this->normalizeText(
                    $soal['pertanyaan']
                    ?? $soal['question_text']
                    ?? ''
                );

                $sourceIds = array_unique(
                    $sourceIdsByText[$text] ?? []
                );

                if (count($sourceIds) === 1) {
                    $sourceId = reset($sourceIds);

                    $matchedImages =
                        $imagesBySourceId[$sourceId] ?? [];
                }
            }

            if ($matchedImages === []) {
                continue;
            }

            /*
             * Key index sesuai urutan soal PDF.
             */
            $gambarPerSoal['index_' . $index] =
                $matchedImages;

            /*
             * Key ID untuk kompatibilitas
             * dengan template PDF sebelumnya.
             */
            foreach ($candidateIds as $candidateId) {
                $key = trim(
                    (string) ($candidateId ?? '')
                );

                if ($key !== '') {
                    $gambarPerSoal[$key] = $matchedImages;
                }
            }
        }

        return $gambarPerSoal;
    }
}
