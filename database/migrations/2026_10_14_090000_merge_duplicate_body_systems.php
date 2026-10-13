<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Nine body systems exist twice: `digestive` and `digestive_system` are two rows
 * with the same display name holding different structures, and the `_system` twin
 * is always the smaller one. The clinical findings filter lists them straight from
 * the table, so "Digestive System" has been offered twice in that dropdown — and
 * picking either half returned a different, silently truncated result.
 *
 * The bare canonical always wins: it holds the structures in every pair, so the
 * surviving row keeps its id and any shared filter link minted against it stays
 * valid. `Endocrine` / `Endocrine System` are the same concept written two ways
 * and merge on the same rule.
 *
 * The systems that hold no structures at all are left alone. `Otic`,
 * `Ophthalmic` and `Systemic` are real taxonomy a lesion atlas will want, and
 * deleting them would lose information to tidy a dropdown. The filter and the
 * body map stop listing them instead.
 */
return new class extends Migration
{
    /**
     * The duplicate pairs, loser => survivor. The survivor is the row that
     * already holds structures, and it keeps its own id.
     *
     * @var array<string, string>
     */
    private const PAIRS = [
        'cardiovascular_system' => 'cardiovascular',
        'digestive_system' => 'digestive',
        'endocrine_system' => 'endocrine',
        'integumentary_system' => 'integumentary',
        'musculoskeletal_system' => 'musculoskeletal',
        'nervous_system' => 'nervous',
        'reproductive_system' => 'reproductive',
        'respiratory_system' => 'respiratory',
        'urinary_system' => 'urinary',
    ];

    public function up(): void
    {
        foreach (self::PAIRS as $loser => $survivor) {
            $survivorId = DB::table('body_systems')->where('canonical_name', $survivor)->value('id');
            $loserId = DB::table('body_systems')->where('canonical_name', $loser)->value('id');

            if ($survivorId === null || $loserId === null) {
                continue;
            }

            DB::table('anatomical_structures')
                ->where('body_system_id', $loserId)
                ->update(['body_system_id' => $survivorId]);

            // The Arabic name is carried across so the merged system does not
            // fall back to English in RTL. `Nervous System` had no Arabic name
            // of its own; its twin does not either, but the write is harmless.
            $survivorNameAr = DB::table('body_systems')->where('id', $survivorId)->value('display_name_ar');

            if (! $survivorNameAr) {
                DB::table('body_systems')
                    ->where('id', $survivorId)
                    ->update(['display_name_ar' => DB::table('body_systems')->where('id', $loserId)->value('display_name_ar')]);
            }

            DB::table('body_systems')->where('id', $loserId)->delete();
        }
    }

    /**
     * Nothing records which structures were moved, so the reverse cannot be
     * exact. The rows come back empty and the structures stay where they are:
     * that is a lossy revert, but it leaves no dangling id behind.
     */
    public function down(): void
    {
        foreach (self::PAIRS as $loser => $survivor) {
            $survivor = DB::table('body_systems')->where('canonical_name', $survivor)->first();

            if ($survivor === null) {
                continue;
            }

            DB::table('body_systems')->insert([
                'canonical_name' => $loser,
                'display_name' => $survivor->display_name,
                'display_name_ar' => $survivor->display_name_ar,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
