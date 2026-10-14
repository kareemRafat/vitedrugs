<?php

use App\Models\LargeAnimals\AnatomicalStructure;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Thirteen of the nineteen organs the body map draws have no Arabic name, and the
 * map is the one page where that shows: `localized_display_name` falls back to
 * English, so under `/ar/poultry/body-map` those hotspots, their tooltips and the
 * panel heading all read in English while the page around them is in Arabic.
 *
 * The other six organs — lungs, heart, trachea, intestines, kidneys — already
 * carry a name, which is why the gap is thirteen and not nineteen. It predates
 * this map; the illustration just made it impossible to miss.
 *
 * These are general anatomical terms and have not been reviewed by an Arabic
 * speaking veterinarian. Poultry usage is not always the general one — cloaca in
 * particular varies between Arabic veterinary sources — so treat this as a
 * starting set to be checked before launch rather than as reviewed copy. It is
 * isolated in its own migration precisely so that correcting a term is a one-line
 * change and not something entangled with the data the map runs on.
 *
 * @see AnatomicalStructure::getLocalizedDisplayNameAttribute()
 */
return new class extends Migration
{
    /**
     * canonical_name => Arabic display name.
     *
     * @var array<string, string>
     */
    private const NAMES = [
        'beak' => 'المنقار',
        'esophagus' => 'المريء',
        'crop' => 'الحوصل',
        'proventriculus' => 'الغدة البوابية',
        'gizzard' => 'المطحنة',
        'ceca' => 'الأعصاء',
        'large_intestine' => 'المعي الغليظ',
        'cloaca' => 'الشرج',
        'liver' => 'الكبد',
        'pancreas' => 'البنكرياس',
        'spleen' => 'الطحال',
        'ovary' => 'المبيض',
        'oviduct' => 'أنبوب البيض',
    ];

    public function up(): void
    {
        foreach (self::NAMES as $canonical => $nameAr) {
            DB::table('anatomical_structures')
                ->where('canonical_name', $canonical)
                ->whereNull('display_name_ar')
                ->update(['display_name_ar' => $nameAr, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        DB::table('anatomical_structures')
            ->whereIn('canonical_name', array_keys(self::NAMES))
            ->where(function ($query): void {
                $query->whereNull('display_name_ar')
                    ->orWhereIn('display_name_ar', array_values(self::NAMES));
            })
            ->update(['display_name_ar' => null, 'updated_at' => now()]);
    }
};
