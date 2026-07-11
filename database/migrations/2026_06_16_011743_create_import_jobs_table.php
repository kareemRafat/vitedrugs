<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_jobs', function (Blueprint $table) {

            $table->ulid('id')->primary();

            $table->string('source_file');

            $table->enum('source_type', [
                'pdf',
                'docx',
                'html',
                'json',
                'txt',
            ]);

            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
            ])->default('pending');

            $table->unsignedInteger('total_products')
                ->default(0);

            $table->unsignedInteger('imported_products')
                ->default(0);

            $table->unsignedInteger('failed_products')
                ->default(0);

            $table->longText('extracted_json')
                ->nullable();

            $table->longText('error_message')
                ->nullable();

            $table->timestamp('started_at')
                ->nullable();

            $table->timestamp('completed_at')
                ->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_jobs');
    }
};
