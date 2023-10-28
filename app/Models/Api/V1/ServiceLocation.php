<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceLocation extends Model
{
    use HasFactory;
    protected $fillable = [
        'provider_id',
        'location',
    ];
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }
    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'service_location_id');
    }
}
