<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Policies\ServicePolicy;
use App\Models\Api\V1\Service;
use App\Models\Api\V1\Product;
use App\Policies\ProductPolicy;
use App\Models\Api\V1\User;
use App\Policies\UserPolicy;
use App\Models\Api\V1\Order;
use App\Policies\OrderPolicy;

use Illuminate\Support\Facades\Gate;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Service::class => ServicePolicy::class,
        Product::class => ProductPolicy::class,
        Order::class => OrderPolicy::class,
        User::class => UserPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('update',[ServicePolicy::class,'update']);
    }
}
