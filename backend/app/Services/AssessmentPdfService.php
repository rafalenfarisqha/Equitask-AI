<?php
namespace App\Services;

use App\Models\Assessment;
use App\Models\AIGeneration;
use App\Models\AIAgentResult;
use Illuminate\Support\Facades\DB;


class AssessmentPdfService
{
    public function getQuestions(
        Assessment $assessment,
        string $format
    ): array {
        abort_unless(
            in_array($format, ['regular', 'easy_read'], true),
            404
        );

        $generation = AIGeneration::where(
            'assessment_id',
            $assessment->id
        )
            ->where('teacher_id', $assessment->user_id)
            ->where('status', 'success')
            ->latest('id')
            ->first();

        abort_unless($generation, 404, 'Hasil generate belum tersedia.');

        $result = AIAgentResult::where(
            'generation_id',
            $generation->id
        )
            ->where('status', 'success')
            ->latest('id')
            ->first();

        abort_unless($result, 404, 'Data soal belum tersedia.');

        $stored = $this->decode($result->output_data);
        $outputs = $this->decode($stored['outputs'] ?? []);

        $reguler = $this->decode(
            $outputs['asesmen_reguler_revisi']
            ?? $outputs['asesmen_reguler']
            ?? []
        );

        $adaptif = $this->decode(
            $outputs['asesmen_inklusif_revisi']
            ?? $outputs['asesmen_inklusif']
            ?? []
        );

        // Revisi guru diprioritaskan daripada hasil AI.
        $review = DB::table('assessment_reviews')
            ->where('assessment_id', $assessment->id)
            ->first();

        if ($review) {
            $reviewReguler = $this->decode($review->reguler);
            $reviewAdaptif = $this->decode($review->adaptif);

            if ($reviewReguler !== []) {
                $reguler = $reviewReguler;
            }

            if ($reviewAdaptif !== []) {
                $adaptif = $reviewAdaptif;
            }
        }

        $questions = $format === 'regular'
            ? ($reguler['soal'] ?? [])
            : (
                $adaptif['soal_inklusif']
                ?? $adaptif['soal_adaptif']
                ?? $adaptif['soal']
                ?? []
            );

        abort_unless(
            is_array($questions) && count($questions) > 0,
            404,
            'Soal untuk format ini belum tersedia.'
        );

        return array_values($questions);
    }

    private function decode(mixed $value): array
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        return is_array($value) ? $value : [];
    }
}
