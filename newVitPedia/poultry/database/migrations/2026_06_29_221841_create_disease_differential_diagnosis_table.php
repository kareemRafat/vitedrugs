<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'disease_differential_diagnosis',
            function (Blueprint $table) {

                $table->id();

                $table->foreignUlid('disease_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignUlid('differential_disease_id')
                    ->constrained(
                        table: 'diseases'
                    )
                    ->cascadeOnDelete();

                $table->unsignedTinyInteger('similarity')
                    ->nullable();

                $table->text('notes')
                    ->nullable();

                $table->timestamps();

                $table->unique(
                    ['disease_id', 'differential_disease_id'],
                    'diff_unique'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'disease_differential_diagnosis'
        );
    }
};
