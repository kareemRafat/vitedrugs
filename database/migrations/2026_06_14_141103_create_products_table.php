<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {

            $table->ulid('id')->primary();

            $table->foreignUlid('company_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('trade_name');

            $table->string('trade_name_ar')
                ->nullable();

            $table->string('slug')
                ->unique();

            $table->foreignUlid('dosage_form_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('product_type', [
                'pharmaceutical',
                'vaccine',
                'supplement',
                'feed_additive',
                'disinfectant',
                'biological',
            ])
                ->default('pharmaceutical');

            $table->text('description')
                ->nullable();

            $table->text('description_ar')
                ->nullable();

            $table->string('package_size')
                ->nullable();

            $table->text('storage_conditions')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
