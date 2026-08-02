<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transmission_routes', function (Blueprint $table) {
            $table->id();

            $table->string('slug')->unique();

            $table->string('canonical_name')->unique();

            $table->string('category')->nullable();

            $table->text('definition')->nullable();

            $table->timestamps();
        });

        Schema::create('disease_transmission_routes', function (Blueprint $table) {
            $table->id();

            $table->foreignUlid('disease_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('transmission_route_id')
                ->constrained('transmission_routes')
                ->cascadeOnDelete();

            $table->boolean('is_primary')->default(false);

            $table->boolean('is_confirmed')->default(true);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique([
                'disease_id',
                'transmission_route_id',
            ], 'disease_transmission_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disease_transmission_routes');

        Schema::dropIfExists('transmission_routes');
    }
};
