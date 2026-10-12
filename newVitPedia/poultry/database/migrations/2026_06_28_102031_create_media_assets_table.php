<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_assets', function (Blueprint $table) {

            $table->ulid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | Polymorphic Relationship
            |--------------------------------------------------------------------------
            */

            $table->string('mediable_type');

            $table->ulid('mediable_id');

            /*
            |--------------------------------------------------------------------------
            | Image Information
            |--------------------------------------------------------------------------
            */

            $table->string('image_path');

            $table->string('title');

            $table->text('caption')
                ->nullable();

            $table->enum('image_type', [

                'clinical',
                'gross_lesion',
                'necropsy',
                'histopathology',
                'microscopy',
                'radiology',
                'anatomy',
                'clinical_case',
                'other'

            ])->default('clinical');

            /*
            |--------------------------------------------------------------------------
            | Display Settings
            |--------------------------------------------------------------------------
            */

            $table->boolean('featured')
                ->default(false);

            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Attribution
            |--------------------------------------------------------------------------
            */

            $table->string('source')
                ->nullable();

            $table->string('copyright')
                ->nullable();

            $table->timestamps();

            $table->index([
                'mediable_type',
                'mediable_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'media_assets'
        );
    }
};