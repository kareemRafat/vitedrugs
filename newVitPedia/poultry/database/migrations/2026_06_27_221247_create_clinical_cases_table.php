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
        Schema::create('clinical_cases', function (Blueprint $table) {

            $table->id();

            $table->string('title');

            $table->string('slug')->unique();

            $table->foreignUlid('species_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignUlid('disease_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->unsignedInteger('age_days')
                ->nullable();

            $table->unsignedInteger('flock_size')
                ->nullable();

            $table->unsignedInteger('affected_birds')
                ->nullable();

            $table->unsignedInteger('dead_birds')
                ->nullable();

            $table->text('history')
                ->nullable();

            $table->longText('clinical_findings')
                ->nullable();

            $table->longText('necropsy_findings')
                ->nullable();

            $table->longText('laboratory_results')
                ->nullable();

            $table->longText('diagnosis')
                ->nullable();

            $table->longText('treatment')
                ->nullable();

            $table->longText('outcome')
                ->nullable();

            $table->json('images')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_cases');
    }
};
