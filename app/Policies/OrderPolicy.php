<?php

namespace App\Policies;

use App\Models\Api\V1\Order;
use App\Models\Api\V1\User;
use App\Models\Api\V1\Role;
use App\Models\Api\V1\Permission;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function approveOrder(User $user, Order $order ): bool
    {
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','approve order')->first();
        return $permission->status === 'allow' && $order->products->provided_id === $user->id;
    }
    public function rejectOrder(User $user, Order $order): bool
    {
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','reject order')->first();
        return $permission->status === 'allow' && $order->products->provided_id === $user->id;
    }
}
