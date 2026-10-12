<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diseases', function (Blueprint $table) {

            $table->ulid('id')->primary();

            $table->string('name');
            $table->string('slug')->unique();

            $table->text('description')->nullable();

            $table->string('agent_type')->nullable();
            $table->string('agent_name')->nullable();
            $table->string('family')->nullable();
            $table->string('genus')->nullable();

            $table->text('incubation_period')->nullable();

            $table->decimal('morbidity_min',5,2)->nullable();
            $table->decimal('morbidity_max',5,2)->nullable();

            $table->decimal('mortality_min',5,2)->nullable();
            $table->decimal('mortality_max',5,2)->nullable();

            $table->boolean('zoonotic')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diseases');
    }
};