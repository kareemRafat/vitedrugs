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
        Schema::create('disease_risk_factor', function (Blueprint $table) {

            $table->id();

            $table->foreignUlid('disease_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUlid('risk_factor_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unique([
                'disease_id',
                'risk_factor_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disease_risk_factor');
    }
};
