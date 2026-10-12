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
        Schema::create('diagnostic_validations', function (Blueprint $table) {

            $table->id();

            $table->foreignUlid('predicted_disease_id')
                ->constrained('diseases')
                ->cascadeOnDelete();

            $table->foreignUlid('confirmed_disease_id')
                ->nullable()
                ->constrained('diseases')
                ->nullOnDelete();

            $table->unsignedTinyInteger('predicted_rank')
                ->default(1);

            $table->decimal(
                'predicted_confidence',
                5,
                2
            )->nullable();

            $table->boolean('is_correct')
                ->default(false);

            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnostic_validations');
    }
};
