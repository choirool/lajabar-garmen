<?php

namespace App\Http\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    public $search;
    public $confirming;

    protected $updatesQueryString = ['search'];

    public function mount()
    {
        if (!auth()->user()->isAbleTo('user-list')) {
            abort(403);
        }

        $this->search = request()->query('search', $this->search);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->confirming = $id;
    }

    public function resetConfirm()
    {
        $this->confirming = '';
    }

    public function deleteUser($id)
    {
        User::where('id', $id)->delete();
    }

    public function render()
    {
        return view('livewire.users.users', [
            'users' => $this->users()
        ]);
    }

    protected function users()
    {
        return User::where('id', '<>', auth()->id())
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('username', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhereHas('roles', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('display_name', 'like', '%' . $this->search . '%');
                    });
            })
            ->with('roles')
            ->paginate();
    }
}
