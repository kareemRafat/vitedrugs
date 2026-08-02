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
        Schema::create('disease_species', function (Blueprint $table) { /* |-------------------------------------------------------------------------- | Disease FK |-------------------------------------------------------------------------- */
            $table->foreignUlid('disease_id')->constrained('diseases')->cascadeOnDelete();
            $table->unsignedBigInteger('species_id')->index();
            $table->string('susceptibility')->nullable(); /* |-------------------------------------------------------------------------- | Composite primary key |-------------------------------------------------------------------------- */
            $table->primary(['disease_id', 'species_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disease_species');
    }
};
