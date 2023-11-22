<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Policies\ServicePolicy;
use App\Models\Api\V1\Service;
use App\Policies\AppointmentPolicy;
use App\Models\Api\V1\Appointment;
use App\Policies\OrderPolicy;
use App\Models\Api\V1\Product;
use App\Models\Api\V1\Order;
use App\Policies\ProductPolicy;
use App\Models\Api\V1\User;
use App\Policies\ProviderPolicy;
use App\Models\Api\V1\Provider;
use Illuminate\Support\Facades\Gate;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */

    protected $policies = [
        Product::class => ProductPolicy::class,
        Service::class => ServicePolicy::class,
        Order::class => OrderPolicy::class,
        Appointment::class => AppointmentPolicy::class,
        Provider::class => ProviderPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
       $this->registerPolicies();
       Gate::define('isSuperAdmin',function(User $user){
        return $user->role === 'super_admin';
       });

       Gate::define('isProvider',function(User $user){
        return $user->role == 'provider';
       });
       Gate::define('isAdmin',function(User $user){
        return $user->role == 'admin';
       });
    }
}
