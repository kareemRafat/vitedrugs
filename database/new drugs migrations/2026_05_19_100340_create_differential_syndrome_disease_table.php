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
        Schema::create('differential_syndrome_disease', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('differential_syndrome_id');

            $table->foreign(
                'differential_syndrome_id',
                'dsd_syndrome_fk'
            )

                ->references('id')

                ->on('differential_syndromes')

                ->cascadeOnDelete();

            $table->foreignId('disease_id')

                ->constrained()

                ->cascadeOnDelete();

            $table->integer('boost_score')

                ->default(15);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('differential_syndrome_disease');
    }
};
