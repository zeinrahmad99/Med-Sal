<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id',
        'service_location_id',
        'name',
        'name_ar',
        'description',
        'description_ar',
        'price',
        'status',
        // 'discount',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'service_id');
    }
    public function serviceLocation()
    {
        return $this->belongsTo(ServiceLocation::class,'service_location_id');
    }
     public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }


}
