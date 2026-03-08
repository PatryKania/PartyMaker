<?php

namespace App\Filament\EventPanel\Resources\Participants\Pages;

use App\Filament\EventPanel\Resources\Participants\ParticipantResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Models\User;

class EditParticipant extends EditRecord
{
    protected static string $resource = ParticipantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
