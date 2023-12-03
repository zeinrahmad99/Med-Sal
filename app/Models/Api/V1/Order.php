<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable=[
        'patient_id',
        'latitude',
        'longitude',
        'cost'
    ];

    public function user()
     {
        return $this->belongsTo(User::class,'patient_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class,'order_product');
    }
}
