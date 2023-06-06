<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use function Sodium\add;

class RoleController extends Controller
{
    public function createRole(Request $request)
    {
        $role_in_db = Role::where('name', $request->role_name)->first();

        if ($role_in_db == null) {
            $role = Role::create(['name' => $request->role_name]);

            return response($role);
        }

        return response(['error' => 'Такая роль уже существует']);
    }

    public function deleteRole(Request $request)
    {
        $role = Role::find($request->id);

        if ($role != null) {
            $role->delete();

            return response(['success' => 'Успешное удаление роли']);
        }

        return response(['error' => 'Ошибка удаления роли']);
    }

    public function updateRole(Request $request)
    {
        $role = Role::where('name', $request->name)->first();
        $check_role = Role::where('name', $request->new_name)->first();

        if ($role != null && $check_role == null) {
            $role->update(['name' => $request->new_name]);

            return response($role);
        }

        return response(['error' => 'Такая роль уже существует']);
    }

    public function showRoles()
    {
        $roles = Role::all();

        return response(['roles' => $roles]);
    }

    public function getRoleByName(Request $request)
    {
        $role = Role::where('name', $request->name)->first();

        return response($role);
    }

    public function getRoleById(Request $request)
    {
        $role = Role::where('id', $request->id)->first();

        return response($role);
    }
}
