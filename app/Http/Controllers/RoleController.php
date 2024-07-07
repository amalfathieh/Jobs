<?php

namespace App\Http\Controllers;

use App\Http\Resources\RolesAndPermissionsResource;
use App\Models\User;
use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use responseTrait;

    public function addRole(Request $request) {

        $validate = Validator::make($request->all(), [
            'title' => 'required|string',
            'permissions' => 'required|array'
        ]);

        if ($validate->fails()){
            return $this->apiResponse(null, $validate->errors(), 400);
        }

        $permissions = $request->permissions;
        $role = Role::create(['name' => $request->title, 'guard_name' => 'web'])->givePermissionTo($permissions);

        return $this->apiResponse($role, 'Role created successfully', 200);
    }

    public function editRole(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'role' => 'required',
                'new_name' => 'string',
                'permissions' => 'array'
            ]);

            if ($validate->fails()) {
                return $this->apiResponse(null, $validate->errors(), 400);
            }
            if (is_numeric($request->role)) {
                $role = Role::findById($request->role, 'web');
                $role->name = $request->new_name;
                if ($request->permissions) {
                    $role->syncPermissions($request->permissions);
                }
                $role->save();
                return $this->apiResponse($role, __('strings.edited_successfully'), 200);
            }
            $role = Role::findByName($request->role, 'web');
            $role->name = $request->new_name;
            if ($request->permissions) {
                $role->syncPermissions($request->permissions);
            }
            $role->save();
            return $this->apiResponse($role, __('strings.edited_successfully'), 200);
        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 404);
        }
    }

    public function deleteRole(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                'role' => 'required'
            ]);

            if ($validate->fails()) {
                return $this->apiResponse(null, $validate->errors(), 400);
            }

            if (is_numeric($request->role)) {
                $role = Role::findById($request->role, 'web');
                $role->delete();
                return $this->apiResponse(null, __('strings.deleted_successfully'), 200);
            }

            $role = Role::findByName($request->role, 'web');
            $role->delete();
            return $this->apiResponse(null, __('strings.deleted_successfully'), 200);

        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 404);
        }
    }

    public function allRoles() {
        $roles = Role::all();
        $data = [];
        foreach ($roles as $role) {
            $data[$role->name] = $role->permissions->pluck('name');
        }
        return RolesAndPermissionsResource::collection($roles);
    }

    public function getRoles() {
        $roles = Role::all();
        $data = [];
        foreach ($roles as $role) {
            $data[$role->name] = $role->permissions->pluck('name');
        }
        return $data;
    }

    public function editUserRoles(Request $request){
        $validate = Validator::make($request->all(), [
            'id' => 'required|integer',
            'roles_name' => 'required|array'
        ]);

        if ($validate->fails()) {
            return $this->apiResponse(null, $validate->errors(), 400);
        }

        $user = User::where('id', $request->id)->first();
        if ($user) {
            $user->syncRoles($request->roles_name);
            $user->roles_name = $request->roles_name;
            $user->save();
            return $this->apiResponse(null, 'Roles updated successfully', 200);
        }
        return $this->apiResponse(null, __('strings.not_found'), 404);
    }
}
