<?php

namespace App\Http\Responses\Order\V2;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Support\Responsable;

class OrderStoreResponse implements Responsable
{
    public function toResponse($request)
    {
        $this->saveData($request);
        session()->flash('message', 'Data successfully created.');

        return response()->json([
            'status' => true,
            'redirect' => route('transactions.orders'),
        ]);
    }

    public function saveData($request)
    {
        if ($formData = $this->filterData($request)) {
            DB::transaction(function () use ($formData, $request) {
                $order = $this->storeOrder($request);

                $formData->each(function ($data) use ($order) {
                    $orderItem = $this->storeOrderItem($data, $order);

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
        return Order::create([
            'invoice_code' => generate_invoice_code(),
            'customer_id' => $request->customer_id,
            'invoice_date' => $request->date,
            'salesman_id' => $request->salesman_id,
        ]);
    }

    protected function storeOrderItem($data, $order)
    {
        $imageName = '';
        if ($data['image']) {
            $upload = $this->storeImage($data['image'], $order->id);
            $imageName = $upload['name'];
        }
        return OrderItem::create([
            'order_id' => $order->id,
            'item_id' => $data['item'],
            'material_id' => $data['material'],
            'color_id' => $data['color'],
            'image' => $imageName,
            'note' => isset($data['note']) ? $data['note'] : '',
            'screen_printing' => $data['printing'],
        ]);
    }

    protected function storeImage($image, $orderId)
    {
        $fileName = $orderId . '-' . time() . '.' . $image->extension();
        $path = Storage::putFileAs(
            'orders',
            $image,
            $fileName
        );

        return [
            'name' => $fileName,
            'path' => $path,
        ];
    }

    protected function storeOrderItemPrice($price, $orderItem)
    {
        OrderItemPrice::create([
            'order_item_id' => $orderItem->id,
            'size_id' => $price['size_id'],
            'qty' => $price['qty'],
            'price' => $price['price'],
        ]);
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
