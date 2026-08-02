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
            $table->foreignId('disease_id')->constrained('diseases')->cascadeOnDelete(); /* |-------------------------------------------------------------------------- | Host Species FK |-------------------------------------------------------------------------- */
            $table->foreignId('species_id')->constrained('host_species')->cascadeOnDelete(); /* |-------------------------------------------------------------------------- | Clinical susceptibility |-------------------------------------------------------------------------- */
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
