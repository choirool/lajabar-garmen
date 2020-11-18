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

    public function scopeFilterByUnpaid(Builder $query)
    {
        $query->where(function ($query) {
            $paidAmount = Payment::withoutGlobalScope('notZero')
                ->selectRaw('sum(amount)')
                ->whereColumn('order_id', 'orders.id')
                ->whereRaw('amount > 0')
                ->toSql();

            $orderAmount = OrderItemPrice::query()
                ->selectRaw('sum(qty * price)')
                ->whereHas('orderItem', function ($query) {
                    $query->whereColumn('order_id', '=', 'orders.id');
                })
                ->toSql();

            $query->whereRaw("(({$orderAmount}) - ({$paidAmount})) > 0")
                ->orWhereDoesntHave('payments');
        });
    }
}
