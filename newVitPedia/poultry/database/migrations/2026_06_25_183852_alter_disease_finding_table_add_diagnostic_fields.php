<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('disease_finding', function (Blueprint $table) {

            $table->boolean('required_for_diagnosis')
                ->default(false)
                ->after('is_pathognomonic');

            $table->boolean('negative_finding')
                ->default(false)
                ->after('required_for_diagnosis');

            $table->decimal(
                'likelihood_ratio_positive',
                8,
                2
            )->nullable()
             ->after('negative_finding');

            $table->decimal(
                'likelihood_ratio_negative',
                8,
                2
            )->nullable()
             ->after('likelihood_ratio_positive');
        });
    }

    public function down(): void
    {
        Schema::table('disease_finding', function (Blueprint $table) {

            $table->dropColumn([
                'required_for_diagnosis',
                'negative_finding',
                'likelihood_ratio_positive',
                'likelihood_ratio_negative'
            ]);
        });
    }
};