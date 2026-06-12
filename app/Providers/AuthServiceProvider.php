<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Order;
use App\Policies\OrderPolicy;
use App\Models\User;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Order::class => OrderPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gates for quick checks
        Gate::define('manage-users', function ($user) {
            return $user->user_type === 'A' && ! $user->blocked;
        });

        Gate::define('process-orders', function ($user) {
            return in_array($user->user_type, ['E', 'A']) && ! $user->blocked;
        });

        Gate::define('view-receipt', function ($user, $order) {
            // Admins and owning customers may view receipts
            if ($user->blocked) return false;
            if ($user->user_type === 'A') return true;
            return $order->customer_id === $user->id;
        });
    }
}
