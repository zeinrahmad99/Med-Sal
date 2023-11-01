<?php

namespace App\Models\Api\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $fillable=[
        'patient_id',
        'date',
        'status',
        'service_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'patient_id');
    }
    public function service()
    {
        return $this->belongsTo(Service::class,'service_id');
    }
}
