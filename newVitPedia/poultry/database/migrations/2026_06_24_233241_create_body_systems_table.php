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
        Schema::create('body_systems', function (Blueprint $table) {

            $table->id();

            $table->string('name')->unique();

            $table->string('display_name')->nullable();

            $table->string('canonical_name')
                ->unique();

            $table->string('slug')
                ->unique();

            $table->text('description')
                ->nullable();

            $table->string('icon')
                ->nullable();

            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('body_systems');
    }
};