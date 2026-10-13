<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * `anatomical_structures` is one shared table, so a chicken diagram drawn from
 * it would offer a rumen, a set of teats and an interdigital space. Nothing in
 * the table says which species an organ belongs to.
 *
 * The column is nullable and `null` means "belongs to every part", because the
 * large majority of the vocabulary is genuinely shared — liver, gizzard, air
 * sacs, comb, syrinx — and marking those for one species would be a claim the
 * data cannot make. Only the ten structures that cannot exist in a bird are
 * scoped.
 *
 * The list is deliberately explicit rather than inferred from which system the
 * organ sits in: `Intestines` is a ruminant word in this dataset and is the one
 * organ two poultry diseases point at, while `Pharynx` and `Tongue` sit inside
 * the same ruminant system and are perfectly good bird anatomy.
 */
return new class extends Migration
{
    /**
     * Structures with no counterpart in a bird. A chicken has no udder, no
     * hooves, no rumen and no urinary bladder.
     *
     * @var array<int, string>
     */
    private const LARGE_ANIMAL_ONLY = [
        'Abomasum',
        'Bladder',
        'Coronary band',
        'Hooves',
        'Interdigital space',
        'Mammary gland',
        'Muzzle',
        'Rumen',
        'Teats',
        'Uterus',
    ];

    public function up(): void
    {
        Schema::table('anatomical_structures', function (Blueprint $table) {
            $table->string('domain')
                ->nullable()
                ->after('type');
        });

        DB::table('anatomical_structures')
            ->whereIn('canonical_name', array_map(
                fn (string $name): string => str($name)->lower()->replace(' ', '_')->toString(),
                self::LARGE_ANIMAL_ONLY,
            ))
            ->update(['domain' => 'large_animal']);

        Schema::table('anatomical_structures', function (Blueprint $table) {
            $table->index('domain', 'anatomical_structures_domain_index');
        });
    }

    public function down(): void
    {
        Schema::table('anatomical_structures', function (Blueprint $table) {
            $table->dropIndex('anatomical_structures_domain_index');
            $table->dropColumn('domain');
        });
    }
};
