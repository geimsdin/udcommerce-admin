<?php

use Illuminate\Support\Facades\Route;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Administration\Carrier\CarrierCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Administration\Carrier\CarrierIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Administration\Currency\CurrencyCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Administration\Currency\CurrencyIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Administration\Tax\TaxCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Administration\Tax\TaxIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Administration\PaymentGateway\PaymentGatewayIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Administration\CountryAddressCustomField\CountryAddressCustomFieldIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Administration\CountryAddressCustomField\CountryAddressCustomFieldCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Config;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Coupon\CouponCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Coupon\CouponIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Customer\Client\ClientCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Customer\Client\ClientIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Customer\ClientGroup\ClientGroupCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Customer\ClientGroup\ClientGroupIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Order\Order\OrderCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Order\Order\OrderIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Order\OrderStatus\OrderStatusCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Order\OrderStatus\OrderStatusIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Product\Brand\BrandCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Product\Brand\BrandIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Product\Feature\FeatureCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Product\Feature\FeatureIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Product\FeatureGroup\FeatureGroupCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Product\FeatureGroup\FeatureGroupIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Product\Product\ProductCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Product\Product\ProductIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Product\Product\ProductMassAssign;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Product\ProductCategory\ProductCategoryCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Product\ProductCategory\ProductCategoryIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Product\ProductImage\ProductImageCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Product\ProductImage\ProductImageIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Product\Season\SeasonCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Product\Season\SeasonIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Size\SizeChart\SizeChartCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Size\SizeChart\SizeChartIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Size\SizeChartEntry\SizeChartEntryCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Size\SizeChartEntry\SizeChartEntryIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Size\TargetUnit\TargetUnitCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Size\TargetUnit\TargetUnitIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\SocialAuth\SocialAuthSettings;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Warehouse\Stock\StockCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Warehouse\Stock\StockIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Warehouse\Variant\VariantCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Warehouse\Variant\VariantIndex;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Warehouse\VariantGroup\VariantGroupCreateEdit;
use Unusualdope\LaravelEcommerce\Livewire\Admin\Warehouse\VariantGroup\VariantGroupIndex;

