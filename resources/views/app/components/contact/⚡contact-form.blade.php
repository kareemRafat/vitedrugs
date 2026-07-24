<?php

use App\Models\ContactSubmission;
use Livewire\Component;

new class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $subject = '';

    public string $message = '';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ];
    }

    public function submit(): void
    {
        $this->validate();

        ContactSubmission::create([
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
        ]);

        $this->reset(['name', 'email', 'subject', 'message']);
        $this->dispatch('contact-sent');
    }
};
?>

<div x-data x-on:contact-sent.window="window.Toast.show('contactToast', 'success', '{{ __('messages.pages.contact.success') }}')">
    <form wire:submit="submit">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
            <div>
                <label for="name" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.contact.name_label') }}</label>
                <input type="text" name="name" id="name" wire:model.blur="name"
                    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('name') border-red-600 @enderror"
                    placeholder="{{ __('messages.pages.contact.name_placeholder') }}">
                @error('name')
                    <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                        <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.contact.email_label') }}</label>
                <input type="email" name="email" id="email" wire:model.blur="email"
                    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('email') border-red-600 @enderror"
                    placeholder="{{ __('messages.pages.contact.email_placeholder') }}">
                @error('email')
                    <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                        <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>
        </div>

        <div class="mb-5">
            <label for="subject" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.contact.subject_label') }}</label>
            <input type="text" name="subject" id="subject" wire:model.blur="subject"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('subject') border-red-600 @enderror"
                placeholder="{{ __('messages.pages.contact.subject_placeholder') }}">
            @error('subject')
                <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                    <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="message" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.contact.message_label') }}</label>
            <textarea name="message" id="message" rows="5" wire:model.blur="message"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white resize-y @error('message') border-red-600 @enderror"
                placeholder="{{ __('messages.pages.contact.message_placeholder') }}"></textarea>
            @error('message')
                <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                    <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                    <span>{{ $message }}</span>
                </p>
            @enderror
        </div>

        <button type="submit" wire:loading.attr="disabled"
            class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base shadow-xs transition-colors disabled:opacity-50">
            <span wire:loading.remove wire:target="submit">
                <x-lucide-send class="w-4 h-4" />
            </span>
            <span wire:loading wire:target="submit">
                <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </span>
            <span wire:loading.remove wire:target="submit">{{ __('messages.pages.contact.submit') }}</span>
            <span wire:loading wire:target="submit">Sending...</span>
        </button>
    </form>
</div>