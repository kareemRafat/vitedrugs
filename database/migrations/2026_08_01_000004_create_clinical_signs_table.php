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
        Schema::create('clinical_signs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anatomical_structure_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('finding_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('modifier_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('canonical_name');
            $table->string('display_name');
            $table->string('stage')
                ->default('clinical');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_signs');
    }
};
