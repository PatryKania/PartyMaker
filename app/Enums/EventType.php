<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum EventType: string implements HasLabel
{
    case Wedding = 'wedding';
    case Birthday = 'birthday';
    case Christening = 'christening';
    case Company = 'company_event';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Wedding => __('Wedding'),
            self::Birthday => __('Birthday'),
            self::Christening => __('Christening'),
            self::Company => __('Company event'),
        };
    }
}
