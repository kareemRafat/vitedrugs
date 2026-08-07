@props([
    'heading' => '',
    'subtitle' => '',
    'stats' => null,
])

<div class="bg-neutral-primary-soft rounded-base shadow-sm p-4 sm:p-6 dark:bg-slate-800">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-7 xl:col-span-8">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-bold text-heading dark:text-white mb-1">{{ $heading }}</h1>
                    @if (filled($subtitle))
                        <p class="text-body dark:text-slate-400 text-sm mb-2">{{ $subtitle }}</p>
                    @endif
                </div>
                @isset($action)
                    <div class="shrink-0">
                        {{ $action }}
                    </div>
                @endisset
            </div>
            @isset($slot)
                @if (filled(trim((string) $slot)))
                    <div class="flex flex-wrap gap-2">{{ $slot }}</div>
                @endif
            @endisset
        </div>

        @if ($stats)
            <div class="lg:col-span-5 xl:col-span-4">
                <div class="grid grid-cols-2 gap-3">
                    @foreach ($stats as $stat)
                        <div class="relative overflow-hidden bg-slate-50 dark:bg-slate-800 rounded-lg p-4 shadow-sm border border-slate-200 dark:border-slate-700">
                            <x-dynamic-component :component="'lucide-' . ($stat['icon'] ?? 'info')"
                                class="absolute -bottom-3 -end-3 w-20 h-20 text-slate-300 dark:text-slate-700" />
                            <div class="relative">
                                <p class="text-xs font-semibold uppercase {{ $stat['labelClass'] ?? 'text-blue-600 dark:text-blue-400' }} mb-1">{{ $stat['label'] }}</p>
                                <p class="text-base font-bold text-slate-900 dark:text-white">{{ $stat['value'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>