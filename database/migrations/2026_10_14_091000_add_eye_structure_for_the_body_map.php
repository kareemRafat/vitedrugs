<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The body map illustration labels the eye, and the vocabulary had no word for it.
 *
 * The `ocular` body system has been in the table since the taxonomy landed and has
 * never held a structure, which is why the eye was missing rather than unknown:
 * nothing had ever needed it. The illustration is now drawn, so something does.
 *
 * `ophthalmic` also exists and is also empty. `ocular` is the one the body map and
 * the anatomy vocabulary both key on, so the eye goes there and `ophthalmic` is
 * left as the taxonomy term it was added to be.
 *
 * The structure carries no `domain`, which is what puts it on the poultry map and
 * on the large-animal map alike — an eye is not species specific, and scoping it
 * would be the kind of tidy that loses information later.
 */
return new class extends Migration
{
    public function up(): void
    {
        $ocularId = DB::table('body_systems')
            ->where('canonical_name', 'ocular')
            ->value('id');

        if ($ocularId === null) {
            return;
        }

        // Every other system carries an Arabic name. Without one the system reads
        // in English under RTL, which on the body map means the organ heading and
        // the system line under it disagree about which language they are in.
        if (! DB::table('body_systems')->where('id', $ocularId)->value('display_name_ar')) {
            DB::table('body_systems')
                ->where('id', $ocularId)
                ->update(['display_name_ar' => 'الجهاز العيني', 'updated_at' => now()]);
        }

        DB::table('anatomical_structures')->updateOrInsert(
            ['canonical_name' => 'eye'],
            [
                'body_system_id' => $ocularId,
                'parent_id' => null,
                'display_name' => 'Eye',
                'display_name_ar' => 'العين',
                'type' => 'organ',
                'domain' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }

    public function down(): void
    {
        DB::table('anatomical_structures')->where('canonical_name', 'eye')->delete();
    }
};
