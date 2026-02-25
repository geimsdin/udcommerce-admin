<?php

namespace Unusualdope\LaravelEcommerce\Models\Report;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Sushi\Sushi;
use Unusualdope\LaravelEcommerce\Models\Customer\Agent;
use Unusualdope\LaravelEcommerce\Models\Customer\Client;
use Unusualdope\LaravelEcommerce\Models\Order\Order;
use Unusualdope\LaravelEcommerce\Models\Order\OrderDetail;
use Unusualdope\LaravelEcommerce\Models\Product\Product;
use Unusualdope\LaravelEcommerce\Models\Product\ProductCategory;
use Unusualdope\LaravelEcommerce\Models\Product\Season;
use Unusualdope\LaravelEcommerce\Models\Stock\Stock;

class SalesDataSushi extends Model
{
    use Sushi;

    public $startDate = null;

    public $endDate = null;

    public $agent_id = null;

    public $client_id = null;

    public $season_id = null;

    public $product_category_id = null;

    public $product_id = null;

    public static $rowsStatic = [];

    public $rows = [];

    public function __construct()
    {
        $this->rows = self::$rowsStatic;
        parent::__construct();
    }

    public static function setRows($data)
    {
        self::$rowsStatic = $data;
    }

    public function getRows()
    {
        return $this->rows;
    }

    protected function sushiShouldCache()
    {
        return false;
    }

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
}
