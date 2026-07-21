<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductSubmissionRequest;
use App\Models\ProductSubmission;

class ProductSubmissionController extends Controller
{
    public function create()
    {
        return view('app.products.create-submission');
    }

    public function store(StoreProductSubmissionRequest $request)
    {
        $validated = $request->validated();

        $submittedData = collect($validated)->except([
            'submitted_by_name',
            'submitted_by_email',
            'submitted_by_phone',
        ])->toArray();

        ProductSubmission::create([
            'user_id' => auth()->id(),
            'submitted_data' => $submittedData,
            'submitted_by_name' => $validated['submitted_by_name'],
            'submitted_by_email' => $validated['submitted_by_email'],
            'submitted_by_phone' => $validated['submitted_by_phone'] ?? null,
            'status' => 'pending',
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => __('messages.pages.products.submission.success'),
            ]);
        }

        return redirect()
            ->route('products.submission.create')
            ->with('success', __('messages.pages.products.submission.success'));
    }
}
