<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Analitik Guru - EquiTask AI</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-50 text-slate-800">

    <main class="mx-auto max-w-6xl px-4 py-8">

        <!-- HEADER -->
        <header class="mb-8">

            <a href="{{ route('guru.assessments.show', $assessment->id) }}"
               class="text-sm font-semibold text-blue-600
                      hover:text-blue-800">
                ← Kembali ke Asesmen
            </a>

            <p class="mt-5 text-sm font-semibold text-blue-600">
                EQUITASK AI · GURU
            </p>

            <h1 class="mt-2 text-3xl font-bold">
                Analitik AI Pembelajaran
            </h1>

            <p class="mt-2 text-slate-500">
                {{ $assessment->nama }}
                ·
                {{ $assessment->mapel }}
            </p>

        </header>

        <!-- STATISTIK KELAS -->
        <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-4">

            <div class="rounded-2xl bg-white p-5 shadow-sm">

                <p class="text-sm text-slate-500">
                    Jumlah Siswa
                </p>

                <p class="mt-2 text-3xl font-bold text-slate-800">
                    {{ $jumlahSiswa }}
                </p>

            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm">

                <p class="text-sm text-slate-500">
                    Sudah Mengumpulkan
                </p>

                <p class="mt-2 text-3xl font-bold text-blue-600">
                    {{ $selesai }}
                </p>

            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm">

                <p class="text-sm text-slate-500">
                    Penilaian Lengkap
                </p>

                <p class="mt-2 text-3xl font-bold text-green-600">
                    {{ $dinilai }}
                </p>

            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm">

                <p class="text-sm text-slate-500">
                    Rata-rata Nilai
                </p>

                <p class="mt-2 text-3xl font-bold text-violet-600">
                    {{ $rataRata !== null
                        ? number_format($rataRata, 2, ',', '.')
                        : '—' }}
                </p>

            </div>

        </div>

        <!-- PROGRES PENGUMPULAN -->
        @php
            $persenSelesai = $jumlahSiswa > 0
                ? min(100, round($selesai / $jumlahSiswa * 100))
                : 0;
        @endphp

        <section class="mb-6 rounded-2xl bg-white p-6 shadow-sm">

            <div class="mb-3 flex items-center
                        justify-between gap-3">

                <h2 class="font-bold">
                    Progres Pengumpulan
                </h2>

                <span class="text-sm font-bold text-blue-600">
                    {{ $persenSelesai }}%
                </span>

            </div>

            <div class="h-3 overflow-hidden rounded-full bg-slate-100">

                <div class="h-full rounded-full bg-blue-600"
                     style="width: {{ $persenSelesai }}%">
                </div>

            </div>

            <p class="mt-3 text-sm text-slate-500">
                {{ $selesai }} dari {{ $jumlahSiswa }}
                siswa telah mengumpulkan asesmen.
            </p>

        </section>

        @if ($analisis)

            <!-- RINGKASAN -->
            <section class="mb-5 rounded-2xl bg-white p-6 shadow-sm">

                <h2 class="mb-4 text-xl font-bold">
                    Ringkasan Hasil Kelas
                </h2>

                <p class="leading-7 text-slate-600">
                    {{ $analisis['ringkasan'] }}
                </p>

            </section>

            <!-- KELEBIHAN -->
            <section class="mb-5 rounded-2xl bg-white p-6 shadow-sm">

                <h2 class="mb-4 text-xl font-bold text-green-700">
                    ✓ Hasil yang Sudah Baik
                </h2>

                <ul class="space-y-3">

                    @forelse ($analisis['kelebihan'] as $item)

                        <li class="rounded-xl bg-green-50
                                   p-4 leading-6">
                            {{ $item }}
                        </li>

                    @empty

                        <li class="text-slate-500">
                            Data belum mencukupi.
                        </li>

                    @endforelse

                </ul>

            </section>

            <!-- PERLU DIPERKUAT -->
            <section class="mb-5 rounded-2xl bg-white p-6 shadow-sm">

                <h2 class="mb-4 text-xl font-bold text-amber-700">
                    Materi yang Perlu Diperkuat
                </h2>

                <ul class="space-y-3">

                    @forelse ($analisis['perlu_diperkuat'] as $item)

                        <li class="rounded-xl bg-amber-50
                                   p-4 leading-6">
                            {{ $item }}
                        </li>

                    @empty

                        <li class="text-slate-500">
                            Materi belum dapat dipetakan.
                        </li>

                    @endforelse

                </ul>

            </section>

            <!-- REKOMENDASI GURU -->
            <section class="mb-5 rounded-2xl bg-white p-6 shadow-sm">

                <h2 class="mb-4 text-xl font-bold text-blue-700">
                    Rekomendasi untuk Guru
                </h2>

                <ul class="space-y-3">

                    @forelse ($analisis['rekomendasi'] as $item)

                        <li class="rounded-xl bg-blue-50 p-4
                                   leading-6">
                            {{ $item }}
                        </li>

                    @empty

                        <li class="text-slate-500">
                            Belum ada rekomendasi.
                        </li>

                    @endforelse

                </ul>

            </section>

            <!-- STRATEGI MENGAJAR -->
            <section class="mb-5 rounded-2xl bg-white p-6 shadow-sm">

                <h2 class="mb-4 text-xl font-bold text-violet-700">
                    Strategi Pembelajaran
                </h2>

                <ol class="space-y-3">

                    @forelse ($analisis['strategi_mengajar'] as $index => $item)

                        <li class="flex gap-3 rounded-xl
                                   bg-violet-50 p-4">

                            <span class="flex h-7 w-7 shrink-0
                                         items-center justify-center
                                         rounded-full bg-violet-600
                                         text-sm font-bold text-white">
                                {{ $index + 1 }}
                            </span>

                            <span class="leading-6">
                                {{ $item }}
                            </span>

                        </li>

                    @empty

                        <li class="text-slate-500">
                            Belum ada strategi yang dapat disarankan.
                        </li>

                    @endforelse

                </ol>

            </section>

            <!-- EVALUASI -->
            <section class="mb-5 rounded-2xl bg-white p-6 shadow-sm">

                <h2 class="mb-4 text-xl font-bold text-teal-700">
                    Evaluasi Pembelajaran Lanjutan
                </h2>

                <ul class="space-y-3">

                    @forelse ($analisis['evaluasi_lanjutan'] as $item)

                        <li class="rounded-xl bg-teal-50
                                   p-4 leading-6">
                            {{ $item }}
                        </li>

                    @empty

                        <li class="text-slate-500">
                            Belum ada rekomendasi evaluasi.
                        </li>

                    @endforelse

                </ul>

            </section>

            <!-- CATATAN -->
            <section class="rounded-2xl border
                            border-slate-200 bg-white p-5">

                <h2 class="mb-2 text-sm font-bold text-slate-600">
                    Catatan dan Keterbatasan Analisis
                </h2>

                <p class="text-sm leading-6 text-slate-500">
                    {{ $analisis['catatan_keterbatasan'] }}
                </p>

            </section>

        @else

            <section class="rounded-2xl border
                            border-slate-200 bg-white p-6">

                <h2 class="text-xl font-bold">
                    Analisis AI Belum Tersedia
                </h2>

                <p class="mt-3 leading-7 text-slate-600">

                    @if ($dinilai === 0)

                        Belum ada pengerjaan yang
                        penilaiannya lengkap.

                        Analisis AI akan tersedia setelah
                        ada hasil asesmen yang selesai dinilai.

                    @else

                        Statistik kelas sudah tersedia,
                        tetapi layanan analisis AI belum
                        berhasil memberikan hasil saat ini.

                    @endif

                </p>

            </section>

        @endif

        <footer class="mt-8 text-center text-xs text-slate-400">
            EquiTask AI · Analitik untuk mendukung
            keputusan pembelajaran guru
        </footer>

    </main>

</body>
</html>
