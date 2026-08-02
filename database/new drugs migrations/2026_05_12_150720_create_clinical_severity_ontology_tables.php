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
        | Clinical Severity Ontology
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'clinical_severity_levels',
            function (Blueprint $table) {

                $table->id();

                $table->string('slug')
                    ->unique();

                $table->string('canonical_name')
                    ->unique();

                $table->string('display_name');

                /*
                |--------------------------------------------------------------------------
                | Severity Intelligence
                |--------------------------------------------------------------------------
                */

                $table->unsignedTinyInteger(
                    'severity_score'
                );

                $table->boolean('is_emergency')
                    ->default(false);

                $table->boolean('is_life_threatening')
                    ->default(false);

                $table->boolean('is_high_mortality')
                    ->default(false);

                $table->boolean('requires_isolation')
                    ->default(false);

                $table->boolean('requires_rapid_intervention')
                    ->default(false);

                $table->timestamps();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Clinical Course Ontology
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'clinical_course_types',
            function (Blueprint $table) {

                $table->id();

                $table->string('slug')
                    ->unique();

                $table->string('canonical_name')
                    ->unique();

                $table->string('display_name');

                /*
                |--------------------------------------------------------------------------
                | Course Intelligence
                |--------------------------------------------------------------------------
                */

                $table->boolean('is_peracute')
                    ->default(false);

                $table->boolean('is_acute')
                    ->default(false);

                $table->boolean('is_subacute')
                    ->default(false);

                $table->boolean('is_chronic')
                    ->default(false);

                $table->boolean('is_progressive')
                    ->default(false);

                $table->boolean('is_fatal')
                    ->default(false);

                $table->timestamps();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Disease ↔ Severity
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'disease_severity',
            function (Blueprint $table) {

                $table->id();

                $table->foreignId('disease_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId(
                    'clinical_severity_level_id'
                )
                    ->constrained()
                    ->cascadeOnDelete();

                $table->boolean('is_primary')
                    ->default(true);

                $table->unsignedTinyInteger(
                    'confidence_score'
                )->default(100);

                $table->timestamps();

                /*
                |--------------------------------------------------------------------------
                | Short Index Name
                |--------------------------------------------------------------------------
                */

                $table->unique(
                    [
                        'disease_id',
                        'clinical_severity_level_id'
                    ],
                    'dsv_unique'
                );
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Disease ↔ Clinical Course
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'disease_clinical_course',
            function (Blueprint $table) {

                $table->id();

                $table->foreignId('disease_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignId(
                    'clinical_course_type_id'
                )
                    ->constrained()
                    ->cascadeOnDelete();

                $table->boolean('is_primary')
                    ->default(true);

                $table->unsignedTinyInteger(
                    'confidence_score'
                )->default(100);

                $table->timestamps();

                /*
                |--------------------------------------------------------------------------
                | Short Index Name
                |--------------------------------------------------------------------------
                */

                $table->unique(
                    [
                        'disease_id',
                        'clinical_course_type_id'
                    ],
                    'dcc_unique'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'disease_clinical_course'
        );

        Schema::dropIfExists(
            'disease_severity'
        );

        Schema::dropIfExists(
            'clinical_course_types'
        );

        Schema::dropIfExists(
            'clinical_severity_levels'
        );
    }
};