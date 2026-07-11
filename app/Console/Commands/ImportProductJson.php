<?php

namespace App\Console\Commands;

use App\Services\Imports\ProductImportService;
use Illuminate\Console\Command;

class ImportProductJson extends Command
{
    protected $signature = 'products:import {file}';

    protected $description = 'Import products from JSON';

    public function handle(ProductImportService $service): int
    {
        $json = file_get_contents(
            $this->argument('file')
        );

        $items = json_decode($json, true);

        foreach ($items as $item) {
            $service->import($item);
        }

        $this->info('Import completed');

        return self::SUCCESS;
    }
}
