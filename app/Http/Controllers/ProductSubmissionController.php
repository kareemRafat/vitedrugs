<?php

namespace App\Http\Controllers;

use App\Actions\Products\CreateProductFromSubmissionDataAction;
use App\Actions\Products\UpdateProductFromSubmissionDataAction;
use App\Enums\ProductStatus;
use App\Http\Requests\StoreProductSubmissionRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductSubmissionController extends Controller
{
    public function create()
    {
        return view('app.products.create-submission');
    }

    public function edit(string $product)
    {
        $product = Product::withoutGlobalScope('approved')
            ->where('created_by', Auth::guard('web')->id())
            ->where('status', ProductStatus::Pending)
            ->findOrFail($product);

        $product->load([
            'companies',
            'dosageForm',
            'activeIngredients',
            'indications',
            'contraindications',
            'precautions',
            'sideEffects',
            'dosages.species',
            'withdrawalPeriods.species',
            'diseases',
            'images',
            'documents',
        ]);

        return view('app.products.create-submission', [
            'editProduct' => $product,
        ]);
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

    public function update(StoreProductSubmissionRequest $request, string $product)
    {
        $product = Product::withoutGlobalScope('approved')
            ->where('created_by', Auth::guard('web')->id())
            ->where('status', ProductStatus::Pending)
            ->findOrFail($product);

        $validated = $request->validated();

        $productData = collect($validated)->except([
            'submitted_by_name',
            'submitted_by_email',
            'submitted_by_phone',
        ])->toArray();

        app(UpdateProductFromSubmissionDataAction::class)
            ->execute($product, $productData);

        return response()->json([
            'message' => __('messages.pages.products.submission.updated'),
        ]);
    }
}
