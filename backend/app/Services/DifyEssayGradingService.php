<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class DifyEssayGradingService
{
    /**
     * Kirim esai ke workflow Dify Analytics untuk memperoleh nilai dan alasan.
     * Mengembalikan null jika request, workflow, atau validasi hasil gagal.
     */
    public function nilai(array $esai): ?array
    {
        if ($esai === []) {
            return [];
        }

        $apiKey = (string) config('services.dify_analytics.api_key', '');
        $baseUrl = rtrim(
            (string) config('services.dify_analytics.base_url', 'https://api.dify.ai/v1'),
            '/'
        );
        $timeout = max(30, (int) config('services.dify_analytics.timeout', 90));

        if ($apiKey === '') {
            Log::warning('API key Dify Analytics belum tersedia.');
            return null;
        }

        try {
            $laporan = [
                'jenis' => 'siswa',
                'hasil' => [
                    'mapel' => '',
                    'score' => null,
                    'benar' => 0,
                    'salah' => 0,
                    'belum_dinilai' => count($esai),
                    'jumlah_jawaban' => count($esai),
                    'penilaian_lengkap' => false,
                    'materi' => [],
                ],
            ];

            $request = Http::withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->connectTimeout(15)
                ->timeout($timeout);

            $caBundle = base_path('certs/cacert.pem');
            if (is_file($caBundle)) {
                $request = $request->withOptions(['verify' => $caBundle]);
            }

            $response = $request->post($baseUrl . '/workflows/run', [
                'inputs' => [
                    'jenis_analisis' => 'siswa',
                    'laporan_json' => json_encode(
                        $laporan,
                        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
                    ),
                    'esai_json' => json_encode(
                        array_values($esai),
                        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
                    ),
                ],
                'response_mode' => 'blocking',
                'user' => 'equitask-essay-grading',
            ]);

            if (!$response->successful()) {
                Log::warning('Dify penilaian esai HTTP error.', [
                    'status' => $response->status(),
                ]);
                return null;
            }

            $payload = $response->json();
            if (!is_array($payload) || data_get($payload, 'data.status') !== 'succeeded') {
                Log::warning('Workflow penilaian esai tidak sukses.');
                return null;
            }

            $hasil = data_get($payload, 'data.outputs.penilaian_esai_json');
            if (is_string($hasil)) {
                $hasil = json_decode(trim($hasil), true, 512, JSON_THROW_ON_ERROR);
            }
            if (!is_array($hasil)) {
                return null;
            }

            $soalAsli = [];
            foreach ($esai as $item) {
                if (!is_array($item) || !isset($item['question_id'], $item['skor_maksimum'])) {
                    return null;
                }
                $id = (string) $item['question_id'];
                if ($id === '' || array_key_exists($id, $soalAsli)) {
                    return null;
                }
                $maksimum = $item['skor_maksimum'];
                if (!is_numeric($maksimum) || !is_finite((float) $maksimum) || (float) $maksimum < 0) {
                    return null;
                }
                $soalAsli[$id] = (float) $maksimum;
            }

            $penilaian = [];
            foreach ($hasil as $item) {
                if (!is_array($item) || !isset($item['question_id'])) {
                    return null;
                }
                $id = (string) $item['question_id'];
                if (!array_key_exists($id, $soalAsli) || array_key_exists($id, $penilaian)) {
                    return null;
                }
                $alasan = $item['alasan'] ?? '';
                $perluTinjau = $item['perlu_tinjau'] ?? false;
                if (!is_string($alasan) || !is_bool($perluTinjau)) {
                    return null;
                }
                $skorRaw = $item['skor'] ?? null;
                if ($perluTinjau || $skorRaw === null) {
                    $penilaian[$id] = [
                        'question_id' => (int) $id,
                        'skor' => null,
                        'alasan' => trim($alasan),
                        'perlu_tinjau' => true,
                    ];
                    continue;
                }
                if (!is_numeric($skorRaw)) {
                    return null;
                }
                $skor = (float) $skorRaw;
                $maksimum = $soalAsli[$id];
                if (!is_finite($skor) || $skor < 0 || $skor > $maksimum) {
                    Log::warning('Skor esai Dify di luar batas.');
                    return null;
                }
                $penilaian[$id] = [
                    'question_id' => (int) $id,
                    'skor' => round($skor, 2),
                    'alasan' => trim($alasan),
                    'perlu_tinjau' => false,
                ];
            }

            if (count($penilaian) !== count($soalAsli)) {
                Log::warning('Jumlah hasil penilaian Dify tidak cocok.');
                return null;
            }
            return array_values($penilaian);
        } catch (Throwable $e) {
            // Tidak mencatat API key maupun jawaban siswa ke log.
            Log::warning('Penilaian esai otomatis gagal.', [
                'exception' => get_class($e),
            ]);
            return null;
        }
    }

    /**
     * Nilai esai belum dinilai dalam satu attempt dan perbarui nilai total.
     * Aman dipanggil dari submit() dan result().
     */
    public function gradeAttempt(int $attemptId): void
    {
        $attempt = DB::table('student_assessments')
            ->where('id', $attemptId)
            ->whereIn('status', ['submitted', 'completed'])
            ->first();

        if (!$attempt) {
            return;
        }

        $rows = DB::table('student_answers as ans')
            ->join('questions as q', 'q.id', '=', 'ans.question_id')
            ->join('assessment_questions as aq', function ($join) use ($attempt) {
                $join->on('aq.question_id', '=', 'q.id')
                    ->where('aq.assessment_id', '=', $attempt->assessment_id);
            })
            ->where('ans.student_assessment_id', $attemptId)
            ->whereNull('ans.is_correct')
            ->select(
                'q.id',
                'q.question_text',
                'q.question_type',
                'q.options',
                'q.correct_answer',
                'aq.points',
                'ans.answer'
            )
            ->get();

        $esai = [];
        $jawabanKosong = [];

        foreach ($rows as $row) {
            $type = strtolower(trim((string) ($row->question_type ?? '')));
            $options = $row->options;
            if (is_string($options)) {
                $options = json_decode($options, true);
            }
            $options = is_array($options) ? $options : [];

            // Sama dengan aturan isEssayQuestion() di AssessmentController.
            $isEssay = str_contains($type, 'esai')
                || str_contains($type, 'essay')
                || str_contains($type, 'uraian')
                || $options === [];

            if (!$isEssay) {
                continue;
            }

            $jawaban = trim((string) ($row->answer ?? ''));
            if ($jawaban === '') {
                $jawabanKosong[] = (int) $row->id;
                continue;
            }

            $esai[] = [
                'question_id' => (int) $row->id,
                'pertanyaan' => (string) $row->question_text,
                'jawaban_siswa' => $jawaban,
                'kunci_jawaban' => (string) ($row->correct_answer ?? ''),
                'skor_maksimum' => max(0.0, (float) $row->points),
            ];
        }

        if ($esai === [] && $jawabanKosong === []) {
            return;
        }

        // Dify dipanggil di luar transaksi DB agar tidak mengunci row selama request HTTP.
        $hasil = $esai !== [] ? $this->nilai($esai) : [];
        $skorValid = [];
        if (is_array($hasil)) {
            foreach ($hasil as $item) {
                if (!is_array($item)
                    || ($item['perlu_tinjau'] ?? true) !== false
                    || !isset($item['question_id'], $item['skor'])
                    || !is_numeric($item['skor'])) {
                    continue;
                }
                $skorValid[(int) $item['question_id']] = (float) $item['skor'];
            }
        }

        DB::transaction(function () use ($attemptId, $attempt, $jawabanKosong, $esai, $skorValid) {
            foreach ($jawabanKosong as $questionId) {
                DB::table('student_answers')
                    ->where('student_assessment_id', $attemptId)
                    ->where('question_id', $questionId)
                    ->whereNull('is_correct')
                    ->update([
                        'points_obtained' => 0,
                        'is_correct' => false,
                    ]);
            }

            foreach ($esai as $item) {
                $questionId = $item['question_id'];
                if (!array_key_exists($questionId, $skorValid)) {
                    continue;
                }
                $skor = $skorValid[$questionId];
                $maksimum = (float) $item['skor_maksimum'];
                if (!is_finite($skor) || $skor < 0 || $skor > $maksimum) {
                    continue;
                }
                DB::table('student_answers')
                    ->where('student_assessment_id', $attemptId)
                    ->where('question_id', $questionId)
                    ->whereNull('is_correct')
                    ->update([
                        'points_obtained' => round($skor, 2),
                        // false juga untuk nilai parsial; NULL berarti belum dinilai.
                        'is_correct' => $skor >= $maksimum,
                    ]);
            }

            $earned = (float) DB::table('student_answers')
                ->where('student_assessment_id', $attemptId)
                ->sum('points_obtained');

            $total = (float) DB::table('assessment_questions')
                ->where('assessment_id', $attempt->assessment_id)
                ->sum('points');

            $score = $total > 0 ? round($earned / $total * 100, 2) : 0;
            DB::table('student_assessments')
                ->where('id', $attemptId)
                ->update([
                    'score' => $score,
                    'updated_at' => now(),
                ]);
        });
    }
}
