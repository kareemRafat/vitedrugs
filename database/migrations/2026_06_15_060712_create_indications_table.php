<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indications', function (Blueprint $table) {

            $table->ulid('id')->primary();

            $table->foreignUlid('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('description');

            $table->text('description_ar')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indications');
    }
};
