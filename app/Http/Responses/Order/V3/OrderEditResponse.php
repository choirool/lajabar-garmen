<?php

namespace App\Http\Responses\Order\V3;

use App\Models\Item;
use App\Models\Size;
use App\Models\Color;
use App\Models\Order;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Material;
use App\Models\Salesman;
use Illuminate\Contracts\Support\Responsable;

class OrderEditResponse implements Responsable
{
    protected $id;
    protected $order;

    public function __construct($id)
    {
        $this->id = $id;
        $this->order = $this->getOrder();
    }

    public function toResponse($request)
    {
        $version = $this->useVersion2() ? 'v2' : 'v3';

        return view('order.' . $version . '.update', [
            'customers' => $this->getCustomer(),
            'salesmen' => Salesman::select('name', 'id')->orderBy('name')->get(),
            'materials' => Material::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'colors' => Color::orderBy('name')->get(),
            'sizes' => Size::select('name', 'id')->get(),
            'items' => Item::select('name', 'id', 'unit', 'category_id', 'material_id')->orderBy('name')->get(),
            'order' => $this->order,
        ]);
    }

    protected function getCustomer()
    {
        return Customer::select('name', 'id')
            ->where('id', $this->order->customer_id)
            ->with(['products' => function ($query) {
                $query->with('item', 'prices');
            }])
            ->get();
    }

    protected function getOrder()
    {
        return Order::query()
            ->with(['orderItems' => fn ($query) => $query->with('item', 'prices')])
            ->with('dp')
            ->findOrFail($this->id);
    }

    protected function useVersion2()
    {
        $result = [];
        $this->order->orderItems->each(function ($orderItems) use (&$result) {
            $pricesList = [];
            $orderItems->prices->each(function ($prices) use (&$pricesList, &$result) {
                $pricesList[] = $prices->price;
            });

            // $result[] = $pricesList;
            $result[] = collect($pricesList)->unique()->count() > 1;
        });

        return in_array(true, $result);
    }
}
