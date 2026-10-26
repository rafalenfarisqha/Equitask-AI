<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AIGeneration;
use App\Models\AIAgentResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankSoalController extends Controller
{
    /**
     * Menampilkan daftar asesmen publik.
     */
    public function index(Request $request)
    {
        $query = Assessment::with('teacher')
            ->where('visibility', 'public')
            ->where('status', 'Published');

        if ($request->filled('mapel')) {
            $query->where('mapel', $request->input('mapel'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('mapel', 'like', '%' . $search . '%');
            });
        }

        $publicAssessments = $query->latest()->get();

        return view(
            'guru.bank-soal',
            compact('publicAssessments')
        );
    }

    /**
     * Menampilkan detail asesmen publik,
     * soal Reguler, Easy Read, dan ilustrasinya.
     */
    public function detail(int $id)
    {
        $assessment = Assessment::with('teacher')
            ->where('visibility', 'public')
            ->where('status', 'Published')
            ->findOrFail($id);

        $questionsRegular = [];
        $questionsEasyRead = [];
        $regularUnavailable = false;
        $easyReadUnavailable = false;
        $imagesByQuestion = [];

        /*
         * Mengubah JSON menjadi array secara aman.
         */
        $decode = static function ($value): array {
            if (is_string($value)) {
                $value = json_decode($value, true);
            }

            return is_array($value) ? $value : [];
        };

        /*
         * Ambil hasil generate terbaru yang berhasil.
         */
        $generation = AIGeneration::where(
            'assessment_id',
            $assessment->id
        )
            ->where('teacher_id', $assessment->user_id)
            ->where('status', 'success')
            ->latest('id')
            ->first();

        if ($generation) {
            $result = AIAgentResult::where(
                'generation_id',
                $generation->id
            )
                ->where('status', 'success')
                ->latest('id')
                ->first();

            if ($result) {
                $stored = $decode($result->output_data);

                $outputs = $decode(
                    $stored['outputs'] ?? []
                );

                /*
                 * Ambil soal Reguler dan Easy Read.
                 * Prioritaskan hasil revisi jika tersedia.
                 */
                $regular = $decode(
                    $outputs['asesmen_reguler_revisi']
                    ?? $outputs['asesmen_reguler']
                    ?? []
                );

                $easyRead = $decode(
                    $outputs['asesmen_inklusif_revisi']
                    ?? $outputs['asesmen_inklusif']
                    ?? []
                );

                $questionsRegular = $regular['soal'] ?? [];

                $questionsEasyRead =
                    $easyRead['soal_inklusif']
                    ?? $easyRead['soal_adaptif']
                    ?? $easyRead['soal']
                    ?? [];

                /*
                 * Ambil gambar asesmen.
                 * Hasil revisi diprioritaskan.
                 */
                $rawImages = $decode(
                    $outputs['gambar_asesmen_revisi']
                    ?? $outputs['gambar_asesmen']
                    ?? []
                );

                /*
                 * Beberapa output AI menyimpan gambar
                 * di dalam properti output atau
                 * gambar_per_soal.
                 */
                $queue = [$rawImages];

                while (!empty($queue)) {
                    $item = array_shift($queue);

                    if (!is_array($item)) {
                        continue;
                    }

                    if (
                        isset($item['output'])
                        && is_array($item['output'])
                    ) {
                        $queue[] = $item['output'];
                        continue;
                    }

                    if (
                        isset($item['gambar_per_soal'])
                        && is_array($item['gambar_per_soal'])
                    ) {
                        $queue[] = $item['gambar_per_soal'];
                        continue;
                    }

                    /*
                     * Jelajahi daftar gambar bertingkat.
                     */
                    if (array_is_list($item)) {
                        foreach ($item as $nested) {
                            if (is_array($nested)) {
                                $queue[] = $nested;
                            }
                        }

                        continue;
                    }

                    $url = $item['url']
                        ?? $item['gambar_url']
                        ?? $item['download_url']
                        ?? $item['preview_url']
                        ?? null;

                    /*
                     * Pastikan URL gambar valid
                     * dan menggunakan HTTP atau HTTPS.
                     */
                    if (
                        !is_string($url)
                        || !filter_var(
                            $url,
                            FILTER_VALIDATE_URL
                        )
                    ) {
                        continue;
                    }

                    $scheme = strtolower(
                        parse_url($url, PHP_URL_SCHEME) ?? ''
                    );

                    if (!in_array(
                        $scheme,
                        ['http', 'https'],
                        true
                    )) {
                        continue;
                    }

                    $questionId = trim(
                        (string) (
                            $item['id_soal_asal'] ?? ''
                        )
                    );

                    /*
                     * Hubungkan ilustrasi dengan ID soal.
                     * Jangan mencocokkan berdasarkan urutan.
                     */
                    if ($questionId !== '') {
                        $imagesByQuestion[$questionId][] = [
                            'id_soal_asal' => $questionId,
                            'id_visual' => $item['id_visual'] ?? null,
                            'url' => $url,
                            'filename' => $item['filename']
                                ?? 'Ilustrasi asesmen',
                            'alt' => $item['teks_alternatif']
                                ?? $item['filename']
                                ?? 'Ilustrasi soal ' . $questionId,
                            'type' => $item['type'] ?? 'image',
                        ];
                    }
                }
            }
        }

        /*
         * Revisi guru mengesampingkan soal hasil generate
         * jika data revisi tersedia.
         */
        $review = DB::table('assessment_reviews')
            ->where('assessment_id', $assessment->id)
            ->first();

        if ($review) {
            $reviewRegular = $decode($review->reguler);
            $reviewEasyRead = $decode($review->adaptif);

            if (!empty($reviewRegular)) {
                $questionsRegular =
                    $reviewRegular['soal'] ?? $questionsRegular;
            }

            if (!empty($reviewEasyRead)) {
                $questionsEasyRead =
                    $reviewEasyRead['soal_inklusif']
                    ?? $reviewEasyRead['soal_adaptif']
                    ?? $reviewEasyRead['soal']
                    ?? $questionsEasyRead;
            }
        }

        /*
         * Pastikan data soal berbentuk array.
         */
        $questionsRegular = is_array($questionsRegular)
            ? array_values($questionsRegular)
            : [];

        $questionsEasyRead = is_array($questionsEasyRead)
            ? array_values($questionsEasyRead)
            : [];

        $regularUnavailable = empty($questionsRegular);
        $easyReadUnavailable = empty($questionsEasyRead);

        return view('guru.bank-soal-detail', [
            'assessment' => $assessment,
            'questionsRegular' => $questionsRegular,
            'questionsEasyRead' => $questionsEasyRead,
            'regularUnavailable' => $regularUnavailable,
            'easyReadUnavailable' => $easyReadUnavailable,
            'imagesByQuestion' => $imagesByQuestion,
        ]);
    }
}
