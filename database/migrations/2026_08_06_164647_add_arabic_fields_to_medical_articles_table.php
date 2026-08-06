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
        Schema::table('medical_articles', function (Blueprint $table) {
            $table->string('title_ar')->nullable()->after('title');
            $table->longText('content_ar')->nullable()->after('content');
            $table->string('species_ar')->nullable()->after('species');
            $table->text('summary_ar')->nullable()->after('summary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_articles', function (Blueprint $table) {
            $table->dropColumn(['title_ar', 'content_ar', 'species_ar', 'summary_ar']);
        });
    }
};
