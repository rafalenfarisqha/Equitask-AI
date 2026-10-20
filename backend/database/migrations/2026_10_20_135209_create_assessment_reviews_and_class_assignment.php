<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assessment_id')
                ->unique()
                ->constrained('assessments')
                ->cascadeOnDelete();

            $table->json('reguler');
            $table->json('adaptif');
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });

        Schema::create('assessment_class_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assessment_id')
                ->constrained('assessments')
                ->cascadeOnDelete();

            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['assessment_id', 'class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_class_assignments');
        Schema::dropIfExists('assessment_reviews');
    }
};
