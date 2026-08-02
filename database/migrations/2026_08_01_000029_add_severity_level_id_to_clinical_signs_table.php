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
        Schema::table('clinical_signs', function (Blueprint $table) {
            $table->foreignId('severity_level_id')->nullable()->after('modifier_id')->constrained('clinical_severity_levels')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('clinical_signs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('severity_level_id');
        });
    }
};
