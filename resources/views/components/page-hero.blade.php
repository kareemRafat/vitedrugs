@props([
    'heading' => '',
    'subtitle' => '',
    'badge' => null,
    'badgeIcon' => null,
    'stats' => null,
])

<div class="bg-gradient-to-br from-brand to-brand-strong dark:from-sky-800 dark:to-sky-950 rounded-base overflow-hidden mt-4">
        <div class="relative px-6 pt-16 pb-20 sm:pt-20 sm:pb-24">
            <div class="absolute inset-0 opacity-10 dark:opacity-20">
                <div class="absolute -top-24 -end-24 w-96 h-96 rounded-full bg-white dark:bg-sky-200"></div>
                <div class="absolute -bottom-32 -start-32 w-80 h-80 rounded-full bg-white dark:bg-sky-200"></div>
            </div>
            <div class="relative">
                @if ($badge)
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium text-blue-200 dark:text-sky-200 border border-white/10 dark:border-sky-700 mb-5">
                        @if ($badgeIcon)
                            <x-dynamic-component :component="'lucide-' . $badgeIcon" class="w-4 h-4" />
                        @endif
                        <span>{{ $badge }}</span>
                    </div>
                @endif
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
            @unless (empty(trim((string) $slot)))
                <div class="mt-5">
                    {{ $slot }}
                </div>
            @endunless
        </div>
        </div>
</div>
