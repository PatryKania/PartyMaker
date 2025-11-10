<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Participant;
use Illuminate\Support\Facades\Auth;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected function afterCreate(): void
    {
        $user = Auth::user();

        if ($user) {
            Participant::create([
                'event_id' => $this->record->id,
                'user_id' => $user->id,
                'first_name' => $user->first_name ?? 'Organizer',
                'last_name' => $user->last_name ?? '',
                'email' => $user->email,
                'role' => 'organizer',
                'type' => 'adult',
                'status' => 'confirmed',
            ]);
        }
    }
}
