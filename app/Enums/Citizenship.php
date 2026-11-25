<?php

namespace App\Enums;

/**
 * Citizenship, stored as the `WNI` / `WNA` codes (the `penduduk` table defaults to `WNI`).
 */
enum Citizenship: string
{
    case Wni = 'WNI';
    case Wna = 'WNA';

    /**
     * Get the human readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Wni => 'WNI (Warga Negara Indonesia)',
            self::Wna => 'WNA (Warga Negara Asing)',
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
            fn (self $citizenship): array => ['value' => $citizenship->value, 'label' => $citizenship->label()],
            self::cases(),
        );
    }
}
