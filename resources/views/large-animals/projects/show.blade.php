@extends('app.layouts.master')

@section('title', $project->title)

@section('meta_description')
    {{ \Illuminate\Support\Str::limit(strip_tags($project->summary ?? ''), 160) }}
@endsection

@section('content')
    <div class="space-y-4">

        {{-- Breadcrumb --}}
        <nav class="flex mb-4 pt-4 flex-wrap items-center gap-x-2 gap-y-1 text-sm text-body dark:text-slate-400" aria-label="{{ __('messages.nav.breadcrumb') }}">
            <a href="{{ route('large-animals.home') }}" wire:navigate class="hover:text-fg-brand dark:hover:text-brand transition-colors">{{ __('large-animals.breadcrumb.drugs') }}</a>
            <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180 shrink-0" />
            <a href="{{ route('large-animals.projects.index') }}" wire:navigate class="hover:text-fg-brand dark:hover:text-brand transition-colors">{{ __('large-animals.breadcrumb.projects') }}</a>
            <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180 shrink-0" />
            <span class="text-heading dark:text-white font-medium">{{ $project->title }}</span>
        </nav>

        {{-- Hero --}}
        <x-large-animals.card-hero
            :heading="$project->title"
            :subtitle="\Illuminate\Support\Str::limit(strip_tags($project->summary ?? ''), 220)"
        >
            <x-slot name="action">
                <a href="{{ route('large-animals.projects.index') }}" wire:navigate
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-base transition-all duration-200 text-xs font-medium whitespace-nowrap text-body hover:text-brand hover:bg-brand/10 dark:text-slate-400 dark:hover:text-brand dark:hover:bg-brand/20">
                    <x-lucide-arrow-left class="w-4 h-4 rtl:rotate-180" />
                    <span>{{ __('large-animals.projects.back') }}</span>
                </a>
            </x-slot>
            @if ($project->featured)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-base bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">
                    <x-lucide-star class="w-3.5 h-3.5" />
                    {{ __('large-animals.projects.featured') }}
                </span>
            @endif
        </x-large-animals.card-hero>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

            {{-- Main --}}
            <div class="lg:col-span-9">
                <article class="bg-neutral-primary-soft rounded-base shadow-xs overflow-hidden dark:bg-slate-800 dark:border dark:border-slate-700">
                    <div class="px-4 sm:px-8 py-8">
                        <div class="max-w-3xl">
                            {{-- Meta --}}
                            <div class="flex flex-wrap items-center gap-2 text-sm mb-4">
                                @if ($project->project_type)
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-base bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">
                                        {{ __('large-animals.projects.types.' . $project->project_type) }}
                                    </span>
                                @endif
                                @if ($project->featured)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-base bg-success-soft border border-success-subtle text-fg-success-strong dark:bg-green-950 dark:border-green-900 dark:text-green-300">
                                        <x-lucide-star class="w-3 h-3" />
                                        {{ __('large-animals.projects.featured') }}
                                    </span>
                                @endif
                                @if ($project->sector)
                                    <span class="inline-flex items-center gap-1.5 text-body dark:text-slate-400">
                                        <x-lucide-folder-kanban class="w-3.5 h-3.5" />
                                        {{ __('large-animals.projects.sector') }}: {{ $project->sector }}
                                    </span>
                                @endif
                            </div>

                            @if (filled($project->content))
                                <div class="max-w-none prose-article text-body dark:text-slate-300 leading-relaxed
                                    [&_h2]:text-heading dark:[&_h2]:text-white [&_h2]:text-2xl [&_h2]:font-bold [&_h2]:mt-8 [&_h2]:mb-4
                                    [&_h3]:text-heading dark:[&_h3]:text-white [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:mt-6 [&_h3]:mb-3
                                    [&_p]:mb-4 [&_p]:leading-relaxed
                                    [&_ul]:space-y-2 [&_ul]:mb-4
                                    [&_ol]:space-y-2 [&_ol]:mb-4
                                    [&_blockquote]:border-s-4 [&_blockquote]:border-brand [&_blockquote]:ps-4 [&_blockquote]:my-6 [&_blockquote]:italic
                                    [&_a]:text-fg-brand dark:[&_a]:text-brand [&_a]:no-underline hover:[&_a]:underline
                                    [&_hr]:border-default-medium dark:[&_hr]:border-slate-700 [&_hr]:my-8
                                    [&_pre]:bg-neutral-secondary-soft dark:[&_pre]:bg-slate-700 [&_pre]:border [&_pre]:border-default-medium dark:[&_pre]:border-slate-600 [&_pre]:rounded-base [&_pre]:p-4 [&_pre]:overflow-x-auto
                                    [&_code]:text-sm
                                    [&_img]:rounded-base [&_img]:shadow-xs [&_img]:max-w-full [&_img]:h-auto">
                                    {!! $project->content !!}
                                </div>
                            @else
                                <p class="text-base text-body dark:text-slate-400">{{ __('large-animals.projects.no_content') }}</p>
                            @endif

                            <div class="mt-10 pt-6 border-t border-default-medium dark:border-slate-700">
                                <a href="{{ route('large-animals.projects.index') }}" wire:navigate
                                    class="inline-flex items-center gap-2 text-sm font-medium text-body hover:text-heading dark:text-slate-400 dark:hover:text-white transition-colors">
                                    <x-lucide-arrow-left class="w-4 h-4 rtl:rotate-180" />
                                    {{ __('large-animals.projects.back') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-3">
                <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800 lg:sticky lg:top-20">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-body dark:text-slate-400 mb-3">{{ __('large-animals.projects.summary') }}</h2>
                    @if ($project->summary)
                        <p class="text-sm text-body dark:text-slate-400 leading-relaxed">{{ $project->summary }}</p>
                    @else
                        <p class="text-sm text-body dark:text-slate-400">{{ __('large-animals.projects.no_content') }}</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
