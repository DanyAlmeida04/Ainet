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
            if ($user->blocked) return false;
            $type = strtoupper((string) ($user->user_type ?? ''));
            return in_array($type, ['A', 'ADMIN']);
        });

        Gate::define('process-orders', function ($user) {
            if ($user->blocked) return false;
            $type = strtoupper((string) ($user->user_type ?? ''));
            return in_array($type, ['E', 'EMPLOYEE', 'F', 'FUNCIONARIO', 'A', 'ADMIN']);
        });

        Gate::define('view-receipt', function ($user, $order) {
            // Admins and owning customers may view receipts
            if ($user->blocked) return false;
            $type = strtoupper((string) ($user->user_type ?? ''));
            if (in_array($type, ['A', 'ADMIN'])) return true;
            return $order->customer_id === $user->id;
        });
    }
}
