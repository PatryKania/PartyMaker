<?php

namespace App\Filament\EventPanel\Resources\Invitations\Pages;

use App\Filament\EventPanel\Resources\Invitations\InvitationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvitation extends CreateRecord
{
    protected static string $resource = InvitationResource::class;
}
