<?php

namespace App\Http\Livewire\Material;

use App\Models\Material;
use Livewire\Component;
use Livewire\WithPagination;

class Materials extends Component
{
    use WithPagination;
    
    public $search;
    public $confirming;

    protected $updatesQueryString = ['search'];

    public function mount()
    {
        if (!auth()->user()->isAbleTo('material-list')) {
            abort(403);
        }

        $this->search = request()->query('search', $this->search);
    }

    public function delete($id)
    {
        Material::where('id', $id)->delete();
    }

    public function confirmDelete($id)
    {
        $this->confirming = $id;
    }

    public function resetConfirm()
    {
        $this->confirming = '';
    }

    protected function getMaterials()
    {
        return Material::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->paginate();
    }
    
    public function render()
    {
        return view('livewire.material.materials', [
            'materials' => $this->getMaterials()
        ]);
    }
}
