@props(['current' => 1])

@php
    $steps = [
        ['key' => 'signs', 'icon' => 'stethoscope'],
        ['key' => 'refine', 'icon' => 'list-checks'],
        ['key' => 'results', 'icon' => 'clipboard-list'],
    ];
    $total = count($steps);
@endphp

<div class="bg-neutral-primary-soft border border-default-medium rounded-base shadow-xs p-4 dark:bg-slate-800 dark:border-slate-700">
    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:gap-0">
        @foreach ($steps as $index => $step)
            @php
                $stepNumber = $index + 1;
                $isCompleted = $stepNumber < $current;
                $isActive = $stepNumber === $current;
            @endphp

            <div class="flex items-center gap-3 sm:flex-none">
                <span @class([
                    'flex items-center justify-center w-10 h-10 rounded-full shrink-0 lg:h-12 lg:w-12',
                    'bg-success-soft text-fg-success-strong' => $isCompleted,
                    'bg-brand text-white ring-4 ring-brand-soft' => $isActive,
                    'bg-neutral-secondary-soft text-body dark:bg-slate-700 dark:text-slate-400' => ! $isCompleted && ! $isActive,
                ])>
                    @if ($isCompleted)
                        <x-lucide-check class="w-5 h-5" />
                    @else
                        <x-dynamic-component :component="'lucide-' . $step['icon']" class="w-5 h-5" />
                    @endif
                </span>
                <div>
                    <p @class([
                        'text-sm font-semibold leading-tight',
                        'text-heading dark:text-white' => $isActive,
                        'text-body dark:text-slate-400' => ! $isActive,
                    ])>{{ __('drugs.diagnosis.steps.' . $step['key']) }}</p>
                </div>
            </div>

            @if (! $loop->last)
                <div @class([
                    'hidden sm:block flex-1 h-1 rounded-full mx-4',
                    'bg-brand' => $stepNumber < $current,
                    'bg-neutral-secondary-soft dark:bg-slate-700' => $stepNumber >= $current,
                ])></div>
            @endif
        @endforeach
    </div>

    <p class="mt-4 pt-3 border-t border-default-medium text-xs text-body dark:text-slate-400">
        {{ __('drugs.diagnosis.step_of', ['current' => $current, 'total' => $total]) }}
    </p>
</div>