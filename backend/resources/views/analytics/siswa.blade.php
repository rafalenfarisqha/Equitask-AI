<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Hasil Belajar - EquiTask AI</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-50 text-slate-800">

    <main class="mx-auto max-w-5xl px-4 py-8">

        <!-- HEADER -->
        <header class="mb-8">

            <a href="{{ route('siswa.riwayat') }}"
               class="text-sm font-semibold text-blue-600
                      hover:text-blue-800">
                ← Kembali ke Riwayat
            </a>

            <p class="mt-5 text-sm font-semibold text-blue-600">
                EQUITASK AI
            </p>

            <h1 class="mt-2 text-3xl font-bold">
                Analitik Hasil Belajarku
            </h1>

            <p class="mt-2 text-slate-500">
                {{ $assessment->nama }}
                ·
                {{ $assessment->mapel }}
            </p>

        </header>

        <!-- STATISTIK NILAI -->
        <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-4">

            <div class="rounded-2xl bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">
                    Nilai Akhir
                </p>

                <p class="mt-2 text-3xl font-bold text-blue-600">
                    {{ $pengerjaan->score ?? '—' }}
                </p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">
                    Jawaban Benar
                </p>

                <p class="mt-2 text-3xl font-bold text-green-600">
                    {{ $benar }}
                </p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">
                    Jawaban Salah
                </p>

                <p class="mt-2 text-3xl font-bold text-red-500">
                    {{ $salah }}
                </p>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">
                    Belum Dinilai
                </p>

                <p class="mt-2 text-3xl font-bold text-amber-500">
                    {{ $belumDinilai }}
                </p>
            </div>

        </div>

        @if ($belumDinilai > 0)

            <div class="mb-6 rounded-2xl border
                        border-amber-200 bg-amber-50 p-5">

                <h2 class="font-bold text-amber-800">
                    Penilaian Belum Lengkap
                </h2>

                <p class="mt-2 text-sm text-amber-700">
                    Masih ada {{ $belumDinilai }}
                    jawaban yang belum dinilai.

                    Analisis AI lengkap akan tersedia
                    setelah penilaian selesai.
                </p>

            </div>

        @endif

        @if ($analisis)

            <!-- RINGKASAN -->
            <section class="mb-5 rounded-2xl bg-white p-6 shadow-sm">

                <div class="mb-4 flex flex-wrap
                            items-center justify-between gap-3">

                    <h2 class="text-xl font-bold">
                        Ringkasan Hasil Belajar
                    </h2>

                    <span class="rounded-full bg-blue-100
                                 px-4 py-2 text-sm font-semibold
                                 text-blue-700">
                        {{ $analisis['kategori_nilai'] }}
                    </span>

                </div>

                <p class="leading-7 text-slate-600">
                    {{ $analisis['ringkasan'] }}
                </p>

            </section>

            <!-- KELEBIHAN -->
            <section class="mb-5 rounded-2xl bg-white p-6 shadow-sm">

                <h2 class="mb-4 text-xl font-bold text-green-700">
                    ✓ Yang Sudah Baik
                </h2>

                <ul class="space-y-3">

                    @forelse ($analisis['kelebihan'] as $item)

                        <li class="rounded-xl bg-green-50 p-4
                                   leading-6 text-slate-700">
                            {{ $item }}
                        </li>

                    @empty

                        <li class="text-slate-500">
                            Belum ada informasi yang cukup.
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

                        <li class="rounded-xl bg-amber-50 p-4
                                   leading-6 text-slate-700">
                            {{ $item }}
                        </li>

                    @empty

                        <li class="text-slate-500">
                            Belum ada materi yang dapat
                            diidentifikasi dari data tersedia.
                        </li>

                    @endforelse

                </ul>

            </section>

            <!-- REKOMENDASI AI -->
            <section class="mb-5 rounded-2xl bg-white p-6 shadow-sm">

                <h2 class="mb-4 text-xl font-bold text-blue-700">
                    ✦ Rekomendasi Belajar AI
                </h2>

                <ol class="space-y-3">

                    @forelse ($analisis['rekomendasi'] as $index => $item)

                        <li class="flex gap-3 rounded-xl
                                   bg-blue-50 p-4">

                            <span class="flex h-7 w-7 shrink-0
                                         items-center justify-center
                                         rounded-full bg-blue-600
                                         text-sm font-bold text-white">
                                {{ $index + 1 }}
                            </span>

                            <span class="leading-6 text-slate-700">
                                {{ $item }}
                            </span>

                        </li>

                    @empty

                        <li class="text-slate-500">
                            Belum ada rekomendasi.
                        </li>

                    @endforelse

                </ol>

            </section>

            <!-- MOTIVASI -->
            <section class="mb-5 rounded-2xl bg-blue-600
                            p-6 text-white">

                <h2 class="mb-3 text-xl font-bold">
                    Semangat Belajar!
                </h2>

                <p class="leading-7 text-blue-50">
                    {{ $analisis['pesan_motivasi'] }}
                </p>

            </section>

            <!-- KETERBATASAN -->
            <section class="rounded-2xl border
                            border-slate-200 bg-white p-5">

                <h2 class="mb-2 text-sm font-bold text-slate-600">
                    Catatan Analisis
                </h2>

                <p class="text-sm leading-6 text-slate-500">
                    {{ $analisis['catatan_keterbatasan'] }}
                </p>

            </section>

        @else

            <!-- JIKA ANALISIS AI BELUM TERSEDIA -->
            <section class="rounded-2xl border
                            border-slate-200 bg-white p-6">

                <h2 class="text-xl font-bold">
                    Analisis AI Belum Tersedia
                </h2>

                <p class="mt-3 leading-7 text-slate-600">

                    @if ($belumDinilai > 0)

                        Analisis AI akan tersedia
                        setelah seluruh jawaban selesai dinilai.

                    @elseif ($pengerjaan->score === null)

                        Nilai akhir belum tersedia,
                        sehingga analisis AI belum dapat dibuat.

                    @else

                        Data nilai sudah tersedia, tetapi
                        layanan analisis AI belum berhasil
                        memberikan hasil saat ini.

                    @endif

                </p>

            </section>

        @endif

        <div class="mt-8 text-center text-xs text-slate-400">
            EquiTask AI · Analitik pembelajaran
            berdasarkan hasil asesmen
        </div>

    </main>

</body>
</html>
