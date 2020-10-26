<?php

namespace App\Http\Responses\Customer;

use App\Models\Category;
use App\Models\Color;
use App\Models\Item;
use App\Models\Customer;
use App\Models\Material;
use Illuminate\Contracts\Support\Responsable;

class ManageProductResponse implements Responsable
{
    protected $customer;

    public function __construct($id)
    {
        $this->getCustomer($id);
    }

    public function toResponse($request)
    {
        return view('customer.manage-product', [
            'customer' => $this->customer,
            'items' => Item::all(),
            'categories' => Category::all(),
            'materials' => Material::all(),
            'colors' => Color::all(),
        ]);
    }

    protected function getCustomer($id)
    {
        $this->customer = Customer::query()
            ->with(['products' => function ($query) {
                $query->with('prices', 'item');
            }])
            ->findOrFail($id);
    }
}
