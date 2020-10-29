<?php

namespace App\Http\Livewire\Users;

use App\Models\Role;
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
    public $role = '';

    public function mount($id)
    {
        if (!auth()->user()->isAbleTo('user-update')) {
            abort(403);
        }

        $user = User::with('roles')->findOrFail($id);
        $this->user = $user;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->role = optional($user->roles->first())->id;
    }

    public function saveUser()
    {
        $this->validate([
            'name' => 'required|min:3|unique:users,name,' . $this->user->id . ',id',
            'username' => 'required|min:5|unique:users,username,' . $this->user->id . ',id',
            'email' => 'required|email|unique:users,email,' . $this->user->id . ',id',
            'password' => ['sometimes', 'string', 'min:6', new Password],
            'role' => ['sometimes', 'exists:roles,id'],
        ]);

        $this->user->name = $this->name;
        $this->user->username = $this->username;
        $this->user->email = $this->email;
        if (auth()->user()->isAbleTo('user-update-password') && $this->password) {
            $this->user->password = Hash::make($this->password);
        }
        $this->user->save();

        if (auth()->user()->isAbleTo('user-set-role') && $this->role) {
            $this->user->syncRoles([$this->role]);
        }

        session()->flash('message', 'User successfully updated.');

        return redirect()->route('master-data.users');
    }

    public function render()
    {
        return view('livewire.users.update-users', [
            'roles' => Role::all()
        ]);
    }
}
