@props([
    'letters' => [],
    'active' => null,
    'wireAction' => 'filterByLetter',
    'rounded' => 'base',
])

<div class="md:bg-neutral-primary-soft md:shadow-xs md:p-4 dark:bg-slate-800 {{ $rounded === 'full' ? 'md:rounded-full' : 'md:rounded-base' }}">
    <div class="flex flex-nowrap md:flex-wrap items-center justify-start md:justify-center gap-4 md:gap-2 overflow-x-auto md:overflow-visible pb-1">
        @php $allLetters = range('A', 'Z'); @endphp
        @foreach ($allLetters as $letter)
            @if (in_array($letter, $letters))
                @if ($active === $letter)
                    <span class="inline-flex items-center justify-center w-9 h-9 {{ $rounded === 'full' ? 'rounded-full' : 'rounded-base' }} text-brand md:bg-brand md:text-white text-sm font-bold cursor-default">{{ $letter }}</span>
                @else
                    <button type="button" wire:click="{{ $wireAction }}('{{ $letter }}')"
                        class="inline-flex items-center justify-center w-9 h-9 {{ $rounded === 'full' ? 'rounded-full' : 'rounded-base' }} text-sm font-medium text-heading bg-neutral-secondary-soft dark:bg-transparent hover:bg-brand/10 hover:text-brand dark:text-white dark:hover:text-brand transition-colors">
                        {{ $letter }}
                    </button>
                @endif
            @else
                <span class="inline-flex items-center justify-center w-9 h-9 {{ $rounded === 'full' ? 'rounded-full' : 'rounded-base' }} text-sm text-body/30 dark:text-slate-600 cursor-default">{{ $letter }}</span>
            @endif
        @endforeach
    </div>
</div>
