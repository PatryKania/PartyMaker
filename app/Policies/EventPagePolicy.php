<?php

namespace App\Policies;

use App\Models\EventPage;
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
    public function update(User $user, EventPage $page): bool
    {
        return $user->isOrganizer();
    }
    public function delete(User $user, EventPage $page): bool
    {
        return $user->isOrganizer();
    }
}
