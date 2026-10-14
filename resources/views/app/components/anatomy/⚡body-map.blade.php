<?php

use App\Services\Knowledge\OrganAttachmentService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * The poultry body map.
 *
 * The page was planned on the belief that `important_organs_affected` was
 * populated for 152 diseases and that only an illustration was missing. There is
 * no such column, and `knowledge_payload` carries no such key either, so the
 * curated path from a disease to an organ reaches exactly two of the 151 poultry
 * diseases. A diagram built on that alone would be a chicken with nothing on it.
 *
 * So the panel shows two tiers and names them. Diseases mapped through
 * `clinical_signs.anatomical_structure_id` are what the data asserts. Diseases
 * reached because a recorded sign or lesion happens to name the organ are what
 * this component inferred, and they are labelled as inferred wherever they
 * appear. 119 of 151 poultry diseases are reachable that way, against 2 mapped —
 * which is also the worklist for the curation pass that would promote any of them.
 *
 * The diagram is schematic and says so. Marker positions live in
 * `anatomy/body-map-diagram.blade.php` and are indicative only.
 *
 * @see \App\Services\Knowledge\OrganAttachmentService
 */
new class extends Component
{
    public string $part = 'poultry';

    #[Url(as: 'system', history: false)]
    public string $selectedSystem = '';

    #[Url(as: 'organ', history: false)]
    public string $selectedOrgan = '';

    #[Url(as: 'view', history: false)]
    public string $viewMode = 'diagram';

    public function mount(?string $part = null): void
    {
        $this->part = $this->resolvePart($part);
    }

    /**
     * @return Collection<int, array{
     *     structure: \App\Models\LargeAnimals\AnatomicalStructure,
     *     mapped: Collection<int, \App\Models\Disease>,
     *     derived: Collection<int, \App\Models\Disease>,
     *     signs: Collection<int, \App\Models\LargeAnimals\Finding>,
     *     lesions: Collection<int, \App\Models\LargeAnimals\Finding>,
     * }>
     */
    #[Computed]
    public function attachments(): Collection
    {
        return app(OrganAttachmentService::class)->attachments($this->domain());
    }

    /**
     * Body systems for the part, richest first.
     *
     * Grouping by the structure's own body system is what keeps a system with no
     * structures off the page: there is no grouping key to put it under, so it
     * never becomes a clickable region that can only lead to an empty panel.
     *
     * Read as `$this->attachments` and `$this->systems`, never called. Livewire
     * caches a computed property on property access; a method call goes straight
     * to the real method and re-runs the whole service on every read, which is
     * how this page went from 6 queries to 30 before it was noticed.
     *
     * @return array<string, array{
     *     system: \App\Models\LargeAnimals\BodySystem,
     *     attachments: Collection<int, array<string, mixed>>,
     *     organs: int,
     *     diseases: int,
     * }>
     */
    #[Computed]
    public function systems(): array
    {
        return $this->attachments
            ->groupBy(fn (array $attachment): string => (string) $attachment['structure']->bodySystem->canonical_name)
            ->map(fn (Collection $attachments, string $key): array => [
                'key' => $key,
                'system' => $attachments->first()['structure']->bodySystem,
                'attachments' => $attachments->sortBy(
                    fn (array $attachment): int => $this->diseaseCount($attachment),
                    SORT_REGULAR,
                    true,
                )->values(),
                'organs' => $attachments->count(),
                'diseases' => $attachments
                    ->flatMap(fn (array $attachment): Collection => $attachment['mapped']->concat($attachment['derived']))
                    ->pluck('id')
                    ->unique()
                    ->count(),
            ])
            ->sortByDesc(fn (array $entry): int => $entry['diseases'])
            ->all();
    }

    /**
     * Every organ the diagram can mark, keyed by `canonical_name`.
     *
     * The markers are drawn from an authored geometry keyed the same way, and this
     * is the lookup that joins the two: a marker whose key is not in here drops
     * off the bird rather than opening a panel with nothing in it.
     *
     * @return array<string, array{name: string, diseases: int}>
     */
    #[Computed]
    public function organIndex(): array
    {
        $index = [];

        foreach ($this->attachments as $attachment) {
            $structure = $attachment['structure'];

            $index[$structure->canonical_name] = [
                'name' => $structure->localized_display_name,
                'diseases' => $this->diseaseCount($attachment),
            ];
        }

        return $index;
    }

    public function selectSystem(string $system): void
    {
        // Clicking the system you are already looking at puts the diagram back
        // to its resting state, which is what a second click on a map means.
        if ($this->selectedSystem === $system && $this->selectedOrgan === '') {
            $this->selectedSystem = '';

            return;
        }

        if (! array_key_exists($system, $this->systems)) {
            return;
        }

        $this->selectedSystem = $system;
        $this->selectedOrgan = '';
    }

    public function selectOrgan(string $organ): void
    {
        $attachment = $this->attachments->first(
            fn (array $candidate): bool => $candidate['structure']->canonical_name === $organ,
        );

        if ($attachment === null) {
            return;
        }

        $this->selectedOrgan = $organ;
        $this->selectedSystem = (string) $attachment['structure']->bodySystem->canonical_name;
    }

    public function clearSelection(): void
    {
        $this->selectedSystem = '';
        $this->selectedOrgan = '';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function selectedSystemEntry(): ?array
    {
        return $this->selectedSystem === ''
            ? null
            : ($this->systems[$this->selectedSystem] ?? null);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function selectedOrganEntry(): ?array
    {
        if ($this->selectedOrgan === '') {
            return null;
        }

        return $this->attachments->first(
            fn (array $attachment): bool => $attachment['structure']->canonical_name === $this->selectedOrgan,
        );
    }

    public function diseaseCount(array $attachment): int
    {
        return $attachment['mapped']->concat($attachment['derived'])
            ->pluck('id')
            ->unique()
            ->count();
    }

    public function totalDiseases(): int
    {
        return collect($this->systems)
            ->flatMap(fn (array $entry): array => $entry['attachments']->all())
            ->flatMap(fn (array $attachment): Collection => $attachment['mapped']->concat($attachment['derived']))
            ->pluck('id')
            ->unique()
            ->count();
    }

    private function domain(): string
    {
        return (string) config("vetpedia.parts.{$this->part}.domain");
    }

    private function resolvePart(?string $part): string
    {
        $parts = array_keys((array) config('vetpedia.parts', []));

        return in_array($part, $parts, true) ? $part : (string) array_key_first($parts);
    }
};
?>

@php
    $t = fn (string $key, array $replace = []): string => __($this->part . '.body_map.' . $key, $replace);
    $systems = $this->systems();
    $organ = $this->selectedOrganEntry();
    $systemEntry = $organ === null ? $this->selectedSystemEntry() : null;
@endphp

<div class="space-y-6">
    <x-large-animals.page-hero
        :heading="$t('heading')"
        :subtitle="$t('subtitle')"
        :badge="$t('badge')"
        badgeIcon="scan-search"
        :stats="[
            ['count' => count($systems), 'label' => $t('stats.systems'), 'icon' => 'layout-grid'],
            ['count' => $this->attachments->count(), 'label' => $t('stats.organs'), 'icon' => 'bone'],
            ['count' => $this->totalDiseases(), 'label' => $t('stats.diseases'), 'icon' => 'stethoscope'],
        ]"
    />

    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-xs text-body dark:text-slate-400">{{ $t('schematic_note') }}</p>

        <div class="inline-flex rounded-base border border-default-medium dark:border-slate-700 p-0.5" role="group"
            aria-label="{{ $t('diagram_view') }} / {{ $t('list_view') }}">
            @foreach (['diagram' => $t('diagram_view'), 'list' => $t('list_view')] as $mode => $modeLabel)
                <button type="button" wire:click="$set('viewMode', '{{ $mode }}')"
                    @class([
                        'px-3 py-1.5 text-sm font-medium rounded-xs transition-colors duration-150',
                        'bg-brand text-white shadow-xs' => $this->viewMode === $mode,
                        'text-body dark:text-slate-400 hover:bg-neutral-secondary-soft dark:hover:bg-slate-700' => $this->viewMode !== $mode,
                    ])
                    aria-pressed="{{ $this->viewMode === $mode ? 'true' : 'false' }}">
                    {{ $modeLabel }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,26rem)] items-start">
        {{-- Diagram or the list that stands in for it ------------------------ --}}
        <div class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs border border-default-medium dark:border-slate-700 p-4 md:p-6">
            @if ($this->viewMode === 'diagram')
                @if ($systems === [])
                    <p class="py-12 text-center text-sm text-body dark:text-slate-400">{{ $t('no_system') }}</p>
                @else
                    <x-anatomy.body-map-diagram :organs="$this->organIndex()"
                        :selected-organ="$this->selectedOrgan" :selected-system="$this->selectedSystem"
                        :label="$t('diagram_label')" />

                    <ul class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-1.5">
                        @foreach ($systems as $entry)
                            <li>
                                <button type="button" wire:click="selectSystem('{{ $entry['key'] }}')"
                                    @class([
                                        'w-full text-start px-2.5 py-1.5 rounded-xs text-xs font-medium transition-colors duration-150 border',
                                        'bg-brand-soft border-brand text-fg-brand-strong' => $this->selectedSystem === $entry['key'],
                                        'border-transparent text-body dark:text-slate-400 hover:bg-neutral-secondary-soft dark:hover:bg-slate-700' => $this->selectedSystem !== $entry['key'],
                                    ])>
                                    {{ $entry['system']->localized_display_name }}
                                    <span class="opacity-70">({{ $entry['diseases'] }})</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @endif
            @else
                {{-- The fallback is a real list, not a degraded diagram: every
                     system and every organ, reachable by keyboard, with the
                     counts the markers would have carried. --}}
                @if ($systems === [])
                    <p class="py-12 text-center text-sm text-body dark:text-slate-400">{{ $t('no_system') }}</p>
                @else
                    <div class="space-y-4">
                        @foreach ($systems as $entry)
                            <section>
                                <h3 class="flex items-baseline justify-between gap-2 text-sm font-semibold text-heading dark:text-white">
                                    {{ $entry['system']->localized_display_name }}
                                    <span class="text-xs font-normal text-body dark:text-slate-400">
                                        {{ $t('organs') }}: {{ $entry['organs'] }} · {{ $t('diseases') }}: {{ $entry['diseases'] }}
                                    </span>
                                </h3>
                                <ul class="mt-2 flex flex-wrap gap-1.5">
                                    @foreach ($entry['attachments'] as $attachment)
                                        @php $organCount = $this->diseaseCount($attachment); @endphp
                                        <li>
                                            <button type="button" wire:click="selectOrgan('{{ $attachment['structure']->canonical_name }}')"
                                                @class([
                                                    'inline-flex items-center gap-1.5 rounded-xs px-2.5 py-1 text-xs font-medium border transition-colors duration-150',
                                                    'bg-brand-soft border-brand text-fg-brand-strong' => $this->selectedOrgan === $attachment['structure']->canonical_name,
                                                    'bg-neutral-secondary-soft border-default-medium text-body dark:text-slate-300 dark:bg-slate-700 dark:border-slate-600 hover:border-brand' => $this->selectedOrgan !== $attachment['structure']->canonical_name,
                                                ])>
                                                {{ $attachment['structure']->localized_display_name }}
                                                <span class="opacity-70">{{ $organCount }}</span>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </section>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>

        {{-- Panel ------------------------------------------------------------- --}}
        <aside class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs border border-default-medium dark:border-slate-700 p-5"
            aria-live="polite">
            @if ($organ !== null)
                @php
                    $structure = $organ['structure'];
                    $mappedCount = $organ['mapped']->count();
                    $derivedCount = $organ['derived']->count();
                @endphp

                <button type="button" wire:click="clearSelection"
                    class="inline-flex items-center gap-1 text-xs font-medium text-fg-brand hover:underline">
                    <x-lucide-arrow-left class="w-3.5 h-3.5 rtl:hidden" />
                    <x-lucide-arrow-right class="w-3.5 h-3.5 hidden rtl:block" />
                    {{ $t('back') }}
                </button>

                <h2 class="mt-2 text-xl font-bold text-heading dark:text-white">{{ $structure->localized_display_name }}</h2>
                <p class="text-sm text-body dark:text-slate-400">{{ $structure->bodySystem->localized_display_name }}</p>

                @if ($mappedCount === 0 && $derivedCount === 0 && $organ['signs']->isEmpty() && $organ['lesions']->isEmpty())
                    <p class="mt-4 text-sm text-body dark:text-slate-400">{{ $t('organ_empty') }}</p>
                @else
                    @if ($mappedCount > 0)
                        <section class="mt-5">
                            <h3 class="flex items-center gap-1.5 text-sm font-semibold text-heading dark:text-white">
                                <x-lucide-link class="w-4 h-4" />
                                {{ $t('mapped') }}
                                <span class="text-xs font-normal text-body dark:text-slate-400">({{ $mappedCount }})</span>
                            </h3>
                            <p class="mt-1 text-xs text-body dark:text-slate-400">{{ $t('mapped_note') }}</p>
                            <ul class="mt-2 space-y-1">
                                @foreach ($organ['mapped'] as $disease)
                                    <li>
                                        <a href="{{ route($this->part.'.diseases.show', $disease->slug) }}" wire:navigate
                                            class="text-sm text-fg-brand hover:underline">{{ $disease->localized_name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    @if ($derivedCount > 0)
                        <section class="mt-5">
                            <h3 class="flex items-center gap-1.5 text-sm font-semibold text-heading dark:text-white">
                                <x-lucide-spell-check class="w-4 h-4" />
                                {{ $t('derived') }}
                                <span class="text-xs font-normal text-body dark:text-slate-400">({{ $derivedCount }})</span>
                            </h3>
                            <p class="mt-1 rounded-base bg-warning-soft border border-warning-subtle px-2.5 py-1.5 text-xs text-fg-warning-strong dark:text-yellow-300">
                                {{ $t('derived_note') }}
                            </p>
                            <ul class="mt-2 space-y-1">
                                @foreach ($organ['derived'] as $disease)
                                    <li>
                                        <a href="{{ route($this->part.'.diseases.show', $disease->slug) }}" wire:navigate
                                            class="text-sm text-fg-brand hover:underline">{{ $disease->localized_name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    @if ($organ['signs']->isNotEmpty())
                        <section class="mt-5">
                            <h3 class="text-sm font-semibold text-heading dark:text-white">
                                {{ $t('signs') }}
                                <span class="text-xs font-normal text-body dark:text-slate-400">({{ $organ['signs']->count() }})</span>
                            </h3>
                            <ul class="mt-2 flex flex-wrap gap-1.5">
                                @foreach ($organ['signs'] as $sign)
                                    <li class="rounded-xs bg-neutral-secondary-soft dark:bg-slate-700 px-2 py-0.5 text-xs text-body dark:text-slate-300">
                                        {{ $sign->display_name }}
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    @if ($organ['lesions']->isNotEmpty())
                        <section class="mt-5">
                            <h3 class="text-sm font-semibold text-heading dark:text-white">
                                {{ $t('lesions') }}
                                <span class="text-xs font-normal text-body dark:text-slate-400">({{ $organ['lesions']->count() }})</span>
                            </h3>
                            <ul class="mt-2 flex flex-wrap gap-1.5">
                                @foreach ($organ['lesions'] as $lesion)
                                    <li class="rounded-xs bg-danger-soft border border-danger-subtle px-2 py-0.5 text-xs text-fg-danger-strong dark:text-red-300">
                                        {{ $lesion->display_name }}
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif
                @endif
            @elseif ($systemEntry !== null)
                <h2 class="text-xl font-bold text-heading dark:text-white">{{ $systemEntry['system']->localized_display_name }}</h2>
                <p class="mt-1 text-sm text-body dark:text-slate-400">{{ $t('select_organ') }}</p>

                <ul class="mt-4 space-y-1.5">
                    @foreach ($systemEntry['attachments'] as $attachment)
                        @php $organCount = $this->diseaseCount($attachment); @endphp
                        <li>
                            <button type="button" wire:click="selectOrgan('{{ $attachment['structure']->canonical_name }}')"
                                class="w-full flex items-center justify-between gap-2 rounded-base border border-default-medium dark:border-slate-600 bg-neutral-secondary-soft dark:bg-slate-700 px-3 py-2 text-sm transition-colors duration-150 hover:border-brand">
                                <span class="text-heading dark:text-white">{{ $attachment['structure']->localized_display_name }}</span>
                                <span @class([
                                    'text-xs font-semibold',
                                    'text-fg-brand-strong' => $organCount > 0,
                                    'text-body dark:text-slate-500' => $organCount === 0,
                                ])>{{ $organCount }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @else
                <h2 class="text-xl font-bold text-heading dark:text-white">{{ $t('legend') }}</h2>
                <p class="mt-1 text-sm text-body dark:text-slate-400">{{ $t('select_system') }}</p>

                <ul class="mt-4 space-y-1.5">
                    @foreach ($systems as $entry)
                        <li>
                            <button type="button" wire:click="selectSystem('{{ $entry['key'] }}')"
                                class="w-full flex items-center justify-between gap-2 rounded-base border border-default-medium dark:border-slate-600 bg-neutral-secondary-soft dark:bg-slate-700 px-3 py-2 text-sm transition-colors duration-150 hover:border-brand">
                                <span class="text-heading dark:text-white">{{ $entry['system']->localized_display_name }}</span>
                                <span class="text-xs font-semibold text-fg-brand-strong">{{ $entry['diseases'] }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </aside>
    </div>
</div>