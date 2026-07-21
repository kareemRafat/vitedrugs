<?php

namespace App\Actions\Products;

use App\Enums\ProductStatus;
use App\Models\ImportJob;
use App\Services\Imports\ProductImportValidator;
use App\Services\Imports\ProductNormalizer;
use Illuminate\Support\Facades\Log;

class ImportProductsAction
{
    public function __construct(
        private CreateProductFromSubmissionDataAction $createProduct,
    ) {}

    public function execute(
        ImportJob $job,
        ProductImportValidator $validator,
        ProductNormalizer $normalizer
    ): void {

        $items = json_decode(
            $job->extracted_json,
            true
        );

        if (! is_array($items)) {
            throw new \RuntimeException(
                'Invalid JSON payload.'
            );
        }

        $job->update([
            'total_products' => count($items),
            'imported_products' => 0,
            'failed_products' => 0,
            'error_message' => null,
        ]);

        $imported = 0;
        $failed = 0;
        $errors = [];

        foreach ($items as $index => $item) {

            try {

                $normalized = $normalizer->normalize($item);

                $validator->validate($normalized);

                $this->createProduct->execute($normalized, status: ProductStatus::Approved);

                $imported++;
            } catch (\Throwable $e) {

                $failed++;

                $errors[] = [
                    'index' => $index,
                    'trade_name' => $item['trade_name'] ?? null,
                    'error' => $e->getMessage(),
                ];

                Log::error('Product import failed', [
                    'index' => $index,
                    'trade_name' => $item['trade_name'] ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $job->update([
            'imported_products' => $imported,
            'failed_products' => $failed,
            'error_message' => empty($errors)
                ? null
                : json_encode(
                    $errors,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                ),
        ]);
    }
}
