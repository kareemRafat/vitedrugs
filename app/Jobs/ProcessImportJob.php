<?php

namespace App\Jobs;

use App\Actions\ImportProductsAction;
use App\Models\ImportJob;
use App\Services\Imports\ProductImportService;
use App\Services\Imports\ProductImportValidator;
use App\Services\Imports\ProductNormalizer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessImportJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $importJobId,
    ) {}

    public function handle(
        ImportProductsAction $action,
        ProductImportService $service,
        ProductImportValidator $validator,
        ProductNormalizer $normalizer,
    ): void {

        $job = ImportJob::findOrFail(
            $this->importJobId
        );

        $job->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);

        try {

            $action->execute(
                $job,
                $service,
                $validator,
                $normalizer,
            );

            $job->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        } catch (\Throwable $e) {

            Log::error(
                'Import job failed',
                [
                    'import_job_id' => $job->id,
                    'error' => $e->getMessage(),
                ]
            );

            $job->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }
}
