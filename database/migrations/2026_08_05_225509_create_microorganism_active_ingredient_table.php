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
        Schema::create('microorganism_active_ingredient', function (Blueprint $table) {
            $table->foreignId('microorganism_id')
                ->constrained('microorganisms')
                ->cascadeOnDelete();

            $table->foreignUlid('active_ingredient_id')
                ->constrained('active_ingredients')
                ->cascadeOnDelete();

            $table->string('sensitivity');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->primary([
                'microorganism_id',
                'active_ingredient_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('microorganism_active_ingredient');
    }
};
