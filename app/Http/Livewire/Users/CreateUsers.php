<?php

namespace App\Http\Livewire\Users;

use App\Models\Role;
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
    public $role = '';

    public function saveUser()
    {
        $this->validate([
            'name' => 'required|min:6|unique:users',
            'username' => 'required|min:5|unique:users',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'string', 'min:6', new Password],
            'role' => ['sometimes', 'exists:roles,id'],
        ]);

        $user = User::create([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        if(auth()->user()->isAbleTo('user-set-role') && $this->role) {
            $user->syncRoles([$this->role]);
        }

        session()->flash('message', 'User successfully created.');

        return redirect()->route('master-data.users');
    }

    public function render()
    {
        return view('livewire.users.create-users', [
            'roles' => Role::all()
        ]);
    }
}
