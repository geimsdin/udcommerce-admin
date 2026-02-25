<?php

namespace Unusualdope\LaravelEcommerce\Livewire\Admin\Order\Order;

use Livewire\Component;
use Unusualdope\LaravelEcommerce\Models\Address;
use Unusualdope\LaravelEcommerce\Models\Administration\Carrier;
use Unusualdope\LaravelEcommerce\Models\Administration\Currency;
use Unusualdope\LaravelEcommerce\Models\Config as EcommerceConfig;
use Unusualdope\LaravelEcommerce\Models\Coupon;
use Unusualdope\LaravelEcommerce\Models\Customer\Client;
use Unusualdope\LaravelEcommerce\Models\Order\Order;
use Unusualdope\LaravelEcommerce\Models\Order\OrderDetail;
use Unusualdope\LaravelEcommerce\Models\Order\OrderOrderStatus;
use Unusualdope\LaravelEcommerce\Models\Order\OrderStatus;
use Unusualdope\LaravelEcommerce\Models\Product\Product;
use Unusualdope\LaravelEcommerce\Models\Product\Season;
use Unusualdope\LaravelEcommerce\Models\Stock\Stock;
use Unusualdope\LaravelEcommerce\Models\Stock\Variation;

class OrderCreateEdit extends Component
{
    public ?Order $order = null;

    public $isEditing = false;

    // Order fields
    public ?int $client_id = null;

    public string $reference = '';

    public ?int $carrier_id = null;

    public ?int $shipping_address_id = null;

    public ?int $billing_address_id = null;

    public ?int $currency_id = null;

    public ?int $season_id = null;

    public ?string $note = null;

    public ?int $last_status_id = null;

    public string $discount_type = 'percent';

    public float $discount = 0;

    public ?string $payment_method = null;

    public ?string $payment_info = null;

    public bool $returned = false;

    public ?string $return_note = null;

    public float $return_amount = 0;

    public ?int $coupon_id = null;

    // Address creation modal
    public bool $show_address_modal = false;

    public ?string $address_name = null;

    public ?string $address_destination_name = null;

    public ?string $address_address = null;

    public ?string $address_post_code = null;

    public ?string $address_city = null;

    public ?string $address_state = null;

    public string $address_country = 'Italy';

    public ?string $address_telephone = null;

    public bool $address_default = false;

    // Order Details
    public $orderDetails = [];

    public $originalOrderDetails = []; // Track original details for stock management

    public bool $show_detail_modal = false;

    public bool $show_delete_detail_modal = false;

    public ?int $editing_detail_index = null;

    // Detail form fields
    public ?int $detail_product_id = null;

    public ?int $variation_id = null;

    public bool $is_detail_product_variant = false;

    public int $detail_quantity = 1;

    public float $detail_price = 0;

    public string $detail_discount_type = 'percent';

    public float $detail_discount = 0;

    public bool $detail_returned = false;

    public ?string $detail_return_note = null;

    public float $detail_return_amount = 0;

    public $languageModel;

    public $selected_language;

    public function mount(?Order $order = null): void
    {
        $languageModel = config('lmt.language_model', 'App\Models\Configuration\Language');
        $this->languageModel = $languageModel;
        $this->selected_language = $this->languageModel::getDefaultLanguage();
        $this->order = $order;

        if ($order?->exists) {
            $this->isEditing = true;
            $this->client_id = $order->client_id;
            $this->reference = $order->reference;
            $this->carrier_id = $order->carrier_id;
            $this->shipping_address_id = $order->shipping_address_id;
            $this->billing_address_id = $order->billing_address_id;
            $this->currency_id = $order->currency_id;
            $this->season_id = $order->season_id;
            $this->note = $order->note;
            $this->last_status_id = $order->last_status_id;
            $this->discount_type = $order->discount_type ?? 'percent';
            $this->discount = $order->discount ?? 0;
            $this->payment_method = $order->payment_method;
            $this->payment_info = $order->payment_info;
            $this->returned = $order->returned;
            $this->return_note = $order->return_note;
            $this->return_amount = $order->return_amount;
            $this->coupon_id = $order->coupon_id;
            // Load order details
            $this->loadOrderDetails();
            // Store original details for stock comparison
            $this->originalOrderDetails = collect($this->orderDetails)->map(function ($detail) {
                return [
                    'id' => $detail['id'] ?? null,
                    'variation_id' => $detail['variation_id'] ?? 0,
                    'quantity' => $detail['quantity'],
                    'returned' => $detail['returned'] ?? false,
                ];
            })->toArray();
        } else {
            $this->order = new Order;
            $this->reference = $this->generateReference();
            $this->orderDetails = [];
            $this->originalOrderDetails = [];
        }
    }

