<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ParticipantStatus: string implements HasLabel
{
    case New = 'new';
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Rejected = 'rejected';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::New => __('New'),
            self::Pending => __('Pending'),
            self::Confirmed => __('Confirmed'),
            self::Rejected => __('Rejected'),
        };
    }
}
