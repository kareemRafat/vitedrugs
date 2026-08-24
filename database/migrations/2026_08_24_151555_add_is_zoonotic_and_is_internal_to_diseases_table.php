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

            $table->boolean('is_zoonotic')

                ->default(false)

                ->after('is_active');

            $table->boolean('is_internal')

                ->default(false)

                ->after('is_zoonotic');
        });

        DB::statement('
            update diseases
            set is_zoonotic = true
            where exists (
                select 1
                from disease_classifications
                where disease_classifications.disease_id = diseases.id
                    and disease_classifications.zoonotic = true
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diseases', function (Blueprint $table) {

            $table->dropColumn(['is_zoonotic', 'is_internal']);
        });
    }
};
