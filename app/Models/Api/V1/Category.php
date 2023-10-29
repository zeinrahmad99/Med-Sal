<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'admin_id',
        'name',
        'name_ar',
        'description',
        'description_ar',
        'status',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function providers(): HasMany
    {
        return $this->hasMany(Provider::class, 'service_type_id');
    }
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }
    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'category_id');
    }
}

