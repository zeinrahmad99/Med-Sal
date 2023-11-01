<?php

namespace App\Policies;

use App\Models\Api\V1\Product;
use App\Models\Api\V1\User;
use App\Models\Api\V1\Role;
use App\Models\Api\V1\Permission;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','view products')->first();
        return ($user->id === $product->provider->user->id && $permission->status === 'allow') || ($product->category->admin_id === $user->id && $permission->status === 'allow');
    }
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','add product')->first();
        return $permission->status === 'allow';
    }
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','update product')->first();
        return $user->id === $product->provider->user->id && $permission->status === 'allow';

    }
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','remove product')->first();
        return $user->id === $product->provider->user->id && $permission->status === 'allow';
    }
    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','delete product')->first();
        return $user->id === $product->provider->user->id && $permission->status === 'allow';
    }
}
