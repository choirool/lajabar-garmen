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

class UpdateOrder extends Component
{
    public $customers;
    public $salesmen;
    public $materials;
    public $categories;
    public $colors;
    public $sizes;
    public $customerItems;
    public $showTable = true;
    public $items;
    public $order;
    public $form = [];
    public $deletedOrderItems = [];

    public function mount($id)
    {
        $this->customers = Customer::select('name', 'id')->orderBy('name')->get();
        $this->salesmen = Salesman::select('name', 'id')->orderBy('name')->get();
        $this->materials = Material::orderBy('name')->get();
        $this->categories = Category::orderBy('name')->get();
        $this->colors = Color::orderBy('name')->get();
        $this->sizes = Size::all();
        $this->items = Item::select('name', 'id', 'unit')->orderBy('name')->get();
        $this->order = $this->getOrderData($id);
        $this->initiateForm();
    }

    protected function getOrderData($id)
    {
        return Order::query()
            ->with(['orderItems' => fn ($query) => $query->with('prices', 'item')])
            ->findOrFail($id);
    }

    protected function initiateForm()
    {
        $this->form = [
            'id' => $this->order->id,
            'customer_id' => $this->order->customer_id,
            'salesman_id' => $this->order->salesman_id,
            'date' => $this->order->invoice_date,
        ];

        foreach ($this->order->orderItems as $i => $orderItem) {
            $this->form['order_lines'][$i] = [
                'id' => $orderItem->id,
                'item' => $orderItem->item_id,
                'unit' => $orderItem->item->unit,
                'type' => $orderItem->item->category_id,
                'material' => $orderItem->material_id,
                'color' => $orderItem->color_id,
                'printing' => $orderItem->screen_printing,
                'note' => $orderItem->note,
            ];
            
            foreach ($this->sizes as $size) {
                $priceData = $orderItem->prices->first(fn ($price) => $price->size_id == $size->id);

                $this->form['order_lines'][$i]['price'][] = [
                    'id' => $priceData ? $priceData->id : null,
                    'size_id' => $size->id,
                    'qty' => $priceData ? $priceData->qty : 0,
                    'price' => $priceData? $priceData->price : 0,
                ];
            }
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
    }

    public function addItem()
    {
        $i = count($this->form['order_lines']);
        $this->form['order_lines'][$i] = [
            'id' => null,
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
                'id' => null,
                'size_id' => $size->id,
                'qty' => 0,
                'price' => 0,
            ];
        }
    }

    public function deleteItem($i)
    {
        if (isset($this->form['order_lines'][$i]['id'])) {
            $this->deletedOrderItems[] = $this->form['order_lines'][$i]['id'];
        }

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
                $this->storeOrder();

                $formData->each(function ($data) {
                    $orderItem = $this->storeOrderItem($data);

                    collect($data['price'])->each(function ($price) use ($orderItem) {
                        if ((int) $price['qty'] > 0 && (int) $price['price'] > 0) {
                            $this->storeOrderItemPrice($price, $orderItem);
                        }
                    });
                });

                $this->deleteOrderItems();
            });

            session()->flash('message', 'Data successfully updated.');
            return redirect()->route('transactions.orders');
        }
    }

    protected function storeOrder()
    {
        Order::where('id', $this->form['id'])->update([
            'customer_id' => $this->form['customer_id'],
            'invoice_date' => $this->form['date'],
            'salesman_id' => $this->form['salesman_id'],
        ]);
    }

    protected function storeOrderItem($data)
    {
        if ($data['id'] == null) {
            return OrderItem::create([
                'order_id' => $this->order->id,
                'item_id' => $data['item'],
                'material_id' => $data['material'],
                'color_id' => $data['color'],
                'image' => '',
                'note' => $data['note'],
                'screen_printing' => $data['printing'],
            ]);
        }

        OrderItem::where('id', $data['id'])->update([
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
        if ($price['id'] == null) {
            OrderItemPrice::create([
                'order_item_id' => $orderItem->id,
                'size_id' => $price['size_id'],
                'qty' => $price['qty'],
                'price' => $price['price'],
            ]);
        }

        OrderItemPrice::where('id', $price['id'])->update([
            'size_id' => $price['size_id'],
            'qty' => $price['qty'],
            'price' => $price['price'],
        ]);
    }

    public function deleteOrderItems()
    {
        if (count($this->deletedOrderItems)) {
            OrderItem::whereIn('id', $this->deletedOrderItems)->delete();
            OrderItemPrice::whereIn('order_item_id', $this->deletedOrderItems)->delete();
        }
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
        return view('livewire.transaction.update-order');
    }
}
