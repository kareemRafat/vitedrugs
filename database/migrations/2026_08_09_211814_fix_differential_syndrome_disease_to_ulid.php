<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The differential_syndrome_disease pivot was created with a bigint
     * disease_id column referencing non-existent integer disease IDs, while
     * diseases uses ULIDs. Clear the orphaned rows, convert disease_id to
     * ulid, and add real foreign keys plus a uniqueness guard.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::table('differential_syndrome_disease')->truncate();

        Schema::table('differential_syndrome_disease', function (Blueprint $table) {
            if (Schema::hasIndex('differential_syndrome_disease', 'dsd_syndrome_fk')) {
                $table->dropIndex('dsd_syndrome_fk');
            }

            if (Schema::hasIndex('differential_syndrome_disease', 'differential_syndrome_disease_disease_id_foreign')) {
                $table->dropIndex('differential_syndrome_disease_disease_id_foreign');
            }
        });

        Schema::table('differential_syndrome_disease', function (Blueprint $table) {
            $table->ulid('disease_id')->collation('utf8mb4_unicode_ci')->change();
            $table->unique(['differential_syndrome_id', 'disease_id'], 'dsd_syndrome_disease_unique');
            $table->foreign('differential_syndrome_id', 'dsd_syndrome_fk')
                ->references('id')
                ->on('differential_syndromes')
                ->cascadeOnDelete();
            $table->foreign('disease_id')
                ->references('id')
                ->on('diseases')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('differential_syndrome_disease', function (Blueprint $table) {
            $table->dropUnique('dsd_syndrome_disease_unique');
            $table->dropForeign('dsd_syndrome_fk');
            $table->dropForeign(['disease_id']);
            $table->bigInteger('disease_id')->unsigned()->change();
        });
    }
};
