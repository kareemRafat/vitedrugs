<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('active_ingredient_drug_class', function (Blueprint $table) {

            $table->id();

            $table->foreignUlid('active_ingredient_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUlid('drug_class_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['active_ingredient_id', 'drug_class_id'],
                'ai_dc_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('active_ingredient_drug_class');
    }
};
