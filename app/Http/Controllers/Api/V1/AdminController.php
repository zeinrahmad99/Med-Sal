<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Api\V1\Admin;
use App\Models\Api\V1\Category;
use Illuminate\Support\Facades\Auth;

use App\Models\Api\V1\Role;
use App\Models\Api\V1\User;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Api\V1\CreateAdminRequest;
use App\Http\Requests\Api\V1\UpdateAdminRequest;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            Gate::authorize('isSuperAdmin');
            $admins = Admin::all();

                return response()->json([
                    'status' => 1,
                    'admins' => $admins,
                ]);
        }catch(\Exception $e){
            return response()->json([
                'status'=>0,
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        try{
        Gate::authorize('isSuperAdmin');
        return DB::transaction(function () use ($id)
        {
            $admin=User::find($id);
            $role=Role::where('name','admin')->first();
            $admin->update(['role'=>'admin']);
            Admin::create([
                'admin_id'=>$admin->id,
                'role_id'=>$role->id,
            ]);
            return response()->json([
                'status'=>1,
            ]);
        });

        }catch(\Exception $e){
            return response()->json([
                'status'=>0,
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

            $admin =Category::where('admin_id',Auth::id())->get();

            return response()->json([
                'status' => 1,
                'admin' => $admin,
            ]);

    }

    /**
     * Show the form for editing the specified resource.
     */

    /**
     * Remove the specified resource from storage.
     */
    public function delete( $id)
    {
        try
       {
        Gate::authorize('isSuperAdmin');

        return DB::transaction(function () use ($id)
        {
            $admin=Admin::where('admin_id',$id)->first();
            $user=User::find($id);
            $user->update(['role' => 'patient']);
            $admin->delete();
            return response()->json([
                'status' => 1,
        ]);
    });
    }catch(\Exception $e){
        return response()->json([
            'status' => 0,
        ]);
    }

    }
}
