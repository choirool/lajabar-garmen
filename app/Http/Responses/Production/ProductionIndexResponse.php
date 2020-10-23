<?php

namespace App\Http\Responses\Production;

use App\Models\Order;
use App\Models\Size;
use Illuminate\Contracts\Support\Responsable;

class ProductionIndexResponse implements Responsable
{
    protected $order;

    public function __construct($orderId)
    {
        $this->order = $this->getOrder($orderId);
    }

    protected function getOrder($orderId)
    {
        return Order::query()
            ->with(['orderItems' => function ($query) {
                $query->with('prices', 'item.category', 'material', 'color');
            }])
            ->with('customer', 'salesman')
            ->findOrFail($orderId);
    }

    public function toResponse($request)
    {
        return view('production.index', [
            'order' => $this->order,
            'sizes' => Size::all()
        ]);
    }
}
