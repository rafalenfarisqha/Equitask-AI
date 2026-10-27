<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Services\AssessmentPdfService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AssessmentPdfController extends Controller
{
    public function download(
        Request $request,
        Assessment $assessment,
        AssessmentPdfService $pdfService
    ) {
        // Hanya guru pemilik asesmen yang dapat mengunduh.
        abort_unless(
            $request->user()
            && $assessment->user_id === $request->user()->id,
            403,
            'Anda tidak memiliki akses ke asesmen ini.'
        );

        $format = $request->query('format', 'regular');

        abort_unless(
            in_array($format, ['regular', 'easy_read'], true),
            404,
            'Format PDF tidak tersedia.'
        );

        $questions = $pdfService->getQuestions(
            $assessment,
            $format
        );

        $pdf = Pdf::loadView('pdf.assessment', [
            'assessment' => $assessment,
            'questions' => $questions,
            'format' => $format,
        ])->setPaper('a4', 'portrait');

        return $pdf->download(
            'asesmen-' . $assessment->id . '-' . $format . '.pdf'
        );
    }

    /**
     * Download PDF dari asesmen yang dibagikan ke Bank Soal publik.
     */
    public function downloadPublic(
        Request $request,
        int $id,
        AssessmentPdfService $pdfService
    ) {
        $assessment = Assessment::where('visibility', 'public')
            ->where('status', 'Published')
            ->findOrFail($id);

        $format = $request->query('format', 'regular');

        abort_unless(
            in_array($format, ['regular', 'easy_read'], true),
            404,
            'Format PDF tidak tersedia.'
        );

        $questions = $pdfService->getQuestions(
            $assessment,
            $format
        );

        $pdf = Pdf::loadView('pdf.assessment', [
            'assessment' => $assessment,
            'questions' => $questions,
            'format' => $format,
        ])->setPaper('a4', 'portrait');

        return $pdf->download(
            'asesmen-' . $assessment->id . '-' . $format . '.pdf'
        );
    }

}
