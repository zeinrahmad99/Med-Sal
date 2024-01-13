<?php

namespace App\Policies;

use App\Models\Api\V1\User;
use App\Models\Api\V1\Service;
use App\Models\Api\V1\Role;
use App\Models\Api\V1\Permission;
use Illuminate\Auth\Access\Response;

class ServicePolicy
{


    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','add service')->first();
        return $permission->status === 'allow';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Service $service): bool
    {
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','update service')->first();
        return $user->id === $service->serviceLocation->provider->user->id && $permission->status === 'allow';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function remove(User $user, Service $service){
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','remove service')->first();
        return $user->id === $service->serviceLocation->provider->user->id && $permission->status === 'allow';

    }

    public function accepted(User $user, Service $service){
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','accept service')->first();
        return $user->id === $service->serviceLocation->provider->user->id && $permission->status === 'allow';

    } 


    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Service $service): bool
    {
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','delete service')->first();
        return $user->id === $service->serviceLocation->provider->user->id && $permission->status === 'allow';
    }

}
