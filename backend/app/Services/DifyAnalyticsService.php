<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class DifyAnalyticsService
{
    /**
     * Jalankan workflow Analytics gabungan.
     *
     * $jenis: siswa atau guru
     * $laporan: data hasil asesmen dari Laravel
     *
     * Mengembalikan array hasil analisis
     * atau null jika Dify gagal.
     */
    public function analisis(
        string $jenis,
        array $laporan
    ): ?array {
        if (! in_array($jenis, ['siswa', 'guru'], true)) {
            throw new RuntimeException(
                'Jenis analisis tidak valid.'
            );
        }

        $apiKey = (string) config(
            'services.dify_analytics.api_key',
            ''
        );

        $baseUrl = rtrim(
            (string) config(
                'services.dify_analytics.base_url',
                'https://api.dify.ai/v1'
            ),
            '/'
        );

        $timeout = (int) config(
            'services.dify_analytics.timeout',
            90
        );

        if ($apiKey === '') {
            Log::warning(
                'API key Dify Analytics belum dikonfigurasi.'
            );

            return null;
        }

        try {
            $request = Http::withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->connectTimeout(15)
                ->timeout($timeout);

            /*
             * Jika aplikasi menggunakan CA bundle lokal,
             * manfaatkan cert yang sudah tersedia.
             * Jangan menonaktifkan SSL verification.
             */
            $caBundle = base_path('certs/cacert.pem');

            if (is_file($caBundle)) {
                $request = $request->withOptions([
                    'verify' => $caBundle,
                ]);
            }

            $response = $request->post(
                $baseUrl . '/workflows/run',
                [
                    'inputs' => [
                        'jenis_analisis' => $jenis,

                        'laporan_json' => json_encode(
                            $laporan,
                            JSON_UNESCAPED_UNICODE
                            | JSON_UNESCAPED_SLASHES
                            | JSON_THROW_ON_ERROR
                        ),
                    ],

                    'response_mode' => 'blocking',

                    'user' => 'equitask-analytics',
                ]
            );

            if (! $response->successful()) {
                Log::warning(
                    'Dify Analytics HTTP error.',
                    ['status' => $response->status()]
                );

                return null;
            }

            $payload = $response->json();

            if (
                ! is_array($payload)
                || data_get($payload, 'data.status') !== 'succeeded'
            ) {
                Log::warning(
                    'Dify Analytics workflow tidak sukses.'
                );

                return null;
            }

            /*
             * OUTPUT workflow:
             * analisis_json
             */
            $hasil = data_get(
                $payload,
                'data.outputs.analisis_json'
            );

            /*
             * Bila OUTPUT mengambil structured_output,
             * hasilnya mungkin langsung berupa array.
             */
            if (is_array($hasil)) {
                return $this->validasi($jenis, $hasil);
            }

            /*
             * Bila OUTPUT mengambil LLM text,
             * hasilnya berupa string JSON.
             */
            if (! is_string($hasil) || trim($hasil) === '') {
                return null;
            }

            $decoded = json_decode(
                trim($hasil),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            if (! is_array($decoded)) {
                return null;
            }

            return $this->validasi($jenis, $decoded);
        } catch (Throwable $e) {
            /*
             * Tidak membocorkan isi laporan siswa,
             * API key, atau seluruh respons Dify ke log.
             */
            Log::warning('Dify Analytics gagal.', [
                'exception' => get_class($e),
            ]);

            return null;
        }
    }

    /**
     * Pastikan struktur output sesuai jenis analisis.
     */
    private function validasi(
        string $jenis,
        array $hasil
    ): ?array {
        $wajibSiswa = [
            'ringkasan',
            'kategori_nilai',
            'kelebihan',
            'perlu_diperkuat',
            'rekomendasi',
            'pesan_motivasi',
            'catatan_keterbatasan',
        ];

        $wajibGuru = [
            'ringkasan',
            'kelebihan',
            'perlu_diperkuat',
            'rekomendasi',
            'strategi_mengajar',
            'evaluasi_lanjutan',
            'catatan_keterbatasan',
        ];

        $wajib = $jenis === 'siswa'
            ? $wajibSiswa
            : $wajibGuru;

        foreach ($wajib as $field) {
            if (! array_key_exists($field, $hasil)) {
                return null;
            }
        }

        $fieldArray = $jenis === 'siswa'
            ? [
                'kelebihan',
                'perlu_diperkuat',
                'rekomendasi',
            ]
            : [
                'kelebihan',
                'perlu_diperkuat',
                'rekomendasi',
                'strategi_mengajar',
                'evaluasi_lanjutan',
            ];

        foreach ($fieldArray as $field) {
            if (
                ! is_array($hasil[$field])
                || ! array_is_list($hasil[$field])
            ) {
                return null;
            }

            foreach ($hasil[$field] as $item) {
                if (! is_string($item)) {
                    return null;
                }
            }
        }

        foreach ($wajib as $field) {
            if (in_array($field, $fieldArray, true)) {
                continue;
            }

            if (! is_string($hasil[$field])) {
                return null;
            }
        }

        return array_intersect_key(
            $hasil,
            array_flip($wajib)
        );
    }
}
