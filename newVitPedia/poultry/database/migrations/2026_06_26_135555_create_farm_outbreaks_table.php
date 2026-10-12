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
       Schema::create('farm_outbreaks', function (Blueprint $table) {

    $table->ulid('id')->primary();

    $table->string('farm_name')->nullable();

    $table->foreignId('species_id')->nullable();

    $table->unsignedInteger('age_days')->nullable();

    $table->unsignedInteger('flock_size')->nullable();

    $table->unsignedInteger('affected_birds')->nullable();

    $table->unsignedInteger('dead_birds')->nullable();

    $table->decimal('morbidity_rate',5,2)->nullable();

    $table->decimal('mortality_rate',5,2)->nullable();

    $table->decimal('case_fatality_rate',5,2)->nullable();

    $table->boolean('vaccinated')->default(false);

    $table->boolean('mixed_species')->default(false);

    $table->boolean('poor_ventilation')->default(false);

    $table->boolean('wet_litter')->default(false);

    $table->boolean('recent_feed_change')->default(false);

    $table->boolean('new_birds_introduced')->default(false);

    $table->text('history')->nullable();

    $table->json('clinical_findings')->nullable();

    $table->json('suspected_diseases')->nullable();

    $table->json('final_diagnosis')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farm_outbreaks');
    }
};
