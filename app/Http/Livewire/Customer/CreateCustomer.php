<?php

namespace App\Http\Livewire\Customer;

use App\Models\Customer;
use Livewire\Component;

class CreateCustomer extends Component
{
    public $name;
    public $address;
    public $phone;
    public $email;
    public $country;

    public function saveCustomer()
    {
        $this->validate([
            'name' => 'required|min:2|unique:customers',
            'address' => 'required|min:2',
            'phone' => 'required|min:2',
            'email' => 'required|email|unique:customers',
            'country' => 'required',
        ]);

        Customer::create([
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'country' => $this->country,
            'email' => $this->email,
        ]);
        
        session()->flash('message', 'Customer successfully created.');

        return redirect()->route('master-data.customers');
    }

    public function render()
    {
        return view('livewire.customer.create-customer');
    }
}
