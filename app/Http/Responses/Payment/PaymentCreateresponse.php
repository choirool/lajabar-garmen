<?php
namespace App\Http\Responses\Payment;

use App\Models\Size;
use App\Models\Order;
use Illuminate\Contracts\Support\Responsable;

class PaymentCreateresponse implements Responsable
{
    protected $order;

    public function __construct($orderId)
    {
        $this->order = $this->getOrder($orderId);
    }
    
    public function toResponse($request)
    {
        return view('payment.create', [
            'order' => $this->order,
            'sizes' => Size::all(),
        ]);
    }

    protected function getOrder($orderId)
    {
        return Order::query()
            ->orderAmount()
            ->paidAmount()
            ->whereId($orderId)
            ->with(['orderItems' => fn ($query) => $query->with('item.category', 'prices')])
            ->with('payments')
            ->first();
    }
}
