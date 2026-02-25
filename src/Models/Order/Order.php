<?php

namespace Unusualdope\LaravelEcommerce\Models\Order;

use App\Mail\Order\OrderStatusChanged;
use App\Models\Report\SalesData;
use App\Notifications\SendOrderReceivedNotification;
use App\Services\Mexal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Unusualdope\LaravelEcommerce\Enums\OrderStatusEnum;
use Unusualdope\LaravelEcommerce\Models\Address;
use Unusualdope\LaravelEcommerce\Models\Administration\Carrier;
use Unusualdope\LaravelEcommerce\Models\Administration\CarrierLanguage;
use Unusualdope\LaravelEcommerce\Models\Administration\Currency;
use Unusualdope\LaravelEcommerce\Models\Cart\Cart;
use Unusualdope\LaravelEcommerce\Models\Customer\Client;
use Unusualdope\LaravelEcommerce\Models\Language;
use Unusualdope\LaravelEcommerce\Models\Product\Product;
use Unusualdope\LaravelEcommerce\Models\Product\Season;
use Unusualdope\LaravelEcommerce\Models\Stock\Stock;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    public $guest_id = 0; // Defaulted to 0 because B2B has no guests.

    /**
     * Define relationships.
     */
    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(OrderStatus::class, 'order_order_status');
    }

    public function status(): HasOne
    {
        return $this->hasOne(OrderStatus::class, 'id', 'last_status_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    public function carrierName(): BelongsTo
    {
        $language_id = Language::getCurrentLanguage();

        return $this->belongsTo(CarrierLanguage::class, 'carrier_id', 'carrier_id')
            ->where('language_id', $language_id);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id', 'id');
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id', 'id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function lastStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'last_status_id', 'id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function salesData(): HasMany
    {
        return $this->hasMany(SalesData::class, 'order_id', 'id');
    }

    public function currentStatusName(): BelongsTo
    {
        $language_id = Language::getCurrentLanguage();

        return $this->belongsTo(OrderStatusLanguage::class, 'last_status_id', 'order_status_id')
            ->where('language_id', $language_id);
    }

    public static function previousStatusName($order_id): string
    {
        $language_id = Language::getCurrentLanguage();
        $prev_order_status_id = OrderOrderStatus::where('order_id', $order_id)
            ->select('order_status_id')
            ->orderBy('updated_at', 'desc')
            ->offset(1)
            ->take(1)
            ->value('order_status_id');

        return OrderStatusLanguage::where('order_status_id', $prev_order_status_id)
            ->where('language_id', $language_id)
            ->value('name');
    }

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public static function changeStatus($order_id, $new_order_status_id)
    {
        $order = self::find($order_id);

        $prev_status_id = $order->last_status_id;
        $order->update([
            'last_status_id' => $new_order_status_id,
            'last_status_name' => OrderStatus::find($new_order_status_id)->currentLanguage->name,
        ]);

        OrderOrderStatus::create([
            'order_status_id' => $new_order_status_id,
            'order_id' => $order_id,
        ]);

    }

    public static function transformCartIntoOrder($cart_id = null, $order_status_id = 0, $session_id = null)
    {
        try {
            DB::beginTransaction();

            if (is_null($cart_id)) {
                $cart = Cart::getCurrentCart(false);
                if (!isset($cart->id)) {
                    return null;
                }
                $cart_id = $cart->id;
                // re-set the cart
                $cart = Cart::find($cart_id);
            }

            if (!$cart || !$cart->id) {
                return false;
            }

            if (!$order_status_id || !OrderStatusEnum::tryFrom($order_status_id)) {
                return false;
            }

            $order_status = OrderStatus::find($order_status_id)->load('currentLanguage');

            // Check if the user is impersonated by someone else in order to add it on the order notes as a reference
            $imp_note = '';
            $imp_manager = app('impersonate');
            if ($imp_manager->isImpersonating()) {
                $imp_user = $imp_manager->getImpersonator();
                $imp_note = __(
                    'order.order_impersonated',
                    [
                        'impersonator_name' => $imp_user->name,
                        'impersonator_role' => $imp_user->user_type,
                        'impersonator_email' => $imp_user->email,
                    ]
                );
            }

            $order = self::create([
                'client_id' => $cart->client_id,
                'agent_id' => $cart->client->agent_id,
                'reference' => $cart->reference,
                'carrier_id' => $cart->carrier_id,
                'shipping_address_id' => $cart->shipping_address_id,
                'billing_address_id' => $cart->billing_address_id,
                'currency_id' => $cart->currency_id,
                'season_id' => $cart->season_id,
                'note' => $imp_note . PHP_EOL . $cart->note,
                'guest_id' => $cart->guest_id,
                'last_status_id' => $order_status_id,
                'last_status_name' => $order_status->currentLanguage->name,
                'discount_type' => 'percent',
                'discount' => 0,
                'coupon_id' => null,
                'payment_method' => config('b2b.payment_methods.' . session()->get('cart.payment_method_id') . '.name'),
                'payment_info' => '',
                'stripe_session_id' => $session_id,

            ]);

            if (!$order) {
                return false;
            }

            OrderOrderStatus::create([
                'order_status_id' => $order_status_id,
                'order_id' => $order->id,
            ]);

            DB::table('carts')->where('id', $cart->id)->update(['order_id' => $order->id]);

            foreach ($cart->details as $cart_detail) {
                $order->details()->create([
                    'order_id' => $order->id,
                    'product_id' => $cart_detail->product_id,
                    'stock_id' => $cart_detail->stock_id,
                    'customization_id' => $cart_detail->customization_id,
                    'language_id' => $cart_detail->language_id,
                    'product_name' => $cart_detail->product_name,
                    'quantity' => $cart_detail->quantity,
                    'price' => Product::getPrice($cart_detail->product_id, $cart_detail->stock_id, $cart_detail->customization_id),
                    'discount_type' => 'percent',
                    'discount' => 0,
                    'coupon_id' => 0,
                ]);

                Stock::decreaseQuantity($cart_detail->stock_id, $cart_detail->quantity);
            }

            DB::commit();

            // remove cart from session
            session()->forget('cart');
            session()->forget('cart_id');
            session()->forget('season_data');

            // queue the email to notify the parties involved about an order received
            SendOrderReceivedNotification::send($order, app()->getLocale());

            // exporting order for mexal using defer to avoid blocking the response
            defer(
                fn() => Mexal::exportOrder($order->id)
            );
            // Saving order data for statistics purposes using defer to avoid blocking the response
            defer(
                fn() => SalesData::storeOrderData($order->id)
            );

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error message
            Log::error('Error transforming cart into order: ' . $e->getMessage());

            // Optionally, log additional context
            Log::error('Cart ID: ' . $cart_id);
            Log::error('Order Status ID: ' . $order_status_id);
            Log::error('Stripe Session ID: ' . $session_id);
            Log::error('Exception Trace: ' . $e->getTraceAsString());

            return null;
        }

        return $order->load('details');
    }

    public static function createFromFrontSession(): ?self
    {
        try {
            DB::beginTransaction();

            $cart = Cart::getCurrentCart(false);
            if (!$cart) {
                return null;
            }

            $shippingMethod = session('checkout.shipping_method');
            $paymentMethod = session('checkout.payment_method');
            $shippingAddressId = session('checkout.shipping_address_id');
            $billingAddressId = session('checkout.billing_address_id');

            // Fallback for older sessions or partial checkouts
            if (!$shippingAddressId) {
                $shippingAddressId = session('checkout.address_id') ?? session('checkout.shipping_address.id');
            }
            if (!$billingAddressId) {
                $billingAddressId = $shippingAddressId;
            }

            if (!$shippingMethod || !$paymentMethod || !$shippingAddressId || !$billingAddressId) {
                return null;
            }

            $order_status_id = OrderStatusEnum::WAITING->value;
            $order_status = OrderStatus::with('currentLanguage')->find($order_status_id);

            // Generate a unique reference
            $reference = strtoupper(str()->random(8));

            $order = self::create([
                'client_id' => $cart->client_id,
                'reference' => $reference,
                'carrier_id' => $shippingMethod['id'],
                'shipping_address_id' => $shippingAddressId,
                'billing_address_id' => $billingAddressId,
                'currency_id' => $cart->currency_id,
                'season_id' => $cart->season_id ?? 0,
                'note' => $shippingMethod['notes'] ?? '',
                'guest_id' => session()->getId(),
                'last_status_id' => $order_status_id,
                'last_status_name' => $order_status->currentLanguage->name ?? $order_status->name ?? 'In attesa',
                'discount_type' => 'percent',
                'discount' => 0,
                'coupon_id' => null,
                'payment_method' => $paymentMethod['name'],
                'payment_info' => json_encode($paymentMethod),
            ]);

            OrderOrderStatus::create([
                'order_status_id' => $order_status_id,
                'order_id' => $order->id,
            ]);

            DB::table('carts')->where('id', $cart->id)->update(['order_id' => $order->id]);

            foreach ($cart->details as $cart_detail) {
                $order->details()->create([
                    'product_id' => $cart_detail->product_id,
                    'variation_id' => $cart_detail->variation_id,
                    'customization_id' => $cart_detail->customization_id ?? 0,
                    'language_id' => $cart_detail->language_id ?? 1,
                    'product_name' => $cart_detail->product_name ?? $cart_detail->product->name ?? 'Prodotto',
                    'quantity' => $cart_detail->quantity,
                    'price' => $cart_detail->variation->price ?? $cart_detail->product->price ?? 0,
                    'discount_type' => 'percent',
                    'discount' => 0,
                    'coupon_id' => null,
                ]);

                if ($cart_detail->variation) {
                    $cart_detail->variation->decreaseQuantity($cart_detail->quantity);
                }
            }

            DB::commit();

            return $order->load('details');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating order from front session: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return null;
        }
    }

    public static function getOrderByStripeSessionId($session_id): Order
    {
        return self::where('stripe_session_id', $session_id)->first();
    }

    public static function getOrderData($order_id, $full_info = true): Order
    {
        $order = Order::findOrFail($order_id);

        $order_total = 0;
        foreach ($order->details as $key => $detail) {
            $order->details[$key]->total_price = $detail->quantity * $detail->price;
            $order_total += $order->details[$key]->total_price;
        }

        $taxes = $order_total * (config('b2b.tax') / 100);
        $shipping = $order->carrier->price ?? 0;

        $paymentInfo = json_decode($order->payment_info, true);
        $paymentFee = $paymentInfo['fee'] ?? 0;

        if ($full_info) {
            $order->load('statuses');
            $order->load('carrier');
            $order->load('shippingAddress');
            $order->load('billingAddress');
            $order->status = $order->lastStatusName;
        }

        $order->shipping_method = $order->carrier->currentLanguage->name ?? '';
        $order->total_amount_no_taxes = round($order_total, 2);
        $order->total_taxes = round($taxes, 2);
        $order->total_discount = 0;
        $order->shipping_cost = round($shipping, 2);
        $order->payment_fee = round($paymentFee, 2);
        $order->grand_total = round($shipping + $taxes + $order_total + $paymentFee, 2);

        return $order;
    }

    public static function getClientHistory($client_id, $page = 1, $pagination = 20)
    {
        if (empty($page) || empty($pagination) || !is_numeric($page) || !is_numeric($pagination)) {
            return [];
        }

        $order_count = Order::where('client_id', $client_id)->count();
        $orders = Order::where('client_id', $client_id)
            ->with('details')
            ->skip(($page - 1) * $pagination)
            ->take($pagination)
            ->get();

        foreach ($orders as $key => $order) {

            $order_total = 0;
            foreach ($order->details as $key => $detail) {
                $order->details[$key]->total_price = $detail->quantity * $detail->price;
                $order_total += $order->details[$key]->total_price;
            }

            $taxes = $order_total * (config('b2b.tax') / 100);
            $shipping = $order->carrier->price ?? 0;
            $status = $order->lastStatusName;

            $orders[$key]->total_amount_no_taxes = round($order_total, 2);
            $orders[$key]->total_taxes = round($taxes, 2);
            $orders[$key]->total_discount = 0;
            $orders[$key]->status = $status;
            $orders[$key]->shipping_cost = round($shipping, 2);
            $orders[$key]->grand_total = round($shipping + $taxes + $order_total, 2);
        }

        return [
            'orders' => $orders,
            'order_count' => $order_count,
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($order) {
            // Check if last_status_id was changed
            if ($order->isDirty('last_status_id')) {
                $new_status_id = $order->last_status_id;

                // Check if this status change wasn't already recorded by changeStatus method
                $existingEntry = OrderOrderStatus::where('order_id', $order->id)
                    ->where('order_status_id', $new_status_id)
                    ->where('created_at', '>=', now()->subSeconds(3))
                    ->exists();

                if (!$existingEntry) {
                    OrderOrderStatus::create([
                        'order_status_id' => $new_status_id,
                        'order_id' => $order->id,
                    ]);
                }

                // Send email notification
                // @todo: send email notification to the client
                // Mail::to($order->client->user->email)
                //     ->queue(new OrderStatusChanged($order->id, app()->getLocale()));
            }
        });
    }

    public function isWaiting(): bool
    {
        return $this->last_status_id === OrderStatusEnum::WAITING->value;
    }

    public function isPaid(): bool
    {
        return $this->last_status_id === OrderStatusEnum::PAID->value;
    }

    public function isShipped(): bool
    {
        return $this->last_status_id === OrderStatusEnum::SHIPPED->value;
    }

    public function isDelivered(): bool
    {
        return $this->last_status_id === OrderStatusEnum::DELIVERED->value;
    }

    public function isCancelled(): bool
    {
        return $this->last_status_id === OrderStatusEnum::CANCELLED->value;
    }

    public static function export($date_from = '', $date_to = '', $order_status = '')
    {
        if (empty($date_from)) {
            $date_from = date('Y-m-d');
        }
        if (empty($date_to)) {
            $date_to = date('Y-m-d');
        }

        $orders = [];
        $products = self::whereDate('created_at', '>=', $date_from)->whereDate('created_at', '<=', $date_to)->with(['client', 'agent', 'carrierName', 'shippingAddress', 'billingAddress', 'currency', 'season']);
        if (!empty($order_status)) {
            $products = $products->where('last_status_id', $order_status);
        }
        foreach ($products->get() as $order) {
            $orders[] = [
                'CLIENT EMAIL' => $order->client->user->email,
                'CLIENT NAME' => !empty($order->client->first_name) ? $order->client->first_name : $order->client->user->name,
                'AGENT EMAIL' => $order->agent->user->email ?? '',
                'AGENT REFERENCE' => $order->agent->reference_code,
                'ORDER REFERENCE' => $order->reference,
                'CARRIER' => $order->carrierName->name,
                'SHIPPING ADDRESS' => $order->shippingAddress->address ?? '',
                'BILLING ADDRESS' => $order->billingAddress->address ?? '',
                'CURRENCY' => $order->currency->name,
                'SEASON' => !empty($order->season) ? $order->season->getNameAttribute() : '',
                'NOTE' => $order->note,
                'LAST STATUS' => $order->last_status_name,
                'PAYMENT METHOD' => $order->payment_method,
            ];
        }

        return $orders;
    }
}
