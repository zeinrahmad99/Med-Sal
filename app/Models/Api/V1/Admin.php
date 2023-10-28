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

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
     public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'admin_id');
    }
}
