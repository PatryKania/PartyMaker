<?php

namespace App\Filament\EventPanel\Resources\Invitations\Pages;

use App\Filament\EventPanel\Resources\Invitations\InvitationResource;
use App\Models\Invitation;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInvitations extends ListRecords
{
    protected static string $resource = InvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function mount(): void
{
    $invitation = Invitation::first();

    if ($invitation) {
        redirect(static::getResource()::getUrl('edit', ['record' => $invitation]));
    } else {
        redirect(static::getResource()::getUrl('create'));
    }
}
}
