<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class MigrateProductCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-product-companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Product::query()
            ->whereNotNull('company_id')
            ->each(function ($product) {

                $product->companies()
                    ->syncWithoutDetaching([
                        $product->company_id => [
                            'role' => 'manufacturer',
                        ],
                    ]);
            });

        $this->info('Done');
    }
}
