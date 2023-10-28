<?php

namespace App\Models\Api\V1;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'email_verified_at',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $dates = [
        'email_verified_at',
    ];

    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class, 'admin_id');
    }
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'user_id');
    }
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class,'patient_id');
    }
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }
    public function providers(): HasMany
    {
        return $this->hasMany(Provider::class, 'user_id');
    }

}
