<?php
namespace App\Http\Responses\Production;

use App\Models\Order;
use Illuminate\Contracts\Support\Responsable;

class ProductionStoreResponse implements Responsable
{
    protected  $order;

    public function __construct($orderId)
    {
        $this->order = Order::findOrFail($orderId);
    }

    public function toResponse($request)
    {
        return $request->all();
    }
}
