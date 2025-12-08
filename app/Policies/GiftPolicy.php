<?php

namespace App\Policies;

use App\Models\Gift;
use App\Models\User;

class GiftPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isOrganizer();
    }
    public function update(User $user, Gift $gift): bool
    {
        return $user->isOrganizer();
    }
    public function delete(User $user, Gift $gift): bool
    {
        return $user->isOrganizer();
    }
}
