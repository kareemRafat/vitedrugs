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
        Schema::create('active_ingredients', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->string('name');

            $table->string('name_ar')->nullable();

            $table->string('slug')->unique();

            $table->text('description')->nullable();

            $table->text('description_ar')->nullable();

            $table->longText('indications')->nullable();

            $table->longText('contraindications')->nullable();

            $table->longText('precautions')->nullable();

            $table->longText('side_effects')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_ingredients');
    }
};
