<?php

namespace App\Http\Responses\Order\Prebilling;

use App\Models\Order;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\Support\Responsable;
use App\Http\Responses\Order\Prebilling\Excel\PrebillingExport;

class PrebillingExportToExcelResponse implements Responsable
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function toResponse($request)
    {
        $order = $this->getOrder();
        $fileName = 'Prebilling_' . $order->invoice_code . '.xlsx';

        return Excel::download(new PrebillingExport($order), $fileName);
    }

    protected function getOrder()
    {
        return Order::query()
            ->orderAmount()
            ->paidAmount()
            ->with('customer', 'salesman', 'dp', 'payments')
            ->with(['orderItems' => function ($query) {
                $query->with('item.category', 'prices', 'color', 'material');
            }])
            ->findOrFail($this->id);
    }
}
