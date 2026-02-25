<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('target-units.title') }}</flux:heading>
            <flux:subheading>{{ __('target-units.subtitle') }}</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.target-units.create')" wire:navigate>
            {{ __('target-units.add_target_unit') }}
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
         x-on:target-unit-deleted.window="show = true; setTimeout(() => show = false, 3000)"
         x-show="show"
         x-transition
         x-cloak>
        <flux:callout variant="success" icon="check-circle">
            {{ __('target-units.target_unit_deleted') }}
        </flux:callout>
    </div>

    {{-- Card --}}
    <flux:card class="space-y-4">
        {{-- Search --}}
        <div class="max-w-sm">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('target-units.search_placeholder') }}"
                icon="magnifying-glass"
            />
        </div>

        {{-- Table --}}
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('target-units.table.id') }}</flux:table.column>
                <flux:table.column>{{ __('target-units.table.name') }}</flux:table.column>
                <flux:table.column class="w-32">{{ __('target-units.table.actions') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($target_units as $target_unit)
                    <flux:table.row wire:key="target_units-{{ $target_unit->id }}">
                        <flux:table.column>{{ $target_unit->id }}</flux:table.column>
                        <flux:table.column>{{ $target_unit->name }}</flux:table.column>
                        <flux:table.column>
                            <div class="flex items-center gap-1">
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    icon="pencil"
                                    :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.target-units.edit', $target_unit)"
                                    wire:navigate
                                />
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    icon="trash"
                                    wire:click="delete({{ $target_unit->id }})"
                                    wire:confirm="{{ __('target-units.delete_confirm') }}"
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
                                <flux:text class="text-zinc-500">{{ __('target-units.no_found') }}</flux:text>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        {{-- Pagination --}}
        @if ($target_units->hasPages())
            <div class="pt-4">
                {{ $target_units->links() }}
            </div>
        @endif
    </flux:card>
</div>

