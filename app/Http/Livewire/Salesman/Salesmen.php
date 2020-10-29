<?php

namespace App\Http\Livewire\Salesman;

use Livewire\Component;
use App\Models\Salesman as SalesmenModel;
use Livewire\WithPagination;

class Salesmen extends Component
{
    use WithPagination;
    
    public $search;
    public $confirming;

    protected $updatesQueryString = ['search'];

    public function mount()
    {
        if (!auth()->user()->isAbleTo('salesman-list')) {
            abort(403);
        }

        $this->search = request()->query('search', $this->search);
    }

    public function delete($id)
    {
        SalesmenModel::where('id', $id)->delete();
    }

    public function confirmDelete($id)
    {
        $this->confirming = $id;
    }

    public function resetConfirm()
    {
        $this->confirming = '';
    }

    protected function getSalesmen()
    {
        return SalesmenModel::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->paginate();
    }

    public function render()
    {
        return view('livewire.salesman.salesmen', [
            'salesmen' => $this->getSalesmen()
        ]);
    }
}
