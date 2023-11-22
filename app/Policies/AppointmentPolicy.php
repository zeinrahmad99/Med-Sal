<?php

namespace App\Policies;

use App\Models\Api\V1\Appointment;
use App\Models\Api\V1\User;
use App\Models\Api\V1\Role;
use App\Models\Api\V1\Permission;
use Illuminate\Auth\Access\Response;

class AppointmentPolicy
{

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->patient_id || $appointment->service->serviceLocation->provider->user->id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->patient_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Appointment $appointment): bool
    {
       return $appointment->service->serviceLocation->provider->user->id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function done(User $user, Appointment $appointment): bool
    {
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','approve service')->first();
        return $appointment->service->serviceLocation->provider->user->id === $user->id && $permission->status === 'allow';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function canceled(User $user, Appointment $appointment): bool
    {
        $role= Role::where('name',$user->role)->first();
        $permission=Permission::where('role_id',$role->id)->where('ability','reject service')->first();
        return $appointment->service->serviceLocation->provider->user->id === $user->id && $permission->status === 'allow';
    }
}
