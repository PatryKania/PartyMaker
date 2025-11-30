<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ParticipantRole: string implements HasLabel
{
    case Guest = 'guest';
    case Organizer = 'organizer';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Guest => __('Guest'),
            self::Organizer => __('Organizer'),
        };
    }
}
