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
       Schema::create('disease_lesion', function (Blueprint $table) {

    $table->id();

    $table->foreignUlid('disease_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('lesion_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->integer('weight')->default(5);

    $table->enum(
        'specificity',
        [
            'low',
            'medium',
            'high',
            'very_high'
        ]
    )->nullable();

    $table->boolean('is_pathognomonic')
        ->default(false);

    $table->boolean('is_confirmatory')
        ->default(false);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disease_lesion');
    }
};
