<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The synonyms table was created with a bigint synonymable_id, but the
     * polymorphic targets mix ULID (Disease) and bigint (ClinicalSign,
     * Finding, Modifier) keys. Clear the orphaned rows that reference models
     * or IDs that no longer exist, then widen the column to a string so ULIDs
     * can be stored.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::table('synonyms')
            ->where('synonymable_type', 'App\Models\Drug')
            ->orWhere(function ($query) {
                $query->where('synonymable_type', 'App\Models\Disease')
                    ->where('synonymable_id', 'REGEXP', '^[0-9]+$');
            })
            ->delete();

        Schema::table('synonyms', function (Blueprint $table) {
            $table->string('synonymable_id', 36)->collation('utf8mb4_unicode_ci')->change();
        });
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('synonyms', function (Blueprint $table) {
            $table->unsignedBigInteger('synonymable_id')->change();
        });
    }
};
