<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'provider_id',
        'category_id',
        'name',
        'name_ar',
        'description',
        'description_ar',
        'price',
        'discount',
        'status',
        'quantity',
        'images'
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class,'provider_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'product_id');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_product');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
