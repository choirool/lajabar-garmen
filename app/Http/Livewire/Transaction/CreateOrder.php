<?php

namespace App\Http\Livewire\Transaction;

use App\Models\Item;
use App\Models\Size;
use App\Models\Color;
use App\Models\Order;
use Livewire\Component;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Material;
use App\Models\Salesman;
use App\Models\OrderItem;
use App\Models\CustomerItem;
use App\Models\OrderItemPrice;
use Illuminate\Support\Facades\DB;

class CreateOrder extends Component
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
        $this->sizes = Size::all();
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
            'printing' => false,
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
        $item = $this->items->first(fn ($item) => $item->id == $this->form['order_lines'][$i]['item']);
        $this->form['order_lines'][$i]['unit'] = $item->unit;
        $this->form['order_lines'][$i]['type'] = $item->category_id;
    }

    public function customerSelected()
    {
        $this->form['order_lines'] = [];
        $this->initiateForm();
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
            'printing' => false,
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

    public function saveData()
    {
        $this->validate([
            'form.customer_id' => 'required|in:' . $this->customers->implode('id', ','),
            'form.salesman_id' => 'required|in:' . $this->salesmen->implode('id', ','),
            'form.date' => 'required|date|date_format:Y-m-d|before_or_equal:today',
            'form.order_lines' => 'required|array',
            'form.order_lines.*.item' => 'required|distinct|in:' . $this->items->implode('id', ','),
            'form.order_lines.*.type' => 'required|in:' . $this->categories->implode('id', ','),
            'form.order_lines.*.material' => 'required|in:' . $this->materials->implode('id', ','),
            'form.order_lines.*.color' => 'required|in:' . $this->colors->implode('id', ','),
            'form.order_lines.*.printing' => 'required|boolean',
            'form.order_lines.*.note' => '',
            'form.order_lines.*.price' => 'required|array',
            'form.order_lines.*.price.*.size_id' => 'required|in:' . $this->sizes->implode('id', ','),
            'form.order_lines.*.price.*.qty' => 'required|numeric|min:0',
            'form.order_lines.*.price.*.price' => 'required|numeric|min:0',
        ]);

        if ($formData = $this->filterData()) {
            DB::transaction(function () use ($formData) {
                $order = $this->storeOrder();

                $formData->each(function ($data) use ($order) {
                    $orderItem = $this->storeOrderItem($data, $order);

                    collect($data['price'])->each(function ($price) use ($orderItem) {
                        if ((int) $price['qty'] > 0 && (int) $price['price'] > 0) {
                            $this->storeOrderItemPrice($price, $orderItem);
                        }
                    });
                });
            });

            session()->flash('message', 'Data successfully created.');
            return redirect()->route('transactions.orders');
        }
    }

    protected function storeOrder()
    {
        return Order::create([
            'invoice_code' => generate_invoice_code(),
            'customer_id' => $this->form['customer_id'],
            'invoice_date' => $this->form['date'],
            'salesman_id' => $this->form['salesman_id'],
        ]);
    }

    protected function storeOrderItem($data, $order)
    {
        return OrderItem::create([
            'order_id' => $order->id,
            'item_id' => $data['item'],
            'material_id' => $data['material'],
            'color_id' => $data['color'],
            'image' => '',
            'note' => $data['note'],
            'screen_printing' => $data['printing'],
        ]);
    }

    protected function storeOrderItemPrice($price, $orderItem)
    {
        OrderItemPrice::create([
            'order_item_id' => $orderItem->id,
            'size_id' => $price['size_id'],
            'qty' => $price['qty'],
            'price' => $price['price'],
        ]);
    }

    protected function filterData()
    {
        return collect($this->form['order_lines'])->filter(function ($orderLine) {
            return collect($orderLine['price'])
                ->filter(fn ($price) => (int) $price['price'] > 0 && (int) $price['price'] > 0)
                ->count();
        });
    }

    public function render()
    {
        return view('livewire.transaction.create-order');
    }
}
