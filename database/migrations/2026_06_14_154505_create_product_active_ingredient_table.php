<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_active_ingredient', function (Blueprint $table) {

            $table->id();

            $table->foreignUlid('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUlid('active_ingredient_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('strength')
                ->nullable();

            $table->string('unit')
                ->nullable();

            $table->integer('sort_order')
                ->default(0);

            $table->timestamps();

            $table->unique(
                ['product_id', 'active_ingredient_id'],
                'product_ai_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_active_ingredient');
    }
};