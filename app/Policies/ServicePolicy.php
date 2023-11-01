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
     * Determine whether the user can view the model.
     */
    public function view(User $user, Service $service): bool
    {
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','view products')->first();
        return ($user->id === $service->serviceLocation->provider->user->id && $permission->status === 'allow') || ($service->category->admin_id === $user->id && $permission->status === 'allow');
    }

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
    public function delete(User $user, Service $service): bool
    {
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','remove service')->first();
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
    public function approveService(User $user, Service $service ): bool
    {
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','approve service')->first();
        return $permission->status === 'allow' && $service->serviceLocation->provided_id === $user->id;
    }
    public function rejectService(User $user, Service $service): bool
    {
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','reject service')->first();
        return $permission->status === 'allow' && $service->serviceLocation->provided_id === $user->id;
    }
}
