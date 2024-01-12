<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Provider extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'service_type_id',
        'bussiness_name',
        'contact_number',
        'bank_name',
        'iban',
        'swift_code',
        'status',
        'document'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function serviceLocations()
    {
        return $this->hasMany(serviceLocation::class, 'provider_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'service_type_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'provider_id');
    }

    public function updateRequests()
    {
        return $this->hasMany(ProviderProfileUpdateRequest::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }


    // Accessor for the document attribute
    // we need to run this command on server php artisan storage:link
    public function getDocumentAttribute($value)
    {
        return '/storage/documents/' . $value;
    }
}
