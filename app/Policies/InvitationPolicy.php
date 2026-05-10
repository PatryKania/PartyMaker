<?php

namespace App\Policies;

use App\Models\Invitation;
use App\Models\User;

class InvitationPolicy
{
    public function viewAny(User $user): bool
    {
          return (bool) $user->isOrganizer();
    }

    public function view(User $user,Invitation $invitation): bool
    {
         return (bool) $user->isOrganizer();
    }

    public function create(User $user): bool
    {
        return (bool) $user->isOrganizer();
    }

    public function update(User $user,Invitation $invitation): bool
    {
        return (bool) $user->isOrganizer();
    }

    public function delete(User $user,Invitation $invitation): bool
    {
        return (bool) $user->isOrganizer();
    }

    public function deleteAny(User $user): bool
    {
        return (bool) $user->isOrganizer();
    }

    public function restore(User $user,Invitation $invitation): bool
    {
        return (bool) $user->isOrganizer();
    }

    public function forceDelete(User $user,Invitation $invitation): bool
    {
        return (bool) $user->isOrganizer();
    }
}