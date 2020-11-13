<?php

namespace App\Http\Responses\Report;

use App\Models\OrderItemPrice;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Contracts\Support\Responsable;

class DailySalesReportResponse implements Responsable
{
    public function toResponse($request)
    {
        if ($request->has('download')) {
            return $this->download($request);
        }

        return view('report.daily-sales', [
            'sales' => $this->sales($request)
        ]);
    }

    protected function sales($request)
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

    protected function download($request)
    {
        $data = collect($this->sales($request)->toArray());

        $data->push([
            'item_name' => 'Total',
            'today_sales' => $data->sum('today_sales'),
            'month_to_date' => $data->sum('month_to_date'),
        ]);

        $fileName = 'Daily_sales_' . now()->startOfMonth()->format('Y-m-d') . '_to_' . now()->format('Y-m-d').'.xlsx';

        return (new FastExcel($data))->download($fileName, function ($item) {
            return [
                'Description' => $item['item_name'],
                'Today' => $item['today_sales'] ? (int) $item['today_sales'] : '0',
                'Month to date' => (int) $item['month_to_date'],
            ];
        });
    }
}
