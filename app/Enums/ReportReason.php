<?php

namespace App\Enums;

enum ReportReason: string
{
    case Spam = 'spam';
    case Prohibited = 'prohibited';
    case Fraud = 'fraud';
    case Offensive = 'offensive';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Spam => 'Spam lub duplikat',
            self::Prohibited => 'Zakazany przedmiot lub treść',
            self::Fraud => 'Oszustwo lub wyłudzenie',
            self::Offensive => 'Treści obraźliwe',
            self::Other => 'Inny powód',
        };
    }
}
