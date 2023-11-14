<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Api\V1\Permission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdatePermissionRequest;
use Illuminate\Support\Facades\Gate;

class PermissionController extends Controller
{
    public function index()
    {
        try{
            Gate::allows('isSuperAdmin');
             $permissions = Permission::all();

            return response()->json([
                'status' => 1,
                'permissions' => $permissions,
            ]);

        }catch(\Exception $e){
            return response()->json([
                'status'=>0,
            ]);
        }

    }

    public function update(UpdatePermissionRequest $request, $id)
    {
        $permission = Permission::find($id);
        try{
            Gate::allows('isSuperAdmin');
            $data = $request->all();

            $permission->update($data);

            return response()->json([
                'status' => 1,
                'permission' => $permission,
            ]);

        }catch(\Exception $e){
            return response()->json([
                'status'=>0,
            ]);
        }

    }

}