    protected function loadOrderDetails(): void
    {
        $this->orderDetails = OrderDetail::where('order_id', $this->order->id)->with('variation')->get()->map(function ($detail) {
            return [
                'product_name' => $detail->product->name ?? '',
                'product_id' => $detail->product_id,
                'variation_id' => $detail->variation_id,
                'variation' => $detail->variation->combination_name ?? '',
                'is_variation' => $detail->variation_id ? true : false,
                'quantity' => $detail->quantity,
                'price' => $detail->price,
                'discount_type' => $detail->discount_type,
                'discount' => $detail->discount,
                'returned' => $detail->returned,
                'return_note' => $detail->return_note,
                'return_amount' => $detail->return_amount,
            ];
        });
        // 'product_id' => $this->detail_product_id,
        // 'product_name' => $product->currentLanguage->name ?? '',
        // 'variation_id' => $this->variation_id ?? 0,
        // 'variation' => $variation->combination_name,
        // 'is_variation' => $this->variation_id ? true : false,
        // 'quantity' => $this->detail_quantity,
        // 'price' => $this->detail_price,
        // 'discount_type' => $this->detail_discount_type,
        // 'discount' => $this->detail_discount,
        // 'returned' => $this->detail_returned,
        // 'return_note' => $this->detail_return_note,
        // 'return_amount' => $this->detail_return_amount,
    }

    protected function generateReference(): string
    {
        do {
            $reference = 'ORD-' . strtoupper(substr(uniqid(), -6));
        } while (Order::where('reference', $reference)->exists());

        return $reference;
    }

    protected function validateAndApplyCoupon(float $subtotal): float|false
    {
        if (!$this->coupon_id) {
            return false;
        }

        $coupon = Coupon::find($this->coupon_id);

        if (!$coupon || !$coupon->active) {
            return false;
        }

        $today = now()->format('Y-m-d');
        if ($coupon->start_date && $coupon->start_date->format('Y-m-d') > $today) {
            return false;
        }
        if ($coupon->end_date && $coupon->end_date->format('Y-m-d') < $today) {
            return false;
        }

        if ($coupon->minimum_amount && $subtotal < $coupon->minimum_amount) {
            return false;
        }

        if ($coupon->usage_limit) {
            $usageCount = Order::where('coupon_id', $coupon->id)
                ->where('id', '!=', $this->order->id ?? 0)
                ->count();

            if ($usageCount >= $coupon->usage_limit) {
                return false;
            }
        }

        if ($this->client_id && $coupon->usage_limit_per_user) {
            $userUsageCount = Order::where('coupon_id', $coupon->id)
                ->where('client_id', $this->client_id)
                ->where('id', '!=', $this->order->id ?? 0)
                ->count();

            if ($userUsageCount >= $coupon->usage_limit_per_user) {
                return false;
            }
        }

        $discountAmount = 0;
        if ($coupon->discount_type === 'percent') {
            $discountAmount = ($subtotal * $coupon->value) / 100;

            if ($coupon->maximum_discount && $discountAmount > $coupon->maximum_discount) {
                $discountAmount = $coupon->maximum_discount;
            }
        } else {
            $discountAmount = $coupon->value;
        }

        return min($discountAmount, $subtotal);
    }

    public function getCouponDetails(): ?array
    {
        if (!$this->coupon_id) {
            return null;
        }

        $coupon = Coupon::find($this->coupon_id);

        if (!$coupon) {
            return null;
        }

        return [
            'code' => $coupon->code ?? '#' . $coupon->id,
            'type' => $coupon->discount_type,
            'value' => $coupon->value,
            'minimum_amount' => $coupon->minimum_amount,
            'maximum_discount' => $coupon->maximum_discount,
        ];
    }

