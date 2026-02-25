<?php

namespace Unusualdope\LaravelEcommerce\Models\Cart;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Unusualdope\LaravelEcommerce\Models\Administration\Carrier;
use Unusualdope\LaravelEcommerce\Models\Administration\Currency;
use Unusualdope\LaravelEcommerce\Models\Coupon;
use Unusualdope\LaravelEcommerce\Models\Customer\Client;
use Unusualdope\LaravelEcommerce\Models\Language;
use Unusualdope\LaravelEcommerce\Models\Product\Product;
use Unusualdope\LaravelEcommerce\Models\Stock\Variation;

class Cart extends Model
{
    use HasFactory;

    public $guest_id = 0;

    public $product_data = [];

    public $tot_quantity = 0;

    protected $fillable = [
        'order_id',
        'client_id',
        'carrier_id',
        'shipping_address_id',
        'billing_address_id',
        'season_id',
        'currency_id',
        'coupon_id',
        'note',
        'guest_id',
        'secure_key',
    ];

    protected static function booted()
    {
        static::creating(function (Cart $cart) {
            if ($cart->reference == '') {
                $cart->reference = self::generateReference();
            }
        });

        static::updating(function (Cart $cart) {
            if ($cart->reference == '') {
                $cart->reference = self::generateReference();
            }
        });
    }