// Admin Ecommerce Routes
$name = config('ud-ecommerce.admin_route_prefix');
$prefix = str_replace('.', '/', $name);
$middleware = config('ud-ecommerce.admin_middleware', ['web', 'auth']);
Route::prefix($prefix)
    ->middleware($middleware)
    ->name($name . '.')
    ->group(function () {
        // Seasons
        Route::livewire('seasons', SeasonIndex::class)->name('seasons.index');
        Route::livewire('seasons/create', SeasonCreateEdit::class)->name('seasons.create');
        Route::livewire('seasons/{season}/edit', SeasonCreateEdit::class)->name('seasons.edit');

        // Brands
        Route::livewire('brands', BrandIndex::class)->name('brands.index');
        Route::livewire('brands/create', BrandCreateEdit::class)->name('brands.create');
        Route::livewire('brands/{brand}/edit', BrandCreateEdit::class)->name('brands.edit');

        // Stocks (Livewire)
        Route::livewire('stocks', StockIndex::class)->name('stocks.index');

        // Variants (Livewire)
        Route::livewire('variants', VariantIndex::class)->name('variants.index');
        Route::livewire('variants/create', VariantCreateEdit::class)->name('variants.create');
        Route::livewire('variants/{variant}/edit', VariantCreateEdit::class)->name('variants.edit');

        // Variant Groups (Livewire)
        Route::livewire('variant-groups', VariantGroupIndex::class)->name('variantgroups.index');
        Route::livewire('variant-groups/create', VariantGroupCreateEdit::class)->name('variantgroups.create');
        Route::livewire('variant-groups/{variantgroup}/edit', VariantGroupCreateEdit::class)->name('variantgroups.edit');

        // Feature Groups
        Route::livewire('feature-groups', FeatureGroupIndex::class)->name('feature-groups.index');
        Route::livewire('feature-groups/create', FeatureGroupCreateEdit::class)->name('feature-groups.create');
        Route::livewire('feature-groups/{featureGroup}/edit', FeatureGroupCreateEdit::class)->name('feature-groups.edit');

        // Features
        Route::livewire('features', FeatureIndex::class)->name('features.index');
        Route::livewire('features/create', FeatureCreateEdit::class)->name('features.create');
        Route::livewire('features/{feature}/edit', FeatureCreateEdit::class)->name('features.edit');

        // Product Categories
        Route::livewire('product-categories', ProductCategoryIndex::class)->name('product-categories.index');
        Route::livewire('product-categories/create', ProductCategoryCreateEdit::class)->name('product-categories.create');
        Route::livewire('product-categories/{productCategory}/edit', ProductCategoryCreateEdit::class)->name('product-categories.edit');

        // Product Images
        Route::livewire('product-images', ProductImageIndex::class)->name('productimages.index');
        Route::livewire('product-images/create', ProductImageCreateEdit::class)->name('productimages.create');
        Route::livewire('product-images/{productImage}/edit', ProductImageCreateEdit::class)->name('productimages.edit');

        // Products
        Route::livewire('products/mass-assign', ProductMassAssign::class)->name('products.mass-assign');
        Route::livewire('products', ProductIndex::class)->name('products.index');
        Route::livewire('products/create', ProductCreateEdit::class)->name('products.create');
        Route::livewire('products/{product}/edit', ProductCreateEdit::class)->name('products.edit');

        // Currencies
        Route::livewire('currencies', CurrencyIndex::class)->name('currencies.index');
        Route::livewire('currencies/create', CurrencyCreateEdit::class)->name('currencies.create');
        Route::livewire('currencies/{currency}/edit', CurrencyCreateEdit::class)->name('currencies.edit');

        // Carriers
        Route::livewire('carriers', CarrierIndex::class)->name('carriers.index');
        Route::livewire('carriers/create', CarrierCreateEdit::class)->name('carriers.create');
        Route::livewire('carriers/{carrier}/edit', CarrierCreateEdit::class)->name('carriers.edit');

        // Taxes
        Route::livewire('taxes', TaxIndex::class)->name('taxes.index');
        Route::livewire('taxes/create', TaxCreateEdit::class)->name('taxes.create');
        Route::livewire('taxes/{tax}/edit', TaxCreateEdit::class)->name('taxes.edit');

        Route::livewire('payment-gateways', PaymentGatewayIndex::class)->name('payment-gateways.index');

        // Clients
        Route::livewire('clients', ClientIndex::class)->name('clients.index');
        Route::livewire('clients/create', ClientCreateEdit::class)->name('clients.create');
        Route::livewire('clients/{client}/edit', ClientCreateEdit::class)->name('clients.edit');

        // Client Groups
        Route::livewire('client-groups', ClientGroupIndex::class)->name('client-groups.index');
        Route::livewire('client-groups/create', ClientGroupCreateEdit::class)->name('client-groups.create');
        Route::livewire('client-groups/{clientGroup}/edit', ClientGroupCreateEdit::class)->name('client-groups.edit');

        // Order Statuses
        Route::livewire('order-statuses', OrderStatusIndex::class)->name('order-statuses.index');
        Route::livewire('order-statuses/create', OrderStatusCreateEdit::class)->name('order-statuses.create');
        Route::livewire('order-statuses/{orderStatus}/edit', OrderStatusCreateEdit::class)->name('order-statuses.edit');

        // Orders
        Route::livewire('orders', OrderIndex::class)->name('orders.index');
        Route::livewire('orders/create', OrderCreateEdit::class)->name('orders.create');
        Route::livewire('orders/{order}/edit', OrderCreateEdit::class)->name('orders.edit');

        // target Unit
        Route::livewire('target-units', TargetUnitIndex::class)->name('target-units.index');
        Route::livewire('target-units/create', TargetUnitCreateEdit::class)->name('target-units.create');
        Route::livewire('target-units/{target_unit}/edit', TargetUnitCreateEdit::class)->name('target-units.edit');

        // Size Chart
        Route::livewire('size-charts', SizeChartIndex::class)->name('size-charts.index');
        Route::livewire('size-charts/create', SizeChartCreateEdit::class)->name('size-charts.create');
        Route::livewire('size-charts/{size_chart}/edit', SizeChartCreateEdit::class)->name('size-charts.edit');

        // Size Chart Entry
        Route::livewire('size-chart-entries', SizeChartEntryIndex::class)->name('size-chart-entries.index');
        Route::livewire('size-chart-entries/create', SizeChartEntryCreateEdit::class)->name('size-chart-entries.create');
        Route::livewire('size-chart-entries/{size_chart_entry}/edit', SizeChartEntryCreateEdit::class)->name('size-chart-entries.edit');

        // Coupons
        Route::livewire('coupons', CouponIndex::class)->name('coupons.index');
        Route::livewire('coupons/create', CouponCreateEdit::class)->name('coupons.create');
        Route::livewire('coupons/{coupon}/edit', CouponCreateEdit::class)->name('coupons.edit');

        // Config
        Route::livewire('configs', Config::class)->name('configs');

        // Country Address Custom Fields
        Route::livewire('country-address-custom-fields', CountryAddressCustomFieldIndex::class)->name('configs.country_address_custom_fields.index');
        Route::livewire('country-address-custom-fields/create', CountryAddressCustomFieldCreateEdit::class)->name('configs.country_address_custom_fields.create');
        Route::livewire('country-address-custom-fields/{field}/edit', CountryAddressCustomFieldCreateEdit::class)->name('configs.country_address_custom_fields.edit');

        // Social Auth Settings
        Route::livewire('social-auth', SocialAuthSettings::class)->name('social-auth.index');
    });
