<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ParticipantType: string implements HasLabel
{
    case Adult = 'adult';
    case Child = 'child';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Adult => __('Adult'),
            self::Child => __('Child'),
        };
    }
}
