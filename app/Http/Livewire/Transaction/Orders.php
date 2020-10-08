<?php

namespace App\Http\Livewire\Transaction;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class Orders extends Component
{
    use WithPagination;

    public $search;
    public $startDate = null;
    public $endDate = null;
    public $confirming;

    protected $updatesQueryString = ['search'];

    public function mount()
    {
        $this->search = request()->query('search', $this->search);
    }

    public function delete($id)
    {
        Order::where('id', $id)->delete();
    }

    public function confirmDelete($id)
    {
        $this->confirming = $id;
    }

    public function resetConfirm()
    {
        $this->confirming = '';
    }

    public function searchData()
    {
        $this->getOrders();
    }

    protected function getOrders()
    {
        $order = Order::query()
            ->where(function ($query) {
                $query->where('invoice_code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function (Builder $query) {
                        $query->where('name', 'like', '%' . $this->search . '%');
                    });
            });

        if ($this->startDate && $this->endDate) {
            $order->whereBetween('invoice_date', [$this->startDate, $this->endDate]);
        } else {
            if ($this->startDate) {
                $order->where('invoice_date', $this->startDate);
            }
        }

        return $order->with('customer')->paginate();
    }

    public function render()
    {
        return view('livewire.transaction.orders', [
            'orders' => $this->getOrders()
        ]);
    }
}
