<?php

namespace App\Http\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Laravel\Fortify\Rules\Password;
use Illuminate\Support\Facades\Hash;

class UpdateUsers extends Component
{
    public $user;
    public $name = '';
    public $username = '';
    public $email = '';
    public $password = '';

    public function mount($id)
    {
        $user = User::findOrFail($id);
        $this->user = $user;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
    }

    public function saveUser()
    {
        $this->validate([
            'name' => 'required|min:6|unique:users,name,' . $this->user->id . ',id',
            'username' => 'required|min:6|unique:users,username,' . $this->user->id . ',id',
            'email' => 'required|email|unique:users,email,' . $this->user->id . ',id',
            'password' => ['sometimes', 'string', 'min:6', new Password],
        ]);

        $this->user->name = $this->name;
        $this->user->username = $this->username;
        $this->user->email = $this->email;
        if ($this->password) {
            $this->user->password = Hash::make($this->password);
        }
        $this->user->save();

        session()->flash('message', 'User successfully updated.');

        return redirect()->route('master-data.users');
    }

    public function render()
    {
        return view('livewire.users.update-users');
    }
}
