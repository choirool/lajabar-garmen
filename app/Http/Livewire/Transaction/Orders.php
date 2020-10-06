<?php

namespace App\Http\Livewire\Transaction;

use App\Models\Customer;
use Livewire\Component;

class Orders extends Component
{
    public $customers;
    public $customer;
    
    public function mount()
    {
        $this->customers = Customer::select('name', 'id')->orderBy('name')->get();
    }

    public function customerSelected()
    {
        dd($this->customer);
    }

    public function render()
    {
        return view('livewire.transaction.orders');
    }
}
