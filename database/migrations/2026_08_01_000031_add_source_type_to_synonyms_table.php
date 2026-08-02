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
        Schema::table('synonyms', function (Blueprint $table) {
            $table->string('source_type')->nullable()->after('normalized_term');
        });
    }

    public function down(): void
    {
        Schema::table('synonyms', function (Blueprint $table) {
            $table->dropColumn('source_type');
        });
    }
};
