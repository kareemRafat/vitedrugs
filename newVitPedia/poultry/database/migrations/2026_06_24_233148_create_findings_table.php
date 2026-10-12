<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('findings', function (Blueprint $table) {

            $table->ulid('id')->primary();

            $table->string('canonical_name')->unique();

            $table->string('display_name');

            $table->string('slug')
            ->nullable()
            ->unique();

            $table->enum('type', [
                'clinical_sign',
                'gross_lesion'
            ]);

            $table->string('body_system')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('findings');
    }
};