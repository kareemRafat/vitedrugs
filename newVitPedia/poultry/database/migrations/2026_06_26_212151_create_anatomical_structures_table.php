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
        Schema::create('anatomical_structures', function (Blueprint $table) {

            $table->id();

            $table->foreignId('body_system_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained(
                    'anatomical_structures'
                )
                ->nullOnDelete();

            $table->string('name');

            $table->string('display_name')
                ->nullable();

            $table->string('canonical_name')
                ->unique();

            $table->string('slug')
                ->unique();

            $table->text('description')
                ->nullable();

            $table->string('anatomical_region')
                ->nullable();

            $table->unsignedSmallInteger('hierarchy_level')
                ->default(0);

            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            $table->boolean('is_vital')
                ->default(false);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index([
                'body_system_id',
                'sort_order'
            ]);

            $table->index([
                'parent_id'
            ]);

            $table->unique([
                'body_system_id',
                'name'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'anatomical_structures'
        );
    }
};