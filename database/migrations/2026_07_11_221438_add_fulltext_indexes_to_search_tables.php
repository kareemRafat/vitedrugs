<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->fullText(['trade_name', 'trade_name_ar'], 'products_trade_name_fulltext');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->fullText(['name', 'name_ar'], 'companies_name_fulltext');
        });

        Schema::table('diseases', function (Blueprint $table) {
            $table->fullText(['name', 'name_ar'], 'diseases_name_fulltext');
        });

        Schema::table('active_ingredients', function (Blueprint $table) {
            $table->fullText(['name', 'name_ar'], 'active_ingredients_name_fulltext');
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('products', fn (Blueprint $t) => $t->dropIndex('products_trade_name_fulltext'));
        Schema::table('companies', fn (Blueprint $t) => $t->dropIndex('companies_name_fulltext'));
        Schema::table('diseases', fn (Blueprint $t) => $t->dropIndex('diseases_name_fulltext'));
        Schema::table('active_ingredients', fn (Blueprint $t) => $t->dropIndex('active_ingredients_name_fulltext'));
    }
};
