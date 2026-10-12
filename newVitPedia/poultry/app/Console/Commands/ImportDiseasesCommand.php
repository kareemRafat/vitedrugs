<?php

namespace App\Console\Commands;

use App\Services\DiseaseImporterService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportDiseasesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diseases:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all poultry diseases JSON files';

    public function __construct(
        protected DiseaseImporterService $importer
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $path = storage_path('app/diseases');

        if (! File::exists($path)) {

            $this->error('Diseases folder not found.');

            return self::FAILURE;
        }

        $files = File::allFiles($path);

        $count = 0;

        foreach ($files as $file) {

            if ($file->getExtension() !== 'json') {
                continue;
            }

            try {

                $disease = $this->importer->import(
                    $file->getRealPath()
                );

                $count++;

                $this->info(
                    "Imported: {$disease->name}"
                );

            } catch (\Throwable $e) {

                $this->error(
                    "Failed: {$file->getFilename()}"
                );

                $this->error($e->getMessage());
            }
        }

        $this->newLine();

        $this->info("Imported {$count} diseases successfully.");

        return self::SUCCESS;
    }
}