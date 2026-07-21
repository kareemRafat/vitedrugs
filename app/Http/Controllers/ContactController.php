<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function create()
    {
        return view('app.pages.contact');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ], [
            'name.required' => __('validation.contact.name_required'),
            'name.max' => __('validation.contact.name_max'),
            'email.required' => __('validation.contact.email_required'),
            'email.email' => __('validation.contact.email_email'),
            'email.max' => __('validation.contact.email_max'),
            'subject.required' => __('validation.contact.subject_required'),
            'subject.max' => __('validation.contact.subject_max'),
            'message.required' => __('validation.contact.message_required'),
            'message.max' => __('validation.contact.message_max'),
        ]);

        ContactSubmission::create($validated);

        Log::channel('mail')->info('Contact form submission', $validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => __('messages.pages.contact.success'),
            ]);
        }

        return redirect()
            ->route('contact')
            ->with('success', __('messages.pages.contact.success'));
    }
}
