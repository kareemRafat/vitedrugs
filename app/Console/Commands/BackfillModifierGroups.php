<?php

namespace App\Console\Commands;

use App\Models\LargeAnimals\Modifier;
use Illuminate\Console\Command;

class BackfillModifierGroups extends Command
{
    protected $signature = 'app:backfill-modifier-groups';

    protected $description = 'Normalize modifiers into JSON facets and backfill type/modifier_group';

    private const PLAIN_MAP = [
        'Bilateral' => ['laterality' => 'bilateral'],
        'Unilateral' => ['laterality' => 'unilateral'],
        'Acute' => ['course' => 'acute'],
        'Chronic' => ['course' => 'chronic'],
        'Progressive' => ['course' => 'progressive'],
        'Intermittent' => ['temporal' => 'intermittent'],
        'Mild' => ['severity' => 'mild'],
        'Severe' => ['severity' => 'severe'],
        'Moderate' => ['severity' => 'moderate'],
        'Marked' => ['severity' => 'marked'],
        'Reduced' => ['severity' => 'reduced'],
        'Profuse' => ['severity' => 'profuse'],
        'Complete' => ['severity' => 'complete'],
        'Partial' => ['severity' => 'partial'],
        'Absent' => ['severity' => 'absent'],
        'Peracute' => ['course' => 'peracute'],
        'Persistent' => ['course' => 'persistent'],
        'Recurrent' => ['course' => 'recurrent'],
        'Diffuse' => ['distribution' => 'diffuse'],
        'Multifocal' => ['distribution' => 'multifocal'],
        'Generalized' => ['distribution' => 'generalized'],
        'Watery' => ['appearance' => 'watery'],
        'Purulent' => ['appearance' => 'purulent'],
        'Serous' => ['appearance' => 'serous'],
        'Mucopurulent' => ['appearance' => 'mucopurulent'],
    ];

    public function handle(): int
    {
        $backfilled = 0;

        Modifier::query()
            ->get()
            ->each(function (Modifier $modifier) use (&$backfilled): void {
                $data = $modifier->decodeData();

                if ($data === null) {
                    $plain = $modifier->display_name;
                    $data = self::PLAIN_MAP[$plain] ?? null;

                    if ($data === null) {
                        return;
                    }

                    $modifier->display_name = json_encode($data, JSON_UNESCAPED_UNICODE);
                }

                if (! is_array($data)) {
                    return;
                }

                $groups = Modifier::groupsForData($data);

                if ($groups !== null) {
                    $modifier->type = $groups;
                    $modifier->modifier_group = $groups;
                }

                $modifier->save();
                $backfilled++;
            });

        $this->info("Processed {$backfilled} modifiers.");

        return self::SUCCESS;
    }
}
