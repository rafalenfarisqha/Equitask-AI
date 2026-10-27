<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Metadata materi untuk analitik per soal.
        if (Schema::hasTable('questions')) {

            if (!Schema::hasColumn('questions', 'materi')) {
                Schema::table('questions', function (Blueprint $table) {
                    $table->string('materi', 255)->nullable();
                });
            }

            if (!Schema::hasColumn('questions', 'topik')) {
                Schema::table('questions', function (Blueprint $table) {
                    $table->string('topik', 255)->nullable();
                });
            }
        }

        // Preferensi versi asesmen yang diatur secara eksplisit.
        if (
            Schema::hasTable('student_profiles')
            && !Schema::hasColumn(
                'student_profiles',
                'versi_pembelajaran'
            )
        ) {
            Schema::table('student_profiles', function (Blueprint $table) {
                $table->string('versi_pembelajaran', 20)
                    ->nullable();
            });
        }
    }

    public function down(): void
    {
        if (
            Schema::hasTable('student_profiles')
            && Schema::hasColumn(
                'student_profiles',
                'versi_pembelajaran'
            )
        ) {
            Schema::table('student_profiles', function (Blueprint $table) {
                $table->dropColumn('versi_pembelajaran');
            });
        }

        if (Schema::hasTable('questions')) {

            if (Schema::hasColumn('questions', 'topik')) {
                Schema::table('questions', function (Blueprint $table) {
                    $table->dropColumn('topik');
                });
            }

            if (Schema::hasColumn('questions', 'materi')) {
                Schema::table('questions', function (Blueprint $table) {
                    $table->dropColumn('materi');
                });
            }
        }
    }
};
