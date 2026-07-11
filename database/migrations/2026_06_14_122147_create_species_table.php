<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('species', function (Blueprint $table) {

            $table->ulid('id')->primary();

            $table->string('name');
            $table->string('name_ar');

            $table->string('slug')->unique();

            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('species');
    }
};