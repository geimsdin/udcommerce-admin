<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('ecommerce::products.mass.title') }}</flux:heading>
            <flux:subheading>{{ __('ecommerce::products.mass.subtitle') }}</flux:subheading>
        </div>
        <flux:button icon="arrow-left"
            :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').
                '.products.index')" wire:navigate>
            {{ __('ecommerce::products.back_to_list') }}
        </flux:button>
    </div>

    <div class="space-y-4">

        {{-- Step 1: Filters --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div
                class="flex items-center gap-3 bg-zinc-50 dark:bg-zinc-800/60 px-4 py-2.5 border-b border-zinc-200 dark:border-zinc-700">
                <span
                    class="flex items-center justify-center h-5 w-5 rounded-full bg-zinc-800 dark:bg-zinc-200 text-white dark:text-zinc-900 text-[10px] font-bold shrink-0">1</span>
                <flux:heading size="sm" class="text-zinc-900">
                    {{ __('ecommerce::products.mass.step1_filters') }}</flux:heading>
            </div>

            <div class="p-4 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">

                    <flux:field>
                        <flux:label class="text-xs">{{ __('ecommerce::products.mass.filter_brand') }}
                        </flux:label>
                        <flux:select wire:model="mass_brand_ids" variant="listbox" searchable clearable multiple>
                            @foreach ($all_brands as $brand)
                                <flux:select.option value="{{ $brand->id }}">{{ $brand->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:label class="text-xs">{{ __('ecommerce::products.mass.filter_category') }}
                        </flux:label>
                        <flux:select wire:model="mass_category_ids" variant="listbox" searchable clearable multiple>
                            @foreach ($all_categories as $cat)
                                <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:label class="text-xs">{{ __('ecommerce::products.mass.filter_name') }}
                        </flux:label>
                        <flux:input wire:model="mass_name"
                            placeholder="{{ __('ecommerce::products.mass.filter_name_placeholder') }}" />
                    </flux:field>

                    <flux:field>
                        <flux:label class="text-xs">{{ __('ecommerce::products.mass.filter_date_start') }}
                        </flux:label>
                        <flux:date-picker wire:model="mass_date_start" clearable />
                    </flux:field>

                    <flux:field>
                        <flux:label class="text-xs">{{ __('ecommerce::products.mass.filter_date_end') }}
                        </flux:label>
                        <flux:date-picker wire:model="mass_date_end" clearable />
                    </flux:field>

                </div>

                <div class="flex items-center gap-3">
                    <flux:button variant="primary" icon="magnifying-glass" wire:click="runMassFilter"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove
                            wire:target="runMassFilter">{{ __('ecommerce::products.mass.btn_find') }}</span>
                        <span wire:loading wire:target="runMassFilter">{{ __('common.loading') }}…</span>
                    </flux:button>

                    @if ($mass_products !== null)
                        <flux:badge color="{{ $mass_products->total() > 0 ? 'blue' : 'zinc' }}" size="lg">
                            {{ number_format($mass_products->total()) }}
                            {{ __('ecommerce::products.mass.products_found') }}
                        </flux:badge>
                    @endif
                </div>
            </div>
        </div>

        {{-- Step 2: Results --}}
        @if ($mass_products !== null)
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <div
                    class="flex items-center gap-3 bg-zinc-50 dark:bg-zinc-800/60 px-4 py-2.5 border-b border-zinc-200 dark:border-zinc-700">
                    <span
                        class="flex items-center justify-center h-5 w-5 rounded-full bg-zinc-800 dark:bg-zinc-200 text-white dark:text-zinc-900 text-[10px] font-bold shrink-0">2</span>
                    <flux:heading size="sm" class="text-zinc-900">
                        {{ __('ecommerce::products.mass.step2_results') }}</flux:heading>
                </div>

                <div class="p-4">
                    @if ($mass_products->total() === 0)
                        <div class="flex flex-col items-center justify-center py-12 gap-2 text-zinc-400">
                            <flux:icon.inbox class="h-10 w-10 opacity-50" />
                            <flux:text class="text-sm">{{ __('ecommerce::products.no_products_found') }}
                            </flux:text>
                        </div>
                    @else
                        <flux:table :paginate="$mass_products">
                            <flux:table.columns>
                                <flux:table.column class="w-16 text-zinc-400">#</flux:table.column>
                                <flux:table.column>{{ __('ecommerce::products.table.name') }}
                                </flux:table.column>
                                <flux:table.column>{{ __('ecommerce::products.mass.col_brand') }}
                                </flux:table.column>
                                <flux:table.column>{{ __('ecommerce::products.mass.col_categories') }}
                                </flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach ($mass_products as $product)
                                    <flux:table.row>
                                        <flux:table.cell class="text-zinc-400 text-sm font-mono tabular-nums">
                                            {{ $product->id }}</flux:table.cell>
                                        <flux:table.cell class="font-medium">{{ $product->name }}
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            @if ($product->brand)
                                                <flux:badge color="zinc" size="sm">
                                                    {{ $product->brand->name }}</flux:badge>
                                            @else
                                                <span class="text-zinc-400 text-sm">—</span>
                                            @endif
                                        </flux:table.cell>
                                        <flux:table.cell class="text-zinc-500 text-sm">
                                            @forelse ($product->categories as $cat)
                                                <flux:badge color="zinc" size="sm" class="mr-1 mb-0.5">
                                                    {{ $cat->name }}</flux:badge>
                                            @empty
                                                <span class="text-zinc-400">—</span>
                                            @endforelse
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    @endif
                </div>
            </div>
        @endif

        {{-- Step 3: Action --}}
        @if ($mass_products !== null && $mass_products->total() > 0)
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <div
                    class="flex items-center gap-3 bg-zinc-50 dark:bg-zinc-800/60 px-4 py-2.5 border-b border-zinc-200 dark:border-zinc-700">
                    <span
                        class="flex items-center justify-center h-5 w-5 rounded-full bg-zinc-800 dark:bg-zinc-200 text-white dark:text-zinc-900 text-[10px] font-bold shrink-0">3</span>
                    <flux:heading size="sm" class="text-zinc-900">
                        {{ __('ecommerce::products.mass.step3_action') }}</flux:heading>
                </div>

                <div class="p-4 space-y-5">

                    @if ($mass_success)
                        <flux:callout variant="success" icon="check-circle">{{ $mass_success }}
                        </flux:callout>
                    @endif
                    @if ($mass_error)
                        <flux:callout variant="danger" icon="exclamation-circle">{{ $mass_error }}
                        </flux:callout>
                    @endif

                    {{-- Action picker --}}
                    <div class="space-y-3">
                        <flux:field>
                            <flux:label class="text-xs">
                                {{ __('ecommerce::products.mass.action_subject_label') }}</flux:label>
                            <flux:radio.group wire:model.live="mass_action_subject" class="mt-1">
                                <flux:radio value="category"
                                    label="{{ __('ecommerce::products.mass.subject_category') }}" />
                                <flux:radio value="brand"
                                    label="{{ __('ecommerce::products.mass.subject_brand') }}" />
                                <flux:radio value="specific_price"
                                    label="{{ __('ecommerce::products.mass.subject_specific_price') }}" />
                            </flux:radio.group>
                        </flux:field>

                        <flux:field>
                            <flux:label class="text-xs">{{ __('ecommerce::products.mass.action_label') }}
                            </flux:label>
                            <flux:radio.group wire:model.live="mass_action" class="mt-1">
                                <flux:radio value="assign"
                                    label="{{ __('ecommerce::products.mass.action_assign') }}" />
                                <flux:radio value="remove"
                                    label="{{ __('ecommerce::products.mass.action_remove') }}" />
                            </flux:radio.group>
                        </flux:field>
                    </div>

                    {{-- Category target --}}
                    @if ($mass_action_subject === 'category')
                        <flux:field>
                            <flux:label class="text-xs">{{ __('ecommerce::products.mass.target_categories') }}
                            </flux:label>
                            <flux:select variant="listbox" searchable wire:model="mass_target_category_ids" clearable
                                multiple>
                                @foreach ($all_categories as $category)
                                    <flux:select.option :value="$category->id">{{ $category->name }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    @endif

                    {{-- Brand target --}}
                    @if ($mass_action_subject === 'brand' && $mass_action === 'assign')
                        <flux:field>
                            <flux:label class="text-xs">{{ __('ecommerce::products.mass.target_brand') }}
                            </flux:label>
                            <flux:select variant="listbox" searchable wire:model="mass_target_brand_id">
                                @foreach ($all_brands as $brand)
                                    <flux:select.option value="{{ $brand->id }}">{{ $brand->name }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    @endif

                    @if ($mass_action_subject === 'brand' && $mass_action === 'remove')
                        <flux:callout variant="warning" icon="exclamation-triangle">
                            {{ __('ecommerce::products.mass.brand_remove_warning') }}
                        </flux:callout>
                    @endif

                    {{-- Specific Price target --}}
                    @if ($mass_action_subject === 'specific_price')
                        @if ($mass_action === 'assign')
                            <div class="space-y-4 p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg">
                                @if (!$applyDiscount && !$setSpecificPrice)
                                    <flux:callout variant="danger" icon="exclamation-triangle">
                                        {{ __('ecommerce::products.form.apply_discount_or_specific_price') }}
                                    </flux:callout>
                                @endif

                                {{-- Conditions Section --}}
                                <div class="space-y-4">
                                    <flux:heading size="sm">{{ __('ecommerce::products.form.conditions') }}</flux:heading>
                                    
                                    <div>
                                        <div class="mb-2">
                                            <flux:label>{{ __('ecommerce::products.form.apply_to') }}</flux:label>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <flux:select
                                                variant="listbox"
                                                :placeholder="__('ecommerce::products.form.all_currencies')"
                                                wire:model="mass_specific_price.id_currency"
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
                                                wire:model="mass_specific_price.id_client_type"
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
                                            <flux:select
                                                variant="listbox"
                                                :label="__('ecommerce::products.form.select_customer')"
                                                :placeholder="__('ecommerce::products.form.search_customer_placeholder')"
                                                wire:model="mass_specific_price.id_customer"
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
                                        @endif
                                    </div>

                                    <flux:input
                                        type="number"
                                        min="1"
                                        wire:model="mass_specific_price.from_quantity"
                                        :label="__('ecommerce::products.form.minimum_units_purchased')"
                                        :placeholder="__('ecommerce::products.form.minimum_units_purchased_placeholder')"
                                    />
                                </div>

                                <flux:separator />

                                {{-- Duration Section --}}
                                <div class="space-y-4">
                                    <flux:heading size="sm">{{ __('ecommerce::products.form.duration') }}</flux:heading>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <flux:input
                                            type="datetime-local"
                                            wire:model="mass_specific_price.from"
                                            :label="__('ecommerce::products.form.start_date')"
                                            :placeholder="__('ecommerce::products.form.start_date_placeholder')"
                                            :disabled="$unlimitedDuration"
                                        />

                                        <flux:input
                                            type="datetime-local"
                                            wire:model="mass_specific_price.to"
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
                                    <flux:heading size="sm">{{ __('ecommerce::products.form.impact_on_price') }}</flux:heading>

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
                                                    wire:model="mass_specific_price.reduction"
                                                    :label="__('ecommerce::products.form.discount_amount')"
                                                    :placeholder="__('ecommerce::products.form.discount_amount_placeholder')"
                                                />

                                                <flux:select
                                                    variant="listbox"
                                                    :label="__('ecommerce::products.form.discount_type')"
                                                    wire:model="mass_specific_price.reduction_type"
                                                >
                                                    <flux:select.option value="amount">{{ __('ecommerce::products.form.reduction_type_amount') }}</flux:select.option>
                                                    <flux:select.option value="percentage">{{ __('ecommerce::products.form.reduction_type_percentage') }}</flux:select.option>
                                                </flux:select>

                                                <div>
                                                    <div class="mb-2">
                                                        <flux:label>{{ __('ecommerce::products.form.tax_included') }}</flux:label>
                                                    </div>
                                                    <flux:switch wire:model="mass_specific_price.reduction_tax" />
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
                                                    wire:model="mass_specific_price.price"
                                                    :label="__('ecommerce::products.form.retail_price_tax_excl')"
                                                    :placeholder="__('ecommerce::products.form.retail_price_tax_excl_placeholder')"
                                                />
                                            </flux:card>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <flux:callout variant="warning" icon="exclamation-triangle">
                                {{ __('ecommerce::products.mass.specific_price_remove_warning') }}
                            </flux:callout>
                        @endif
                    @endif

                    {{-- Execute row --}}
                    <div class="flex items-center gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                        <flux:button 
                            variant="{{ $mass_action === 'remove' ? 'danger' : 'primary' }}"
                            icon="{{ $mass_action === 'remove' ? 'trash' : 'tag' }}" 
                            wire:click="confirmMassAction"
                            wire:loading.attr="disabled"
                            :disabled="
                                ($mass_action_subject === 'category' && count($mass_target_category_ids) === 0) ||
                                ($mass_action_subject === 'brand' && $mass_action === 'assign' && $mass_target_brand_id === 0) ||
                                ($mass_action_subject === 'specific_price' && $mass_action === 'assign' && !$applyDiscount && !$setSpecificPrice)
                            "
                        >
                            <span wire:loading.remove wire:target="confirmMassAction">
                                {{ $mass_action === 'remove'
                                    ? __('ecommerce::products.mass.btn_execute_remove')
                                    : __('ecommerce::products.mass.btn_execute_assign') }}
                            </span>
                            <span wire:loading wire:target="confirmMassAction">{{ __('common.loading') }}…</span>
                        </flux:button>

                        @if ($mass_action_subject === 'category' && count($mass_target_category_ids) === 0)
                            <flux:text class="text-zinc-400 text-sm">
                                {{ __('ecommerce::products.mass.hint_select_categories') }}</flux:text>
                        @elseif ($mass_action_subject === 'brand' && $mass_action === 'assign' && $mass_target_brand_id === 0)
                            <flux:text class="text-zinc-400 text-sm">
                                {{ __('ecommerce::products.mass.hint_select_brand') }}</flux:text>
                        @elseif ($mass_action_subject === 'specific_price' && $mass_action === 'assign' && !$applyDiscount && !$setSpecificPrice)
                            <flux:text class="text-zinc-400 text-sm">
                                {{ __('ecommerce::products.form.apply_discount_or_specific_price') }}</flux:text>
                        @endif
                    </div>

                </div>
            </div>
        @endif

    </div>

    {{-- Mass action confirmation modal --}}
    <flux:modal wire:model="mass_show_confirm" class="max-w-md">
        <div class="text-center">
            <div
                class="mx-auto flex items-center justify-center h-12 w-12 rounded-full {{ $mass_action === 'remove' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-blue-100 dark:bg-blue-900/30' }} mb-4">
                <flux:icon.exclamation-triangle
                    class="h-6 w-6 {{ $mass_action === 'remove' ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400' }}" />
            </div>
            <flux:heading size="lg" class="mb-1">{{ __('ecommerce::products.mass.confirm_title') }}
            </flux:heading>
            @if ($mass_products !== null)
                <flux:subheading class="mb-1 font-semibold text-zinc-700 dark:text-zinc-300">
                    {{ __('ecommerce::products.mass.confirm_body', ['count' => number_format($mass_products->total())]) }}
                </flux:subheading>
            @endif
            <flux:subheading class="mb-6 text-zinc-400 text-sm">
                @if ($mass_action_subject === 'category')
                    {{ $mass_action === 'assign' ? __('ecommerce::products.mass.confirm_detail_cat_assign') : __('ecommerce::products.mass.confirm_detail_cat_remove') }}
                @elseif ($mass_action_subject === 'brand')
                    {{ $mass_action === 'assign' ? __('ecommerce::products.mass.confirm_detail_brand_assign') : __('ecommerce::products.mass.confirm_detail_brand_remove') }}
                @elseif ($mass_action_subject === 'specific_price')
                    {{ $mass_action === 'assign' ? __('ecommerce::products.mass.confirm_detail_specific_price_assign') : __('ecommerce::products.mass.confirm_detail_specific_price_remove') }}
                @endif
            </flux:subheading>
        </div>
        <div class="flex gap-3 justify-center">
            <flux:button type="button" wire:click="$set('mass_show_confirm', false)">{{ __('common.cancel') }}
            </flux:button>
            <flux:button type="button" variant="{{ $mass_action === 'remove' ? 'danger' : 'primary' }}"
                wire:click="executeMassAction" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="executeMassAction">{{ __('common.confirm') }}</span>
                <span wire:loading wire:target="executeMassAction">{{ __('common.loading') }}…</span>
            </flux:button>
        </div>
    </flux:modal>

</div>
