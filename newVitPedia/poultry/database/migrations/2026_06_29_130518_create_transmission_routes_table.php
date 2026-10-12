<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transmission_routes', function (Blueprint $table) {

            $table->ulid('id')->primary();

            $table->string('name')->unique();

            $table->string('slug')->unique();

            $table->string('display_name');

            $table->text('description')
                ->nullable();

            $table->timestamps();
        });

        Schema::create(
            'disease_transmission_route',
            function (Blueprint $table) {

                $table->id();

                $table->foreignUlid('disease_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->foreignUlid(
                    'transmission_route_id'
                )
                    ->constrained()
                    ->cascadeOnDelete();

                $table->unique(
                    [
                        'disease_id',
                        'transmission_route_id'
                    ],
                    'disease_transmission_unique'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'disease_transmission_route'
        );

        Schema::dropIfExists(
            'transmission_routes'
        );
    }
};
