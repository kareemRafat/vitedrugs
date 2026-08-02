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
        Schema::create('differential_syndrome_clinical_sign', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('differential_syndrome_id');

            $table->foreign(
                'differential_syndrome_id',
                'dscs_syndrome_fk'
            )

                ->references('id')

                ->on('differential_syndromes')

                ->cascadeOnDelete();

            $table->unsignedBigInteger('clinical_sign_id');

            $table->foreign(
                'clinical_sign_id',
                'dscs_sign_fk'
            )

                ->references('id')

                ->on('clinical_signs')

                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('differential_syndrome_clinical_sign');
    }
};
