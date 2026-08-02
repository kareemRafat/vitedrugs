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
        Schema::create('drug_therapeutic_use', function (Blueprint $table) {

            $table->id();

            $table->foreignId('drug_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('therapeutic_use_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique([
                'drug_id',
                'therapeutic_use_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'drug_therapeutic_use'
        );
    }
};
