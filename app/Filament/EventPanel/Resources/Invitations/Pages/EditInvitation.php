<?php

namespace App\Filament\EventPanel\Resources\Invitations\Pages;

use App\Filament\EventPanel\Resources\Invitations\InvitationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInvitation extends EditRecord
{
    protected static string $resource = InvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
