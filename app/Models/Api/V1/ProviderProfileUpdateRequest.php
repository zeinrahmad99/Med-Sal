<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderProfileUpdateRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'business_name',
        'contact_number',
        'bank_name',
        'iban',
        'swift_code',
        'reason',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