    public function save(): void
    {
        $this->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'reference' => ['required', 'string', 'max:10', 'unique:orders,reference,' . ($this->order?->id ?? '')],
            'carrier_id' => ['nullable', 'exists:carriers,id'],
            'shipping_address_id' => ['nullable', 'exists:addresses,id'],
            'billing_address_id' => ['nullable', 'exists:addresses,id'],
            'currency_id' => ['required', 'exists:currencies,id'],
            'season_id' => ['nullable', 'exists:seasons,id'],
            'last_status_id' => ['required', 'exists:order_statuses,id'],
            'discount_type' => ['required', 'in:percent,amount'],
            'coupon_id' => ['nullable', 'exists:coupons,id'],
        ]);

        $orderStatus = null;
        if ($this->last_status_id) {
            $orderStatus = OrderStatus::find($this->last_status_id);
        }

        $data = [
            'client_id' => $this->client_id,
            'reference' => $this->reference,
            'carrier_id' => $this->carrier_id ?? 0,
            'shipping_address_id' => $this->shipping_address_id ?? 0,
            'billing_address_id' => $this->billing_address_id ?? 0,
            'currency_id' => $this->currency_id,
            'season_id' => $this->season_id ?? 0,
            'note' => $this->note,
            'last_status_id' => $this->last_status_id ?? 0,
            'last_status_name' => $orderStatus?->currentLanguage->name ?? '',
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,
            'payment_method' => $this->payment_method,
            'payment_info' => $this->payment_info,
            'returned' => $this->returned,
            'return_note' => $this->return_note,
            'return_amount' => $this->return_amount,
            'coupon_id' => $this->coupon_id,
        ];

        if (!$this->order->exists) {
            $this->order = Order::create($data);

            if ($this->last_status_id) {
                OrderOrderStatus::create([
                    'order_status_id' => $this->last_status_id,
                    'order_id' => $this->order->id,
                ]);
            }

            $this->saveOrderDetails();

            session()->flash('status', __('ecommerce::orders.order_created'));
        } else {
            $oldStatusId = $this->order->last_status_id;
            $this->order->update($data);

            if ($this->last_status_id && $oldStatusId != $this->last_status_id) {
                OrderOrderStatus::create([
                    'order_status_id' => $this->last_status_id,
                    'order_id' => $this->order->id,
                ]);
            }

            $this->saveOrderDetails();

            session()->flash('status', __('ecommerce::orders.order_updated'));
        }

        $this->redirect(route(config('ud-ecommerce.admin_route_prefix', 'admin') . '.orders.index'), navigate: true);
    }

    protected function saveOrderDetails(): void
    {
        $existingIds = [];
        $isReturnAffectStock = EcommerceConfig::getValue('is_returned_product_affect_stock', false);

        foreach ($this->orderDetails as $detail) {
            $detailData = [
                'order_id' => $this->order->id,
                'product_id' => $detail['product_id'],
                'product_name' => $detail['product_name'],
                'variation_id' => $detail['variation_id'] ?? 0,
                'quantity' => $detail['quantity'],
                'price' => $detail['price'],
                'discount_type' => $detail['discount_type'],
                'discount' => $detail['discount'],
                'returned' => $detail['returned'],
                'return_note' => $detail['return_note'],
                'return_amount' => $detail['return_amount'],
                'language_id' => $this->selected_language,
            ];

            if (isset($detail['id'])) {
                // Update existing detail
                $originalDetail = collect($this->originalOrderDetails)->firstWhere('id', $detail['id']);
                $oldQuantity = $originalDetail['quantity'] ?? 0;
                $newQuantity = $detail['quantity'];
                $oldReturned = $originalDetail['returned'] ?? false;
                $newReturned = $detail['returned'] ?? false;
                $oldVariationId = $originalDetail['variation_id'] ?? 0;
                $newVariationId = $detail['variation_id'] ?? 0;

                // Handle stock changes when updating
                if ($oldVariationId > 0 || $newVariationId > 0) {
                    // Calculate how the stock should be affected
                    $shouldRestoreOldStock = !$oldReturned || !$isReturnAffectStock;
                    $shouldDecreaseNewStock = !$newReturned || !$isReturnAffectStock;

                    if ($oldVariationId == $newVariationId) {
                        // Same stock item
                        $quantityDiff = $newQuantity - $oldQuantity;

                        if ($quantityDiff != 0 && $shouldRestoreOldStock && $shouldDecreaseNewStock) {
                            if ($quantityDiff > 0) {
                                Variation::find($newVariationId)->decreaseQuantity($quantityDiff);
                            } else {
                                Variation::find($oldVariationId)->increaseQuantity(abs($quantityDiff));
                            }
                        }

                        // Handle return status change
                        if ($oldReturned != $newReturned && $isReturnAffectStock) {
                            if ($newReturned) {
                                // Item is now returned - restore stock
                                Variation::find($newVariationId)->increaseQuantity($newQuantity);
                            } else {
                                // Item is no longer returned - decrease stock
                                Variation::find($newVariationId)->decreaseQuantity($newQuantity);
                            }
                        }
                    } else {
                        // Stock changed
                        if ($oldVariationId > 0 && $shouldRestoreOldStock) {
                            Variation::find($oldVariationId)->increaseQuantity($oldQuantity);
                        }
                        if ($newVariationId > 0 && $shouldDecreaseNewStock) {
                            Variation::find($newVariationId)->decreaseQuantity($newQuantity);
                        }
                    }
                }

                OrderDetail::where('id', $detail['id'])->update($detailData);
                $existingIds[] = $detail['id'];
            } else {
                // New detail - decrease stock
                if ($detail['variation_id'] > 0) {
                    $shouldDecreaseStock = !$detail['returned'] || !$isReturnAffectStock;
                    if ($shouldDecreaseStock) {
                        Variation::find($detail['variation_id'])->decreaseQuantity($detail['quantity']);
                    }
                }

                $newDetail = OrderDetail::create($detailData);
                $existingIds[] = $newDetail->id;
            }
        }

        // Delete removed details and restore their stock
        if ($this->order->exists) {
            $deletedDetails = OrderDetail::where('order_id', $this->order->id)
                ->whereNotIn('id', $existingIds)
                ->get();

            foreach ($deletedDetails as $deletedDetail) {
                if ($deletedDetail->variation_id > 0) {
                    $originalDetail = collect($this->originalOrderDetails)->firstWhere('id', $deletedDetail->id);
                    if ($originalDetail) {
                        $shouldRestoreStock = !$originalDetail['returned'] || !$isReturnAffectStock;
                        if ($shouldRestoreStock) {
                            Variation::find($deletedDetail->variation_id)->increaseQuantity($deletedDetail->quantity);
                        }
                    } else {
                        // Fallback if original detail not found
                        Variation::find($deletedDetail->variation_id)->increaseQuantity($deletedDetail->quantity);
                    }
                }
            }

            OrderDetail::where('order_id', $this->order->id)
                ->whereNotIn('id', $existingIds)
                ->delete();
        }
    }

    public function openDetailModal(): void
    {
        $this->resetDetailForm();
        $this->editing_detail_index = null;
        $this->show_detail_modal = true;
    }

    public function editDetail(int $index): void
    {
        $detail = $this->orderDetails[$index];

        $this->editing_detail_index = $index;
        $this->detail_product_id = $detail['product_id'];
        $this->variation_id = $detail['variation_id'] ?? null;
        $this->is_detail_product_variant = $detail['variation_id'] ? true : false;
        $this->detail_quantity = $detail['quantity'];
        $this->detail_price = $detail['price'];
        $this->detail_discount_type = $detail['discount_type'];
        $this->detail_discount = $detail['discount'];
        $this->detail_returned = $detail['returned'];
        $this->detail_return_note = $detail['return_note'];
        $this->detail_return_amount = $detail['return_amount'];
        $this->show_detail_modal = true;
    }

    public function requestDeleteDetail(int $index): void
    {
        $this->editing_detail_index = $index;
        $this->show_delete_detail_modal = true;
    }

    public function deleteDetail(): void
    {
        unset($this->orderDetails[$this->editing_detail_index]);
        $this->orderDetails = array_values($this->orderDetails);
        $this->show_delete_detail_modal = false;
        $this->editing_detail_index = null;
        session()->flash('status', __('ecommerce::orders.detail_deleted'));
    }

    public function closeDeleteDetailModal(): void
    {
        $this->show_delete_detail_modal = false;
        $this->editing_detail_index = null;
    }

    public function closeDetailModal(): void
    {
        $this->show_detail_modal = false;
        $this->resetDetailForm();
    }

    protected function resetDetailForm(): void
    {
        $this->detail_product_id = null;
        $this->variation_id = null;
        $this->is_detail_product_variant = false;
        $this->detail_quantity = 1;
        $this->detail_price = 0;
        $this->detail_discount_type = 'percent';
        $this->detail_discount = 0;
        $this->detail_returned = false;
        $this->detail_return_note = null;
        $this->detail_return_amount = 0;
    }

    public function updatedDetailProductId($value): void
    {
        if ($value) {
            $product = Product::find($value);
            if ($product) {
                $this->detail_price = $product->price ?? 0;
                if ($product->type == 'variable') {
                    $this->is_detail_product_variant = true;
                } else {
                    $this->is_detail_product_variant = false;
                }
            }
        }
    }

    public function updatedVariationId($value): void
    {
        if ($value) {
            $variation = Variation::find($value);
            if ($variation) {
                $this->detail_price = $variation->price ?? 0;
            }
        }
    }

    public function saveDetail(): void
    {
        $this->validate([
            'detail_product_id' => ['required', 'exists:products,id'],
            'variation_id' => ['nullable', 'exists:variations,id', 'required_if:is_detail_product_variant,true'],
            'detail_quantity' => ['required', 'integer', 'min:1'],
            'detail_price' => ['required', 'numeric', 'min:0'],
            'detail_discount_type' => ['required', 'in:percent,amount'],
        ]);

        $product = Product::find($this->detail_product_id);

        // Check stock availability if stock_id is provided
        if ($this->variation_id) {
            $variation = Variation::find($this->variation_id);
            if (!$variation) {
                session()->flash('error', __('ecommerce::orders.variation_not_found'));

                return;
            }

            // Calculate available quantity (considering current order details and original details)
            $availableQuantity = $variation->quantity;

            // Add back quantities from original order details for this stock
            foreach ($this->originalOrderDetails as $originalDetail) {
                if (isset($originalDetail['variation_id']) && $originalDetail['variation_id'] == $this->variation_id) {
                    $isReturnAffectStock = EcommerceConfig::getValue('is_returned_product_affect_stock', false);
                    // Add back original quantity if not returned or if returns don't affect stock
                    if (!$originalDetail['returned'] || !$isReturnAffectStock) {
                        $availableQuantity += $originalDetail['quantity'];
                    }
                }
            }

            // Calculate total quantity needed for this variant in current order details (excluding the one being edited)
            $totalNeeded = 0;
            foreach ($this->orderDetails as $index => $detail) {
                if ($index !== $this->editing_detail_index && isset($detail['variation_id']) && $detail['variation_id'] == $this->variation_id) {
                    $totalNeeded += $detail['quantity'];
                }
            }

            // Add the new/edited quantity
            $totalNeeded += $this->detail_quantity;

            // Check if total needed quantity is available
            if ($totalNeeded > $availableQuantity) {
                session()->flash('error', __('ecommerce::orders.insufficient_stock', ['available' => $availableQuantity - ($totalNeeded - $this->detail_quantity)]));

                return;
            }
        }

        $detailData = [
            'product_id' => $this->detail_product_id,
            'product_name' => $product->currentLanguage->name ?? '',
            'variation_id' => $this->variation_id ?? 0,
            'variation' => $variation->combination_name,
            'is_variation' => $this->variation_id ? true : false,
            'quantity' => $this->detail_quantity,
            'price' => $this->detail_price,
            'discount_type' => $this->detail_discount_type,
            'discount' => $this->detail_discount,
            'returned' => $this->detail_returned,
            'return_note' => $this->detail_return_note,
            'return_amount' => $this->detail_return_amount,
        ];

        if ($this->editing_detail_index !== null) {
            // Keep the existing ID if editing
            if (isset($this->orderDetails[$this->editing_detail_index]['id'])) {
                $detailData['id'] = $this->orderDetails[$this->editing_detail_index]['id'];
            }
            $this->orderDetails[$this->editing_detail_index] = $detailData;
            session()->flash('status', __('ecommerce::orders.detail_updated'));
        } else {
            $this->orderDetails[] = $detailData;
            session()->flash('status', __('ecommerce::orders.detail_added'));
        }

        $this->closeDetailModal();
    }

    public function calculateDetailTotal(array $detail): float
    {
        $subtotal = $detail['price'] * $detail['quantity'];

        if ($detail['discount'] > 0) {
            if ($detail['discount_type'] === 'percent') {
                $subtotal -= ($subtotal * $detail['discount'] / 100);
            } else {
                $subtotal -= $detail['discount'];
            }
        }

        return max(0, $subtotal);
    }

    public function calculateOrderTotal(): float
    {
        $total = 0;

        foreach ($this->orderDetails as $detail) {
            $total += $this->calculateDetailTotal($detail);
        }

        if ($this->discount > 0) {
            if ($this->discount_type === 'percent') {
                $total -= ($total * $this->discount / 100);
            } else {
                $total -= $this->discount;
            }
        }

        $couponDiscount = $this->validateAndApplyCoupon($total);
        if ($couponDiscount !== false && $couponDiscount > 0) {
            $total -= $couponDiscount;
        }

        return max(0, $total);
    }

    public function calculateSubtotal(): float
    {
        $subtotal = 0;

        foreach ($this->orderDetails as $detail) {
            $subtotal += $detail['price'] * $detail['quantity'];
        }

        return $subtotal;
    }

    public function calculateTotalAfterItemDiscounts(): float
    {
        $total = 0;

        foreach ($this->orderDetails as $detail) {
            $total += $this->calculateDetailTotal($detail);
        }

        return $total;
    }

    public function calculateOrderDiscount(): float
    {
        if ($this->discount <= 0) {
            return 0;
        }

        $total = $this->calculateTotalAfterItemDiscounts();

        if ($this->discount_type === 'percent') {
            return ($total * $this->discount) / 100;
        }

        return min($this->discount, $total);
    }

    public function calculateCouponDiscount(): float
    {
        $totalAfterOrderDiscount = $this->calculateTotalAfterItemDiscounts() - $this->calculateOrderDiscount();
        $couponDiscount = $this->validateAndApplyCoupon($totalAfterOrderDiscount);

        return $couponDiscount !== false ? $couponDiscount : 0;
    }

    // Address methods
    public function openAddressModal(): void
    {
        if (!$this->client_id) {
            session()->flash('error', __('ecommerce::orders.select_client_first'));

            return;
        }
        $this->resetAddressForm();
        $this->show_address_modal = true;
    }

    public function closeAddressModal(): void
    {
        $this->show_address_modal = false;
        $this->resetAddressForm();
    }

    protected function resetAddressForm(): void
    {
        $this->address_name = null;
        $this->address_destination_name = null;
        $this->address_address = null;
        $this->address_post_code = null;
        $this->address_city = null;
        $this->address_state = null;
        $this->address_country = 'Italy';
        $this->address_telephone = null;
        $this->address_default = false;
    }

    public function saveAddress(): void
    {
        if (!$this->client_id) {
            session()->flash('error', __('ecommerce::orders.select_client_first'));

            return;
        }

        $this->validate([
            'address_address' => ['required', 'string', 'max:255'],
            'address_default' => ['boolean'],
        ]);

        $address = Address::create([
            'client_id' => $this->client_id,
            'name' => $this->address_name,
            'destination_name' => $this->address_destination_name,
            'address' => $this->address_address,
            'post_code' => $this->address_post_code,
            'city' => $this->address_city,
            'state' => $this->address_state,
            'country' => $this->address_country,
            'telephone' => $this->address_telephone,
            'default' => $this->address_default,
        ]);

        $this->shipping_address_id = $address->id;
        $this->billing_address_id = $address->id;
        $this->closeAddressModal();

        session()->flash('status', __('ecommerce::orders.address_created'));
    }

    public function render()
    {
        $orderStatusHistory = collect();
        if ($this->order?->exists) {
            $orderStatusHistory = OrderOrderStatus::where('order_id', $this->order->id)
                ->with('orderStatus')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $today = now()->format('Y-m-d');

        return view('ecommerce::livewire.admin.order.order.order-create-edit', [
            'isEditing' => $this->isEditing,
            'clients' => Client::with('user')->orderBy('id')->get(),
            'carriers' => Carrier::where('active', true)->orderBy('id')->get(),
            'currencies' => Currency::orderBy('id')->get(),
            'seasons' => Season::orderBy('id')->get(),
            'orderStatuses' => OrderStatus::orderBy('id')->get(),
            'addresses' => $this->client_id
                ? Address::where('client_id', $this->client_id)->get()
                : collect(),
            'orderStatusHistory' => $orderStatusHistory,
            'products' => Product::with('currentLanguage')->orderBy('id')->get(),
            'variations' => Variation::where('product_id', $this->detail_product_id)->where('quantity', '>', 0)->get(),
            'coupons' => Coupon::where('active', true)
                ->where(function ($q) use ($today) {
                    $q->where(function ($q2) {
                        $q2->whereNull('start_date')
                            ->whereNull('end_date');
                    })
                        ->orWhere(function ($q2) use ($today) {
                            $q2->whereDate('start_date', '<=', $today)
                                ->whereDate('end_date', '>=', $today);
                        });
                })
                ->orderBy('id')
                ->get(),
        ]);
    }
}
