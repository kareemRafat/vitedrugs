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
            'login' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $attempt = [
            $field => $field === 'phone'
                ? preg_replace('/\D/', '', $credentials['login'])
                : $credentials['login'],
            'password' => $credentials['password'],
        ];

        if (Auth::guard('web')->attempt($attempt, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::guard('web')->user()->role === UserRole::Admin) {
                Auth::guard('web')->logout();

                $request->session()->regenerate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'login' => __('validation.login.admin_restricted'),
                ])->onlyInput('login');
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'login' => __('validation.login.credentials_mismatch'),
        ])->onlyInput('login');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect(route('home'));
    }
}
