<?php

namespace App\Http\Responses\Order\V2;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Responsable;

class OrderUpdateResponse implements Responsable
{
    protected $order;

    public function toResponse($request)
    {
        $this->order = $this->getOrder($request->id);
        $this->saveData($request);
        session()->flash('message', 'Data successfully updated.');

        return response()->json([
            'status' => true,
            'redirect' => route('transactions.orders'),
        ]);
    }

    protected function getOrder($orderId)
    {
        return Order::findOrFail($orderId);
    }

    public function saveData($request)
    {
        if ($formData = $this->filterData($request)) {
            DB::transaction(function () use ($formData, $request) {
                $this->storeOrder($request);

                $formData->each(function ($data) {
                    $orderItem = $this->storeOrderItem($data);

                    collect($data['price'])->each(function ($price) use ($orderItem) {
                        if ((int) $price['qty'] > 0 && (int) $price['price'] > 0) {
                            $this->storeOrderItemPrice($price, $orderItem);
                        }
                    });
                });
            });
        }
    }

    protected function storeOrder($request)
    {
        $this->order->update([
            'customer_id' => $request->customer_id,
            'invoice_date' => $request->date,
            'salesman_id' => $request->salesman_id,
        ]);
    }

    protected function storeOrderItem($data)
    {
        if ($data['id'] == null) {
            return OrderItem::create([
                'order_id' => $this->order->id,
                'item_id' => $data['item'],
                'material_id' => $data['material'],
                'color_id' => $data['color'],
                'image' => '',
                'note' => isset($data['note']) ? $data['note'] : '',
                'screen_printing' => $data['printing'],
            ]);
        }

        OrderItem::where('id', $data['id'])->update([
            'item_id' => $data['item'],
            'material_id' => $data['material'],
            'color_id' => $data['color'],
            'image' => '',
            'note' => isset($data['note']) ? $data['note'] : '',
            'screen_printing' => $data['printing'],
        ]);
    }

    protected function storeOrderItemPrice($price, $orderItem)
    {
        if ($price['id'] == null) {
            OrderItemPrice::create([
                'order_item_id' => $orderItem->id,
                'size_id' => $price['size_id'],
                'qty' => $price['qty'],
                'price' => $price['price'],
            ]);
        }

        OrderItemPrice::where('id', $price['id'])->update([
            'size_id' => $price['size_id'],
            'qty' => $price['qty'],
            'price' => $price['price'],
        ]);
    }

    public function deleteOrderItems($request)
    {
        if (count($request->deleted_items)) {
            OrderItem::whereIn('id', $request->deleted_items)->delete();
            OrderItemPrice::whereIn('order_item_id', $request->deleted_items)->delete();
        }
    }
    
    protected function filterData($request)
    {
        return collect($request['order_lines'])->filter(function ($orderLine) {
            return collect($orderLine['price'])
                ->filter(fn ($price) => (int) $price['price'] > 0 && (int) $price['price'] > 0)
                ->count();
        });
    }
}
