<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosages', function (Blueprint $table) {

            $table->ulid('id')->primary();

            $table->foreignUlid('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUlid('species_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('dosage');

            $table->string('route')
                ->nullable();

            $table->string('duration')
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosages');
    }
};
