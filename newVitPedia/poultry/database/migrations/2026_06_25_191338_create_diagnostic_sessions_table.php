<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnostic_sessions', function (Blueprint $table) {

            $table->ulid('id')->primary();

            $table->string('species_id')->nullable();

            $table->integer('age_days')->nullable();

            $table->json('positive_findings')
                ->nullable();

            $table->json('negative_findings')
                ->nullable();

            $table->json('asked_questions')
                ->nullable();

            $table->json('current_results')
                ->nullable();

            $table->boolean('is_finished')
                ->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'diagnostic_sessions'
        );
    }
};