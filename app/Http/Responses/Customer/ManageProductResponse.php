<?php

namespace App\Http\Responses\Customer;

use App\Models\Item;
use App\Models\Color;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Material;
use App\Models\CustomerItem;
use Illuminate\Contracts\Support\Responsable;

class ManageProductResponse implements Responsable
{
    protected $customer;
    protected $customerProducts;

    public function __construct($id)
    {
        $this->getCustomer($id);
        $this->getProducts($id);
    }

    public function toResponse($request)
    {
        return view('customer.manage-product', [
            'customer' => $this->customer,
            'products' => $this->customerProducts,
            'items' => Item::all(),
            'categories' => Category::all(),
            'materials' => Material::all(),
            'colors' => Color::all(),
        ]);
    }

    protected function getCustomer($id)
    {
        $this->customer = Customer::query()
            // ->with(['products' => function ($query) {
            //     $query->with('prices', 'item')->paginate(3);
            // }])
            ->findOrFail($id);
    }

    protected function getProducts($id)
    {
        $this->customerProducts = CustomerItem::query()
            ->where('customer_id', $id)
            ->with('prices', 'item')
            ->paginate(50);
    }
}
