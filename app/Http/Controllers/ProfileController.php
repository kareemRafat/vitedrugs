<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        return view('app.profile.show', [
            'user' => Auth::guard('web')->user(),
        ]);
    }

    public function edit(): View
    {
        return view('app.profile.edit', [
            'user' => Auth::guard('web')->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
        ]);

        $user->update($validated);

        return redirect()->route('profile.show')
            ->with('success', __('messages.profile.updated'));
    }

    public function security(): View
    {
        return view('app.profile.security', [
            'user' => Auth::guard('web')->user(),
        ]);
    }

    public function updateSecurity(Request $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => __('validation.profile.wrong_password'),
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.security')
            ->with('success', __('messages.profile.password_updated'));
    }

    public function submissions(): View
    {
        $products = Auth::guard('web')->user()
            ->products()
            ->withoutGlobalScope('approved')
            ->latest()
            ->paginate(15);

        return view('app.profile.submissions', [
            'products' => $products,
        ]);
    }

    public function showSubmission(string $product): View
    {
        $product = Product::withoutGlobalScope('approved')
            ->where('created_by', Auth::guard('web')->id())
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

        return view('app.profile.submission-show', compact('product'));
    }
}