    /**
     * Define relationships.
     */
    public function details(): HasMany
    {
        return $this->hasMany(CartDetail::class, 'cart_id', 'id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public static function getTotalQuantity(int $cart_id = 0)
    {
        if ($cart_id == 0) {
            $cart_id = session()->get('cart_id');
        }

        return CartDetail::where('cart_id', $cart_id)->sum('quantity');
    }

    public function detailsByProduct(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->details->groupBy('product_id')
        );
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function carrier(): HasOne
    {
        return $this->hasOne(Carrier::class, 'carrier_id', 'id');
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(\Unusualdope\LaravelEcommerce\Models\Address::class, 'shipping_address_id', 'id');
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(\Unusualdope\LaravelEcommerce\Models\Address::class, 'billing_address_id', 'id');
    }

    /**
     * Get the current cart for the logged-in user.
     */
    public static function getCurrentCart($with_details = true, $return_simple = false): ?self
    {
        $cartId = session()->get('cart_id');
        $cart = null;

        if ($cartId && is_numeric($cartId)) {
            $cart = self::where('id', $cartId)
                ->whereNull('order_id')
                ->when($with_details, fn($q) => $q->with(['details']))
                ->first();
        }

        if (!$cart) {
            $cart_query = self::where('secure_key', self::getUserSecureKey())
                ->whereNull('order_id')
                ->where(function ($q) {
                    $q->where('client_id', session()->get('client_id'))
                        ->orWhere('guest_id', session()->getId());
                });

            if ($with_details) {
                $cart_query->with(['details']);
            }

            $cart = $cart_query->first();
        }

        // If no cart exists, initialize a new one without checking for an existing cart to avoid recursive calls between initializeCart() and getCurrentCart()
        if (!$cart) {
            $cart = self::initializeCart(false);
        }

        if (isset($cart) && is_int($cart->id)) {

            session()->put('cart_id', $cart->id);
            session()->save();

            if ($return_simple) {
                return $cart;
            }

            $tot_quantity = $cart->details->sum('quantity'); // Sum up total quantity
            if ($with_details) {
                $cart->tot_quantity = $tot_quantity;
            }

            $products = Product::whereIn('id', $cart->details->pluck('product_id')->toArray())
                ->get()
                ->keyBy('id');

            // Load variations for cart details that have variation_id != 0
            $variationIds = $cart->details->where('variation_id', '!=', 0)->pluck('variation_id')->unique()->toArray();
            $variations = [];
            if (!empty($variationIds)) {
                $variations = Variation::whereIn('id', $variationIds)
                    ->with('image')
                    ->get()
                    ->keyBy('id');
            }
            // dd($variations);
            $product_data = [];
            foreach ($cart->details as $detail) {
                $key = $detail->product_id . '_' . $detail->variation_id;
                $product_data[$key]['product_id'] = $detail->product_id;
                $product_data[$key]['variation_id'] = $detail->variation_id;
                // total quantity handling
                if (!isset($product_data[$key]['total_quantity'])) {
                    $product_data[$key]['total_quantity'] = 0;
                }

                // price handling
                if (!isset($product_data[$key]['product_price'])) {
                    $price = $products[$detail->product_id]->price;
                    if ($detail->variation_id != 0 && isset($variations[$detail->variation_id])) {
                        $variation = $variations[$detail->variation_id];
                        if ($variation->price > 0) {
                            $price = $variation->price;
                        }
                    }
                    $product_data[$key]['product_price'] = $price;
                }

                // total price handling
                if (!isset($product_data[$key]['total_price'])) {
                    $product_data[$key]['total_price'] = 0;
                }
                $product_data[$key]['total_price'] += $detail->quantity * $product_data[$key]['product_price'];

                // image handling
                if (!isset($product_data[$key]['product_image'])) {
                    if (isset($variations[$detail->variation_id]->image)) {
                        if ($variations[$detail->variation_id]->image->image) {
                            $product_data[$key]['product_image'] = $variations[$detail->variation_id]->image->image;
                        } else {
                            $product_data[$key]['product_image'] = $variations[$detail->variation_id]->image;
                        }
                    } else {
                        if ($products[$detail->product_id]->defaultImage->image) {
                            $product_data[$key]['product_image'] = $products[$detail->product_id]->defaultImage->image;
                        } else {
                            $product_data[$key]['product_image'] = $products[$detail->product_id]->defaultImage;
                        }
                    }
                }

                // name handling
                if (!isset($product_data[$key]['product_name'])) {
                    $product_data[$key]['product_name'] = $products[$detail->product_id]->name;
                }

                // variant name handling
                if ($detail->variation_id != 0 && isset($variations[$detail->variation_id])) {
                    $variation = $variations[$detail->variation_id];
                    if ($variation instanceof Variation) {
                        $product_data[$key]['variants'] = $variation->getVariantLabelsForLanguage(Language::getCurrentLanguage());
                    } else {
                        $product_data[$key]['variants'] = null;
                    }
                } else {
                    $product_data[$key]['variants'] = null;
                }

                $product_data[$key]['total_quantity'] += $detail->quantity;
            }
            $cart->product_data = $product_data;

            if ($cart->reference == '') {
                $simple_cart = self::find($cart->id);
                self::generateReference($simple_cart);
                $simple_cart->save();
            }

            return $cart;
        }

        return null;
    }

    /**
     * Generate a secure key based on the authenticated user's ID.
     */
    public static function getUserSecureKey($userId = null): string
    {
        $userId = $userId ?? Auth::id() ?? session()->getId();

        return hash('sha256', $userId . config('app.key'));
    }

    public static function saveMultipleQuantities($product_id, $product_name, $selected_quantity)
    {
        foreach ($selected_quantity as $variation_id => $quantity) {
            Cart::addToCart(
                $product_id,
                $variation_id,
                $quantity,
                session()->get('language_id'),
                $product_name
            );
        }
    }

    /**
     * Add a product to the cart.
     */
    public static function addToCart(
        $product_id,
        $variation_id,
        $quantity = 1,
        $language_id = null,
        $product_name = null,
    ) {
        $cart = self::getCurrentCart();

        // Ensure a cart is always returned (prevent possible null errors)
        if (!$cart) {
            return null;
        }

        // Check if a cart detail with the same product & attribute already exists
        $existingDetail = $cart->details()
            ->where('product_id', $product_id)
            ->where('variation_id', $variation_id)
            ->first();

        if ($existingDetail) {
            // Update quantity using direct DB query to avoid triggering model events/cache
            // which could cause recursive calls to getCurrentCart()
            $newQuantity = $existingDetail->quantity + $quantity;
            DB::table('cart_details')
                ->where('cart_id', $cart->id)
                ->where('product_id', $product_id)
                ->where('variation_id', $variation_id)
                ->update([
                    'quantity' => $newQuantity,
                    'language_id' => $language_id,
                    'product_name' => $product_name,
                    'updated_at' => now(),
                ]);
        } elseif ($quantity > 0) {
            // Create a new cart detail entry if quantity > 0
            $cart->details()->create([
                'product_id' => $product_id,
                'variation_id' => $variation_id,
                'quantity' => $quantity,
                'language_id' => $language_id,
                'product_name' => $product_name,
            ]);
        }
    }

    /**
     * Initialize a new cart only if none exists.
     */
    public static function initializeCart(bool $check_existing_cart = true): ?self
    {
        // If checking for an existing cart, only create if one doesn't exist
        if ($check_existing_cart) {
            $existingCart = self::getCurrentCart();
            if ($existingCart) {
                $cart = $existingCart;
            }
        }
        if (!isset($cart)) {
            $cart = self::create([
                'client_id' => session()->get('client_id'),
                'secure_key' => self::getUserSecureKey(),
                'order_id' => null,
                'currency_id' => Currency::getCurrentCurrency()->id,
                'season_id' => session()->get('season_id') ?? 0,
                'reference' => '',
                'guest_id' => session()->getId(),
            ]);
        }

        session()->put('cart_id', $cart->id);

        return $cart;
    }

    public static function getTotalsForSummary($cart_id = null)
    {
        if (is_null($cart_id)) {
            $cart = self::getCurrentCart();
            if (!isset($cart->id)) {
                return null;
            }
            $cart_id = $cart->id;
        }

        // Calculate total with consideration for variation prices
        // If variation_id is not 0, use variation price, otherwise use product price
        $result = DB::table('cart_details as cd')
            ->leftJoin('products as p', 'cd.product_id', '=', 'p.id')
            ->leftJoin('variations as v', function ($join) {
                $join->on('cd.variation_id', '=', 'v.id')
                    ->where('cd.variation_id', '>', 0);
            })
            ->where('cd.cart_id', $cart_id)
            ->selectRaw('
                SUM(cd.quantity) as total_quantity,
                SUM(cd.quantity * CASE 
                    WHEN cd.variation_id > 0 AND v.price IS NOT NULL AND v.price > 0 THEN v.price 
                    ELSE p.price 
                END) as total_amount_no_taxes
            ')
            ->first();
        $taxes = $result->total_amount_no_taxes * (config('b2b.tax') / 100);

        // Shipping cost from session (set in Step 3)
        $shipping = session('checkout.shipping_method.price', 0);

        // Payment fee from session (set in Step 4, e.g., COD fee)
        $paymentFee = session('checkout.payment_method.fee', 0);

        $result->total_amount_no_taxes = round($result->total_amount_no_taxes, 2);
        $result->total_taxes = round($taxes, 2);
        $result->shipping_cost = round($shipping, 2);
        $result->payment_fee = round($paymentFee, 2);
        $result->grand_total = round($shipping + $paymentFee + $taxes + $result->total_amount_no_taxes, 2);

        return $result;

        return $result;
    }

    public static function getTotalQuantityInCart($product_id, $cart_id = null)
    {
        if (is_null($cart_id)) {
            $cart = self::getCurrentCart();
            if (!isset($cart->id)) {
                return null;
            }
            $cart_id = $cart->id;
        }

        return CartDetail::where('cart_id', $cart_id)->where('product_id', $product_id)->sum('quantity');
    }

    public static function emptyCartLines($cart_id = null, $except_season_id = null)
    {
        if (is_null($cart_id)) {
            $cart = self::getCurrentCart();
            if (!isset($cart->id)) {
                return null;
            }
            $cart_id = $cart->id;
        }

        return CartDetail::where('cart_id', $cart_id)->delete();
    }

    public function getTotal($cart_id)
    {
        if (is_null($cart_id)) {
            $cart = self::getCurrentCart();
            if (!isset($cart->id)) {
                return null;
            }
            $cart_id = $cart->id;
        }

        $data = self::getTotalsForSummary($cart_id);

        return $data->grand_total;
    }

    private static function generateReference($cart = null): string
    {
        $reference = Str::random(10);
        while (Cart::where('reference', $reference)->exists()) {
            $reference = Str::random(10);
        }

        if ($cart) {
            $cart->reference = $reference;
        }

        return $reference;
    }

    public static function removeProductFromCart($product_id, $variation_id)
    {
        $cart = self::getCurrentCart();
        if (!isset($cart->id)) {
            return null;
        }
        $cart_id = $cart->id;

        return CartDetail::where('cart_id', $cart_id)->where('product_id', $product_id)->where('variation_id', $variation_id)->delete();
    }

    public static function increaseQuantity($product_id, $variation_id)
    {
        $cart = self::getCurrentCart();
        if (!isset($cart->id)) {
            return null;
        }
        $cart_id = $cart->id;

        return CartDetail::where('cart_id', $cart_id)->where('product_id', $product_id)->where('variation_id', $variation_id)->increment('quantity');
    }

    public static function decreaseQuantity($product_id, $variation_id)
    {
        $cart = self::getCurrentCart();
        if (!isset($cart->id)) {
            return null;
        }
        $cart_id = $cart->id;

        return CartDetail::where('cart_id', $cart_id)->where('product_id', $product_id)->where('variation_id', $variation_id)->decrement('quantity');
    }
}
