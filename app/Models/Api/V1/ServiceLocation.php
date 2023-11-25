<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceLocation extends Model
{
    use HasFactory;
    protected $fillable = [
        'provider_id',
        'latitude',
        'longitude'
    ];
    public function provider()
    {
        return $this->belongsTo(Provider::class,'provider_id');
    }
    public function services()
    {
        return $this->hasMany(Service::class, 'service_location_id');
    }
}
