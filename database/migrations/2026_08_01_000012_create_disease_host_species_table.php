<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'disease_host_species',
            function (Blueprint $table) {

                $table->id();

                $table->foreignUlid(
                    'disease_id'
                )
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId(
                    'host_species_id'
                )
                    ->constrained()
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Epidemiological Role
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'role'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Susceptibility
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'susceptibility'
                )->nullable();

                /*
                |--------------------------------------------------------------------------
                | Ontology Flags
                |--------------------------------------------------------------------------
                */

                $table->boolean(
                    'is_primary_host'
                )->default(false);

                $table->boolean(
                    'is_reservoir'
                )->default(false);

                $table->boolean(
                    'is_vector'
                )->default(false);

                $table->boolean(
                    'is_carrier'
                )->default(false);

                $table->boolean(
                    'is_incidental_host'
                )->default(false);

                /*
                |--------------------------------------------------------------------------
                | Notes
                |--------------------------------------------------------------------------
                */

                $table->text(
                    'notes'
                )->nullable();

                $table->timestamps();

                /*
                |--------------------------------------------------------------------------
                | Duplicate Protection
                |--------------------------------------------------------------------------
                */

                $table->unique([
                    'disease_id',
                    'host_species_id',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'disease_host_species'
        );
    }
};
