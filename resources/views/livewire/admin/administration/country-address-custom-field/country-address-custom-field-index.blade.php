<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('ecommerce::country_address_custom_fields.title') }}</flux:heading>
            <flux:subheading>{{ __('ecommerce::country_address_custom_fields.subtitle') }}</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus"
            :href="route(config('ud-ecommerce.admin_route_prefix', 'ecommerce') . '.configs.country_address_custom_fields.create')"
            wire:navigate>
            {{ __('ecommerce::country_address_custom_fields.add_field') }}
        </flux:button>
    </div>

    {{-- Status Message --}}
    @if (session('status'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('status') }}
        </flux:callout>
    @endif

    {{-- Livewire Flash Message --}}
    <div x-data="{ show: false }" x-on:field-deleted.window="show = true; setTimeout(() => show = false, 3000)"
        x-show="show" x-transition x-cloak>
        <flux:callout variant="success" icon="check-circle">
            {{ __('ecommerce::country_address_custom_fields.field_deleted') }}
        </flux:callout>
    </div>

    {{-- Card --}}
    <flux:card class="space-y-4">
        {{-- Search --}}
        <div class="max-w-sm">
            <flux:input wire:model.live.debounce.300ms="search"
                placeholder="{{ __('ecommerce::country_address_custom_fields.search_placeholder') }}"
                icon="magnifying-glass" />
        </div>

        {{-- Table --}}
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('ecommerce::country_address_custom_fields.table.id') }}</flux:table.column>
                <flux:table.column>{{ __('ecommerce::country_address_custom_fields.table.country') }}
                </flux:table.column>
                <flux:table.column>{{ __('ecommerce::country_address_custom_fields.table.name') }}</flux:table.column>
                <flux:table.column>{{ __('ecommerce::country_address_custom_fields.table.label') }}</flux:table.column>
                <flux:table.column>{{ __('ecommerce::country_address_custom_fields.table.type') }}</flux:table.column>
                <flux:table.column>{{ __('ecommerce::country_address_custom_fields.table.is_required') }}
                </flux:table.column>
                <flux:table.column>{{ __('ecommerce::country_address_custom_fields.table.is_active') }}
                </flux:table.column>
                <flux:table.column class="w-32">{{ __('ecommerce::country_address_custom_fields.table.actions') }}
                </flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($fields as $field)
                    <flux:table.row wire:key="field-{{ $field->id }}">
                        <flux:table.cell>{{ $field->id }}</flux:table.cell>
                        <flux:table.cell class="font-medium">
                            <flux:badge color="zinc" size="sm">{{ $field->country }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ $field->name }}</flux:table.cell>
                        <flux:table.cell>{{ $field->label }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge variant="outline" size="sm">{{ $field->type }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($field->is_required)
                                <flux:icon.check class="size-4 text-green-500" />
                            @else
                                <flux:icon.x-mark class="size-4 text-zinc-400" />
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($field->is_active)
                                <flux:badge color="green" size="sm">
                                    {{ __('ecommerce::country_address_custom_fields.status.active') }}</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">
                                    {{ __('ecommerce::country_address_custom_fields.status.inactive') }}</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button size="sm" variant="ghost" icon="pencil"
                                    :href="route(config('ud-ecommerce.admin_route_prefix', 'ecommerce') . '.configs.country_address_custom_fields.edit', $field)"
                                    wire:navigate />
                                <flux:button size="sm" variant="ghost" icon="trash" wire:click="delete({{ $field->id }})"
                                    wire:confirm="{{ __('ecommerce::country_address_custom_fields.delete_confirm') }}"
                                    class="text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="text-center py-8">
                            <div class="flex flex-col items-center gap-2">
                                <flux:icon.document-text class="size-12 text-zinc-300 dark:text-zinc-600" />
                                <flux:text class="text-zinc-500">
                                    {{ __('ecommerce::country_address_custom_fields.no_fields_found') }}</flux:text>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        {{-- Pagination --}}
        @if ($fields->hasPages())
            <div class="pt-4">
                {{ $fields->links() }}
            </div>
        @endif
    </flux:card>
</div>