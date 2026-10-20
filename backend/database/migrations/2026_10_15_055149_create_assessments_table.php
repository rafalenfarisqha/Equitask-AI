<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama');
            $table->string('mapel');
            $table->string('kelas')->nullable();
            $table->text('tujuan_pembelajaran')->nullable();
            $table->string('materi_ajar')->nullable();
            $table->string('versi_asesmen')->nullable();
            $table->integer('jumlah_pilihan_ganda')->default(10);
            $table->integer('jumlah_esai')->default(2);
            $table->boolean('c1')->default(false);
            $table->boolean('c2')->default(false);
            $table->boolean('c3')->default(false);
            $table->boolean('c4')->default(false);
            $table->boolean('c5')->default(false);
            $table->boolean('c6')->default(false);
            $table->enum('status', ['Draft', 'Published'])->default('Draft');
            $table->enum('visibility', ['private', 'public'])->default('private');
            $table->string('icon')->default('smart_toy');
            $table->string('color')->default('#2563EB');
            $table->text('description')->nullable(); // Tempat hasil AI dari Dify
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
