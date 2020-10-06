<?php

namespace App\Http\Livewire\Customer;

use App\Models\Item;
use App\Models\Color;
use Livewire\Component;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Material;
use App\Models\CustomerItem;
use App\Models\CustomerItemPrice;
use App\Models\Size;
use Illuminate\Support\Facades\DB;

class ManageProductCustomer extends Component
{
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
        'price' => [],
        'type' => '',
        'material' => '',
        'color' => '',
        'printing' => '',
        'sablon' => false,
        'note' => '',
    ];

    public function mount($id)
    {
        $this->items = Item::select('name', 'id', 'unit')->orderBy('name')->get();
        $this->customer = Customer::findOrFail($id);
        $this->materials = Material::orderBy('name')->get();
        $this->categories = Category::orderBy('name')->get();
        $this->colors = Color::orderBy('name')->get();
        $this->sizes = Size::orderBy('name')->get();
        $this->addSizesToCustomerItems();
        $this->customerItems[] = $this->itemsData;
        $this->getCustomerItems();
    }

    protected function addSizesToCustomerItems()
    {
        foreach ($this->sizes as $size) {
            $this->itemsData['price'][] = [
                'size_id' => $size->id,
                'value' => 0,
            ];
        }
    }

    public function getCustomerItems()
    {
        $customerItems = CustomerItem::where('customer_id', $this->customer->id)
            ->with('item', 'prices')
            ->get();

        if ($customerItems->count()) {
            $this->customerItems = [];
        }

        foreach ($customerItems as $i => $customerItem) {
            $this->customerItems[$i] = [
                'item_id' => $customerItem->item_id,
                'item_name' => $customerItem->item->name,
                'unit' => $customerItem->item->unit,
                'price' => [],
                'type' => $customerItem->item->category_id,
                'material' => $customerItem->material_id,
                'color' => $customerItem->color_id,
                'sablon' => $customerItem->screen_printing,
                'note' => $customerItem->note,
            ];

            foreach ($this->sizes as $size) {
                $priceData = $customerItem->prices->first(function ($price) use ($size) {
                    return $price->size_id == $size->id;
                });

                $this->customerItems[$i]['price'][] = [
                    'size_id' => $priceData ? $priceData->size_id : $size->id,
                    'value' => $priceData ? $priceData->price : 0,
                ];
            }
        }
    }

    public function addCustomerItem()
    {
        $this->addSizesToCustomerItems();
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
            'customerItems.*.item_id' => 'required|distinct|exists:items,id',
            'customerItems.*.price' => 'required|array',
            'customerItems.*.price.*.value' => 'required|numeric|min:0',
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
        DB::transaction(function () {
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

    protected function saveCustomerItemPrices($customerItem, $priceData)
    {
        $data = collect($priceData)->map(function ($item) use ($customerItem) {
            return [
                'customer_item_id' => $customerItem->id,
                'size_id' => $item['size_id'],
                'price' => $item['value'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        CustomerItemPrice::insert($data->toArray());
    }

    public function render()
    {
        return view('livewire.customer.manage-product-customer');
    }
}
