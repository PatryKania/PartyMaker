<?php

namespace App\Filament\EventPanel\Resources\Participants\Pages;

use App\Filament\EventPanel\Resources\Participants\ParticipantResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;

class CreateParticipant extends CreateRecord
{
    protected static string $resource = ParticipantResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['email'])) {
            $user = User::where('email', $data['email'])->first();

            if ($user) {
                $data['user_id'] = $user->id;
            }
        }

        return $data;
    }
}
