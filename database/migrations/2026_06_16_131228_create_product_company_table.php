<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_company', function (Blueprint $table) {


            $table->foreignUlid('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUlid('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('role', [
                'manufacturer',
                'agent',
                'distributor',
                'marketing',
            ]);

            $table->timestamps();

            $table->unique([
                'product_id',
                'company_id',
                'role',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_company');
    }
};