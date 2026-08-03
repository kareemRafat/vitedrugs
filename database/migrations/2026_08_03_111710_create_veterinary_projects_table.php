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
        Schema::create('veterinary_projects', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->string('title');

            $table->string('slug')->unique();

            $table->text('summary')->nullable();

            $table->longText('content');

            $table->string('project_type')
                ->default('feasibility_study');

            $table->string('sector')->nullable();

            $table->boolean('featured')
                ->default(false);

            $table->boolean('is_published')
                ->default(true);

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veterinary_projects');
    }
};
