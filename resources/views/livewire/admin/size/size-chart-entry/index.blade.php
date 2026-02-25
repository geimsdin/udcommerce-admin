<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('size-chart-entries.title') }}</flux:heading>
            <flux:subheading>{{ __('size-chart-entries.subtitle') }}</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.size-chart-entries.create')" wire:navigate>
            {{ __('size-chart-entries.add_size_chart_entry') }}
        </flux:button>
    </div>

    {{-- Status Message --}}
    @if (session('status'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('status') }}
        </flux:callout>
    @endif

    {{-- Livewire Flash Message --}}
    <div x-data="{ show: false }"
         x-on:size-chart-entry-deleted.window="show = true; setTimeout(() => show = false, 3000)"
         x-show="show"
         x-transition
         x-cloak>
        <flux:callout variant="success" icon="check-circle">
            {{ __('size-chart-entries.size_chart_entry_deleted') }}
        </flux:callout>
    </div>

    {{-- Card --}}
    <flux:card class="space-y-4">
        {{-- Search --}}
        <div class="max-w-sm">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('size-chart-entries.search_placeholder') }}"
                icon="magnifying-glass"
            />
        </div>

        {{-- Table --}}
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('size-chart-entries.table.id') }}</flux:table.column>
                <flux:table.column>{{ __('size-chart-entries.table.converted_value') }}</flux:table.column>
                <flux:table.column>{{ __('size-chart-entries.table.size_chart') }}</flux:table.column>
                <flux:table.column>{{ __('size-chart-entries.table.variant') }}</flux:table.column>
                <flux:table.column>{{ __('size-chart-entries.table.target_unit') }}</flux:table.column>
                <flux:table.column class="w-32">{{ __('size-chart-entries.table.actions') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($size_chart_entries as $size_chart_entry)
                    <flux:table.row wire:key="size_chart_entries-{{ $size_chart_entry->id }}">
                        <flux:table.column>{{ $size_chart_entry->id }}</flux:table.column>
                        <flux:table.column>{{ $size_chart_entry->converted_value }}</flux:table.column>
                        <flux:table.column>{{ $size_chart_entry->sizechart->name }}</flux:table.column>
                        <flux:table.column>{{ $size_chart_entry->variant->getNameCurrentLanguage($selected_language) }}</flux:table.column>
                        <flux:table.column>{{ $size_chart_entry->targetunit->name }}</flux:table.column>
                        <flux:table.column>
                            <div class="flex items-center gap-1">
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    icon="pencil"
                                    :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.size-chart-entries.edit', $size_chart_entry)"
                                    wire:navigate
                                />
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    icon="trash"
                                    wire:click="delete({{ $size_chart_entry->id }})"
                                    wire:confirm="{{ __('size-chart-entries.delete_confirm') }}"
                                    class="text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20"
                                />
                            </div>
                        </flux:table.column>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center py-8">
                            <div class="flex flex-col items-center gap-2">
                                <flux:icon.users class="size-12 text-zinc-300 dark:text-zinc-600" />
                                <flux:text class="text-zinc-500">{{ __('size-chart-entries.no_found') }}</flux:text>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        {{-- Pagination --}}
        @if ($size_chart_entries->hasPages())
            <div class="pt-4">
                {{ $size_chart_entries->links() }}
            </div>
        @endif
    </flux:card>
</div>

