
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->string('kelas')->nullable();

            $table->text('tujuan_pembelajaran')->nullable();

            // Menyimpan lokasi file PDF materi ajar
            $table->string('materi_ajar')->nullable();

            $table->string('versi_asesmen')->nullable();

            $table->unsignedInteger('jumlah_pilihan_ganda')
                ->default(0);

            $table->unsignedInteger('jumlah_esai')
                ->default(0);

            $table->boolean('c1')->default(false);
            $table->boolean('c2')->default(false);
            $table->boolean('c3')->default(false);
            $table->boolean('c4')->default(false);
            $table->boolean('c5')->default(false);
            $table->boolean('c6')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn([
                'kelas',
                'tujuan_pembelajaran',
                'materi_ajar',
                'versi_asesmen',
                'jumlah_pilihan_ganda',
                'jumlah_esai',
                'c1',
                'c2',
                'c3',
                'c4',
                'c5',
                'c6',
            ]);
        });
    }
};
