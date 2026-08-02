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
            $table->unique(['anatomical_structure_id', 'finding_id', 'modifier_id'], 'clinical_signs_semantic_unique');
        });
    }
    public function down(): void
    {
        Schema::table('clinical_signs', function (Blueprint $table) {
            $table->dropUnique('clinical_signs_semantic_unique');
        });
    }
};
