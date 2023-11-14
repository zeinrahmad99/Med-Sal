<?php

namespace App\Policies;

use App\Models\Api\V1\User;
use App\Models\Api\V1\Order;
use App\Models\Api\V1\Role;
use App\Models\Api\V1\Permission;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can update any models.
     */
    public function update(User $user , Order $order){
        return $user->id === $order->user->id;
    }

    /* approve an order */
    public function approveOrder(User $user): bool
    {
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','approve order')->first();
        return $permission->status === 'allow';
    }
    /**reject an order */

    public function rejectOrder(User $user): bool
    {
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','reject order')->first();
        return $permission->status === 'allow';
    }
}
