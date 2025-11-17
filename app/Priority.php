<?php

namespace App;

enum Priority: string
{
    case SIMPLE = 'simple';
    case MEDIUM = 'medium';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::SIMPLE => 'Simples',
            self::MEDIUM => 'Média',
            self::URGENT => 'Urgente',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SIMPLE => 'green',
            self::MEDIUM => 'yellow',
            self::URGENT => 'red',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
