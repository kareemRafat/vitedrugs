<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function notice(Request $request): View|RedirectResponse
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(route('home'))
            : view('app.auth.verify-email');
    }

    public function verify(Request $request, string $id): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            throw new AuthorizationException('Invalid or expired verification link.');
        }

        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return redirect(route('home'));
        }

        $user->markEmailAsVerified();

        return redirect(route('home'))->with('status', 'email-verified');
    }

    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('home'));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
