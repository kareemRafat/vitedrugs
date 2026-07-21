<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('app.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::guard('web')->user()->role === UserRole::Admin) {
                Auth::guard('web')->logout();

                $request->session()->regenerate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => __('validation.login.admin_restricted'),
                ])->onlyInput('email');
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => __('validation.login.credentials_mismatch'),
        ])->onlyInput('email');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect(route('home'));
    }
}
