<?php

namespace App\Console\Commands;

use App\Actions\Products\CreateProductFromSubmissionDataAction;
use App\Enums\ProductStatus;
use Illuminate\Console\Command;

class ImportProductJson extends Command
{
    protected $signature = 'products:import {file}';

    protected $description = 'Import products from JSON';

    public function handle(CreateProductFromSubmissionDataAction $action): int
    {
        $json = file_get_contents(
            $this->argument('file')
        );

        $items = json_decode($json, true);

        foreach ($items as $item) {
            $action->execute($item, status: ProductStatus::Approved);
        }

        $this->info('Import completed');

        return self::SUCCESS;
    }
}
