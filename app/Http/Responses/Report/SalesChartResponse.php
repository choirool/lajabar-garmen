<?php

namespace App\Http\Responses\Report;

use App\Models\Item;
use App\Models\OrderItem;
use App\Models\OrderItemPrice;
use Illuminate\Contracts\Support\Responsable;

class SalesChartResponse implements Responsable
{
    public function toResponse($request)
    {
        return view('report.sales-chart', [
            'item_sales' => $this->itemSales(),
            'daily_sales' => $this->dailySales($request)
        ]);
    }

    protected function itemSales()
    {
        return OrderItemPrice::query()
            ->selectRaw('sum(qty) as count_item')
            ->selectRaw('items.name as item_name')
            ->join('order_items', 'order_items.id', '=', 'order_item_prices.order_item_id')
            ->join('items', 'items.id', '=', 'order_items.item_id')
            ->whereBetween('order_items.created_at', [now()->subMonth(), now()])
            ->groupBy('order_items.item_id')
            ->orderBy('count_item', 'desc')
            ->limit(5)
            ->get();
    }

    protected function dailySales($request)
    {
        return OrderItemPrice::query()
            ->selectRaw('sum(qty * price) as month_to_date')
            ->selectRaw('items.name as item_name')
            ->addSelect([
                'today_sales' => OrderItemPrice::query()
                    ->selectRaw('sum(qty * price)')
                    ->where('order_items.created_at', now())
                    ->join('order_items', 'order_items.id', '=', 'order_item_prices.order_item_id')
                    ->join('items', 'items.id', '=', 'order_items.item_id')
                    ->whereColumn('items.name', '=', 'item_name')
            ])
            ->join('order_items', 'order_items.id', '=', 'order_item_prices.order_item_id')
            ->join('items', 'items.id', '=', 'order_items.item_id')
            ->whereBetween('order_items.created_at', [now()->startOfMonth()->format('Y-m-d'), now()->format('Y-m-d')])
            ->groupBy('order_items.item_id')
            ->orderBy('item_name')
            ->get();
    }
}
