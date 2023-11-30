<?php

namespace App\Policies;

use App\Models\Api\V1\Provider;
use App\Models\Api\V1\User;
use App\Models\Api\V1\Role;
use App\Models\Api\V1\Permission;
use Illuminate\Auth\Access\Response;

class ProviderPolicy
{
    /**
     * can  view products and services for each provider
     */
    public function admin(User $user,Provider $provider): bool
    {

        return $provider->category->admin_id === $user->id;
    }
    public function view(User $user, Provider $provider): bool
    {
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','view products and services')->first();
        return  $user->id === $provider->user->id && $permission->status==='allow' || $provider->category->admin_id === $user->id && $permission->status==='allow';
    }
    /** request an update of personal data */

    public function update(User $user, Provider $provider){
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','edit personal data')->first();
        return $user->id === $provider->user_id && $permission->status === 'allow';
    }
    /* who can approve register for service provider */

    public function approveProvider(User $user,Provider $provider){
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','accept provider register')->first();
        return $user->role === 'admin' && $user->id === $provider->category->admin->user->id && $permission->status === 'allow';
    }

    /* who can reject register for service provider */

    public function rejectProvider(User $user,Provider $provider){
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','reject provider register')->first();
        return $user->role === 'admin' && $user->id === $provider->category->admin->user->id && $permission->status === 'allow';
    }
    public function c(User $user,Provider $provider){
        return $user->id === $provider->category->admin_id;
    }

   }
