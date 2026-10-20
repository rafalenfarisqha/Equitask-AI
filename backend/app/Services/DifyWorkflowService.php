<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class DifyWorkflowService
{
    /**
     * Upload PDF, jalankan workflow,
     * dan kembalikan output Dify.
     */
    public function generateAsesmen(
        array $inputs,
        UploadedFile $materiAjar,
        string $userId
    ): array {
        $baseUrl = rtrim(
            (string) config(
                'services.dify.base_url',
                'https://api.dify.ai/v1'
            ),
            '/'
        );

        $apiKey = (string) config(
            'services.dify.api_key',
            ''
        );

        $fileVariable = (string) config(
            'services.dify.file_variable',
            'materi_ajar'
        );

        $timeout = max(
            60,
            (int) config('services.dify.timeout', 600)
        );

        $certificatePath = base_path('certs/cacert.pem');

        if ($apiKey === '') {
            throw new RuntimeException(
                'DIFY_API_KEY belum dikonfigurasi.'
            );
        }

        if (
            !is_file($certificatePath) ||
            !is_readable($certificatePath)
        ) {
            throw new RuntimeException(
                'Sertifikat certs/cacert.pem tidak ditemukan.'
            );
        }

        if (!$materiAjar->isValid()) {
            throw new RuntimeException(
                'File materi ajar tidak valid.'
            );
        }

        // 1. Upload PDF ke Dify
        $fileId = $this->uploadPdf(
            $baseUrl,
            $apiKey,
            $certificatePath,
            $materiAjar,
            $userId
        );

        // 2. Tambahkan PDF ke input START
        $inputs[$fileVariable] = [
            'type' => 'document',
            'transfer_method' => 'local_file',
            'upload_file_id' => $fileId,
        ];

        // 3. Jalankan workflow Dify
        return $this->jalankanWorkflow(
            $baseUrl,
            $apiKey,
            $certificatePath,
            $inputs,
            $userId,
            $timeout
        );
    }

    /**
     * Upload PDF dengan Laravel HTTP Client.
     */
    private function uploadPdf(
        string $baseUrl,
        string $apiKey,
        string $certificatePath,
        UploadedFile $materiAjar,
        string $userId
    ): string {
        $filePath = $materiAjar->getRealPath();

        if (!$filePath || !is_readable($filePath)) {
            throw new RuntimeException(
                'PDF tidak dapat dibaca.'
            );
        }

        $handle = fopen($filePath, 'rb');

        if ($handle === false) {
            throw new RuntimeException(
                'Gagal membuka file PDF.'
            );
        }

        try {
            Log::info('Memulai upload PDF ke Dify');

            $response = Http::withToken($apiKey)
                ->withOptions([
                    'verify' => $certificatePath,
                    'version' => '1.1',
                ])
                ->acceptJson()
                ->connectTimeout(30)
                ->timeout(120)
                ->attach(
                    'file',
                    $handle,
                    $materiAjar->getClientOriginalName(),
                    [
                        'Content-Type' => 'application/pdf',
                    ]
                )
                ->post(
                    $baseUrl . '/files/upload',
                    [
                        'user' => $userId,
                    ]
                );

        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }

        if (!$response->successful()) {
            throw new RuntimeException(
                'Upload PDF ditolak Dify. HTTP ' .
                $response->status() .
                '. ' .
                $this->pesanError($response->json())
            );
        }

        $fileId = $response->json('id');

        if (!is_string($fileId) || $fileId === '') {
            throw new RuntimeException(
                'Dify tidak mengembalikan ID file PDF.'
            );
        }

        Log::info('Upload PDF Dify berhasil');

        return $fileId;
    }

    /**
     * Jalankan workflow menggunakan streaming SSE.
     *
     * Jika stream putus setelah mendapatkan workflow ID,
     * cek hasil eksekusi yang sama, bukan menjalankan ulang.
     */
    private function jalankanWorkflow(
        string $baseUrl,
        string $apiKey,
        string $certificatePath,
        array $inputs,
        string $userId,
        int $timeout
    ): array {
        Log::info('Memulai workflow Dify mode streaming');

        $response = Http::withToken($apiKey)
            ->withOptions([
                'verify' => $certificatePath,
                'version' => '1.1',
                'stream' => true,
            ])
            ->withHeaders([
                'Accept' => 'text/event-stream',
            ])
            ->asJson()
            ->connectTimeout(30)
            ->timeout($timeout)
            ->post(
                $baseUrl . '/workflows/run',
                [
                    'inputs' => $inputs,
                    'response_mode' => 'streaming',
                    'user' => $userId,
                ]
            );

        if (!$response->successful()) {
            throw new RuntimeException(
                'Dify menolak workflow. HTTP ' .
                $response->status() .
                '. ' .
                $this->pesanError($response->json())
            );
        }

        $stream = $response
            ->toPsrResponse()
            ->getBody();

        $buffer = '';
        $workflowRunId = null;
        $streamError = null;

        try {
            while (!$stream->eof()) {
                try {
                    $chunk = $stream->read(4096);
                } catch (Throwable $e) {
                    $streamError = $e;

                    Log::warning(
                        'Koneksi streaming Dify terputus',
                        [
                            'workflow_run_id' => $workflowRunId,
                            'error' => $e->getMessage(),
                        ]
                    );

                    break;
                }

                if ($chunk === '') {
                    continue;
                }

                // Simpan chunk terlebih dahulu agar
                // event yang terpotong tetap bisa digabung.
                $buffer .= $chunk;

                // Pisahkan event SSE dengan baris kosong.
                while (
                    preg_match(
                        "/\r\n\r\n|\n\n|\r\r/",
                        $buffer,
                        $matches,
                        PREG_OFFSET_CAPTURE
                    )
                ) {
                    $separator = $matches[0][0];
                    $position = $matches[0][1];

                    $eventBlock = substr(
                        $buffer,
                        0,
                        $position
                    );

                    $buffer = substr(
                        $buffer,
                        $position + strlen($separator)
                    );

                    $event = $this->parseSseEvent(
                        $eventBlock
                    );

                    if ($event === null) {
                        continue;
                    }

                    $eventType = $event['event'] ?? '';

                    if ($eventType === 'workflow_started') {
                        $id = $event['workflow_run_id']
                            ?? ($event['data']['id'] ?? null);

                        if (
                            is_string($id) &&
                            $id !== ''
                        ) {
                            $workflowRunId = $id;
                        }

                        Log::info(
                            'Workflow Dify mulai diproses',
                            [
                                'workflow_run_id' =>
                                    $workflowRunId,
                            ]
                        );
                    }

                    if ($eventType === 'error') {
                        $message = $event['message']
                            ?? 'Dify mengirim event error.';

                        throw new RuntimeException(
                            is_string($message)
                                ? $message
                                : 'Error workflow tidak dikenali.'
                        );
                    }

                    if ($eventType === 'workflow_finished') {
                        $data = $event['data'] ?? [];

                        if (!is_array($data)) {
                            throw new RuntimeException(
                                'Data akhir workflow tidak valid.'
                            );
                        }

                        $id = $event['workflow_run_id']
                            ?? ($data['id'] ?? null);

                        if (
                            is_string($id) &&
                            $id !== ''
                        ) {
                            $workflowRunId = $id;
                        }

                        $status = (string) (
                            $data['status'] ?? ''
                        );

                        if ($status !== 'succeeded') {
                            $error = $data['error']
                                ?? 'Workflow tidak berhasil.';

                            throw new RuntimeException(
                                'Workflow Dify berstatus ' .
                                ($status ?: 'tidak diketahui') .
                                '. ' .
                                (
                                    is_string($error)
                                        ? $error
                                        : 'Error tidak dikenali.'
                                )
                            );
                        }

                        $outputs = $data['outputs'] ?? [];

                        if (!is_array($outputs)) {
                            throw new RuntimeException(
                                'Output akhir Dify tidak valid.'
                            );
                        }

                        Log::info(
                            'Workflow Dify berhasil',
                            [
                                'workflow_run_id' =>
                                    $workflowRunId,
                                'output_keys' =>
                                    array_keys($outputs),
                            ]
                        );

                        return [
                            'workflow_run_id' =>
                                $workflowRunId,
                            'status' => 'succeeded',
                            'outputs' => $outputs,
                        ];
                    }
                }
            }
        } finally {
            $stream->close();
        }

        // Jangan memulai workflow baru ketika
        // respons streaming terputus.
        if (
            !is_string($workflowRunId) ||
            $workflowRunId === ''
        ) {
            throw new RuntimeException(
                'Streaming Dify terputus sebelum ID workflow diterima. ' .
                'Jangan ulangi Generate sebelum memeriksa eksekusi Dify.',
                0,
                $streamError
            );
        }

        Log::warning(
            'Mencoba memulihkan hasil workflow Dify',
            [
                'workflow_run_id' => $workflowRunId,
            ]
        );

        return $this->ambilHasilWorkflow(
            $baseUrl,
            $apiKey,
            $certificatePath,
            $workflowRunId
        );
    }

    /**
     * Ambil hasil dari workflow run yang sama.
     *
     * Tidak membuat workflow baru.
     */
    private function ambilHasilWorkflow(
        string $baseUrl,
        string $apiKey,
        string $certificatePath,
        string $workflowRunId
    ): array {
        $maxAttempts = 12;

        for (
            $attempt = 1;
            $attempt <= $maxAttempts;
            $attempt++
        ) {
            try {
                $response = Http::withToken($apiKey)
                    ->withOptions([
                        'verify' => $certificatePath,
                        'version' => '1.1',
                    ])
                    ->acceptJson()
                    ->connectTimeout(15)
                    ->timeout(30)
                    ->get(
                        $baseUrl .
                        '/workflows/run/' .
                        rawurlencode($workflowRunId)
                    );

            } catch (Throwable $e) {
                Log::warning(
                    'Gagal memeriksa hasil workflow Dify',
                    [
                        'workflow_run_id' =>
                            $workflowRunId,
                        'attempt' => $attempt,
                        'error' => $e->getMessage(),
                    ]
                );

                if ($attempt >= $maxAttempts) {
                    throw new RuntimeException(
                        'Tidak dapat mengambil hasil workflow. ' .
                        'Workflow run ID: ' .
                        $workflowRunId,
                        0,
                        $e
                    );
                }

                sleep(5);
                continue;
            }

            if (!$response->successful()) {
                throw new RuntimeException(
                    'API hasil workflow Dify mengembalikan HTTP ' .
                    $response->status() .
                    '. Workflow run ID: ' .
                    $workflowRunId
                );
            }

            $data = $response->json();

            if (!is_array($data)) {
                throw new RuntimeException(
                    'Respons hasil workflow tidak valid.'
                );
            }

            // Endpoint detail eksekusi Dify
            // mengembalikan status dan outputs.
            $status = (string) (
                $data['status'] ?? ''
            );

            if ($status === 'succeeded') {
                $outputs = $data['outputs'] ?? [];

                if (!is_array($outputs)) {
                    throw new RuntimeException(
                        'Output workflow Dify tidak valid.'
                    );
                }

                Log::info(
                    'Hasil workflow Dify berhasil dipulihkan',
                    [
                        'workflow_run_id' =>
                            $workflowRunId,
                        'output_keys' =>
                            array_keys($outputs),
                    ]
                );

                return [
                    'workflow_run_id' =>
                        $workflowRunId,
                    'status' => 'succeeded',
                    'outputs' => $outputs,
                ];
            }

            if (in_array(
                $status,
                ['failed', 'stopped'],
                true
            )) {
                $error = $data['error']
                    ?? 'Tidak ada detail error.';

                throw new RuntimeException(
                    'Workflow Dify berstatus ' .
                    $status .
                    '. ' .
                    (
                        is_string($error)
                            ? $error
                            : 'Error tidak dikenali.'
                    )
                );
            }

            if ($status !== 'running') {
                throw new RuntimeException(
                    'Status workflow Dify tidak dikenali: ' .
                    ($status ?: 'kosong')
                );
            }

            Log::info(
                'Workflow Dify masih berjalan',
                [
                    'workflow_run_id' =>
                        $workflowRunId,
                    'attempt' => $attempt,
                ]
            );

            if ($attempt < $maxAttempts) {
                sleep(5);
            }
        }

        throw new RuntimeException(
            'Workflow Dify belum selesai dalam waktu pemantauan. ' .
            'Workflow run ID: ' .
            $workflowRunId .
            '. Jangan ulangi Generate sebelum memeriksa statusnya.'
        );
    }

    /**
     * Parse event Server-Sent Events dari Dify.
     */
    private function parseSseEvent(
        string $block
    ): ?array {
        $block = str_replace(
            ["\r\n", "\r"],
            "\n",
            $block
        );

        $dataLines = [];

        foreach (
            explode("\n", $block) as $line
        ) {
            if (str_starts_with($line, 'data:')) {
                $dataLines[] = ltrim(
                    substr($line, 5),
                    ' '
                );
            }
        }

        if (empty($dataLines)) {
            return null;
        }

        $decoded = json_decode(
            implode("\n", $dataLines),
            true
        );

        return is_array($decoded)
            ? $decoded
            : null;
    }

    /**
     * Ambil pesan error dari API Dify.
     */
    private function pesanError($response): string
    {
        if (!is_array($response)) {
            return 'Tidak ada detail error.';
        }

        $message = $response['message']
            ?? $response['error']
            ?? 'Tidak ada detail error.';

        return is_string($message)
            ? mb_substr($message, 0, 500)
            : 'Format error tidak dikenali.';
    }
}
