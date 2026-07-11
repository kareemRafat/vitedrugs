<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drug_interactions', function (Blueprint $table) {

            $table->ulid('id')->primary();

            $table->foreignUlid('active_ingredient_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('interacting_drug');

            $table->enum('severity', [
                'minor',
                'moderate',
                'major',
            ])->default('moderate');

            $table->text('effect');

            $table->text('recommendation')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drug_interactions');
    }
};
