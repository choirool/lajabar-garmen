<?php

namespace App\Http\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Laravel\Fortify\Rules\Password;
use Illuminate\Support\Facades\Hash;

class CreateUsers extends Component
{
    public $name = '';
    public $username = '';
    public $email = '';
    public $password = '';

    public function saveUser()
    {
        $this->validate([
            'name' => 'required|min:6|unique:users',
            'username' => 'required|min:6|unique:users',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'string', 'min:6', new Password],
        ]);

        User::create([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);
        
        session()->flash('message', 'User successfully created.');

        return redirect()->route('master-data.users');
    }

    public function render()
    {
        return view('livewire.users.create-users');
    }
}
