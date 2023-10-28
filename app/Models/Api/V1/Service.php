<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'discount',
        'status',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'service_id');
    }
    public function serviceLocation(): BelongsTo
    {
        return $this->belongsTo(ServiceLocation::class);
    }
     public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }


}
