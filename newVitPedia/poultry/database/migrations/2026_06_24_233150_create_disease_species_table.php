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
        Schema::create('disease_species', function (Blueprint $table) {

    $table->id();

    $table->foreignUlid('disease_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignUlid('species_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->unique([
        'disease_id',
        'species_id'
    ]);
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
