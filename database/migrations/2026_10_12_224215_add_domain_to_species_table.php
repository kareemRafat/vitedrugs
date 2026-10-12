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
        Schema::table('species', function (Blueprint $table) {
            $table->string('domain')
                ->default('large_animal')
                ->after('is_active');
        });

        // Every species that predates the Poultry section belongs to Large Animals.
        DB::table('species')->whereNull('domain')->update(['domain' => 'large_animal']);

        Schema::table('species', function (Blueprint $table) {
            $table->index(['domain', 'is_active'], 'species_domain_is_active_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('species', function (Blueprint $table) {
            $table->dropIndex('species_domain_is_active_index');
            $table->dropColumn('domain');
        });
    }
};
