<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateRoleRequest;
use Illuminate\Http\Request;
use App\Models\Api\V1\Role;
use App\Models\Api\V1\Permission;

class RoleController extends Controller
{
    public function index()
    {
        try
        {
            Gate::authorize('isSuperAdmin');
            $roles = Role::all();

            return response()->json([
                'status' => 1,
                'roles' => $roles,
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status'=>0,
            ]);
        }
    }

    public function show($id)
    {

        try{
            Gate::authorize('isSuperAdmin');
            $role = Role::where('id', $id)->first();

            return response()->json([
                'status' => 1,
                'role' => $role->load('permissions'),
            ]);
        }
        catch(\Exception $e)
        {
            return response()->json([
                'status' => 0,
            ]);
        }

    }

    public function update(Role $role, UpdateRoleRequest $request)
    {
        // $permissions = array_keys(app('permissions'));
        $permissions = Permission::pluck('ability')->toArray();


        try {
            Gate::authorize('isSuperAdmin');

            foreach ($request->permissions as $key => $value) {

                if (!in_array($key, $permissions) || !in_array($value, ['allow', 'deny'])) {
                    return response()->json([
                        'status' => 0,
                    ]);
                }
                // update the permission
                $role->permissions()->where('ability', $key)->update([
                    'status' => $value,
                ]);
            }

            return response()->json([
                'status' => 1,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
    }
}
