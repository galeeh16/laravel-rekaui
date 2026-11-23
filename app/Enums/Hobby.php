<?php

namespace App\Enums;

enum Hobby: string
{
    case Reading = 'reading';
    case Sports = 'sports';
    case Music = 'music';
    case Cooking = 'cooking';
    case Traveling = 'traveling';
    case Photography = 'photography';
    case Gaming = 'gaming';

    /**
     * Get the human readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Reading => 'Membaca',
            self::Sports => 'Olahraga',
            self::Music => 'Musik',
            self::Cooking => 'Memasak',
            self::Traveling => 'Traveling',
            self::Photography => 'Fotografi',
            self::Gaming => 'Bermain game',
        };
    }

    /**
     * Get every case as an option for select and radio inputs.
     *
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $hobby): array => ['value' => $hobby->value, 'label' => $hobby->label()],
            self::cases(),
        );
    }
}
