<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disease_age_groups', function (Blueprint $table) {

            $table->id();

            $table->foreignUlid('disease_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('min_days')
                ->nullable();

            $table->unsignedInteger('max_days')
                ->nullable();

            $table->string('description')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disease_age_groups');
    }
};