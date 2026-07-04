<?php

namespace App\Enums;

enum AccountType: string
{
    case Private = 'private';
    case Company = 'company';

    public function label(): string
    {
        return match ($this) {
            self::Private => 'Osoba prywatna',
            self::Company => 'Firma',
        };
    }
}
