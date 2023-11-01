<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;
    protected $fillable = [
        'admin_id',
         'role_id'
        ];

    public function role()
    {
        return $this->belongsTo(Role::class,'role_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'admin_id');
    }
     public function categories()
    {
        return $this->hasMany(Category::class, 'admin_id');
    }
}
