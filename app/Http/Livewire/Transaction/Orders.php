<?php

namespace App\Http\Livewire\Transaction;

use App\Models\Item;
use App\Models\Size;
use App\Models\Color;
use Livewire\Component;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Material;
use App\Models\Salesman;
use App\Models\CustomerItem;

class Orders extends Component
{
    public $customers;
    public $salesmen;
    public $materials;
    public $categories;
    public $colors;
    public $sizes;
    public $customerItems;
    public $showTable = false;
    public $items;
    public $form = [
        'customer_id' => null,
        'salesman_id' => null,
        'order_lines' => []
    ];

    public function mount()
    {
        $this->customers = Customer::select('name', 'id')->orderBy('name')->get();
        $this->salesmen = Salesman::select('name', 'id')->orderBy('name')->get();
        $this->materials = Material::orderBy('name')->get();
        $this->categories = Category::orderBy('name')->get();
        $this->colors = Color::orderBy('name')->get();
        $this->sizes = Size::orderBy('name')->get();
        $this->items = Item::select('name', 'id', 'unit')->orderBy('name')->get();
        $this->initiateForm();
    }

    protected function initiateForm()
    {
        $this->form['date'] = now()->format('Y-m-d');
        $this->form['order_lines'][0] = [
            'item' => '',
            'unit' => '',
            'type' => '',
            'material' => '',
            'color' => '',
            'printing' => '',
            'note' => '',
        ];

        foreach ($this->sizes as $size) {
            $this->form['order_lines'][0]['price'][] = [
                'size_id' => $size->id,
                'qty' => 0,
                'price' => 0,
            ];
        }
    }

    public function itemSelected($i)
    {
        $item = $this->items->first(fn($item) => $item->id == $this->form['order_lines'][$i]['item']);
        $this->form['order_lines'][$i]['unit'] = $item->unit;
        $this->form['order_lines'][$i]['type'] = $item->category_id;
    }

    public function customerSelected()
    {
        $this->customerItems = $this->loadCustomerItems();
        $this->initiateFormFromCustomerItems();
        $this->showTable = true;
    }

    protected function initiateFormFromCustomerItems()
    {
        if ($this->customerItems->count()) {
            $this->form['order_lines'] = [];

            foreach ($this->customerItems as $i => $customerItem) {
                $this->form['order_lines'][$i] = [
                    'item' => $customerItem->item->id,
                    'unit' => $customerItem->item->unit,
                    'type' => $customerItem->item->category_id,
                    'material' => $customerItem->material_id,
                    'color' => $customerItem->color_id,
                    'printing' => false,
                    'note' => '',
                ];

                foreach ($this->sizes as $size) {
                    $priceData = $customerItem->prices->first(function ($item) use ($size) {
                        return $item->size_id == $size->id;
                    });

                    $this->form['order_lines'][$i]['price'][] = [
                        'size_id' => $size->id,
                        'qty' => 0,
                        'price' => $priceData ? $priceData->price : 0,
                    ];
                }
            }
        } else {
            $this->initiateForm();
        }
    }

    protected function loadCustomerItems()
    {
        return CustomerItem::query()
            ->where('customer_id', $this->form['customer_id'])
            ->with('item', 'prices')
            ->get();
    }

    public function addItem()
    {
        $i = count($this->form['order_lines']);
        $this->form['order_lines'][$i] = [
            'item' => '',
            'unit' => '',
            'type' => '',
            'material' => '',
            'color' => '',
            'printing' => '',
            'note' => '',
        ];

        foreach ($this->sizes as $size) {
            $this->form['order_lines'][$i]['price'][] = [
                'size_id' => $size->id,
                'qty' => 0,
                'price' => 0,
            ];
        }
    }

    public function deleteItem($i)
    {
        unset($this->form['order_lines'][$i]);
        $this->form['order_lines'] = array_values($this->form['order_lines']);
    }

    public function render()
    {
        return view('livewire.transaction.orders');
    }
}
