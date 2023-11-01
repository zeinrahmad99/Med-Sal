<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'name_ar',
    ];

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'role_id');
    }

    public function admins()
    {
        return $this->hasMany(Admin::class, 'role_id');
    }
}
