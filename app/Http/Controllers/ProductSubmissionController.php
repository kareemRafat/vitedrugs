<?php

namespace App\Http\Controllers;

use App\Actions\Products\CreateProductFromSubmissionDataAction;
use App\Http\Requests\StoreProductSubmissionRequest;

class ProductSubmissionController extends Controller
{
    public function create()
    {
        return view('app.products.create-submission');
    }

    public function store(StoreProductSubmissionRequest $request)
    {
        $validated = $request->validated();

        $productData = collect($validated)->except([
            'submitted_by_name',
            'submitted_by_email',
            'submitted_by_phone',
        ])->toArray();

        app(CreateProductFromSubmissionDataAction::class)
            ->execute($productData, auth()->user());

        $response = [
            'message' => __('messages.pages.products.submission.success'),
        ];

        if ($request->hasDuplicatePending) {
            $response['warning'] = __('messages.pages.products.submission.trade_name_pending_warning');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($response);
        }

        return redirect()
            ->route('products.submission.create')
            ->with('success', $response['message'])
            ->with('warning', $response['warning'] ?? null);
    }
}
