@props([
    'applyAction',
    'form',
])

<div {{ $attributes->class(['fi-ta-filters grid gap-y-4']) }}>
    <div class="flex items-center justify-between">
        <h4
            class="text-base font-semibold leading-6 text-gray-950 dark:text-white"
        >
            {{ __('filament-tables::table.filters.heading') }}
        </h4>

        <x-filament::loading-indicator
            :attributes="
                \Filament\Support\prepare_inherited_attributes(
                    new \Illuminate\View\ComponentAttributeBag([
                        'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                        'wire:target' => 'tableFilters,applyTableFilters,resetTableFiltersForm',
                    ])
                )->class(['h-5 w-5 text-gray-400 dark:text-gray-500'])
            "
        />
    </div>

    {{ $form }}

    @if ($applyAction->isVisible())
        <div class="flex items-center gap-3">
            {{ $applyAction }}

            <x-filament::button
                color="gray"
                tag="button"
                wire:click="resetTableFiltersForm"
                wire:loading.remove.delay="{{ config('filament.livewire_loading_delay', 'default') }}"
                wire:target="resetTableFiltersForm"
            >
                Clear
            </x-filament::button>
        </div>
    @endif
</div>
