<?php

namespace App\Policies;

use App\Models\Memory;
use App\Models\User;

class MemoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissions();
    }

    public function create(User $user): bool
    {
        return $user->hasPermissions();
    }
    public function update(User $user, Memory $memory): bool
    {
        return $user->isOrganizer();
    }
    public function delete(User $user, Memory $memory): bool
    {
        return $user->isOrganizer();
    }
}
