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
            'name.required' => __('messages.pages.contact.validation.name_required'),
            'name.max' => __('messages.pages.contact.validation.name_max'),
            'email.required' => __('messages.pages.contact.validation.email_required'),
            'email.email' => __('messages.pages.contact.validation.email_email'),
            'email.max' => __('messages.pages.contact.validation.email_max'),
            'subject.required' => __('messages.pages.contact.validation.subject_required'),
            'subject.max' => __('messages.pages.contact.validation.subject_max'),
            'message.required' => __('messages.pages.contact.validation.message_required'),
            'message.max' => __('messages.pages.contact.validation.message_max'),
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
