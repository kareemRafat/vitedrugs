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
        Schema::create('disease_epidemiology_profiles', function (Blueprint $table) {

            $table->id();

            $table->foreignUlid('disease_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
    |--------------------------------------------------------------------------
    | Age
    |--------------------------------------------------------------------------
    */

            $table->unsignedInteger('min_age_days')->nullable();

            $table->unsignedInteger('max_age_days')->nullable();

            /*
    |--------------------------------------------------------------------------
    | Morbidity / Mortality
    |--------------------------------------------------------------------------
    */

            $table->decimal('expected_morbidity_min', 5, 2)->nullable();

            $table->decimal('expected_morbidity_max', 5, 2)->nullable();

            $table->decimal('expected_mortality_min', 5, 2)->nullable();

            $table->decimal('expected_mortality_max', 5, 2)->nullable();

            /*
    |--------------------------------------------------------------------------
    | Vaccination
    |--------------------------------------------------------------------------
    */

            $table->boolean('common_in_unvaccinated')
                ->default(false);

            /*
    |--------------------------------------------------------------------------
    | Outbreak Pattern
    |--------------------------------------------------------------------------
    */

            $table->enum(
                'outbreak_speed',
                [
                    'hyperacute',
                    'acute',
                    'subacute',
                    'chronic'
                ]
            )->nullable();

            /*
    |--------------------------------------------------------------------------
    | Seasonality
    |--------------------------------------------------------------------------
    */

            $table->json('common_seasons')->nullable();

            /*
    |--------------------------------------------------------------------------
    | Management
    |--------------------------------------------------------------------------
    */

            $table->boolean('associated_with_wet_litter')
                ->default(false);

            $table->boolean('associated_with_poor_ventilation')
                ->default(false);

            $table->boolean('associated_with_mixed_ages')
                ->default(false);

            $table->boolean('associated_with_new_birds')
                ->default(false);

            /*
    |--------------------------------------------------------------------------
    | Production Type
    |--------------------------------------------------------------------------
    */

            $table->json('production_types')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disease_epidemiology_profiles');
    }
};
