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
        Schema::create('disease_microorganism', function (Blueprint $table) {
            $table->foreignUlid('disease_id')
                ->constrained('diseases')
                ->cascadeOnDelete();

            $table->foreignId('microorganism_id')
                ->constrained('microorganisms')
                ->cascadeOnDelete();

            $table->string('role')
                ->default('cause');

            $table->timestamps();

            $table->primary([
                'disease_id',
                'microorganism_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disease_microorganism');
    }
};
