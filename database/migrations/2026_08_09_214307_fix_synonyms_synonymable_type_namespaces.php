<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The synonyms were created before the clinical models moved into the
     * LargeAnimals namespace, so synonymable_type still holds the old
     * (non-existent) class names. Point them at the current classes.
     */
    public function up(): void
    {
        $this->updateTypes([
            'App\Models\ClinicalSign' => 'App\Models\LargeAnimals\ClinicalSign',
            'App\Models\Finding' => 'App\Models\LargeAnimals\Finding',
            'App\Models\Modifier' => 'App\Models\LargeAnimals\Modifier',
        ]);
    }

    public function down(): void
    {
        $this->updateTypes([
            'App\Models\LargeAnimals\ClinicalSign' => 'App\Models\ClinicalSign',
            'App\Models\LargeAnimals\Finding' => 'App\Models\Finding',
            'App\Models\LargeAnimals\Modifier' => 'App\Models\Modifier',
        ]);
    }

    /**
     * @param  array<string, string>  $map
     */
    private function updateTypes(array $map): void
    {
        foreach ($map as $old => $new) {
            DB::table('synonyms')
                ->where('synonymable_type', $old)
                ->update(['synonymable_type' => $new]);
        }
    }
};
