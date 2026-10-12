<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('diseases', function (Blueprint $table) {
            $table->string('domain')
                ->default('large_animal')
                ->after('is_internal');
        });

        // Every disease that predates the Poultry section belongs to Large Animals.
        DB::table('diseases')->whereNull('domain')->update(['domain' => 'large_animal']);

        Schema::table('diseases', function (Blueprint $table) {
            $table->index(['domain', 'is_active'], 'diseases_domain_is_active_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diseases', function (Blueprint $table) {
            $table->dropIndex('diseases_domain_is_active_index');
            $table->dropColumn('domain');
        });
    }
};
