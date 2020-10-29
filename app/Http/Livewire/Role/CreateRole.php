<?php

namespace App\Http\Livewire\Role;

use App\Models\Role;
use Livewire\Component;
use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CreateRole extends Component
{
    public $name;
    public $displayName;
    public $description;
    public $permissions = [];
    public $permissionData;

    public function mount()
    {
        if (!auth()->user()->isAbleTo('role-create')) {
            abort(403);
        }

        $this->permissionData = Permission::all();

        foreach ($this->permissionData as $permission) {
            $this->permissions[$permission->id] = false;
        }
    }

    public function saveRole()
    {
        $this->name = Str::slug($this->displayName, '-');

        $this->validate([
            'name' => 'required|min:2|unique:roles,name',
            'displayName' => 'required|min:2|unique:roles,display_name',
            'description' => 'sometimes|max:100',
            'permissions.*' => 'required|boolean',
        ]);

        $permissionData = [];
        foreach ($this->permissions as $key => $permission) {
            if ($permission) {
                $permissionData[] = $key;
            }
        }

        DB::transaction(function () use($permissionData) {
            $role = Role::create([
                'name' => $this->name,
                'display_name' => ucfirst($this->displayName),
                'description' => $this->description,
            ]);

            $role->attachPermissions($permissionData);
        });

        session()->flash('message', 'Role successfully created.');

        return redirect()->route('master-data.roles');
    }

    public function render()
    {
        return view('livewire.role.create-role', [
            'permission_data' => $this->permissionData,
        ]);
    }
}
