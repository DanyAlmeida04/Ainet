<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function manage(User $user)
    {
        return $user->user_type === 'A' && ! $user->blocked;
    }

    public function viewCustomer(User $user, User $customer)
    {
        // Admins can view customer list but not private profile details
        return $user->user_type === 'A' && ! $user->blocked;
    }
}
