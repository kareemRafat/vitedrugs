<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alternatives', function (Blueprint $table) {

            $table->ulid('id')->primary();

            $table->foreignUlid('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUlid('alternative_product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->enum('type', [
                'commercial',
                'therapeutic',
                'economic',
            ]);

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->unique(
                ['product_id', 'alternative_product_id'],
                'product_alternative_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alternatives');
    }
};