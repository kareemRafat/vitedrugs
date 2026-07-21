<?php

namespace App\Http\Controllers;

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
            'user' => Auth::user(),
        ]);
    }

    public function edit(): View
    {
        return view('app.profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

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
            'user' => Auth::user(),
        ]);
    }

    public function updateSecurity(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => __('messages.profile.wrong_password'),
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
        $products = Auth::user()
            ->products()
            ->withoutGlobalScope('approved')
            ->latest()
            ->paginate(15);

        return view('app.profile.submissions', [
            'products' => $products,
        ]);
    }
}
