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
        Schema::create('disease_clinical_sign', function (Blueprint $table) {

            $table->foreignUlid('disease_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('clinical_sign_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('weight')
                ->default(5);

            $table->boolean('is_specific')
                ->default(false);

            $table->boolean('is_required')
                ->default(false);

            $table->primary([
                'disease_id',
                'clinical_sign_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disease_clinical_sign');
    }
};
