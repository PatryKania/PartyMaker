<?php

namespace App\Policies;

use App\Models\Schedule;
use App\Models\User;

class SchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isOrganizer();
    }
    public function update(User $user, Schedule $Schedule): bool
    {
        return $user->isOrganizer();
    }
    public function delete(User $user, Schedule $Schedule): bool
    {
        return $user->isOrganizer();
    }
}
