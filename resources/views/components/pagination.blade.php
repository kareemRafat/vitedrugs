@props([
    'paginator',
    'translationPrefix',
    'simple' => false,
    'borderTop' => false,
])

@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $window = 2;
        $startPage = max(1, $currentPage - $window);
        $endPage = min($lastPage, $currentPage + $window);
        $showStartEllipsis = $startPage > 2;
        $showEndEllipsis = $endPage < $lastPage - 1;
    @endphp
    <div @class([
        'flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-4',
        'border-t border-default-medium dark:border-slate-700' => $borderTop,
    ])>
        <div @class([
            'text-sm text-body dark:text-slate-400',
            'hidden sm:block' => !$simple,
        ])>
            {{ __($translationPrefix . '.showing') }}
            {{ $paginator->firstItem() ?? 0 }}&ndash;{{ $paginator->lastItem() ?? 0 }}
            {{ __($translationPrefix . '.of') }}
            {{ number_format($paginator->total()) }}
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ $paginator->previousPageUrl() }}" wire:navigate rel="prev" @if($paginator->onFirstPage()) aria-disabled="true" @endif
                @class([
                    'inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-base transition-colors',
                    'text-heading bg-neutral-primary-soft border border-default-medium hover:bg-neutral-secondary-soft dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600',
                    'opacity-40 cursor-not-allowed pointer-events-none' => $paginator->onFirstPage(),
                ])>
                <x-lucide-chevron-left class="w-4 h-4 rtl:rotate-180" />
                <span>{{ __($translationPrefix . '.previous_page') }}</span>
            </a>

            @unless ($simple)
                <div class="hidden sm:flex items-center gap-1">
                    @if ($startPage > 1)
                        <a href="{{ $paginator->withQueryString()->url(1) }}" wire:navigate
                            class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600 transition-colors">
                            1
                        </a>
                    @endif

                    @if ($showStartEllipsis)
                        <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-body dark:text-slate-400">...</span>
                    @endif

                    @for ($i = $startPage; $i <= $endPage; $i++)
                        <a href="{{ $paginator->withQueryString()->url($i) }}" wire:navigate
                            @class([
                                'inline-flex items-center justify-center w-9 h-9 text-sm font-medium rounded-base transition-colors',
                                'text-white bg-brand shadow-xs' => $i === $currentPage,
                                'text-heading bg-neutral-primary-soft border border-default-medium hover:bg-neutral-secondary-soft dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600' => $i !== $currentPage,
                            ])>
                            {{ $i }}
                        </a>
                    @endfor

                    @if ($showEndEllipsis)
                        <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-body dark:text-slate-400">...</span>
                    @endif

                    @if ($endPage < $lastPage)
                        <a href="{{ $paginator->withQueryString()->url($lastPage) }}" wire:navigate
                            class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600 transition-colors">
                            {{ $lastPage }}
                        </a>
                    @endif
                </div>
            @endunless

            <a href="{{ $paginator->nextPageUrl() }}" wire:navigate rel="next" @if(!$paginator->hasMorePages()) aria-disabled="true" @endif
                @class([
                    'inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-base transition-colors',
                    'text-heading bg-neutral-primary-soft border border-default-medium hover:bg-neutral-secondary-soft dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600',
                    'opacity-40 cursor-not-allowed pointer-events-none' => !$paginator->hasMorePages(),
                ])>
                <span>{{ __($translationPrefix . '.next_page') }}</span>
                <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180" />
            </a>
        </div>
    </div>
@endif
