<?php

namespace App\Http\Responses\Order\V2;

use App\Models\Item;
use App\Models\Size;
use App\Models\Color;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Category;
use App\Models\Material;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use App\Models\CustomerItem;
use App\Models\OrderItemPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Support\Responsable;

class OrderStoreResponse implements Responsable
{
    protected $itemData;
    protected $size;
    protected $items;
    protected $customerItems;
    protected $categories;
    protected $materials;
    protected $colors;

    public function __construct()
    {
        $this->getCustomerItems();
        $this->sizes = Size::all();
        $this->categories = Category::orderBy('name')->get();
        $this->materials = Material::orderBy('name')->get();
        $this->colors = Color::orderBy('name')->get();
        $this->items = Item::select('name', 'id', 'unit')
            ->whereHas('customerItems.customer', function ($query) {
                $query->where('id', request('customer_id'));
            })
            ->orderBy('name')
            ->get();
    }

    public function toResponse($request)
    {
        $this->getDataItems($request);
        $validator = Validator::make($this->itemData, [
            'order_lines.*.item' => 'required|in:' . $this->items->implode('id', ','),
            // 'order_lines.*.item_combination' => 'required|distinct|in:' . $this->customerItems,
            'order_lines.*.type' => 'required|in:' . $this->categories->implode('id', ','),
            'order_lines.*.material' => 'required|in:' . $this->materials->implode('id', ','),
            'order_lines.*.color' => 'required|in:' . $this->colors->implode('id', ','),
            'order_lines.*.printing' => 'required|boolean',
            'order_lines.*.note' => '',
            // 'order_lines.*.image' => 'sometimes|image',
            'order_lines.*.price' => 'required|array',
            'order_lines.*.price.*.size_id' => 'required|in:' . $this->sizes->implode('id', ','),
            'order_lines.*.price.*.qty' => 'required|numeric|min:0',
            'order_lines.*.price.*.price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $this->saveData($request);
        session()->flash('message', 'Data successfully created.');

        return response()->json([
            'status' => true,
            'redirect' => route('transactions.orders'),
        ]);
    }

    protected function getDataItems($request)
    {
        $data = collect($request->input('order_lines.*.data'))->map(function ($item, $key) {
            $itemData = json_decode($item, true);

            $data = [
                'item' => $itemData['order_lines.' . $key . '.item_id'],
                'unit' => $itemData['order_lines.' . $key . '.unit'],
                'item_combination' => $itemData['order_lines.' . $key . '.item_combination'],
                'type' => $itemData['order_lines.' . $key . '.type'],
                'material' => $itemData['order_lines.' . $key . '.material'],
                'color' => $itemData['order_lines.' . $key . '.color'],
                'printing' => $itemData['order_lines.' . $key . '.printing'],
                'note' => $itemData['order_lines.' . $key . '.note'],
                'special_note' => $itemData['order_lines.' . $key . '.special_note'],
                'price' => [],
            ];

            foreach ($this->sizes as $k => $size) {
                $data['price'][$k] = [
                    'size_id' => $itemData['order_lines.' . $key . '.price.' . $k . '.size_id'],
                    'qty' => $itemData['order_lines.' . $key . '.price.' . $k . '.qty'],
                    'price' => $itemData['order_lines.' . $key . '.price.' . $k . '.price'],
                    'special_price' => $itemData['order_lines.' . $key . '.price.' . $k . '.special_price'],
                ];
            }

            return $data;
        });

        $this->itemData = [
            'order_lines' => $data->toArray()
        ];
    }

    protected function getCustomerItems()
    {
        $this->customerItems = CustomerItem::query()
            ->where('customer_id', request('customer_id'))
            ->get()
            ->map(function ($item) {
                return [
                    'item' => $item->item_id . '_' . $item->material_id . '_' . $item->color_id
                ];
            })->implode('item', ',');
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
            'invoice_name' => $request->invoice_name,
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

    protected function filterData()
    {
        return collect($this->itemData['order_lines'])->filter(function ($orderLine) {
            return collect($orderLine['price'])
                ->filter(fn ($price) => (int) $price['price'] > 0 && (int) $price['price'] > 0)
                ->count();
        });
    }
}
