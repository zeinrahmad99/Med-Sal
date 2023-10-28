<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'provider_id',
        'category_id',
        'title',
        'title_ar',
        'description',
        'description_ar',
        'price',
        'discount',
        'status',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'product_id');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_product');
    }
}
