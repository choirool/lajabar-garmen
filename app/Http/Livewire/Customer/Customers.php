<?php

namespace App\Http\Livewire\Customer;

use Livewire\Component;
use App\Models\Customer;
use Livewire\WithPagination;

class Customers extends Component
{
    use WithPagination;

    public $search;
    public $confirming;

    protected $updatesQueryString = ['search'];

    public function mount()
    {
        if (!auth()->user()->isAbleTo('customer-list')) {
            abort(403);
        }

        $this->search = request()->query('search', $this->search);
    }

    public function toggleActive($id, $deleted = false)
    {
        $customer = Customer::where('id', $id);
        if ($deleted) {
            $customer->restore();
        } else {
            $customer->delete();
        }

        $this->resetConfirm();
    }

    public function confirmDelete($id)
    {
        $this->confirming = $id;
    }

    public function resetConfirm()
    {
        $this->confirming = '';
    }

    protected function getCustomers()
    {
        return Customer::query()
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('address', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            })
            ->withTrashed()
            ->paginate();
    }

    public function render()
    {
        return view('livewire.customer.customers', [
            'customers' => $this->getCustomers()
        ]);
    }
}
