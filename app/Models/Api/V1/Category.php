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

    public function admin()
    {
        return $this->belongsTo(Admin::class,'admin_id');
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

        //applying filters 
 
        public function ScopeFilter( $query, QueryFilter $filters ) { 
            return $filters->apply( $query ); 
        }
}

