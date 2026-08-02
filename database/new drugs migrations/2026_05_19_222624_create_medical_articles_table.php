<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_articles', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Core
            |--------------------------------------------------------------------------
            */

            $table->string('title');

            $table->string('slug')->unique();

            $table->longText('content');


            /*
            |--------------------------------------------------------------------------
            | Relations
            |--------------------------------------------------------------------------
            */

            $table->foreignId('disease_id')

                ->nullable()

                ->constrained()

                ->nullOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */

            $table->string('species')

                ->nullable();

            $table->string('article_type')

                ->default('disease_reference');

            $table->boolean('is_published')

                ->default(true);


            /*
            |--------------------------------------------------------------------------
            | SEO / Search
            |--------------------------------------------------------------------------
            */

            $table->text('summary')

                ->nullable();


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_articles');
    }
};