<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        return view('livewire.users', [
            'users' => $this->users()
        ]);
    }

    protected function users()
    {
        return User::where('id', '<>', auth()->id())
            ->where('name', 'like', '%'.$this->search.'%')
            ->paginate();
    }
}
