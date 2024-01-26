<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Api\V1\Role;
use App\Models\Api\V1\User;
use App\Models\Api\V1\Admin;
use Illuminate\Http\Request;
use App\Models\Api\V1\Category;
use App\Models\Api\V1\Permission;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Api\V1\CreateAdminRequest;
use App\Http\Requests\Api\V1\UpdateAdminRequest;

class AdminController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        try {
            Gate::authorize('isSuperAdmin');
            $admins = Admin::with('categories')->get();

            return response()->json([
                'status' => 1,
                'admins' => $admins,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
    }

    // Index permissions list for admin
    public function indexAdminPermissions()
    {
        try {
            Gate::authorize('isSuperAdmin');
            $role = Role::where('name', 'admin')->first();
            $permissions = Permission::where('role_id', $role->id)->get();

            return response()->json([
                'status' => 1,
                'permissions' => $permissions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'An error occurred while retrieving admin permissions.',
            ]);
        }
    }

    // Index permissions list for provider
    public function indexProviderPermissions()
    {
        try {
            Gate::authorize('isSuperAdmin');
            $role = Role::where('name', 'provider')->first();
            $permissions = Permission::where('role_id', $role->id)->get();

            return response()->json([
                'status' => 1,
                'permissions' => $permissions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'An error occurred while retrieving provider permissions.',
            ]);
        }
    }

    // create a new resource.
    public function create($id)
    {
        try {
            Gate::authorize('isSuperAdmin');
            return DB::transaction(function () use ($id) {
                $admin = User::findorfail($id);
                $role = Role::where('name', 'admin')->first();
                $admin->update(['role' => 'admin']);
                Admin::create([
                    'admin_id' => $admin->id,
                    'role_id' => $role->id,
                ]);
                return response()->json([
                    'status' => 1,
                ]);
            });

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
            ]);
        }
    }

    // Display the specified resource.
    public function show($id)
    {
        $admin = Admin::where('admin_id', $id)->with(['user', 'categories'])->get();
        return response()->json([
            'status' => 1,
            'admin' => $admin,
        ]);

    }

    // Remove the specified resource from storage.
    public function delete($id)
    {
        try {
            Gate::authorize('isSuperAdmin');

            return DB::transaction(function () use ($id) {
                $admin = Admin::where('admin_id', $id)->first();
                $user = User::find($id);
                $user->update(['role' => 'patient']);

                // Check if the admin exists to handle the null case
                if ($admin) {
                    $admin->delete();
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Admin does not exist.',
                    ]);
                }

                return response()->json([
                    'status' => 1,
                    'message' => 'Admin deleted successfully.',
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'An error occurred while deleting the admin.',
            ]);
        }
    }
}
