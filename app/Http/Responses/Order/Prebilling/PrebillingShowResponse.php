<?php

namespace App\Http\Responses\Order\Prebilling;

use App\Models\Size;
use App\Models\Order;
use Illuminate\Contracts\Support\Responsable;

class PrebillingShowResponse implements Responsable
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function toResponse($request)
    {
        return view('order.prebilling', [
            'order' => $this->getOrder(),
            'sizes' => Size::all(),
        ]);
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
