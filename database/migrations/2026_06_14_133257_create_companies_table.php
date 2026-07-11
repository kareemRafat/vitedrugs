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
        Schema::create('companies', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->string('name');
            $table->string('name_ar')->nullable();

            $table->string('slug')->unique();

            $table->foreignUlid('parent_company_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();

            $table->enum('company_type', [
                'manufacturer',
                'agent',
                'distributor',
                'marketing'
            ]);

            $table->string('logo')->nullable();

            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();

            $table->string('country')->nullable();

            $table->string('address_ar')->nullable();

            $table->string('governorate')->nullable();

            $table->string('google_maps_url')->nullable();

            $table->string('coverage_area')->nullable();

            $table->string('registration_number')->nullable();

            $table->string('website')->nullable();

            $table->string('email')->nullable();

            $table->string('phone')->nullable();

            $table->string('mobile')->nullable();

            $table->string('address')->nullable();

            $table->string('facebook')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('contact_person')->nullable();

            $table->string('whatsapp')->nullable();

            $table->string('telegram')->nullable();

            $table->string('youtube')->nullable();

            $table->string('instagram')->nullable();

            $table->boolean('is_active')->default(true);


            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
