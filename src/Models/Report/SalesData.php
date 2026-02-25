<?php

namespace Unusualdope\LaravelEcommerce\Models\Report;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Unusualdope\LaravelEcommerce\Models\Customer\Agent;
use Unusualdope\LaravelEcommerce\Models\Customer\Client;
use Unusualdope\LaravelEcommerce\Models\Order\Order;
use Unusualdope\LaravelEcommerce\Models\Order\OrderDetail;
use Unusualdope\LaravelEcommerce\Models\Product\Product;
use Unusualdope\LaravelEcommerce\Models\Product\ProductCategory;
use Unusualdope\LaravelEcommerce\Models\Product\Season;
use Unusualdope\LaravelEcommerce\Models\Stock\Stock;

class SalesData extends Model
{
    use Cachable;

    protected $index = [
        'sales_idx' => ['order_id', 'agent_id', 'client_id', 'season_id',
            'product_category_id', 'product_id', 'stock_id'],
    ];

    public $listeners = [
        'OrderDetail::created' => 'addOrderDetailsDataToSalesData',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function orderDetail(): BelongsTo
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public static function addOrderDetailsDataToSalesData(OrderDetail $order_detail)
    {

        SalesData::create([
            'order_id' => $order_detail->order_id,
            'order_date' => $order_detail->order->created_at,
            'agent_id' => $order_detail->order->agent_id ?? 0,
            'client_id' => $order_detail->order->client_id,
            'season_id' => $order_detail->order->season_id,
            'product_category_id' => $order_detail->product->default_category_id,
            'product_id' => $order_detail->product_id,
            'stock_id' => $order_detail->stock_id,
            'quantity' => $order_detail->quantity,
            'price' => $order_detail->price,
        ]);
    }

    public static function storeOrderData($order_id): bool
    {
        $result = true;
        $order_details = OrderDetail::where('order_id', $order_id)->get();
        foreach ($order_details as $order_detail) {
            $result = $result && self::addOrderDetailsDataToSalesData($order_detail);
        }

        return $result;
    }

    // takes care of keeping synced the sales data with the modifications and deletions of order details
    public static function deleteOrderData($order_id = null, ?OrderDetail $order_detail = null, $rebuild = true): bool
    {
        if (is_null($order_id) && is_null($order_detail)) {
            return false;
        }
        if (! is_null($order_detail)) {
            $result = SalesData::where('order_id', $order_detail->order_id)
                ->where('product_id', $order_detail->product_id)
                ->where('stock_id', $order_detail->stock_id)
                ->where('customization_id', $order_detail->customization_id)
                ->delete();
        } else {
            $result = SalesData::where('order_id', $order_id)->delete();
            if ($rebuild) {
                $result = $result && self::storeOrderData($order_id);
            }
        }

        return $result;
    }

    public static function getOrdersNotStored()
    {
        $order_ids = SalesData::select('order_id')->distinct()->get()->pluck('order_id');

        return Order::whereNotIn('id', $order_ids)->get();
    }

    public static function getOrderDetailsNotStored()
    {
        $order_details_ids = SalesData::select('order_detail_id')->distinct()->get()->pluck('order_detail_id');

        return OrderDetail::whereNotIn('id', $order_details_ids)->get();
    }

    public function syncData()
    {
        $orders_to_store = SalesData::getOrdersNotStored();
        foreach ($orders_to_store as $order_to_store) {
            SalesData::storeOrderData($order_to_store->id);
        }
    }

    public static function getBestSellingProducts($startDate, $endDate, $limit = 1)
    {
        // dump($limit);
        $sales = SalesData::where('order_date', '>=', $startDate)
            ->where('order_date', '<=', $endDate);
        if ($limit <= 1) {
            $sales->first();
        } else {
            $sales->limit($limit)->get();
        }

        return $sales;

    }
}
