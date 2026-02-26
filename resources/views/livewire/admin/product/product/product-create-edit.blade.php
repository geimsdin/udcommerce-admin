<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" class="text-zinc-900 dark:text-zinc-100">
                {{ $isEditing ? __('ecommerce::products.edit_product') : __('ecommerce::products.add_product') }}
            </flux:heading>
            <flux:subheading class="mt-1 text-zinc-500 dark:text-zinc-400">
                {{ $isEditing ? __('ecommerce::products.messages.edit_subtitle') : __('ecommerce::products.messages.create_subtitle') }}
            </flux:subheading>
        </div>
        <flux:button 
            variant="ghost" 
            :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.products.index')" 
            wire:navigate
            icon="arrow-left"
        >
            {{ __('ecommerce::products.back_to_list') }}
        </flux:button>
    </div>
    {{-- Flash Messages --}}
    <div class="space-y-3">
        @if (session('success'))
            <div x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 5000)"
                x-show="show"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                <flux:callout variant="success" icon="check-circle">
                    {{ session('success') }}
                </flux:callout>
            </div>
        @endif
        @if (session('error'))
            <div x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 5000)"
                x-show="show"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                <flux:callout variant="danger" icon="x-circle">
                    {{ session('error') }}
                </flux:callout>
            </div>
        @endif
        @if (session('warning'))
            <div x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 5000)"
                x-show="show"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
                <flux:callout variant="warning" icon="exclamation-triangle">
                    {{ session('warning') }}
                </flux:callout>
            </div>
        @endif
    </div>

    <flux:card>
        <form wire:submit.prevent="save">
            <flux:tab.group>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <flux:tabs>
                            @foreach($this->getProductTab as $key => $value)
                                <flux:tab name="{{ $key }}">{{ $value }}</flux:tab>
                            @endforeach
                        </flux:tabs>
                    </div>
                    <div class="lg:col-span-1">
                        <livewire:lmt-LangSelector wire:model.live="selected_language" />
                    </div>
                </div>
                <flux:tab.panel name="general" wire:key="panel-general">
                    <div class="space-y-6">
                        {{-- Basic Information --}}
                        <flux:card>
                            <div class="mb-4">
                                <flux:heading size="md" class="text-zinc-900 dark:text-zinc-100">
                                    {{ __('ecommerce::products.form.basic_information') }}
                                </flux:heading>
                                <flux:subheading class="text-zinc-500 dark:text-zinc-400">
                                    {{ __('ecommerce::products.form.basic_information_subtitle') }}
                                </flux:subheading>
                            </div>
                            <div class="space-y-4">
                                <livewire:lmt-TextInput 
                                    label="{{ __('ecommerce::products.form.name') }}"
                                    placeholder="{{ __('ecommerce::products.form.name_placeholder') }}" 
                                    wire:model="name"
                                    :required="true" 
                                />
                                <livewire:lmt-TextInput 
                                    label="{{ __('ecommerce::products.form.link_rewrite') }}"
                                    placeholder="{{ __('ecommerce::products.form.link_rewrite_placeholder') }}" 
                                    wire:model="link_rewrite"
                                />
                                <livewire:lmt-RichEditor 
                                    wire:model="description_short" 
                                    :label="__('ecommerce::products.form.description_short')" 
                                />
                                <livewire:lmt-RichEditor 
                                    wire:model="description_long" 
                                    :label="__('ecommerce::products.form.description_long')" 
                                />
                            </div>
                        </flux:card>

                        {{-- Product Settings --}}
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <flux:card class="lg:col-span-2">
                                <div class="mb-4">
                                    <flux:heading size="md" class="text-zinc-900 dark:text-zinc-100">
                                        {{ __('ecommerce::products.form.product_settings') }}
                                    </flux:heading>
                                    <flux:subheading class="text-zinc-500 dark:text-zinc-400">
                                        {{ __('ecommerce::products.form.product_settings_subtitle') }}
                                    </flux:subheading>
                                </div>
                                <div class="space-y-4">
                                    <flux:select 
                                        variant="listbox" 
                                        :required="true" 
                                        :placeholder="__('ecommerce::products.form.product_type_placeholder')" 
                                        :label="__('ecommerce::products.form.product_type')" 
                                        wire:model.live="product_type"
                                    >
                                        @foreach($this->getProductTypes() as $key => $product_type_label)
                                            <flux:select.option :value="$key">{{ $product_type_label }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    <flux:select 
                                        variant="listbox" 
                                        :required="true" 
                                        :placeholder="__('ecommerce::products.form.brand_placeholder')" 
                                        :label="__('ecommerce::products.form.brand')" 
                                        wire:model="brand_id" 
                                        searchable 
                                        clearable
                                    >
                                        @foreach($this->getBrands() as $brand)
                                            <flux:select.option :value="$brand->id">{{ $brand->name }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    <flux:select 
                                        variant="listbox" 
                                        :required="true" 
                                        :placeholder="__('ecommerce::products.form.category_placeholder')" 
                                        :label="__('ecommerce::products.form.category')" 
                                        wire:model="category_ids" 
                                        searchable 
                                        clearable 
                                        multiple
                                    >
                                        @foreach($this->getCategories() as $category)
                                            <flux:select.option :value="$category->id">
                                                {{ $category->currentLanguage?->name ?? $category->name ?? __('ecommerce::products.form.category') . ' #' . $category->id }}
                                            </flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    <flux:select 
                                        variant="listbox" 
                                        :required="true" 
                                        :placeholder="__('ecommerce::products.form.season_placeholder')" 
                                        :label="__('ecommerce::products.form.season')" 
                                        wire:model="season_ids" 
                                        searchable 
                                        clearable 
                                        multiple
                                    >
                                        @foreach($this->getSeasons() as $season)
                                            <flux:select.option :value="$season->id">{{ $season->name }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                </div>
                            </flux:card>

                            <flux:card>
                                {{-- <div class="mb-4">
                                    <flux:heading size="md" class="text-zinc-900 dark:text-zinc-100">
                                        {{ __('ecommerce::products.form.status') }}
                                    </flux:heading>
                                    <flux:subheading class="text-zinc-500 dark:text-zinc-400">
                                        {{ __('ecommerce::products.form.status_subtitle') }}
                                    </flux:subheading>
                                </div> --}}
                                <div class="space-y-4">
                                    <flux:switch 
                                        :label="__('ecommerce::products.form.status')" 
                                        wire:model="status"
                                        :description="__('ecommerce::products.form.status_description')"
                                    />
                                </div>
                            </flux:card>
                        </div>

                        {{-- Product Identifiers --}}
                        <flux:card>
                            <div class="mb-4">
                                <flux:heading size="md" class="text-zinc-900 dark:text-zinc-100">
                                    {{ __('ecommerce::products.form.product_identifiers') }}
                                </flux:heading>
                                <flux:subheading class="text-zinc-500 dark:text-zinc-400">
                                    {{ __('ecommerce::products.form.product_identifiers_subtitle') }}
                                </flux:subheading>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <flux:input
                                    wire:model="sku"
                                    :label="__('ecommerce::products.form.sku')"
                                    placeholder="{{ __('ecommerce::products.form.sku_placeholder') }}"
                                />
                                <flux:input
                                    wire:model="ean"
                                    :label="__('ecommerce::products.form.ean')"
                                    placeholder="{{ __('ecommerce::products.form.ean_placeholder') }}"
                                />
                                <flux:input
                                    wire:model="mpn"
                                    :label="__('ecommerce::products.form.mpn')"
                                    placeholder="{{ __('ecommerce::products.form.mpn_placeholder') }}"
                                />
                                <flux:input
                                    wire:model="upc"
                                    :label="__('ecommerce::products.form.upc')"
                                    placeholder="{{ __('ecommerce::products.form.upc_placeholder') }}"
                                />
                                <flux:input
                                    wire:model="isbn"
                                    :label="__('ecommerce::products.form.isbn')"
                                    placeholder="{{ __('ecommerce::products.form.isbn_placeholder') }}"
                                />
                            </div>
                        </flux:card>

                        {{-- Features --}}
                        <flux:card>
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <flux:heading size="md" class="text-zinc-900 dark:text-zinc-100">
                                        {{ __('ecommerce::products.form.features') }}
                                    </flux:heading>
                                    <flux:subheading class="text-zinc-500 dark:text-zinc-400">
                                        {{ __('ecommerce::products.form.features_subtitle') }}
                                    </flux:subheading>
                                </div>
                                <flux:button
                                    variant="primary"
                                    icon="plus"
                                    wire:click.prevent="addProductFeature"
                                    type="button"
                                    size="sm"
                                >
                                    {{ __('ecommerce::products.form.add_feature') }}
                                </flux:button>
                            </div>

                            @if (count($productFeatures) > 0)
                                <div class="space-y-4">
                                    @foreach ($productFeatures as $index => $featureRow)
                                        <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-4" wire:key="feature-row-{{ $index }}">
                                            <div class="grid grid-cols-12 gap-4 items-end">
                                                {{-- Feature Group --}}
                                                <div class="col-span-3">
                                                    <flux:select
                                                        variant="listbox"
                                                        :label="__('ecommerce::products.form.feature_group')"
                                                        :placeholder="__('ecommerce::products.form.feature_group_placeholder')"
                                                        wire:model.live="productFeatures.{{ $index }}.feature_group_id"
                                                        searchable
                                                        clearable
                                                    >
                                                        @foreach($this->getFeatureGroups as $featureGroup)
                                                            <flux:select.option :value="$featureGroup->id">
                                                                {{ $featureGroup->name }}
                                                            </flux:select.option>
                                                        @endforeach
                                                    </flux:select>
                                                </div>

                                                <div class="col-span-6">
                                                    @if (!empty($featureRow['feature_group_id']))
                                                        @if($this->getFeaturesByGroup($featureRow['feature_group_id'])->isNotEmpty())
                                                            <flux:select
                                                                variant="listbox"
                                                                :label="__('ecommerce::products.form.feature')"
                                                                :placeholder="__('ecommerce::products.form.feature_placeholder')"
                                                                wire:model="productFeatures.{{ $index }}.feature_ids"
                                                                searchable
                                                                clearable
                                                                multiple
                                                            >
                                                                @foreach($this->getFeaturesByGroup($featureRow['feature_group_id']) as $feature)
                                                                    <flux:select.option :value="$feature->id">
                                                                        {{ $feature->name }}
                                                                    </flux:select.option>
                                                                @endforeach
                                                            </flux:select>
                                                        @else
                                                            <flux:label>{{ __('ecommerce::products.form.feature') }}</flux:label>
                                                            <flux:text class="text-zinc-400 dark:text-zinc-500 text-sm mt-1">
                                                                {{ __('ecommerce::products.form.no_features_in_group') }}
                                                            </flux:text>
                                                        @endif
                                                    @else
                                                        <flux:label>{{ __('ecommerce::products.form.feature') }}</flux:label>
                                                        <flux:text class="text-zinc-400 dark:text-zinc-500 text-sm mt-1">
                                                            {{ __('ecommerce::products.form.select_feature_group_first') }}
                                                        </flux:text>
                                                    @endif
                                                </div>

                                                {{-- Delete Button --}}
                                                <div class="col-span-2 flex items-end">
                                                    <flux:button
                                                        variant="danger"
                                                        icon="trash"
                                                        wire:click.prevent="removeProductFeature({{ $index }})"
                                                        type="button"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                            <flux:icon.tag class="w-8 h-8 text-zinc-400" />
                                        </div>
                                        <flux:text class="text-zinc-500 dark:text-zinc-400">
                                            {{ __('ecommerce::products.form.no_features_added') }}
                                        </flux:text>
                                        <flux:button
                                            variant="ghost"
                                            icon="plus"
                                            wire:click.prevent="addProductFeature"
                                            type="button"
                                            size="sm"
                                        >
                                            {{ __('ecommerce::products.form.add_feature') }}
                                        </flux:button>
                                    </div>
                                </div>
                            @endif
                        </flux:card>
                    </div>
                </flux:tab.panel>
                @if ($product_type == 'variable')
                    <flux:tab.panel name="variations" wire:key="panel-variations">
                        <flux:card>
                            <div class="flex justify-between items-center mb-6">
                                <div>
                                    <flux:heading size="md" class="text-zinc-900 dark:text-zinc-100">
                                        {{ __('ecommerce::products.variations') }}
                                    </flux:heading>
                                    <flux:subheading class="text-zinc-500 dark:text-zinc-400">
                                        {{ __('ecommerce::products.variations_subtitle') }}
                                    </flux:subheading>
                                </div>
                                <div class="flex gap-2">
                                    <flux:button
                                        variant="primary"
                                        icon="sparkles"
                                        wire:click.prevent="openGroupVariantModal"
                                        type="button"
                                    >
                                        {{ __('ecommerce::products.auto_generate_variations') }}
                                    </flux:button>
                                    <flux:button
                                        variant="subtle"
                                        icon="plus"
                                        wire:click.prevent="addVariation"
                                        type="button"
                                    >
                                        {{ __('ecommerce::products.add_variation') }}
                                    </flux:button>
                                </div>
                            </div>

                            @if (count($variations) > 0)
                                <div class="space-y-4">
                                    <flux:accordion transition>
                                        @foreach ($variations as $index => $variation)
                                            <flux:card class="mb-6" wire:key="variation-card-{{ $index }}">
                                                <flux:accordion.item>
                                                    <flux:accordion.heading>
                                                        <div class="flex justify-between items-center gap-2">
                                                            <div class="flex items-center gap-2">
                                                                <flux:heading size="sm">#{{ __('ecommerce::products.variation') }} {{ $index + 1 }}</flux:heading>
                                                                @if (!empty($variation['variants_by_group']))
                                                                    <div class="flex flex-wrap gap-2">
                                                                        @foreach($variation['variants_by_group'] as $groupId => $variantId)
                                                                            @php
                                                                                $variant = $this->getVariants->find($variantId);
                                                                                $group = $this->getVariantGroups->find($groupId);
                                                                            @endphp
                                                                            @if($variant && $group)
                                                                                <flux:badge>{{ $group->name }} : {{ $variant->name }}</flux:badge>
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </flux:accordion.heading>
                                                    <flux:accordion.content class="mt-2">
                                                        <flux:separator class="mb-4"/>
                                                        <div class="grid grid-cols-1 gap-4">
                                                            <div class="grid grid-cols-1 grid-cols-4 gap-4">
                                                                <flux:input
                                                                    type="number"
                                                                    step="0.01"
                                                                    wire:model.defer="variations.{{ $index }}.price"
                                                                    :label="__('ecommerce::products.form.price')"
                                                                    :placeholder="__('ecommerce::products.form.price_placeholder')"
                                                                />
                                                                <flux:input
                                                                    wire:model.defer="variations.{{ $index }}.sku"
                                                                    :label="__('ecommerce::products.form.sku')"
                                                                    :placeholder="__('ecommerce::products.form.sku_placeholder')"
                                                                />
                                                                <flux:input
                                                                    wire:model.defer="variations.{{ $index }}.ean"
                                                                    :label="__('ecommerce::products.form.ean')"
                                                                    :placeholder="__('ecommerce::products.form.ean_placeholder')"
                                                                />
                                                                <flux:input
                                                                    wire:model.defer="variations.{{ $index }}.mpn"
                                                                    :label="__('ecommerce::products.form.mpn')"
                                                                    :placeholder="__('ecommerce::products.form.mpn_placeholder')"
                                                                />
                                                                <flux:input
                                                                    wire:model.defer="variations.{{ $index }}.upc"
                                                                    :label="__('ecommerce::products.form.upc')"
                                                                    :placeholder="__('ecommerce::products.form.upc_placeholder')"
                                                                />
                                                                <flux:input
                                                                    wire:model.defer="variations.{{ $index }}.isbn"
                                                                    :label="__('ecommerce::products.form.isbn')"
                                                                    :placeholder="__('ecommerce::products.form.isbn_placeholder')"
                                                                />
                                                            </div>
                                                            <div>
                                                                <flux:separator class="my-4"/>
                                                                <div class="grid grid-cols-1 grid-cols-4 gap-4">
                                                                    <flux:input
                                                                        type="number"
                                                                        wire:model.defer="variations.{{ $index }}.quantity"
                                                                        :label="__('ecommerce::products.form.quantity')"
                                                                        :placeholder="__('ecommerce::products.form.quantity_placeholder')"
                                                                    />
                                                                    <flux:input
                                                                        type="number"
                                                                        wire:model.defer="variations.{{ $index }}.minimal_quantity"
                                                                        :label="__('ecommerce::products.form.minimal_quantity')"
                                                                        :placeholder="__('ecommerce::products.form.minimal_quantity_placeholder')"
                                                                    />
                                                                    <flux:input
                                                                        type="number"
                                                                        wire:model.defer="variations.{{ $index }}.low_stock_alert"
                                                                        :label="__('ecommerce::products.form.low_stock_alert')"
                                                                        :placeholder="__('ecommerce::products.form.low_stock_alert_placeholder')"
                                                                    />
                                                                    <flux:date-picker
                                                                        wire:model.defer="variations.{{ $index }}.available_from"
                                                                        :label="__('ecommerce::products.form.available_from')"
                                                                        :placeholder="__('ecommerce::products.form.available_from_placeholder')"
                                                                    />
                                                                    <flux:date-picker
                                                                        wire:model.defer="variations.{{ $index }}.available_to"
                                                                        :label="__('ecommerce::products.form.available_to')"
                                                                        :placeholder="__('ecommerce::products.form.available_to_placeholder')"
                                                                    />
                                                                </div>
                                                            </div>
                                                            <div class="pt-4">
                                                                <flux:heading size="sm" class="mb-3">{{ __('ecommerce::products.form.variation_image') }}</flux:heading>
                                                                @if (isset($variation['image']) || isset($variation['image_path']))
                                                                    <div class="mt-2">
                                                                        <div class="relative w-full">
                                                                            <div class="w-full h-48 rounded-lg overflow-hidden border-2 border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 flex items-center justify-center p-6">
                                                                                @if (isset($variation['image']))
                                                                                    <img src="{{ $variation['image']->temporaryUrl() }}" class="max-h-full max-w-full object-contain" />
                                                                                @elseif (isset($variation['image_path']))
                                                                                    <img src="{{ asset('storage/' . $variation['image_path']) }}" class="max-h-full max-w-full object-contain" />
                                                                                @endif
                                                                            </div>

                                                                            <button
                                                                                type="button"
                                                                                wire:click="removeVariationImage({{ $index }})"
                                                                                class="absolute -top-2 -right-2 size-8 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center shadow-lg transition-colors"
                                                                            >
                                                                                <flux:icon name="x-mark" class="size-5" />
                                                                            </button>
                                                                        </div>

                                                                    </div>
                                                                @else
                                                                    <flux:file-upload wire:model="variations.{{ $index }}.image" class="mt-2">
                                                                        <div class="flex flex-col items-center justify-center py-8 px-4 border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-900/50 hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors cursor-pointer">
                                                                            <div class="size-16 rounded-full bg-zinc-200 dark:bg-zinc-800 flex items-center justify-center mb-4">
                                                                                <flux:icon name="photo" class="size-8 text-zinc-500 dark:text-zinc-400" />
                                                                            </div>

                                                                            <flux:heading size="lg" class="mb-1">
                                                                                {{ __('ecommerce::products.form.upload_image') }}
                                                                            </flux:heading>

                                                                            <flux:subheading class="text-center">
                                                                                {{ __('ecommerce::products.form.upload_image_variation_text') }}
                                                                            </flux:subheading>
                                                                        </div>
                                                                    </flux:file-upload>
                                                                @endif
                                                            </div>
                                                            <flux:separator class="my-4"/>
                                                            <div class="flex items-center gap-2 justify-end">
                                                                <flux:button variant="danger" wire:click.prevent="removeVariation({{ $index }})" type="button">{{ __('ecommerce::products.remove_variation') }}</flux:button>
                                                                <flux:button
                                                                    variant="primary"
                                                                    wire:click.prevent="openVariantModal({{ $index }})"
                                                                    type="button"
                                                                >
                                                                    <span class="truncate">
                                                                        @if (!empty($variation['variants_by_group']))
                                                                            {{ count($variation['variants_by_group']) }} {{ __('ecommerce::products.selected_variants') }}
                                                                        @else
                                                                            {{ __('ecommerce::products.select_variants') }}
                                                                        @endif
                                                                    </span>
                                                                </flux:button>
                                                            </div>
                                                        </div>
                                                    </flux:accordion.content>
                                                </flux:accordion.item>
                                            </flux:card>
                                        @endforeach
                                    </flux:accordion>
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                            <flux:icon.squares-2x2 class="w-8 h-8 text-zinc-400" />
                                        </div>
                                        <flux:text class="text-zinc-500 dark:text-zinc-400">
                                            {{ __('ecommerce::products.no_variations_added_yet') }}
                                        </flux:text>
                                        <div class="flex gap-2 mt-2">
                                            <flux:button
                                                variant="primary"
                                                icon="sparkles"
                                                wire:click.prevent="openGroupVariantModal"
                                                type="button"
                                                size="sm"
                                            >
                                                {{ __('ecommerce::products.auto_generate_variations') }}
                                            </flux:button>
                                            <flux:button
                                                variant="ghost"
                                                icon="plus"
                                                wire:click.prevent="addVariation"
                                                type="button"
                                                size="sm"
                                            >
                                                {{ __('ecommerce::products.add_variation') }}
                                            </flux:button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </flux:card>
                    </flux:tab.panel>
                @endif
                <flux:tab.panel name="images" wire:key="panel-images">
                    <div class="space-y-6">
                        <flux:card>
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <flux:heading size="md" class="text-zinc-900 dark:text-zinc-100">
                                        {{ __('ecommerce::products.form.product_images') }}
                                    </flux:heading>
                                    <flux:subheading class="text-zinc-500 dark:text-zinc-400">
                                        {{ __('ecommerce::products.form.product_images_subtitle') }}
                                    </flux:subheading>
                                </div>
                                <flux:button
                                    variant="primary"
                                    icon="plus"
                                    wire:click="addImageCard"
                                    type="button"
                                >
                                    {{ __('ecommerce::products.add_new_image') }}
                                </flux:button>
                            </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($selections as $index => $selection)
                                <flux:card wire:key="image-card-{{ $index }}">
                                    <div class="space-y-4">
                                        @if (data_get($selection, 'photo') || !empty($selection['path']))
                                            <div class="relative w-full">
                                                <div class="w-full h-48 rounded-lg overflow-hidden border-2 border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 flex items-center justify-center p-6">
                                                    @if (data_get($selection, 'photo'))
                                                        <img src="{{ $selection['photo']->temporaryUrl() }}" class="max-h-full max-w-full object-contain" />
                                                    @elseif (!empty($selection['path']))
                                                        <img src="{{ asset('storage/'.$selection['path']) }}" class="max-h-full max-w-full object-contain" />
                                                    @endif
                                                </div>

                                                <button
                                                    type="button"
                                                    wire:click="removeImageCard({{ $index }})"
                                                    class="absolute -top-2 -right-2 size-8 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center shadow-lg transition-colors"
                                                >
                                                    <flux:icon name="x-mark" class="size-5" />
                                                </button>
                                            </div>
                                        @else
                                            <flux:file-upload wire:model="selections.{{ $index }}.photo">
                                                <div class="flex flex-col items-center justify-center p-6 mb-6 border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-900/50 hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors cursor-pointer">
                                                    <div class="size-16 rounded-full bg-zinc-200 dark:bg-zinc-800 flex items-center justify-center mb-4">
                                                        <flux:icon name="photo" class="size-8 text-zinc-500 dark:text-zinc-400" />
                                                    </div>

                                                    <flux:heading size="lg" class="mb-1">
                                                        {{ __('ecommerce::products.form.upload_image') }}
                                                    </flux:heading>

                                                    <flux:subheading class="text-center">
                                                        {{ __('ecommerce::products.form.upload_image_text') }}
                                                    </flux:subheading>
                                                </div>
                                            </flux:file-upload>
                                        @endif

                                        <div>
                                            <livewire:lmt-TextInput
                                                label="Caption"
                                                wire:model="selections.{{ $index }}.caption.{{ $selected_language }}"
                                            />
                                        </div>
                                    </div>
                                </flux:card>
                            @endforeach
                        </div>
                        </flux:card>
                    </div>
                </flux:tab.panel>
                @if ($product_type != 'variable')
                    <flux:tab.panel name="stocks" wire:key="panel-stocks">
                        <flux:card>
                            <div class="mb-4">
                                <flux:heading size="md" class="text-zinc-900 dark:text-zinc-100">
                                    {{ __('ecommerce::products.form.stock_management') }}
                                </flux:heading>
                                <flux:subheading class="text-zinc-500 dark:text-zinc-400">
                                    {{ __('ecommerce::products.form.stock_management_subtitle') }}
                                </flux:subheading>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <flux:input
                                    type="number"
                                    wire:model="quantity"
                                    :label="__('ecommerce::products.form.quantity')"
                                    :placeholder="__('ecommerce::products.form.quantity_placeholder')"
                                />
                                <flux:input
                                    type="number"
                                    wire:model="minimal_quantity"
                                    :label="__('ecommerce::products.form.minimal_quantity')"
                                    :placeholder="__('ecommerce::products.form.minimal_quantity_placeholder')"
                                />
                                <flux:input
                                    type="number"
                                    wire:model="low_stock_alert"
                                    :label="__('ecommerce::products.form.low_stock_alert')"
                                    :placeholder="__('ecommerce::products.form.low_stock_alert_placeholder')"
                                />
                                <flux:date-picker
                                    type="number"
                                    wire:model="available_from"
                                    :label="__('ecommerce::products.form.available_from')"
                                    :placeholder="__('ecommerce::products.form.available_from_placeholder')"
                                />
                                <flux:date-picker
                                    type="number"
                                    wire:model="available_to"
                                    :label="__('ecommerce::products.form.available_to')"
                                    :placeholder="__('ecommerce::products.form.available_to_placeholder')"
                                />
                            </div>
                        </flux:card>
                    </flux:tab.panel>
                @endif
                <flux:tab.panel name="prices" wire:key="panel-prices">
                    <div class="space-y-6">
                        {{-- Base Price --}}
                        <flux:card>
                            <div class="mb-4">
                                <flux:heading size="md" class="text-zinc-900 dark:text-zinc-100">
                                    {{ __('ecommerce::products.form.base_price') }}
                                </flux:heading>
                                <flux:subheading class="text-zinc-500 dark:text-zinc-400">
                                    {{ __('ecommerce::products.form.base_price_subtitle') }}
                                </flux:subheading>
                            </div>
                            <div class="max-w-xs">
                                <flux:input.group>
                                    <flux:input 
                                        wire:model="price" 
                                        placeholder="{{ __('ecommerce::products.form.price_placeholder') }}"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                    />
                                    <flux:input.group.suffix>€</flux:input.group.suffix>
                                </flux:input.group>
                            </div>
                        </flux:card>

                        {{-- Specific Prices --}}
                        <flux:card>
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <flux:heading size="md" class="text-zinc-900 dark:text-zinc-100">
                                        {{ __('ecommerce::products.form.specific_prices') }}
                                    </flux:heading>
                                    <flux:subheading class="text-zinc-500 dark:text-zinc-400">
                                        {{ __('ecommerce::products.form.specific_prices_subtitle') }}
                                    </flux:subheading>
                                </div>
                                <flux:button
                                    variant="primary"
                                    icon="plus"
                                    wire:click.prevent="openSpecificPriceModal"
                                    type="button"
                                >
                                    {{ __('ecommerce::products.form.add_specific_price') }}
                                </flux:button>
                            </div>

                        @if (count($specificPrices) > 0)
                            <div class="overflow-x-auto">
                                <flux:table>
                                    <flux:table.columns>
                                        <flux:table.column>{{ __('ecommerce::products.form.currency') }}</flux:table.column>
                                        <flux:table.column>{{ __('ecommerce::products.form.client_group') }}</flux:table.column>
                                        <flux:table.column>{{ __('ecommerce::products.form.customer') }}</flux:table.column>
                                        <flux:table.column class="text-right">{{ __('ecommerce::products.form.price') }}</flux:table.column>
                                        <flux:table.column class="text-center">{{ __('ecommerce::products.form.from_quantity') }}</flux:table.column>
                                        <flux:table.column class="text-right">{{ __('ecommerce::products.form.reduction') }}</flux:table.column>
                                        <flux:table.column class="text-center">{{ __('ecommerce::products.form.reduction_type') }}</flux:table.column>
                                        <flux:table.column>{{ __('ecommerce::products.form.from_date') }}</flux:table.column>
                                        <flux:table.column>{{ __('ecommerce::products.form.to_date') }}</flux:table.column>
                                        <flux:table.column class="text-right">{{ __('common.actions') }}</flux:table.column>
                                    </flux:table.columns>
                                <flux:table.rows>
                                    @foreach ($specificPrices as $index => $specificPrice)
                                        <flux:table.row wire:key="specific-price-row-{{ $index }}">
                                            <flux:table.cell>
                                                @php
                                                    $currency = $this->getCurrencies()->firstWhere('id', $specificPrice['id_currency']);
                                                @endphp
                                                {{ $currency ? $currency->name . ' (' . $currency->iso_code . ')' : '-' }}
                                            </flux:table.cell>
                                            <flux:table.cell>
                                                @php
                                                    $clientGroup = $this->getClientGroups()->firstWhere('id', $specificPrice['id_client_type']);
                                                @endphp
                                                {{ $clientGroup ? $clientGroup->name : '-' }}
                                            </flux:table.cell>
                                            <flux:table.cell>
                                                @php
                                                    $client = $this->getClients()->firstWhere('id', $specificPrice['id_customer']);
                                                @endphp
                                                {{ $client ? ($client->user->name ?? $client->company_name ?? 'Client #' . $client->id) : '-' }}
                                            </flux:table.cell>
                                            <flux:table.cell class="text-right font-medium">
                                                {{ number_format($specificPrice['price'] ?? 0, 2) }} €
                                            </flux:table.cell>
                                            <flux:table.cell class="text-center">
                                                <flux:badge color="zinc" size="sm">{{ $specificPrice['from_quantity'] ?? 1 }}</flux:badge>
                                            </flux:table.cell>
                                            <flux:table.cell class="text-right font-medium text-green-600 dark:text-green-400">
                                                {{ number_format($specificPrice['reduction'] ?? 0, 2) }}
                                                @if($specificPrice['reduction_type'] === 'percentage')
                                                    %
                                                @else
                                                    €
                                                @endif
                                            </flux:table.cell>
                                            <flux:table.cell class="text-center">
                                                <flux:badge color="{{ $specificPrice['reduction_type'] === 'percentage' ? 'blue' : 'purple' }}" size="sm">
                                                    {{ $specificPrice['reduction_type'] === 'percentage' ? __('ecommerce::products.form.reduction_type_percentage') : __('ecommerce::products.form.reduction_type_amount') }}
                                                </flux:badge>
                                            </flux:table.cell>
                                            <flux:table.cell>
                                                {{ $specificPrice['from'] ? \Carbon\Carbon::parse($specificPrice['from'])->format('Y-m-d H:i') : '-' }}
                                            </flux:table.cell>
                                            <flux:table.cell>
                                                {{ $specificPrice['to'] ? \Carbon\Carbon::parse($specificPrice['to'])->format('Y-m-d H:i') : '-' }}
                                            </flux:table.cell>
                                            <flux:table.cell class="text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <flux:button
                                                        variant="ghost"
                                                        size="sm"
                                                        icon="pencil"
                                                        wire:click.prevent="openSpecificPriceModal({{ $index }})"
                                                        type="button"
                                                    >
                                                        {{ __('common.edit') }}
                                                    </flux:button>
                                                    <flux:button
                                                        variant="danger"
                                                        size="sm"
                                                        icon="trash"
                                                        wire:click.prevent="removeSpecificPrice({{ $index }})"
                                                        type="button"
                                                    >
                                                        {{ __('common.delete') }}
                                                    </flux:button>
                                                </div>
                                            </flux:table.cell>
                                        </flux:table.row>
                                        @endforeach
                                    </flux:table.rows>
                                </flux:table>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                        <flux:icon.tag class="w-8 h-8 text-zinc-400" />
                                    </div>
                                    <flux:text class="text-zinc-500 dark:text-zinc-400">
                                        {{ __('ecommerce::products.form.no_specific_prices') }}
                                    </flux:text>
                                    <flux:button
                                        variant="ghost"
                                        icon="plus"
                                        wire:click.prevent="openSpecificPriceModal"
                                        type="button"
                                        size="sm"
                                    >
                                        {{ __('ecommerce::products.form.add_specific_price') }}
                                    </flux:button>
                                </div>
                            </div>
                        @endif
                        </flux:card>
                    </div>
                </flux:tab.panel>
                <flux:tab.panel name="seo" wire:key="panel-seo">
                    <flux:card>
                        <div class="mb-4">
                            <flux:heading size="md" class="text-zinc-900 dark:text-zinc-100">
                                {{ __('ecommerce::products.form.seo_settings') }}
                            </flux:heading>
                            <flux:subheading class="text-zinc-500 dark:text-zinc-400">
                                {{ __('ecommerce::products.form.seo_settings_subtitle') }}
                            </flux:subheading>
                        </div>
                        <div class="space-y-4">
                            <livewire:lmt-TextInput 
                                label="{{ __('ecommerce::products.form.meta_title') }}"
                                placeholder="{{ __('ecommerce::products.form.meta_title_placeholder') }}" 
                                wire:model="meta_title"
                            />
                            <livewire:lmt-Textarea 
                                label="{{ __('ecommerce::products.form.meta_description') }}"
                                placeholder="{{ __('ecommerce::products.form.meta_description_placeholder') }}" 
                                wire:model="meta_description"
                            />
                        </div>
                    </flux:card>
                </flux:tab.panel>
            </flux:tab.group>
            <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center justify-between">
                    <flux:button 
                        variant="ghost" 
                        :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.products.index')" 
                        wire:navigate
                        icon="arrow-left"
                    >
                        {{ __('common.cancel') }}
                    </flux:button>
                    <div class="flex items-center gap-3">
                        <flux:button 
                            variant="primary" 
                            type="submit"
                            icon="check"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="save">
                                {{ $isEditing ? __('ecommerce::products.update_product') : __('ecommerce::products.create_product') }}
                            </span>
                            <span wire:loading wire:target="save">
                                {{ __('common.loading') }}...
                            </span>
                        </flux:button>
                    </div>
                </div>
            </div>
        </form>
    </flux:card>


    {{-- Variant Selection Modal --}}
    <flux:modal wire:model="showVariantModal" class="max-w-3xl w-full">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('ecommerce::products.select_variants') }}</flux:heading>
                <flux:subheading>{{ __('ecommerce::products.select_variants_subtitle') }}</flux:subheading>
            </div>

            <div class="space-y-6 max-h-[600px] overflow-y-auto grid grid-cols-2 gap-4">
                @foreach($this->getVariantGroups() as $group)
                    @if($group->variants->isNotEmpty())
                        <flux:card>
                            <flux:heading size="lg" class="mb-3">{{ $group->name }}</flux:heading>
                            <flux:separator class="my-4"/>
                            <div class="space-y-2">
                                @if ($group->type == 'radio')
                                    <flux:radio.group wire:model="selectedVariants.{{ $group->id }}" class="grid grid-cols-2 gap-4 space-y-6" wire:key="variant-group-{{ $group->id }}">
                                        @foreach($group->variants as $variant)
                                            <flux:radio
                                                value="{{ $variant->id }}"
                                                label="{{ $variant->name }}"
                                                wire:key="variant-{{ $variant->id }}"
                                            />
                                        @endforeach
                                    </flux:radio.group>
                                @elseif ($group->type == 'select')
                                    <flux:select wire:model="selectedVariants.{{ $group->id }}" variant="listbox" wire:key="variant-group-{{ $group->id }}">
                                        @foreach($group->variants as $variant)
                                            <flux:select.option value="{{ $variant->id }}">{{ $variant->name }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                @elseif ($group->type == 'color')
                                    <div class="flex flex-wrap gap-3">
                                        @foreach($group->variants as $variant)
                                            <label class="relative cursor-pointer">
                                                <input
                                                    type="radio"
                                                    wire:model="selectedVariants.{{ $group->id }}"
                                                    value="{{ $variant->id }}"
                                                    class="peer sr-only"
                                                    wire:key="variant-{{ $variant->id }}"
                                                />
                                                <div
                                                    class="w-10 h-6 border-2 border-gray-300 peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-500 peer-checked:ring-offset-2 transition-all"
                                                    style="background-color: {{ $variant->color }}"
                                                    title="{{ $variant->name }}"
                                                ></div>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </flux:card>
                    @endif
                @endforeach
            </div>

            <div class="flex gap-2 justify-end">
                <flux:button variant="ghost" wire:click.prevent="closeVariantModal" type="button">
                    {{ __('common.cancel') }}
                </flux:button>
                <flux:button variant="primary" wire:click.prevent="saveVariantSelection" type="button">
                    {{ __('common.save') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal wire:model="showGroupVariantModal" class="max-w-3xl w-full">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('ecommerce::products.select_group_variants') }}</flux:heading>
                <flux:subheading>{{ __('ecommerce::products.select_group_variants_subtitle') }}</flux:subheading>
                @if (session('error_group_variants'))
                    <div x-data="{ show: true }"
                        x-init="setTimeout(() => show = false, 3000)"
                        x-show="show"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" class="mt-3">
                        <flux:callout variant="danger" icon="x-circle">
                            {{ session('error_group_variants') }}
                        </flux:callout>
                    </div>
                @endif
            </div>

            <div class="space-y-6 max-h-[600px] overflow-y-auto grid grid-cols-2 gap-4">
                @foreach($this->getVariantGroups() as $group)
                    @if($group->variants->isNotEmpty())
                        <flux:card>
                            <flux:heading size="lg" class="mb-3">{{ $group->name }}</flux:heading>
                            <flux:separator class="my-4"/>
                            <flux:checkbox.group wire:model="selectedGroupVariants.{{ $group->id }}" wire:key="variant-group-{{ $group->id }}">
                                <flux:checkbox.all :label="__('ecommerce::products.select_all')" />
                                <div class="grid grid-cols-2 gap-4">
                                    @foreach($group->variants as $variant)
                                        <flux:checkbox value="{{ $variant->id }}" label="{{ $variant->name }}" wire:key="variant-{{ $variant->id }}" class="mb-0"/>
                                    @endforeach
                                </div>
                            </flux:checkbox.group>
                        </flux:card>
                    @endif
                @endforeach
            </div>

            <div class="flex gap-2 justify-end">
                <flux:button variant="ghost" wire:click.prevent="closeGroupVariantModal" type="button">
                    {{ __('common.cancel') }}
                </flux:button>
                <flux:button variant="primary" wire:click.prevent="autoGenerateVariations" type="button">
                    {{ __('ecommerce::products.auto_generate_variations') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Specific Price Modal --}}
    <flux:modal wire:model="showSpecificPriceModal" class="max-w-4xl w-full">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $editingSpecificPriceIndex !== null ? __('ecommerce::products.form.edit_specific_price') : __('ecommerce::products.form.add_specific_price') }}
                </flux:heading>
            </div>

            @if (session('error'))
                <flux:callout variant="danger" icon="exclamation-triangle">
                    {{ session('error') }}
                </flux:callout>
            @endif

            {{-- Conditions Section --}}
            <div class="space-y-4">
                <flux:heading size="md">{{ __('ecommerce::products.form.conditions') }}</flux:heading>
                
                <div>
                    <div class="mb-2">
                        <flux:label>{{ __('ecommerce::products.form.apply_to') }}</flux:label>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:select
                            variant="listbox"
                            :placeholder="__('ecommerce::products.form.all_currencies')"
                            wire:model="currentSpecificPrice.id_currency"
                            searchable
                            clearable
                        >
                            @foreach($this->getCurrencies() as $currency)
                                <flux:select.option :value="$currency->id">
                                    {{ $currency->name }} ({{ $currency->iso_code }})
                                </flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:select
                            variant="listbox"
                            :placeholder="__('ecommerce::products.form.all_groups')"
                            wire:model="currentSpecificPrice.id_client_type"
                            searchable
                            clearable
                        >
                            @foreach($this->getClientGroups() as $clientGroup)
                                <flux:select.option :value="$clientGroup->id">
                                    {{ $clientGroup->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <flux:label>{{ __('ecommerce::products.form.apply_to_all_customers') }}</flux:label>
                        <flux:switch wire:model.live="applyToAllCustomers" />
                    </div>

                    @if (!$applyToAllCustomers)
                        <div class="space-y-2">
                            <flux:select
                                variant="listbox"
                                :label="__('ecommerce::products.form.select_customer')"
                                :placeholder="__('ecommerce::products.form.search_customer_placeholder')"
                                wire:model="currentSpecificPrice.id_customer"
                                searchable
                                clearable
                            >
                                @foreach($this->getClients() as $client)
                                    <flux:select.option :value="$client->id">
                                        {{ $client->user->name ?? $client->company_name ?? 'Client #' . $client->id }}
                                        @if($client->user)
                                            - {{ $client->user->email }}
                                        @endif
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                    @endif
                </div>

                <flux:input
                    type="number"
                    min="1"
                    wire:model="currentSpecificPrice.from_quantity"
                    :label="__('ecommerce::products.form.minimum_units_purchased')"
                    :placeholder="__('ecommerce::products.form.minimum_units_purchased_placeholder')"
                />
            </div>

            <flux:separator />

            {{-- Duration Section --}}
            <div class="space-y-4">
                <flux:heading size="md">{{ __('ecommerce::products.form.duration') }}</flux:heading>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input
                        type="datetime-local"
                        wire:model="currentSpecificPrice.from"
                        :label="__('ecommerce::products.form.start_date')"
                        :placeholder="__('ecommerce::products.form.start_date_placeholder')"
                        :disabled="$unlimitedDuration"
                    />

                    <flux:input
                        type="datetime-local"
                        wire:model="currentSpecificPrice.to"
                        :label="__('ecommerce::products.form.end_date')"
                        :placeholder="__('ecommerce::products.form.end_date_placeholder')"
                        :disabled="$unlimitedDuration"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <flux:checkbox wire:model.live="unlimitedDuration" :label="__('ecommerce::products.form.unlimited')" />
                </div>
            </div>

            <flux:separator />

            {{-- Impact on price Section --}}
            <div class="space-y-4">
                <flux:heading size="md">{{ __('ecommerce::products.form.impact_on_price') }}</flux:heading>

                @if (!$applyDiscount && !$setSpecificPrice)
                    <flux:callout variant="danger" icon="exclamation-triangle">
                        {{ __('ecommerce::products.form.apply_discount_or_specific_price') }}
                    </flux:callout>
                @endif

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <flux:label>{{ __('ecommerce::products.form.apply_discount_to_initial_price') }}</flux:label>
                        <flux:switch wire:model.live="applyDiscount" :disabled="$setSpecificPrice" />
                    </div>

                    @if ($applyDiscount)
                        <flux:card class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:input
                                type="number"
                                step="0.01"
                                min="0"
                                wire:model="currentSpecificPrice.reduction"
                                :label="__('ecommerce::products.form.discount_amount')"
                                :placeholder="__('ecommerce::products.form.discount_amount_placeholder')"
                            />

                            <flux:select
                                variant="listbox"
                                :label="__('ecommerce::products.form.discount_type')"
                                wire:model="currentSpecificPrice.reduction_type"
                            >
                                <flux:select.option value="amount">{{ __('ecommerce::products.form.reduction_type_amount') }}</flux:select.option>
                                <flux:select.option value="percentage">{{ __('ecommerce::products.form.reduction_type_percentage') }}</flux:select.option>
                            </flux:select>

                            <div>
                                <div class="mb-2">
                                    <flux:label>{{ __('ecommerce::products.form.tax_included') }}</flux:label>
                                </div>
                                <flux:switch wire:model="currentSpecificPrice.reduction_tax" />
                            </div>
                        </flux:card>

                    @endif
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <flux:label>{{ __('ecommerce::products.form.set_specific_price') }}</flux:label>
                        <flux:switch wire:model.live="setSpecificPrice" :disabled="$applyDiscount" />
                    </div>

                    @if ($setSpecificPrice)
                        <flux:card>
                            <flux:input
                                type="number"
                                step="0.01"
                                min="0"
                                wire:model="currentSpecificPrice.price"
                                :label="__('ecommerce::products.form.retail_price_tax_excl')"
                                :placeholder="__('ecommerce::products.form.retail_price_tax_excl_placeholder')"
                            />
                        </flux:card>
                    @endif
                </div>
            </div>

            <div class="flex gap-2 justify-end pt-4 border-t">
                <flux:button variant="ghost" wire:click.prevent="closeSpecificPriceModal" type="button">
                    {{ __('common.cancel') }}
                </flux:button>
                <flux:button variant="primary" wire:click.prevent="saveSpecificPrice" type="button">
                    {{ __('ecommerce::products.form.save_and_publish') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
