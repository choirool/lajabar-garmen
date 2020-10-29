<?php

namespace App\Http\Livewire\Role;

use App\Models\Role;
use Livewire\Component;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class UpdateRole extends Component
{
    public $role;
    public $name;
    public $displayName;
    public $description;
    public $permissions = [];
    public $permissionData;

    public function mount($id)
    {
        if (!auth()->user()->isAbleTo('role-update')) {
            abort(403);
        }

        $this->permissionData = Permission::all();

        $role = Role::with('permissions')->findOrFail($id);
        $this->role = $role;
        $this->name = $role->name;
        $this->displayName = $role->display_name;
        $this->description = $role->description;

        $permissionIds = $role->permissions->pluck('id')->toArray();

        foreach (Permission::all() as $permission) {
            $this->permissions[$permission->id] = in_array($permission->id, $permissionIds);
        }
    }

    public function saveRole()
    {
        $this->validate([
            'name' => 'required|min:2|unique:roles,name,' . $this->role->id . ',id',
            'displayName' => 'required|min:2|unique:roles,display_name,' . $this->role->id . ',id',
            'description' => 'sometimes|max:100',
            'permissions.*' => 'required|boolean',
        ]);

        $permissionData = [];
        foreach ($this->permissions as $key => $permission) {
            if ($permission) {
                $permissionData[] = $key;
            }
        }

        DB::transaction(function () use ($permissionData) {
            $this->role->name = $this->name;
            $this->role->display_name = $this->displayName;
            $this->role->description = $this->description;
            $this->role->save();

            $this->role->syncPermissions($permissionData);
        });

        session()->flash('message', 'role successfully updated.');

        return redirect()->route('master-data.roles');
    }

    public function render()
    {
        return view('livewire.role.update-role', [
            'permission_data' => Permission::all(),
        ]);
    }
}
