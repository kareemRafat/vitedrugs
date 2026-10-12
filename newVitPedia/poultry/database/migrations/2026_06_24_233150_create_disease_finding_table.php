<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disease_finding', function (Blueprint $table) {

            $table->id();

            $table->foreignUlid('disease_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUlid('finding_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('weight')->default(5);

            $table->enum('specificity', [
                'low',
                'medium',
                'high',
                'very_high'
            ])->default('medium');

            $table->boolean('is_pathognomonic')
                ->default(false);

            $table->unique([
                'disease_id',
                'finding_id'
            ]);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disease_finding');
    }
};