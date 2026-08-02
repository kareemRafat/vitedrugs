<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('host_species', function (Blueprint $table) {

            $table->id();

            $table->string(
                'canonical_name'
            )->unique();

            $table->string(
                'display_name'
            );

            $table->string(
                'taxonomy_group'
            )->nullable();

            $table->boolean(
                'is_domestic'
            )->default(false);

            $table->boolean(
                'is_wildlife'
            )->default(false);

            $table->boolean(
                'is_human'
            )->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'host_species'
        );
    }
};