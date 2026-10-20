<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AIGeneration;
use App\Models\AIAgentResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssessmentReviewController extends Controller
{
    private function findAssessment(int $id): Assessment
    {
        return Assessment::where('user_id', Auth::id())
            ->findOrFail($id);
    }

    private function decodeJson($value): array
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        return is_array($value) ? $value : [];
    }

    /**
     * Membaca hasil Dify dari PostgreSQL.
     * Tidak menjalankan workflow Dify kembali.
     */
    private function getDifyOutputs(Assessment $assessment): array
    {
        $generation = AIGeneration::where(
            'assessment_id',
            $assessment->id
        )
            ->where('teacher_id', Auth::id())
            ->where('status', 'success')
            ->latest('id')
            ->first();

        if (!$generation) {
            abort(404, 'Hasil Generate AI belum ditemukan.');
        }

        $result = AIAgentResult::where(
            'generation_id',
            $generation->id
        )
            ->where('status', 'success')
            ->latest('id')
            ->first();

        if (!$result) {
            abort(404, 'Output Dify belum ditemukan.');
        }

        $stored = $this->decodeJson($result->output_data);

        return $this->decodeJson($stored['outputs'] ?? []);
    }

    /**
     * Membaca soal asli dari Dify.
     */
    private function originalData(Assessment $assessment): array
    {
        $outputs = $this->getDifyOutputs($assessment);

        return [
            'reguler' => $this->decodeJson(
                $outputs['asesmen_reguler_revisi']
                ?? $outputs['asesmen_reguler']
                ?? []
            ),

            'adaptif' => $this->decodeJson(
                $outputs['asesmen_inklusif_revisi']
                ?? $outputs['asesmen_inklusif']
                ?? []
            ),
        ];
    }

    /**
     * Menggunakan revisi guru jika sudah disimpan.
     * Hasil Dify asli tetap dipertahankan.
     */
    private function editableData(Assessment $assessment): array
    {
        $data = $this->originalData($assessment);

        $review = DB::table('assessment_reviews')
            ->where('assessment_id', $assessment->id)
            ->first();

        if ($review) {
            $data['reguler'] = $this->decodeJson($review->reguler);
            $data['adaptif'] = $this->decodeJson($review->adaptif);
        }

        return $data;
    }

    /**
     * Membaca gambar dari output Dify.
     *
     * Jalur revisi didahulukan.
     * Mendukung gambar biasa dan array bertingkat.
     */
    private function getGambarAsesmen(Assessment $assessment): array
    {
        $outputs = $this->getDifyOutputs($assessment);

        $raw = $this->decodeJson(
            $outputs['gambar_asesmen_revisi']
            ?? $outputs['gambar_asesmen']
            ?? []
        );

        if (
            isset($raw['output'])
            && is_array($raw['output'])
        ) {
            $raw = $raw['output'];
        }

        if (
            isset($raw['gambar_per_soal'])
            && is_array($raw['gambar_per_soal'])
        ) {
            $raw = $raw['gambar_per_soal'];
        }

        if (
            isset($raw['url'])
            || isset($raw['gambar_url'])
            || isset($raw['download_url'])
            || isset($raw['preview_url'])
        ) {
            $raw = [$raw];
        }

        $gambarAsesmen = [];
        $queue = array_values($raw);

        while ($queue !== []) {
            $item = array_shift($queue);

            if (!is_array($item)) {
                continue;
            }

            if (array_is_list($item)) {
                foreach ($item as $nested) {
                    $queue[] = $nested;
                }

                continue;
            }

            if (
                isset($item['gambar_per_soal'])
                && is_array($item['gambar_per_soal'])
            ) {
                foreach ($item['gambar_per_soal'] as $nested) {
                    $queue[] = $nested;
                }

                continue;
            }

            $url = $item['url']
                ?? $item['gambar_url']
                ?? $item['download_url']
                ?? $item['preview_url']
                ?? null;

            if (
                !is_string($url)
                || !filter_var($url, FILTER_VALIDATE_URL)
            ) {
                continue;
            }

            $scheme = strtolower(
                parse_url($url, PHP_URL_SCHEME) ?? ''
            );

            if (!in_array($scheme, ['http', 'https'], true)) {
                continue;
            }

            $gambarAsesmen[] = [
                'id_soal_asal' => trim(
                    (string) ($item['id_soal_asal'] ?? '')
                ),
                'id_visual' => $item['id_visual'] ?? null,
                'url' => $url,
                'filename' => $item['filename']
                    ?? 'Ilustrasi asesmen',
                'teks_alternatif' => $item['teks_alternatif']
                    ?? null,
                'type' => $item['type'] ?? 'image',
            ];
        }

        return $gambarAsesmen;
    }

    /**
     * Halaman Edit Soal dan Publikasi.
     */
    public function edit(int $id)
    {
        $assessment = $this->findAssessment($id);

        $data = $this->editableData($assessment);

        $soalReguler = array_values(
            $data['reguler']['soal'] ?? []
        );

        $soalAdaptif = array_values(
            $data['adaptif']['soal_inklusif']
            ?? $data['adaptif']['soal_adaptif']
            ?? $data['adaptif']['soal']
            ?? []
        );

        $gambarAsesmen = $this->getGambarAsesmen($assessment);

        // Pasangkan gambar ke ID soal yang benar.
        $gambarPerSoal = [];

        foreach ($gambarAsesmen as $gambar) {
            $idSoal = $gambar['id_soal_asal'];

            if ($idSoal !== '') {
                $gambarPerSoal[$idSoal][] = $gambar;
            }
        }

        $classes = DB::table('classes')
            ->where('teacher_id', Auth::id())
            ->orderBy('name')
            ->get(['id', 'name']);

        $assignedClassIds = DB::table(
            'assessment_class_assignments'
        )
            ->where('assessment_id', $assessment->id)
            ->pluck('class_id')
            ->map(fn ($classId) => (int) $classId)
            ->all();

        $hasReview = DB::table('assessment_reviews')
            ->where('assessment_id', $assessment->id)
            ->exists();

        return view('guru.assessments.edit', compact(
            'assessment',
            'soalReguler',
            'soalAdaptif',
            'gambarAsesmen',
            'gambarPerSoal',
            'classes',
            'assignedClassIds',
            'hasReview'
        ));
    }

    /**
     * Menyimpan hasil revisi guru.
     */
    public function update(Request $request, int $id)
    {
        $assessment = $this->findAssessment($id);

        if ($assessment->status === 'Published') {
            return back()->with(
                'error',
                'Paket sudah dipublikasikan. Pengeditan dikunci.'
            );
        }

        $validated = $request->validate([
            'reguler' => ['required', 'array', 'min:1'],
            'reguler.*.pertanyaan' => ['required', 'string'],
            'reguler.*.kunci_jawaban' => ['required', 'string'],
            'reguler.*.pembahasan' => ['nullable', 'string'],
            'reguler.*.opsi' => ['sometimes', 'array'],
            'reguler.*.opsi.*.teks' => ['required', 'string'],

            'adaptif' => ['required', 'array', 'min:1'],
            'adaptif.*.pertanyaan' => ['required', 'string'],
            'adaptif.*.kunci_jawaban' => ['required', 'string'],
            'adaptif.*.pembahasan' => ['nullable', 'string'],
            'adaptif.*.opsi' => ['sometimes', 'array'],
            'adaptif.*.opsi.*.teks' => ['required', 'string'],
            'adaptif.*.scaffolding' => ['nullable', 'string'],
        ]);

        $data = $this->editableData($assessment);

        $reguler = $data['reguler'];
        $adaptif = $data['adaptif'];

        $regulerQuestions = array_values(
            $reguler['soal'] ?? []
        );

        $adaptifKey = array_key_exists(
            'soal_inklusif',
            $adaptif
        )
            ? 'soal_inklusif'
            : (
                array_key_exists('soal_adaptif', $adaptif)
                ? 'soal_adaptif'
                : 'soal'
            );

        $adaptifQuestions = array_values(
            $adaptif[$adaptifKey] ?? []
        );

        if (
            count($validated['reguler']) !== count($regulerQuestions)
            || count($validated['adaptif']) !== count($adaptifQuestions)
        ) {
            throw ValidationException::withMessages([
                'reguler' => 'Jumlah soal tidak sesuai dengan paket asli.',
            ]);
        }

        // Simpan perubahan pada soal Reguler.
        foreach ($regulerQuestions as $index => &$soal) {
            $input = $validated['reguler'][$index] ?? null;

            if (!is_array($input)) {
                throw ValidationException::withMessages([
                    'reguler' => 'Data soal Reguler tidak lengkap.',
                ]);
            }

            $soal['pertanyaan'] = $input['pertanyaan'];
            $soal['kunci_jawaban'] = $input['kunci_jawaban'];
            $soal['pembahasan'] = $input['pembahasan'] ?? '';

            if (!empty($soal['opsi'])) {
                foreach ($soal['opsi'] as $optionIndex => &$opsi) {
                    if (
                        !isset(
                            $input['opsi'][$optionIndex]['teks']
                        )
                    ) {
                        throw ValidationException::withMessages([
                            'reguler' =>
                                'Pilihan jawaban Reguler tidak lengkap.',
                        ]);
                    }

                    $opsi['teks'] =
                        $input['opsi'][$optionIndex]['teks'];
                }

                unset($opsi);
            }
        }

        unset($soal);

        // Simpan perubahan pada soal Adaptif.
        foreach ($adaptifQuestions as $index => &$soal) {
            $input = $validated['adaptif'][$index] ?? null;

            if (!is_array($input)) {
                throw ValidationException::withMessages([
                    'adaptif' => 'Data soal Adaptif tidak lengkap.',
                ]);
            }

            $soal['pertanyaan'] = $input['pertanyaan'];
            $soal['kunci_jawaban'] = $input['kunci_jawaban'];
            $soal['pembahasan'] = $input['pembahasan'] ?? '';

            if (!empty($soal['opsi'])) {
                foreach ($soal['opsi'] as $optionIndex => &$opsi) {
                    if (
                        !isset(
                            $input['opsi'][$optionIndex]['teks']
                        )
                    ) {
                        throw ValidationException::withMessages([
                            'adaptif' =>
                                'Pilihan jawaban Adaptif tidak lengkap.',
                        ]);
                    }

                    $opsi['teks'] =
                        $input['opsi'][$optionIndex]['teks'];
                }

                unset($opsi);
            }

            $lines = preg_split(
                '/\r\n|\r|\n/',
                $input['scaffolding'] ?? ''
            );

            $soal['dukungan_scaffolding'] = array_values(
                array_filter(
                    array_map('trim', $lines ?: []),
                    fn ($line) => $line !== ''
                )
            );
        }

        unset($soal);

        $reguler['soal'] = $regulerQuestions;
        $adaptif[$adaptifKey] = $adaptifQuestions;

        $values = [
            'reguler' => json_encode(
                $reguler,
                JSON_UNESCAPED_UNICODE
            ),
            'adaptif' => json_encode(
                $adaptif,
                JSON_UNESCAPED_UNICODE
            ),
            'reviewed_at' => now(),
            'updated_at' => now(),
        ];

        $reviewExists = DB::table('assessment_reviews')
            ->where('assessment_id', $assessment->id)
            ->exists();

        if ($reviewExists) {
            DB::table('assessment_reviews')
                ->where('assessment_id', $assessment->id)
                ->update($values);
        } else {
            DB::table('assessment_reviews')->insert(
                array_merge($values, [
                    'assessment_id' => $assessment->id,
                    'created_at' => now(),
                ])
            );
        }

        return redirect()
            ->route('guru.assessments.edit', $assessment->id)
            ->with(
                'success',
                'Revisi soal berhasil disimpan.'
            );
    }

    /**
     * Publikasikan paket asesmen ke kelas terpilih.
     */
    public function publishToClass(Request $request, int $id)
    {
        $assessment = $this->findAssessment($id);

        if ($assessment->status === 'Published') {
            return back()->with(
                'error',
                'Paket ini sudah dipublikasikan.'
            );
        }

        $validated = $request->validate([
            'class_ids' => ['required', 'array', 'min:1'],
            'class_ids.*' => [
                'required',
                'integer',
                'distinct',
            ],
            'review_confirmed' => ['accepted'],
        ]);

        $hasReview = DB::table('assessment_reviews')
            ->where('assessment_id', $assessment->id)
            ->exists();

        if (!$hasReview) {
            return back()->with(
                'error',
                'Simpan hasil pemeriksaan dan revisi guru sebelum publikasi.'
            );
        }

        $classIds = array_map(
            'intval',
            $validated['class_ids']
        );

        $ownedCount = DB::table('classes')
            ->where('teacher_id', Auth::id())
            ->whereIn('id', $classIds)
            ->count();

        if ($ownedCount !== count($classIds)) {
            throw ValidationException::withMessages([
                'class_ids' =>
                    'Ada kelas yang bukan milik guru ini.',
            ]);
        }

        DB::transaction(function () use (
            $assessment,
            $classIds
        ) {
            DB::table('assessment_class_assignments')
                ->where('assessment_id', $assessment->id)
                ->delete();

            foreach ($classIds as $classId) {
                DB::table(
                    'assessment_class_assignments'
                )->insert([
                    'assessment_id' => $assessment->id,
                    'class_id' => $classId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Publikasi khusus kelas, bukan Bank Soal Publik.
            $assessment->update([
                'status' => 'Published',
                'visibility' => 'private',
            ]);
        });

        return redirect()
            ->route('guru.assessments.show', $assessment->id)
            ->with(
                'success',
                'Paket asesmen berhasil dipublikasikan ke kelas.'
            );
    }
}
