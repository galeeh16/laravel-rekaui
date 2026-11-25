<?php

namespace App\Enums;

/**
 * Religions recorded on Indonesian identity cards (KTP).
 */
enum Religion: string
{
    case Islam = 'islam';
    case Kristen = 'kristen';
    case Katolik = 'katolik';
    case Hindu = 'hindu';
    case Buddha = 'buddha';
    case Konghucu = 'konghucu';
    case Kepercayaan = 'kepercayaan';

    /**
     * Get the human readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Islam => 'Islam',
            self::Kristen => 'Kristen',
            self::Katolik => 'Katolik',
            self::Hindu => 'Hindu',
            self::Buddha => 'Buddha',
            self::Konghucu => 'Konghucu',
            self::Kepercayaan => 'Kepercayaan terhadap Tuhan YME',
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
            fn (self $religion): array => ['value' => $religion->value, 'label' => $religion->label()],
            self::cases(),
        );
    }
}
