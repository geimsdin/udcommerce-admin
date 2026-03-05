<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('ecommerce::image-settings.title') }}</flux:heading>
            <flux:subheading>{{ __('ecommerce::image-settings.subtitle') }}</flux:subheading>
        </div>
        <div class="flex items-center gap-2">
            <flux:button wire:click="regenerateThumbnails"
                wire:confirm="{{ __('ecommerce::image-settings.regenerate_confirm') }}">
                <flux:icon name="arrow-path" class="size-4 mr-1" />
                {{ __('ecommerce::image-settings.regenerate_thumbnails') }}
            </flux:button>
            <flux:button wire:click="seedDefaults"
                wire:confirm="{{ __('ecommerce::image-settings.seed_defaults_confirm') }}">
                {{ __('ecommerce::image-settings.seed_defaults') }}
            </flux:button>
            <flux:button variant="primary"
                :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.image-settings.create')"
                wire:navigate>
                {{ __('ecommerce::image-settings.add_image_type') }}
            </flux:button>
        </div>
    </div>

    {{-- Table --}}
    <flux:card>
        @if ($imageSettings->isEmpty())
            <div class="text-center py-12">
                <flux:icon name="photo" class="size-12 text-zinc-300 dark:text-zinc-600 mx-auto mb-4" />
                <flux:heading size="lg">{{ __('ecommerce::image-settings.no_image_types') }}</flux:heading>
                <flux:subheading class="mt-1">{{ __('ecommerce::image-settings.no_image_types_text') }}</flux:subheading>
            </div>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('ecommerce::image-settings.table.name') }}</flux:table.column>
                    <flux:table.column>{{ __('ecommerce::image-settings.table.dimensions') }}</flux:table.column>
                    <flux:table.column>{{ __('ecommerce::image-settings.table.products') }}</flux:table.column>
                    <flux:table.column>{{ __('ecommerce::image-settings.table.categories') }}</flux:table.column>
                    <flux:table.column>{{ __('ecommerce::image-settings.table.brands') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($imageSettings as $imageSetting)
                        <flux:table.row>
                            <flux:table.cell>
                                <span class="font-mono text-sm font-medium">{{ $imageSetting->name }}</span>
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="text-sm">{{ $imageSetting->width }} × {{ $imageSetting->height }} px</span>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if ($imageSetting->products)
                                    <flux:badge color="green" size="sm">{{ __('common.yes') }}</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">{{ __('common.no') }}</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                @if ($imageSetting->categories)
                                    <flux:badge color="green" size="sm">{{ __('common.yes') }}</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">{{ __('common.no') }}</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                @if ($imageSetting->brands)
                                    <flux:badge color="green" size="sm">{{ __('common.yes') }}</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">{{ __('common.no') }}</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex items-center gap-2 justify-end">
                                    <flux:button size="sm" variant="ghost"
                                        :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.image-settings.edit', $imageSetting)"
                                        wire:navigate>
                                        {{ __('common.edit') }}
                                    </flux:button>
                                    <flux:button size="sm" variant="ghost" wire:click="delete({{ $imageSetting->id }})"
                                        wire:confirm="{{ __('ecommerce::image-settings.delete_confirmation_text') }}">
                                        <flux:icon name="trash" class="size-4 text-red-500" />
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif
    </flux:card>

    {{-- Info callout --}}
    <flux:callout variant="info" icon="information-circle">
        {{ __('ecommerce::image-settings.regenerate_info') }}
    </flux:callout>
</div>