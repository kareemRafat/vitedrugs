@extends('app.layouts.master')

@section('title', __('drugs.projects.title'))

@section('content')
    <div class="space-y-4">

        {{-- Hero --}}
        <x-drugs.page-hero
            :heading="__('drugs.projects.heading')"
            :subtitle="__('drugs.projects.subtitle')"
            :badge="__('drugs.hero.badge.projects')"
            badgeIcon="folder-kanban"
            :stats="[
                ['count' => $projects->total(), 'label' => __('drugs.hero.stats.projects'), 'icon' => 'folder-kanban'],
            ]"
        />

        @if ($projects->isEmpty())

            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-12 text-center dark:bg-slate-800">
                <x-lucide-folder-kanban class="w-16 h-16 text-body mx-auto mb-4 dark:text-slate-500" />
                <h2 class="text-xl font-semibold text-heading dark:text-white mb-2">{{ __('drugs.projects.no_projects') }}</h2>
                <p class="text-base text-body dark:text-slate-400">{{ __('drugs.projects.no_projects_desc') }}</p>
            </div>

        @else

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($projects as $project)
                    <article class="group bg-neutral-primary-soft rounded-base shadow-xs overflow-hidden border border-default-medium hover:shadow-md hover:border-brand transition-all duration-300 dark:bg-slate-800 dark:border-slate-700 flex flex-col">
                        <a href="{{ route('drugs.projects.show', $project->slug) }}" wire:navigate class="flex flex-col h-full">
                            <div class="relative h-32 bg-gradient-to-br from-brand to-brand-strong flex items-center justify-center overflow-hidden shrink-0">
                                <div class="absolute inset-0 opacity-10">
                                    <div class="absolute -top-10 -end-10 w-40 h-40 rounded-full bg-white"></div>
                                </div>
                                <x-lucide-folder-kanban class="w-10 h-10 text-white relative" />
                                @if ($project->featured)
                                    <span class="absolute top-3 start-3 inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-base bg-white text-brand-strong">
                                        <x-lucide-star class="w-3 h-3" />
                                        {{ __('drugs.projects.featured') }}
                                    </span>
                                @endif
                                @if ($project->project_type)
                                    <span class="absolute bottom-3 start-3 inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-base bg-black/25 text-white backdrop-blur-sm">
                                        {{ __('drugs.projects.types.' . $project->project_type) }}
                                    </span>
                                @endif
                            </div>
                            <div class="p-5 flex flex-col flex-1">
                                @if ($project->sector)
                                    <p class="text-xs text-body dark:text-slate-400 mb-1 uppercase tracking-wider">{{ $project->sector }}</p>
                                @endif
                                <h2 class="text-lg font-semibold text-heading dark:text-white mb-2 line-clamp-2 group-hover:text-fg-brand dark:group-hover:text-brand transition-colors duration-150">{{ $project->title }}</h2>
                                @if ($project->summary)
                                    <p class="text-sm text-body dark:text-slate-400 line-clamp-3">{{ $project->summary }}</p>
                                @endif
                                <div class="flex items-center gap-1.5 text-sm font-medium text-fg-brand pt-4 mt-auto">
                                    {{ __('drugs.projects.read_more') }}
                                    <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform duration-150" />
                                </div>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>

            <x-pagination :paginator="$projects" translation-prefix="drugs.common" />

        @endif

    </div>
@endsection
