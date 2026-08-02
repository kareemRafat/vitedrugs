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
        Schema::create('microorganisms', function (Blueprint $table) {

            $table->id();

            // Basic identity
            $table->string('name')->index();

            $table->string('normalized_name')
                ->nullable()
                ->index();

            // Taxonomy
            $table->string('kingdom')->nullable()->index();

            $table->string('phylum')->nullable();

            $table->string('class')->nullable();

            $table->string('order')->nullable();

            $table->string('family')->nullable();

            $table->string('genus')
                ->nullable()
                ->index();

            $table->string('species')
                ->nullable()
                ->index();

            // Organism type
            $table->enum('microorganism_type', [
                'bacteria',
                'fungus',
                'virus',
                'parasite',
                'other',
            ])->default('bacteria')->index();

            // Optional metadata
            $table->boolean('is_pathogenic')
                ->default(false);

            // Search layer
            $table->longText('searchable_text')
                ->nullable();

            // Canonical JSON storage
            $table->json('json_data');

            // Source file tracking
            $table->string('source_json_file')
                ->nullable();

            // Flexible tags
            $table->json('tags')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('microorganisms');
    }
};
