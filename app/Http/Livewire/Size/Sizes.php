<?php

namespace App\Http\Livewire\Size;

use App\Models\Size;
use Livewire\Component;
use Livewire\WithPagination;

class Sizes extends Component
{
    use WithPagination;
    
    public $search;
    public $confirming;

    protected $updatesQueryString = ['search'];

    public function mount()
    {
        $this->search = request()->query('search', $this->search);
    }

    public function delete($id)
    {
        Size::where('id', $id)->delete();
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

    protected function getSizes()
    {
        return Size::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->paginate();
    }
    
    public function render()
    {
        return view('livewire.size.sizes', [
            'sizes' => $this->getSizes()
        ]);
    }
}
