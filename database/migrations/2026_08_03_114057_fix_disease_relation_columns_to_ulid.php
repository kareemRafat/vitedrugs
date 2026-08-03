<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The disease-linking KB tables were imported with bigint disease_id columns
     * referencing non-existent integer disease IDs, while diseases uses ULIDs.
     * Clear the orphaned rows, convert disease_id to ulid, and add real FKs.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('disease_clinical_sign', function (Blueprint $table) {
            $table->dropPrimary();
        });

        DB::table('disease_clinical_sign')->truncate();

        Schema::table('disease_clinical_sign', function (Blueprint $table) {
            $table->ulid('disease_id')->collation('utf8mb4_unicode_ci')->change();
            $table->primary(['disease_id', 'clinical_sign_id']);
            $table->foreign('disease_id')->references('id')->on('diseases')->cascadeOnDelete();
            $table->foreign('clinical_sign_id')->references('id')->on('clinical_signs')->cascadeOnDelete();
        });

        Schema::table('disease_host_species', function (Blueprint $table) {
            $table->dropUnique('disease_host_species_disease_id_host_species_id_unique');
        });

        DB::table('disease_host_species')->truncate();

        Schema::table('disease_host_species', function (Blueprint $table) {
            $table->ulid('disease_id')->collation('utf8mb4_unicode_ci')->change();
            $table->unique(['disease_id', 'host_species_id']);
            $table->foreign('disease_id')->references('id')->on('diseases')->cascadeOnDelete();
            $table->foreign('host_species_id')->references('id')->on('host_species')->cascadeOnDelete();
        });

        Schema::table('disease_classifications', function (Blueprint $table) {
            $table->dropUnique('disease_classifications_disease_id_unique');
        });

        DB::table('disease_classifications')->truncate();

        Schema::table('disease_classifications', function (Blueprint $table) {
            $table->ulid('disease_id')->collation('utf8mb4_unicode_ci')->change();
            $table->unique('disease_id');
            $table->foreign('disease_id')->references('id')->on('diseases')->cascadeOnDelete();
        });

        DB::table('medical_articles')
            ->whereNotNull('disease_id')
            ->update(['disease_id' => null]);

        Schema::table('medical_articles', function (Blueprint $table) {
            $table->dropIndex('medical_articles_disease_id_foreign');
        });

        Schema::table('medical_articles', function (Blueprint $table) {
            $table->ulid('disease_id')->collation('utf8mb4_unicode_ci')->nullable()->change();
            $table->foreign('disease_id')->references('id')->on('diseases')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('medical_articles', function (Blueprint $table) {
            $table->dropForeign(['disease_id']);
            $table->bigInteger('disease_id')->unsigned()->nullable()->change();
        });

        Schema::table('disease_classifications', function (Blueprint $table) {
            $table->dropUnique('disease_classifications_disease_id_unique');
            $table->bigInteger('disease_id')->unsigned()->change();
            $table->unique('disease_id');
        });

        Schema::table('disease_host_species', function (Blueprint $table) {
            $table->dropUnique('disease_host_species_disease_id_host_species_id_unique');
            $table->bigInteger('disease_id')->unsigned()->change();
            $table->unique(['disease_id', 'host_species_id']);
        });

        Schema::table('disease_clinical_sign', function (Blueprint $table) {
            $table->dropPrimary();
            $table->bigInteger('disease_id')->unsigned()->change();
            $table->primary(['disease_id', 'clinical_sign_id']);
        });
    }
};
