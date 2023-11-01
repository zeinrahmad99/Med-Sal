<?php

namespace App\Policies;

use App\Models\Api\V1\User;
use App\Models\Api\V1\Provider;
use App\Models\Api\V1\Role;
use App\Models\Api\V1\Permission;

use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function editPersonalData(User $user, Provider $provider): bool
    {
        return $user->role === 'provider' && $user->id === $provider->user->id;
    }

    public function userManagement(User $user,Provider $provider): bool
    {
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','user management')->first();
        return $user->role == 'admin' && $user->admins->id === $provider->category->id && $permission->status === 'allow';
    }

}
