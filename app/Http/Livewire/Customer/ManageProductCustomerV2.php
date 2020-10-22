<?php

namespace App\Http\Livewire\Customer;

use App\Models\Item;
use App\Models\Size;
use App\Models\Color;
use Livewire\Component;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Material;
use App\Models\CustomerItem;
use App\Models\CustomerItemPrice;
use Illuminate\Support\Facades\DB;

class ManageProductCustomerV2 extends Component
{
    protected $currentCustomerItems;
    protected $version = 'v2';
    public $customer;
    public $categories;
    public $materials;
    public $colors;
    public $items;
    public $sizes;
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
        if ($this->useVersion1($id)) {
            $this->version = 'v1';
        }

        $this->items = Item::select('name', 'id', 'unit')->orderBy('name')->get();
        $this->customer = Customer::findOrFail($id);
        $this->materials = Material::orderBy('name')->get();
        $this->categories = Category::orderBy('name')->get();
        $this->colors = Color::orderBy('name')->get();
        $this->customerItems[] = $this->itemsData;
        $this->getCustomerItems();
    }

    protected function useVersion1($id)
    {
        $this->currentCustomerItems = CustomerItem::where('customer_id', $id)
            ->with('item', 'prices')
            ->get();

        $result = [];
        $this->currentCustomerItems->each(function ($customerItem) use (&$result) {
            $pricesList = [];
            $customerItem->prices->each(function ($prices) use (&$pricesList) {
                $pricesList[] = $prices->price;
            });

            $result[] = collect($pricesList)->unique()->count() > 1;
        });

        return in_array(true, $result);
    }

    public function getCustomerItems()
    {
        if ($this->currentCustomerItems->count()) {
            $this->customerItems = [];

            foreach ($this->currentCustomerItems as $i => $customerItem) {
                $this->customerItems[$i] = [
                    'item_id' => $customerItem->item_id,
                    'item_name' => $customerItem->item->name,
                    'unit' => $customerItem->item->unit,
                    'price' => $customerItem->prices->first()->price,
                    'type' => $customerItem->item->category_id,
                    'material' => $customerItem->material_id,
                    'color' => $customerItem->color_id,
                    'sablon' => $customerItem->screen_printing,
                    'note' => $customerItem->note,
                ];
            }
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
        if ($this->customerItems[$index]['item_id'] !== 'null') {
            $item = $this->items->first(fn ($item) => $item->id == $this->customerItems[$index]['item_id']);
            $this->customerItems[$index]['item_id'] = $item->id;
            $this->customerItems[$index]['item_name'] = $item->name;
            $this->customerItems[$index]['unit'] = $item->unit;
        } else {
            $this->customerItems[$index]['item_id'] = '';
            $this->customerItems[$index]['item_name'] = '';
            $this->customerItems[$index]['unit'] = '';
        }
    }

    public function saveData()
    {
        $this->validate([
            'customerItems.*.item_id' => 'required|distinct|in:' . $this->items->implode('id', ','),
            'customerItems.*.price' => 'required|numeric|min:0',
            'customerItems.*.type' => 'required|in:' . $this->categories->implode('id', ','),
            'customerItems.*.material' => 'required|in:' . $this->materials->implode('id', ','),
            'customerItems.*.color' => 'required|in:' . $this->colors->implode('id', ','),
            'customerItems.*.sablon' => 'required|boolean',
            'customerItems.*.note' => '',
        ]);

        $this->saveCustomerProduct();
    }

    protected function saveCustomerProduct()
    {
        DB::transaction(function () {
            $this->sizes = Size::orderBy('name')->get();
            CustomerItemPrice::whereHas('customerItem', function ($query) {
                $query->where('customer_id', $this->customer->id);
            })->forceDelete();

            CustomerItem::where('customer_id', $this->customer->id)->forceDelete();
            $this->saveCustomerItem();
        });

        $this->emit('dataSaved');
        $this->resetErrorBag();
    }

    protected function saveCustomerItem()
    {
        foreach ($this->customerItems as $item) {
            $customerItem = CustomerItem::create([
                'customer_id' => $this->customer->id,
                'item_id' => $item['item_id'],
                'material_id' => $item['material'],
                'color_id' => $item['color'],
                'image' => '',
                'note' => $item['note'],
                'screen_printing' => $item['sablon'],
            ]);

            $this->saveCustomerItemPrices($customerItem, $item['price']);
        }
    }

    protected function saveCustomerItemPrices($customerItem, $price)
    {
        $priceData = [];
        foreach ($this->sizes as $size) {
            $this->itemsData['prices'][] = [
                'customer_item_id' => $customerItem->id,
                'size_id' => $size->id,
                'price' => $price,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        CustomerItemPrice::insert($priceData);
    }

    public function render()
    {
        if ($this->version == 'v1') {
            return view('livewire.customer.manage-product-customer');
        }

        return view('livewire.customer.manage-product-customer-v2');
    }
}
