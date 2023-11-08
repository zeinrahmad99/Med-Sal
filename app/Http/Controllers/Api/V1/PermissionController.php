<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Api\V1\Permission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdatePermissionRequest;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();

        return response()->json([
            'status' => 1,
            'permissions' => $permissions,
        ]);
    }

    public function update(UpdatePermissionRequest $request, $id)
    {
        $permission = Permission::find($id);

        $data = $request->all();

        $permission->update($data);

        return response()->json([
            'status' => 1,
            'permission' => $permission,
        ]);
    }

}
