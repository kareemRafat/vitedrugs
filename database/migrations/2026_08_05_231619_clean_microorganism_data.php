<?php

use App\Models\LargeAnimals\Microorganism;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const TOPIC_NAMES = [
        'Aseptate_Moulds',
        'Dermatophytes_Overview',
        'Dimorphic_Fungi_Overview',
        'Fungal_Classification',
        'Ringworm_Laboratory_Diagnosis',
        'Septate_Moulds',
    ];

    private const MISCLASSIFIED_FUNGI = [
        'Coccidioides immitis',
        'Epidermophyton floccosum',
        'Microsporum',
        'Paracoccidioides brasiliensis',
        'Trichophyton',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Microorganism::whereIn('name', self::TOPIC_NAMES)
            ->update(['is_topic' => true]);

        Microorganism::where('name', 'Unknown')->delete();

        Microorganism::where('name', 'Mycobacterium paratuberculosis')
            ->whereNull('slug')
            ->delete();

        Microorganism::whereIn('name', self::MISCLASSIFIED_FUNGI)
            ->update(['microorganism_type' => 'fungus']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data cleanup is intentionally not reverted.
    }
};
