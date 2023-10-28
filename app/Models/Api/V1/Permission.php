<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    protected $fillable = [
        'role_id',
        'ability',
        'ability_ar',
        'status',
    ];


    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
