<?php

namespace App\Http\Livewire\Customer;

use App\Models\Customer;
use Livewire\Component;

class UpdateCustomer extends Component
{
    public $customer;
    public $name = '';
    public $address = '';
    public $phone = '';
    public $email = '';
    public $country = '';
    public $invoiceColor = '';

    public function mount($id)
    {
        if (!auth()->user()->isAbleTo('customer-update')) {
            abort(403);
        }

        $customer = Customer::findOrFail($id);
        $this->customer = $customer;
        $this->name = $customer->name;
        $this->address = $customer->address;
        $this->phone = $customer->phone;
        $this->email = $customer->email;
        $this->country = $customer->country;
        $this->invoiceColor = $customer->invoice_color;
    }

    public function saveCustomer()
    {
        $this->validate([
            'name' => 'required|min:2|unique:customers,name,' . $this->customer->id . ',id',
            'address' => 'required|min:2',
            'phone' => 'required|min:2',
            'email' => 'required|email|unique:customers,email,'. $this->customer->id . ',id',
            'country' => 'required',
            'invoiceColor' => 'required',
        ]);

        $this->customer->name = $this->name;
        $this->customer->address = $this->address;
        $this->customer->phone = $this->phone;
        $this->customer->country = $this->country;
        $this->customer->email = $this->email;
        $this->customer->invoice_color = $this->invoiceColor;
        $this->customer->save();

        session()->flash('message', 'Customer successfully updated.');

        return redirect()->route('master-data.customers');
    }

    public function render()
    {
        return view('livewire.customer.update-customer');
    }
}
