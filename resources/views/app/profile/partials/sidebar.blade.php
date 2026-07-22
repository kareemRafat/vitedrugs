<nav class="space-y-1">
    <a href="{{ route('profile.show') }}" wire:navigate
       @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium transition-all duration-200',
               'bg-brand-soft text-fg-brand border-s-[3px] border-brand shadow-xs dark:bg-sky-900/50 dark:text-sky-400 dark:border-sky-500' => request()->routeIs('profile.show'),
               'text-body hover:text-heading hover:bg-neutral-secondary-soft hover:border-s-[3px] hover:border-default-medium dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-700 dark:hover:border-slate-500 border-s-[3px] border-transparent' => !request()->routeIs('profile.show')])>
        <x-lucide-user class="w-5 h-5 shrink-0" />
        <span>{{ __('messages.profile.nav_info') }}</span>
    </a>

    <a href="{{ route('profile.edit') }}" wire:navigate
       @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium transition-all duration-200',
               'bg-brand-soft text-fg-brand border-s-[3px] border-brand shadow-xs dark:bg-sky-900/50 dark:text-sky-400 dark:border-sky-500' => request()->routeIs('profile.edit'),
               'text-body hover:text-heading hover:bg-neutral-secondary-soft hover:border-s-[3px] hover:border-default-medium dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-700 dark:hover:border-slate-500 border-s-[3px] border-transparent' => !request()->routeIs('profile.edit')])>
        <x-lucide-pencil class="w-5 h-5 shrink-0" />
        <span>{{ __('messages.profile.nav_edit') }}</span>
    </a>

    <a href="{{ route('profile.security') }}" wire:navigate
       @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium transition-all duration-200',
               'bg-brand-soft text-fg-brand border-s-[3px] border-brand shadow-xs dark:bg-sky-900/50 dark:text-sky-400 dark:border-sky-500' => request()->routeIs('profile.security'),
               'text-body hover:text-heading hover:bg-neutral-secondary-soft hover:border-s-[3px] hover:border-default-medium dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-700 dark:hover:border-slate-500 border-s-[3px] border-transparent' => !request()->routeIs('profile.security')])>
        <x-lucide-shield class="w-5 h-5 shrink-0" />
        <span>{{ __('messages.profile.nav_security') }}</span>
    </a>

    <a href="{{ route('profile.submissions') }}" wire:navigate
       @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium transition-all duration-200',
               'bg-brand-soft text-fg-brand border-s-[3px] border-brand shadow-xs dark:bg-sky-900/50 dark:text-sky-400 dark:border-sky-500' => request()->routeIs('profile.submissions*'),
               'text-body hover:text-heading hover:bg-neutral-secondary-soft hover:border-s-[3px] hover:border-default-medium dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-700 dark:hover:border-slate-500 border-s-[3px] border-transparent' => !request()->routeIs('profile.submissions*')])>
        <x-lucide-package class="w-5 h-5 shrink-0" />
        <span>{{ __('messages.profile.nav_submissions') }}</span>
    </a>
</nav>
