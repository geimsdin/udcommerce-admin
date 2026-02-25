<div>
    <div class="flex items-center justify-between mb-4">
        <div>
            <flux:heading size="xl">{{ $isEditing ? __('ecommerce::coupons.edit_coupon') : __('ecommerce::coupons.create_coupon') }}</flux:heading>
            <flux:subheading>{{ $isEditing ? __('ecommerce::coupons.messages.edit_subtitle') : __('ecommerce::coupons.messages.create_subtitle') }}</flux:subheading>
        </div>
    </div>

    <flux:card>
        <form wire:submit="save" class="space-y-6">
            <flux:heading size="lg">{{ __('ecommerce::coupons.form.basic_information') }}</flux:heading>
            
            <flux:input 
                wire:model="code" 
                :label="__('ecommerce::coupons.form.code')" 
                placeholder="{{ __('ecommerce::coupons.form.code_placeholder') }}" 
            />

            <flux:textarea 
                wire:model="description" 
                :label="__('ecommerce::coupons.form.description')" 
                placeholder="{{ __('ecommerce::coupons.form.description_placeholder') }}" 
                rows="3"
            />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:date-picker 
                    wire:model="start_date" 
                    :label="__('ecommerce::coupons.form.start_date')" 
                    clearable
                />
                
                <flux:date-picker 
                    wire:model="end_date" 
                    :label="__('ecommerce::coupons.form.end_date')" 
                    clearable
                />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:select variant="listbox" wire:model="type" :label="__('ecommerce::coupons.form.type')" placeholder="{{ __('ecommerce::coupons.form.type_placeholder') }}" clearable>
                    <flux:select.option value="fixed">{{ __('ecommerce::coupons.form.fixed') }}</flux:select.option>
                    <flux:select.option value="percentage">{{ __('ecommerce::coupons.form.percentage') }}</flux:select.option>
                </flux:select>
                <flux:input wire:model="value" type="number" min="0" :label="__('ecommerce::coupons.form.value')" placeholder="{{ __('ecommerce::coupons.form.value_placeholder') }}" />
            </div>
            <div class="grid grid-cols-4">
                <div class="flex flex-row gap-2">
                    <flux:label>{{ __('ecommerce::coupons.form.active') }}</flux:label>
                    <flux:switch wire:model="active" inline />
                </div>
            </div>

            <flux:separator />
            <flux:heading size="lg">{{ __('ecommerce::coupons.form.spending_limits') }}</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input 
                    wire:model="minimum_spend" 
                    type="number"
                    min="0"
                    :label="__('ecommerce::coupons.form.minimum_spend')" 
                    placeholder="{{ __('ecommerce::coupons.form.minimum_spend_placeholder') }}"
                />
                
                <flux:input 
                    wire:model="maximum_spend" 
                    type="number"
                    min="0"
                    :label="__('ecommerce::coupons.form.maximum_spend')" 
                    placeholder="{{ __('ecommerce::coupons.form.maximum_spend_placeholder') }}"
                />
            </div>

            <flux:separator />
            <flux:heading size="lg">{{ __('ecommerce::coupons.form.usage_limits') }}</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input 
                    wire:model="limit_use_by_user" 
                    type="number"
                    min="0"
                    :label="__('ecommerce::coupons.form.limit_use_by_user')" 
                    placeholder="{{ __('ecommerce::coupons.form.limit_use_by_user_placeholder') }}"
                />
                
                <flux:input 
                    wire:model="limit_use_by_coupon" 
                    type="number"
                    min="0"
                    :label="__('ecommerce::coupons.form.limit_use_by_coupon')" 
                    placeholder="{{ __('ecommerce::coupons.form.limit_use_by_coupon_placeholder') }}"
                />
            </div>

            <flux:separator />
            <flux:heading size="lg">{{ __('ecommerce::coupons.form.brands') }}</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <flux:select
                        variant="listbox"
                        wire:model="include_brand_ids"
                        :label="__('ecommerce::coupons.form.include_brands')"
                        placeholder="{{ __('ecommerce::coupons.form.include_brands_placeholder') }}"
                        multiple
                        searchable
                        clearable
                    >
                        @foreach ($brands as $brand)
                            <flux:select.option value="{{ $brand->id }}">
                                {{ $brand->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                
                <div>
                    <flux:select
                        variant="listbox"
                        wire:model="exclude_brand_ids"
                        :label="__('ecommerce::coupons.form.exclude_brands')"
                        placeholder="{{ __('ecommerce::coupons.form.exclude_brands_placeholder') }}"
                        multiple
                        searchable
                        clearable
                    >
                        @foreach ($brands as $brand)
                            <flux:select.option value="{{ $brand->id }}">
                                {{ $brand->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </div>

            <flux:separator />
            <flux:heading size="lg">{{ __('ecommerce::coupons.form.categories') }}</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <flux:select
                        variant="listbox"
                        wire:model="include_category_ids"
                        :label="__('ecommerce::coupons.form.include_categories')"
                        placeholder="{{ __('ecommerce::coupons.form.include_categories_placeholder') }}"
                        multiple
                        searchable
                        clearable
                    >
                        @foreach ($categories as $category)
                            <flux:select.option value="{{ $category->id }}">
                                {{ $category->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                
                <div>
                    <flux:select
                        variant="listbox"
                        wire:model="exclude_category_ids"
                        :label="__('ecommerce::coupons.form.exclude_categories')"
                        placeholder="{{ __('ecommerce::coupons.form.exclude_categories_placeholder') }}"
                        multiple
                        searchable
                        clearable
                    >
                        @foreach ($categories as $category)
                            <flux:select.option value="{{ $category->id }}">
                                {{ $category->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </div>

            <flux:separator />
            <flux:heading size="lg">{{ __('ecommerce::coupons.form.products') }}</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <flux:select
                        variant="listbox"
                        wire:model="include_product_ids"
                        :label="__('ecommerce::coupons.form.include_products')"
                        placeholder="{{ __('ecommerce::coupons.form.include_products_placeholder') }}"
                        multiple
                        searchable
                        clearable
                    >
                        @foreach ($products as $product)
                            <flux:select.option value="{{ $product->id }}">
                                {{ $product->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                
                <div>
                    <flux:select
                        variant="listbox"
                        wire:model="exclude_product_ids"
                        :label="__('ecommerce::coupons.form.exclude_products')"
                        placeholder="{{ __('ecommerce::coupons.form.exclude_products_placeholder') }}"
                        multiple
                        searchable
                        clearable
                    >
                        @foreach ($products as $product)
                            <flux:select.option value="{{ $product->id }}">
                                {{ $product->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </div>

            <flux:separator />
            
            <div class="flex items-center gap-4 justify-end">
                <flux:button variant="ghost" :href="route(config('ud-ecommerce.admin_route_prefix', 'admin').'.coupons.index')" wire:navigate>
                    {{ __('common.cancel') }}
                </flux:button>
                
                <flux:button variant="primary" type="submit">
                    {{ $isEditing ? __('ecommerce::coupons.update_coupon') : __('ecommerce::coupons.create_coupon') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>

