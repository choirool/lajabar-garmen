<?php

namespace App\Models\Scopes;

use App\Models\Payment;
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

    public function scopePaidAmount(Builder $query)
    {
        $query->addSelect([
            'paid_amount' => Payment::query()
                ->selectRaw('sum(amount)')
                ->whereColumn('order_id', 'orders.id')
        ]);
    }
}
