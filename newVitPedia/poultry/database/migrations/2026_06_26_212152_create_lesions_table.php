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
        Schema::create('lesions', function (Blueprint $table) {

            $table->id();

            $table->string('canonical_name')->unique();

            $table->string('display_name');

            $table->foreignId('body_system_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('anatomical_structure_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesions');
    }
};
