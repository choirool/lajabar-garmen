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
        return Order::with('dp')->findOrFail($orderId);
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

                $this->deleteOrderItems($request);
                $this->proccessDp($request);
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
        $imageName = '';
        if (isset($data['image'])) {
            $upload = $this->storeImage($data['image'], $this->order->id);
            $imageName = $upload['name'];
        }

        $orderItemData = [
            'item_id' => $data['item'],
            'material_id' => $data['material'],
            'color_id' => $data['color'],
            'note' => isset($data['note']) ? $data['note'] : '',
            'special_note' => isset($data['special_note']) ? $data['special_note'] : '',
            'screen_printing' => $data['printing'],
        ];

        if ($data['id'] == null) {
            $orderItemData['order_id'] = $this->order->id;
            $orderItemData['image'] = $imageName;
            return OrderItem::create($orderItemData);
        }

        if ($imageName !== '') {
            $orderItemData['image'] = $imageName;
        }
        
        OrderItem::where('id', $data['id'])->update($orderItemData);
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
        if ($price['id'] == null) {
            OrderItemPrice::create([
                'order_item_id' => $orderItem->id,
                'size_id' => $price['size_id'],
                'qty' => $price['qty'],
                'price' => $price['price'],
                'special_price' => $price['special_price'],
            ]);
        }

        OrderItemPrice::where('id', $price['id'])->update([
            'size_id' => $price['size_id'],
            'qty' => $price['qty'],
            'price' => $price['price'],
            'special_price' => $price['special_price'],
        ]);
    }

    public function deleteOrderItems($request)
    {
        if ($request->has('deleted_items') && count($request->deleted_items)) {
            OrderItem::whereIn('id', $request->deleted_items)->delete();
            OrderItemPrice::whereIn('order_item_id', $request->deleted_items)->delete();
        }
    }

    protected function proccessDp($request)
    {
        if ($request->dp['has_dp']) {
            $dp = new Payment;
            if ($this->order->dp) {
                $dp = $this->order->dp;
            }

            $dp->order_id = $this->order->id;
            $dp->payment_date = $request->dp['date'];
            $dp->payment_method = Str::of($request->dp['payment_method'])->replace('_', ' ');
            $dp->payment_type = 'dp';
            $dp->amount = $request->dp['amount'];
            $dp->meta = $request->dp['meta'];
            $dp->save();
        } else {
            if ($this->order->dp) {
                $this->order->dp->delete();
            }
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
