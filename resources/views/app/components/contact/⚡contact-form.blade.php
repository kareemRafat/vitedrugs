<?php

use App\Enums\ContactCategory;
use App\Models\ContactSubmission;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component
{
    /**
     * `landing` renders the homepage inquiry form (role + category select, subject
     * derived from the category). `page` keeps the standalone /contact markup.
     */
    public string $variant = 'page';

    public string $name = '';

    public string $email = '';

    public string $role = '';

    public ?string $category = null;

    public string $subject = '';

    public string $message = '';

    public function isLanding(): bool
    {
        return $this->variant === 'landing';
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $isLanding = $this->isLanding();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'category' => [
                $isLanding ? 'required' : 'nullable',
                Rule::enum(ContactCategory::class),
            ],
            // On the landing variant the subject is derived from the chosen
            // category, so the free-text field is not shown or required.
            'subject' => [
                $isLanding ? 'nullable' : 'required',
                'string',
                'max:255',
            ],
            'message' => ['required', 'string', 'max:5000'],
        ];
    }

    public function submit(): void
    {
        $this->validate();

        $category = $this->category !== null
            ? ContactCategory::from($this->category)
            : null;

        ContactSubmission::create([
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role !== '' ? $this->role : null,
            'category' => $category?->value,
            // Snapshot of the label the visitor actually saw, so the inbox shows
            // it verbatim even if the translation is edited later.
            'subject' => $category?->getLabel() ?? $this->subject,
            'locale' => app()->getLocale(),
            'message' => $this->message,
        ]);

        $this->reset(['name', 'email', 'role', 'category', 'subject', 'message']);

        $this->dispatch('contact-sent');
    }

    /**
     * @return array<string, string>
     */
    public function categoryOptions(): array
    {
        return collect(ContactCategory::cases())
            ->mapWithKeys(fn (ContactCategory $category): array => [$category->value => $category->getLabel()])
            ->all();
    }
};
?>

<div x-data x-on:contact-sent.window="window.Toast.show('contactToast', 'success', '{{ __('messages.pages.contact.success') }}')">
    @if ($this->isLanding())
        {{-- Homepage inquiry form: name, clinical role, institutional email, category, message. --}}
        <form wire:submit="submit" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('messages.pages.contact.name_label') }}</label>
                    <input type="text" name="name" id="name" wire:model.blur="name" required
                        class="w-full py-3 px-4 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all @error('name') border-red-500 @enderror"
                        placeholder="{{ __('messages.pages.contact.name_placeholder') }}">
                    @error('name')
                        <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="role" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('messages.pages.contact.role_label') }}</label>
                    <input type="text" name="role" id="role" wire:model.blur="role"
                        class="w-full py-3 px-4 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all @error('role') border-red-500 @enderror"
                        placeholder="{{ __('messages.pages.contact.role_placeholder') }}">
                    @error('role')
                        <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('messages.pages.contact.email_label') }}</label>
                    <input type="email" name="email" id="email" wire:model.blur="email" required
                        class="w-full py-3 px-4 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all @error('email') border-red-500 @enderror"
                        placeholder="{{ __('messages.pages.contact.email_placeholder') }}">
                    @error('email')
                        <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="category" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('messages.pages.contact.category_label') }}</label>
                    <select name="category" id="category" wire:model.blur="category" required
                        class="w-full py-3 px-4 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all @error('category') border-red-500 @enderror">
                        <option value="">{{ __('messages.pages.contact.category_placeholder') }}</option>
                        @foreach ($this->categoryOptions() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="message" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">{{ __('messages.pages.contact.message_label') }}</label>
                <textarea name="message" id="message" rows="4" wire:model.blur="message" required
                    class="w-full py-3 px-4 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 transition-all resize-none @error('message') border-red-500 @enderror"
                    placeholder="{{ __('messages.pages.contact.message_placeholder') }}"></textarea>
                @error('message')
                    <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 shadow-md shadow-indigo-600/30 transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 disabled:opacity-50">
                    <span wire:loading.remove wire:target="submit">{{ __('messages.pages.contact.submit') }}</span>
                    <span wire:loading wire:target="submit">...</span>
                    <span class="rtl:rotate-180 transition-transform">&rarr;</span>
                </button>
            </div>
        </form>
    @else
        {{-- Standalone /contact form: name, email, free-text subject, message. --}}
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
    @endif
</div>