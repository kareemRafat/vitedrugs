<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disease_classifications', function (Blueprint $table) {

            $table->id();

            $table->foreignId('disease_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Infectiousness
            |--------------------------------------------------------------------------
            |
            | infectious
            | non_infectious
            |
            */

            $table->string('infectiousness')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Transmissibility
            |--------------------------------------------------------------------------
            |
            | contagious
            | non_contagious
            |
            */

            $table->string('transmissibility')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Etiology Type
            |--------------------------------------------------------------------------
            |
            | bacterial
            | viral
            | fungal
            | parasitic
            | multifactorial
            |
            */

            $table->string('etiology_type')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Occurrence Pattern
            |--------------------------------------------------------------------------
            |
            | sporadic
            | endemic
            | epizootic
            | pandemic
            | exotic
            |
            */

            $table->json('occurrence_patterns')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Disease Course
            |--------------------------------------------------------------------------
            |
            | peracute
            | acute
            | subacute
            | chronic
            |
            */

            $table->json('disease_courses')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Regulatory Metadata
            |--------------------------------------------------------------------------
            */

            $table->boolean('notifiable')
                ->default(false);

            $table->boolean('zoonotic')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | OIE Classification
            |--------------------------------------------------------------------------
            */

            $table->string('oie_category')
                ->nullable();

            $table->timestamps();

            $table->unique('disease_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disease_classifications');
    }
};