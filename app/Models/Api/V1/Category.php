<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Classes\Api\V1\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

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
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id','admin_id');
    }

    public function providers()
    {
        return $this->hasMany(Provider::class, 'service_type_id');
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
    public function services()
    {
        return $this->hasMany(Service::class, 'category_id');
    }

    // apply the given filters to the query.
    public function ScopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    // add a where clause to the query to filter for records with a 'status' value of 'active'.
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}

