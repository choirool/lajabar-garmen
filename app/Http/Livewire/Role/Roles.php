<?php

namespace App\Http\Livewire\Role;

use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;

class Roles extends Component
{
    use WithPagination;

    public $search;
    public $confirming;

    protected $updatesQueryString = ['search'];

    public function mount()
    {
        if (!auth()->user()->isAbleTo('role-list')) {
            abort(403);
        }

        $this->search = request()->query('search', $this->search);
    }

    public function delete($id)
    {
        Role::where('id', $id)->delete();
    }

    public function confirmDelete($id)
    {
        $this->confirming = $id;
    }

    public function resetConfirm()
    {
        $this->confirming = '';
    }

    protected function getRoles()
    {
        return Role::query()
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->where('display_name', 'like', '%' . $this->search . '%');
            })
            ->paginate();
    }

    public function render()
    {
        return view('livewire.role.roles', [
            'roles' => $this->getRoles(),
        ]);
    }
}
