<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;

class OrderPolicy
{
    /**
     * Determine whether the user can view the order/receipt.
     */
    public function view(User $user, Order $order)
    {
        if ($user->blocked) return false;
        if ($user->user_type === 'A') return true; // admin can view any order
        return $order->customer_id === $user->id; // owner can view
    }

    /**
     * Determine whether the user can close the order (estampar/expedir).
     */
    public function close(User $user, Order $order)
    {
        if ($user->blocked) return false;
        return in_array($user->user_type, ['E', 'A']);
    }

    /**
     * Determine whether the user can cancel the order.
     */
    public function cancel(User $user, Order $order)
    {
        if ($user->blocked) return false;
        return $user->user_type === 'A';
    }
}
