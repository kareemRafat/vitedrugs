<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->role !== UserRole::Admin) {
            Auth::guard('admin')->logout();

            $request->session()->regenerate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => __('validation.login.admin_restricted'),
            ]);
        }

        return $next($request);
    }
}
