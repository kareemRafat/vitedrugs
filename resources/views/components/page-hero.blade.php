@props([
    'heading' => '',
    'subtitle' => '',
    'stats' => null,
])

<div class="bg-gradient-to-br from-brand to-brand-strong dark:from-sky-800 dark:to-sky-950 rounded-base overflow-hidden">
    <div class="relative px-6 py-16 sm:py-20">
        <div class="absolute inset-0 opacity-10 dark:opacity-20">
            <div class="absolute -top-24 -end-24 w-96 h-96 rounded-full bg-white dark:bg-sky-200"></div>
            <div class="absolute -bottom-32 -start-32 w-80 h-80 rounded-full bg-white dark:bg-sky-200"></div>
        </div>
        <div class="relative">
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">{{ $heading }}</h1>
            <p class="text-white/80 text-base max-w-xl mb-4">{{ $subtitle }}</p>
            @if ($stats)
                <div class="flex flex-wrap gap-2">
                    @foreach ($stats as $stat)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-base text-sm font-medium bg-white/15 text-white border border-white/20 dark:bg-sky-900/40 dark:border-sky-700">
                            @if ($stat['icon'] ?? null)
                                <x-dynamic-component :component="'lucide-' . $stat['icon']" class="w-3.5 h-3.5" />
                            @endif
                            {{ $stat['count'] ?? 0 }} {{ $stat['label'] ?? '' }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
