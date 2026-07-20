<?php

use App\Actions\CompareAction;
use Livewire\Component;

new class extends Component
{
    public string $productId;

    public string $productName = '';

    public function toggle(): void
    {
        $action = app(CompareAction::class);

        if ($action->has($this->productId)) {
            $action->remove($this->productId);
        } elseif (! $action->isFull()) {
            $action->add($this->productId);
        }

        $this->dispatch('compare-updated');
    }
};
?>

<div x-data="{ active: @js(app(CompareAction::class)->has($productId)), full: @js(app(CompareAction::class)->isFull()) }"
     x-on:compare-count.window="full = $event.detail.count >= $event.detail.max"
     x-on:compare-toggle.window="if ($event.detail.id === '{{ $productId }}') { active = $event.detail.add; }"
     x-on:compare-clear.window="
         if (active) {
             active = false;
             $wire.toggle();
         }
">
    <button
        x-on:click="
            if (!active) {
                if (full) return;
                active = true;
            } else {
                active = false;
            }
            window.dispatchEvent(new CustomEvent('compare-toggle', {
                detail: { id: '{{ $productId }}', name: '{{ $productName }}', add: active }
            }));
            $wire.toggle().then(() => {
                window.dispatchEvent(new CustomEvent('compare-updated'));
            });
        "
        x-bind:disabled="!active && full"
        x-bind:title="active
            ? '{{ __('messages.compare.remove_from_compare') }}'
            : (full
                ? '{{ __('messages.compare.compare_full', ['max' => app(CompareAction::class)->max()]) }}'
                : '{{ __('messages.compare.add_to_compare') }}')"
        x-bind:class="active
            ? 'bg-brand/10 text-brand hover:bg-brand/20 dark:bg-brand/20 dark:text-brand'
            : (full
                ? 'text-body/30 cursor-not-allowed dark:text-slate-600'
                : 'text-body hover:text-brand hover:bg-brand/10 dark:text-slate-400 dark:hover:text-brand dark:hover:bg-brand/20')"
        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-base transition-all duration-200 text-xs font-medium whitespace-nowrap">
        <span x-show="active" x-cloak>
            <x-lucide-check-circle class="w-4 h-4" />
        </span>
        <span x-show="!active" x-cloak>
            <x-lucide-scale class="w-4 h-4" />
        </span>
        <span x-show="active" x-cloak>{{ __('messages.compare.in_compare') }}</span>
        <span x-show="!active" x-cloak>{{ __('messages.compare.compare') }}</span>
    </button>
</div>
