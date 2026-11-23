<?php

namespace App\Enums;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';

    /**
     * Get the human readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Male => 'Laki-laki',
            self::Female => 'Perempuan',
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
            fn (self $gender): array => ['value' => $gender->value, 'label' => $gender->label()],
            self::cases(),
        );
    }
}
