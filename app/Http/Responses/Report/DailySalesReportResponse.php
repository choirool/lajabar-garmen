<?php

namespace App\Http\Responses\Report;

use App\Models\OrderItemPrice;
use Illuminate\Contracts\Support\Responsable;
use Jimmyjs\ReportGenerator\ReportMedia\PdfReport;
use Jimmyjs\ReportGenerator\ReportMedia\ExcelReport;

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

    public function download($request)
    {
        $title = 'Daily sales';

        $meta = [
            'Periode' => now()->startOfMonth()->format('Y-m-d') . ' - ' . now()->format('Y-m-d'),
            'Generated at' => now(),
        ];

        $queryBuilder = OrderItemPrice::query()
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
            ->orderBy('item_name');

        $columns = [
            'Name' => fn ($item) => ucfirst($item->item_name),
            'Today' => fn ($item) => $item->today_sales ? $item->today_sales : 0,
            'Month to date' => 'month_to_date',
        ];

        $fileName = 'Daily_sales_' . now()->startOfMonth()->format('Y-m-d') . '_to_' . now()->format('Y-m-d');
        $exporter = 'ExcelReport';
        if ($request->download == 'pdf') {
            $exporter = 'PdfReport';
        }

        return $exporter::of($title, $meta, $queryBuilder, $columns)
            ->showTotal([
                'Today' => 'point',
                'Month to date' => 'point',
            ])
            ->editColumns(['Today', 'Month to date'], [
                'class' => 'right'
            ])
            ->download($fileName);
    }
}
