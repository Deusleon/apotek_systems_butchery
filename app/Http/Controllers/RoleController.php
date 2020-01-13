<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    public function index()
    {
        $roles = Role::get();
        $permissions = Permission::get();

        return view("roles.index", compact("roles", "permissions"));
    }

    public function create()
    {
        $permissions = Permission::get();
        return view("roles.create", compact("permissions"));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'unique:roles|required|string|min:2|max:50',
            'description' => 'required|string|min:5|max:50',
            'permissions' => 'required',
        ]);


        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description
        ]);
        $role->givePermissionTo($request->permissions);

        session()->flash("alert-success", "Role created successfully!");
        return redirect()->route('roles.index');
    }


    public function edit($id)
    {
        $role = Role::find($id);

        $AssignedPermissions = DB::select("SELECT permission_id id FROM role_has_permissions where role_id = " . $id . "");
        $permissionsAll = Permission::get();

        $permissionsAssigned = [];
        foreach ($AssignedPermissions as $p) {
            $permissionsAssigned[] = $p->id;
        }

        return view("roles.edit", compact('role', 'permissionsAll', 'permissionsAssigned'));
    }


    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:50',
            'description' => 'required|string|min:5|max:50',
            'permissions' => 'required',
        ]);


        $role = Role::findOrfail($request->id);
        $role->name = $request->name;
        $role->description = $request->description;

        $role->save();
        $role->syncPermissions($request->permissions);

        session()->flash("alert-success", "Role updated successfully!");
        return redirect()->route('roles.index');

    }

    public function destroy(Request $request)
    {
        $role = Role::find($request->role_id);
        $role->syncPermissions([]);

        Role::destroy($request->role_id);
        session()->flash("alert-success", "Role deleted successfully!");
        return back();
    }
}
