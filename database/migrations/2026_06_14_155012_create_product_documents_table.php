<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_documents', function (Blueprint $table) {

            $table->ulid('id')->primary();

            $table->foreignUlid('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');

            $table->string('file_path');

            $table->enum('type', [
                'leaflet',
                'datasheet',
                'brochure',
                'certificate',
            ]);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_documents');
    }
};
