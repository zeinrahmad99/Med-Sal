<?php

namespace App\Policies;

use App\Models\Api\V1\Category;
use App\Models\Api\V1\User;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'super_admin';

    }

    /**
     * Determine whether the user can view the model.
     */

}
