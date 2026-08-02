<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Reservoirs
        |--------------------------------------------------------------------------
        */

        Schema::create('reservoirs', function (Blueprint $table) {

            $table->id();

            $table->string('slug')
                ->unique();

            $table->string('canonical_name')
                ->unique();

            $table->string('display_name');

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Reservoir Intelligence
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_wildlife')
                ->default(false);

            $table->boolean('is_domestic')
                ->default(false);

            $table->boolean('is_bird')
                ->default(false);

            $table->boolean('is_rodent')
                ->default(false);

            $table->boolean('is_ruminant')
                ->default(false);

            $table->boolean('is_human_related')
                ->default(false);

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Vectors
        |--------------------------------------------------------------------------
        */

        Schema::create('vectors', function (Blueprint $table) {

            $table->id();

            $table->string('slug')
                ->unique();

            $table->string('canonical_name')
                ->unique();

            $table->string('display_name');

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Vector Intelligence
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_arthropod')
                ->default(false);

            $table->boolean('is_tick')
                ->default(false);

            $table->boolean('is_mosquito')
                ->default(false);

            $table->boolean('is_fly')
                ->default(false);

            $table->boolean('is_mechanical')
                ->default(false);

            $table->boolean('is_biological')
                ->default(false);

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Disease ↔ Reservoir
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'disease_reservoir',
            function (Blueprint $table) {

                $table->id();

                $table->foreignUlid('disease_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('reservoir_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->boolean('is_primary')
                    ->default(false);

                $table->boolean('is_confirmed')
                    ->default(true);

                $table->unsignedTinyInteger(
                    'confidence_score'
                )->default(100);

                $table->text('notes')
                    ->nullable();

                $table->timestamps();

                $table->unique([
                    'disease_id',
                    'reservoir_id',
                ]);
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Disease ↔ Vector
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'disease_vector',
            function (Blueprint $table) {

                $table->id();

                $table->foreignUlid('disease_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('vector_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->boolean('is_primary')
                    ->default(false);

                $table->boolean('is_confirmed')
                    ->default(true);

                $table->unsignedTinyInteger(
                    'confidence_score'
                )->default(100);

                $table->text('notes')
                    ->nullable();

                $table->timestamps();

                $table->unique([
                    'disease_id',
                    'vector_id',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'disease_vector'
        );

        Schema::dropIfExists(
            'disease_reservoir'
        );

        Schema::dropIfExists(
            'vectors'
        );

        Schema::dropIfExists(
            'reservoirs'
        );
    }
};
