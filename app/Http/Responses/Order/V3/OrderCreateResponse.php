<?php

namespace App\Http\Responses\Order\V3;

use App\Models\Item;
use App\Models\Size;
use App\Models\Color;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Material;
use App\Models\Salesman;
use Illuminate\Contracts\Support\Responsable;

class OrderCreateResponse implements Responsable
{
    public function toResponse($request)
    {
        return view('order.v3.create', [
            'customers' => Customer::select('name', 'id')->orderBy('name')->get(),
            'salesmen' => Salesman::select('name', 'id')->orderBy('name')->get(),
            'materials' => Material::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'colors' => Color::orderBy('name')->get(),
            'sizes' => Size::select('name', 'id')->get(),
            'items' => Item::select('name', 'id', 'unit', 'category_id')->orderBy('name')->get(),
        ]);
    }
}
