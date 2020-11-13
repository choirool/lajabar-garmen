<?php

namespace App\Http\Livewire\Transaction;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class Invoices extends Component
{
    use WithPagination;

    public $search;
    public $startDate = null;
    public $endDate = null;
    public $confirming;
    public $unpaid = false;

    protected $updatesQueryString = ['search'];

    public function mount()
    {
        if (!auth()->user()->isAbleTo('view-invoices')) {
            abort(403);
        }

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
            ->orderAmount()
            ->paidAmount()
            ->where(function ($query) {
                $query->where('invoice_code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function (Builder $query) {
                        $query->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->unpaid, fn ($query) => $query->filterByUnpaid());

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
        return view('livewire.transaction.invoices', [
            'orders' => $this->getOrders()
        ]);
    }
}
