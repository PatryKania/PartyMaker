<?php

namespace App\Policies;

use App\Models\Survey;
use App\Models\User;

class SurveyPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Survey $survey): bool
    {
        if ($user->isOrganizer()) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return (bool) $user->isOrganizer();
    }

    public function update(User $user, Survey $survey): bool
    {
        return (bool) $user->isOrganizer();
    }

    public function delete(User $user, Survey $survey): bool
    {
        return (bool) $user->isOrganizer();
    }

    public function deleteAny(User $user): bool
    {
        return (bool) $user->isOrganizer();
    }

    public function restore(User $user, Survey $survey): bool
    {
        return (bool) $user->isOrganizer();
    }

    public function forceDelete(User $user, Survey $survey): bool
    {
        return (bool) $user->isOrganizer();
    }
}