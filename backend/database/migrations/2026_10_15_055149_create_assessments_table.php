<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ID Guru yang membuat
            $table->string('nama');                // Judul assessment
            $table->string('mapel');               // Mata pelajaran (IPA, Matematika, dll)
            $table->enum('status', ['Published', 'Draft'])->default('Draft');
            $table->enum('visibility', ['private', 'public'])->default('private'); // Status privasi/publik
            $table->string('icon')->default('eco');  // Icon material symbols
            $table->string('color')->default('#2563EB'); // Warna aksen
            $table->timestamps();
        });
    }
};
