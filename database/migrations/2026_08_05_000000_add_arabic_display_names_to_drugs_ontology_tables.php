<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('body_systems', function (Blueprint $table) {
            $table->string('display_name_ar')->nullable()->after('display_name');
        });

        Schema::table('anatomical_structures', function (Blueprint $table) {
            $table->string('display_name_ar')->nullable()->after('display_name');
        });

        Schema::table('findings', function (Blueprint $table) {
            $table->string('display_name_ar')->nullable()->after('display_name');
        });

        Schema::table('clinical_signs', function (Blueprint $table) {
            $table->string('display_name_ar')->nullable()->after('display_name');
        });

        Schema::table('host_species', function (Blueprint $table) {
            $table->string('display_name_ar')->nullable()->after('display_name');
        });

        Schema::table('differential_syndromes', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('differential_syndromes', function (Blueprint $table) {
            $table->dropColumn('name_ar');
        });

        Schema::table('host_species', function (Blueprint $table) {
            $table->dropColumn('display_name_ar');
        });

        Schema::table('clinical_signs', function (Blueprint $table) {
            $table->dropColumn('display_name_ar');
        });

        Schema::table('findings', function (Blueprint $table) {
            $table->dropColumn('display_name_ar');
        });

        Schema::table('anatomical_structures', function (Blueprint $table) {
            $table->dropColumn('display_name_ar');
        });

        Schema::table('body_systems', function (Blueprint $table) {
            $table->dropColumn('display_name_ar');
        });
    }
};
