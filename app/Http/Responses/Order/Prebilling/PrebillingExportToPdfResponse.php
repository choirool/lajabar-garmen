<?php

namespace App\Http\Responses\Order\Prebilling;

use App\Models\Order;
use App\Models\Size;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Contracts\Support\Responsable;

class PrebillingExportToPdfResponse implements Responsable
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function toResponse($request)
    {
        $order = $this->getOrder();
        $fileName = 'Prebilling_' . $order->invoice_code . '.pdf';

        $pdf = PDF::loadView('pdf.prebilling', [
            'order' => $order,
            'sizes' => Size::all(),
            'request' => $request,
        ]);
        return $pdf->stream();
    }

    protected function getOrder()
    {
        return Order::query()
            ->orderAmount()
            ->paidAmount()
            ->orderTo()
            ->with('customer', 'salesman', 'dp', 'payments')
            ->with(['orderItems' => function ($query) {
                $query->with('item.category', 'prices', 'color', 'material');
            }])
            ->findOrFail($this->id);
    }
}
