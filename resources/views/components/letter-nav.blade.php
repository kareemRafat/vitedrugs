@props([
    'letters' => [],
    'active' => null,
    'wireAction' => 'filterByLetter',
])

<div class="bg-neutral-primary-soft shadow-xs p-4 rounded-lg dark:bg-slate-800">
    <div class="flex flex-nowrap md:flex-wrap items-center justify-start md:justify-center gap-4 md:gap-2 overflow-x-auto md:overflow-visible pb-1">
        @php $allLetters = range('A', 'Z'); @endphp
        @foreach ($allLetters as $letter)
            @if (in_array($letter, $letters))
                @if ($active === $letter)
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-brand dark:text-amber-400 text-base font-extrabold cursor-default">{{ $letter }}</span>
                @else
                    <button type="button" wire:click="{{ $wireAction }}('{{ $letter }}')"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-heading bg-neutral-secondary-soft max-md:bg-transparent dark:bg-transparent hover:bg-brand/10 hover:text-brand dark:text-white dark:hover:text-brand transition-colors">
                        {{ $letter }}
                    </button>
                @endif
            @else
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm text-body/30 dark:text-slate-600 cursor-default">{{ $letter }}</span>
            @endif
        @endforeach
    </div>
</div>
