<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TshirtImage;
use Illuminate\Auth\Access\HandlesAuthorization;

class TshirtImagePolicy
{
    use HandlesAuthorization;

    public function view(User $user, TshirtImage $tshirtImage)
    {
        return $user->customer->id === $tshirtImage->customer_id;
    }

    public function update(User $user, TshirtImage $tshirtImage)
    {
        return $user->customer->id === $tshirtImage->customer_id;
    }

    public function delete(User $user, TshirtImage $tshirtImage)
    {
        return $user->customer->id === $tshirtImage->customer_id;
    }
}
