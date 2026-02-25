<?php

namespace Unusualdope\LaravelEcommerce\Models\Order;

use Illuminate\Database\Eloquent\Model;

class OrderOrderStatus extends Model
{
    protected $table = 'order_order_status';

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class);
    }
}
