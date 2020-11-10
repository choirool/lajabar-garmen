<?php
namespace App\Models\Scopes;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemPrice;
use Illuminate\Database\Eloquent\Builder;

trait OrderScope  
{
    public function scopeOrderAmount(Builder $query)
    {
        $query->addSelect([
            'order_amount' => OrderItemPrice::query()
            ->selectRaw('sum(qty * price)')
            ->whereHas('orderItem', function ($query) {
                $query->whereColumn('order_id', '=', 'orders.id');
            })
        ]);
    }
}
