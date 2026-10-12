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
        Schema::create('disease_mortality_profiles', function (Blueprint $table) {

            $table->id();

            $table->foreignUlid('disease_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
    |--------------------------------------------------------------------------
    | Mortality Level
    |--------------------------------------------------------------------------
    */

            $table->decimal('expected_mortality_min', 5, 2)
                ->nullable();

            $table->decimal('expected_mortality_max', 5, 2)
                ->nullable();

            /*
    |--------------------------------------------------------------------------
    | Mortality Pattern
    |--------------------------------------------------------------------------
    */

            $table->enum(
                'mortality_pattern',
                [
                    'peracute',
                    'acute',
                    'subacute',
                    'chronic'
                ]
            )->nullable();

            /*
    |--------------------------------------------------------------------------
    | Sudden Death
    |--------------------------------------------------------------------------
    */

            $table->boolean('sudden_deaths')
                ->default(false);

            /*
    |--------------------------------------------------------------------------
    | Peak Day
    |--------------------------------------------------------------------------
    */

            $table->unsignedInteger('peak_day_min')
                ->nullable();

            $table->unsignedInteger('peak_day_max')
                ->nullable();

            /*
    |--------------------------------------------------------------------------
    | Duration
    |--------------------------------------------------------------------------
    */

            $table->unsignedInteger('outbreak_duration_days')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disease_mortality_profiles');
    }
};
