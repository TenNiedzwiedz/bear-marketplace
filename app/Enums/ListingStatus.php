<?php

namespace App\Enums;

enum ListingStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Ended = 'ended';
    case Hidden = 'hidden';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Robocze',
            self::Active => 'Aktywne',
            self::Ended => 'Zakończone',
            self::Hidden => 'Ukryte',
        };
    }
}
