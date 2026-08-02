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
        | Transmission Types
        |--------------------------------------------------------------------------
        */

        Schema::create('transmission_types', function (Blueprint $table) {

            $table->id();

            $table->string('slug')->unique();

            $table->string('canonical_name')->unique();

            $table->string('display_name');

            $table->text('description')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Epidemiological Flags
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_airborne')
                ->default(false);

            $table->boolean('is_vector_borne')
                ->default(false);

            $table->boolean('is_foodborne')
                ->default(false);

            $table->boolean('is_waterborne')
                ->default(false);

            $table->boolean('is_fomite')
                ->default(false);

            $table->boolean('is_vertical')
                ->default(false);

            $table->boolean('is_direct_contact')
                ->default(false);

            $table->boolean('is_indirect_contact')
                ->default(false);

            $table->boolean('is_venereal')
                ->default(false);

            $table->boolean('is_zoonotic_related')
                ->default(false);

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | Disease ↔ Transmission Type
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'disease_transmission_type',
            function (Blueprint $table) {

                $table->id();

                $table->foreignUlid('disease_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId('transmission_type_id')
                    ->constrained()
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Ontology Confidence
                |--------------------------------------------------------------------------
                */

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
                    'transmission_type_id',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'disease_transmission_type'
        );

        Schema::dropIfExists(
            'transmission_types'
        );
    }
};
