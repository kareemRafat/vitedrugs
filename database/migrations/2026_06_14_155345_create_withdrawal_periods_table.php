<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawal_periods', function (Blueprint $table) {

            $table->ulid('id')->primary();

            $table->foreignUlid('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUlid('species_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('meat_days')
                ->nullable();

            $table->integer('milk_days')
                ->nullable();

            $table->integer('egg_days')
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal_periods');
    }
};
