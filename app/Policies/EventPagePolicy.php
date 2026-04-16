<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class EventPagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isOrganizer();
    }

    public function create(User $user): bool
    {
        return $user->isOrganizer();
    }
    public function update(User $user, Task $Task): bool
    {
        return $user->isOrganizer();
    }
    public function delete(User $user, Task $Task): bool
    {
        return $user->isOrganizer();
    }
}
