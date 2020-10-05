<?php

namespace App\Http\Livewire\Customer;

use App\Models\Item;
use App\Models\Color;
use Livewire\Component;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Material;
use App\Models\CustomerItem;
use Illuminate\Support\Facades\DB;

class ManageProductCustomer extends Component
{
    public $customer;
    public $categories;
    public $materials;
    public $colors;
    public $items;
    public $customerItems = [];
    protected $itemsData = [
        'item_id' => '',
        'item_name' => '',
        'unit' => '',
        'price' => '',
        'type' => '',
        'material' => '',
        'color' => '',
        'printing' => '',
        'sablon' => false,
        'note' => '',
    ];

    public function mount($id)
    {
        $this->items = Item::select('name', 'id', 'unit')->get();
        $this->customer = Customer::findOrFail($id);
        $this->materials = Material::all();
        $this->categories = Category::all();
        $this->colors = Color::all();
        $this->customerItems[] = $this->itemsData;
        $this->getCustomerItems();
    }

    public function getCustomerItems()
    {
        $customerItems = CustomerItem::where('customer_id', $this->customer->id)
            ->with('item')
            ->get();
        if ($customerItems->count()) {
            $this->customerItems = [];
        }

        foreach ($customerItems as $customerItem) {
            $this->customerItems[] = [
                'item_id' => $customerItem->item_id,
                'item_name' => $customerItem->item->name,
                'unit' => $customerItem->item->unit,
                'price' => $customerItem->price,
                'type' => $customerItem->item->category_id,
                'material' => $customerItem->material_id,
                'color' => $customerItem->color_id,
                'sablon' => $customerItem->screen_printing,
                'note' => $customerItem->note,
            ];
        }
    }

    public function addCustomerItem()
    {
        $this->customerItems[] = $this->itemsData;
    }

    public function removeCustomerItem($index)
    {
        unset($this->customerItems[$index]);
        $this->customerItems = array_values($this->customerItems);
    }

    public function itemSelected($index)
    {
        $item = $this->items->first(fn ($item) => $item->id == $this->customerItems[$index]['item_id']);
        $this->customerItems[$index]['item_id'] = $item->id;
        $this->customerItems[$index]['item_name'] = $item->name;
        $this->customerItems[$index]['unit'] = $item->unit;
    }

    public function saveData()
    {
        $this->validate([
            'customerItems.*.item_id' => 'required|exists:items,id',
            'customerItems.*.price' => 'required|numeric',
            'customerItems.*.type' => 'required|exists:categories,id',
            'customerItems.*.material' => 'required|exists:materials,id',
            'customerItems.*.color' => 'required|exists:colors,id',
            'customerItems.*.sablon' => 'required|boolean',
            'customerItems.*.note' => '',
        ]);

        $this->saveCustomerProduct();
    }

    protected function saveCustomerProduct()
    {
        $data = collect($this->customerItems)->map(function ($item) {
            return [
                'customer_id' => $this->customer->id,
                'item_id' => $item['item_id'],
                'material_id' => $item['material'],
                'color_id' => $item['color'],
                'price' => $item['price'],
                'image' => '',
                'note' => $item['note'],
                'screen_printing' => $item['sablon'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        DB::transaction(function () use ($data) {
            CustomerItem::where('customer_id', $this->customer->id)->forceDelete();
            CustomerItem::insert($data->toArray());
        });
        $this->emit('dataSaved');
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.customer.manage-product-customer');
    }
}
