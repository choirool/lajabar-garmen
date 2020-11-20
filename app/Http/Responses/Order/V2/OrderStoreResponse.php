<?php

namespace App\Http\Responses\Order\V2;

use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderItem;
use Illuminate\Support\Str;
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

                if ($request->dp['has_dp']) {
                    $this->createDp($order, $request);
                }
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
        $imageName = isset($data['image']) ? $data['image'] : '';
        if (isset($data['image']) && get_class($data['image']) == 'Illuminate\Http\UploadedFile') {
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
            'special_note' => isset($data['special_note']) ? $data['special_note'] : '',
            'screen_printing' => $data['printing'],
        ]);
    }

    protected function storeImage($image, $orderId)
    {
        $fileName = $orderId . '-' . time() . Str::random(9) . '.' . $image->extension();
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
            'special_price' => $price['special_price'],
        ]);
    }

    protected function createDp($order, $request)
    {
        Payment::create([
            'order_id' => $order->id,
            'payment_date' => $request->dp['date'],
            'payment_method' => Str::of($request->dp['payment_method'])->replace('_', ' '),
            'payment_type' => 'dp',
            'amount' => $request->dp['amount'],
            'meta' => [],
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
