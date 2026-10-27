<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_members', function (Blueprint $table) {
            $table->string('assessment_track', 10)
                ->default('reguler');
        });
    }

    public function down(): void
    {
        Schema::table('class_members', function (Blueprint $table) {
            $table->dropColumn('assessment_track');
        });
    }
};
