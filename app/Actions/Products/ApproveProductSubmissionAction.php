<?php

namespace App\Actions\Products;

use App\Enums\SubmissionStatus;
use App\Models\ProductSubmission;
use App\Services\Imports\ProductImportService;
use Illuminate\Support\Facades\DB;

class ApproveProductSubmissionAction
{
    public function execute(
        ProductSubmission $submission,
        ProductImportService $service,
    ): void {
        $data = $submission->submitted_data;

        if (! is_array($data)) {
            throw new \RuntimeException('Invalid submitted data.');
        }

        DB::transaction(function () use ($data, $service, $submission) {
            $service->import($data);

            $submission->update([
                'status' => SubmissionStatus::Approved,
                'admin_notes' => null,
            ]);
        });
    }
}
