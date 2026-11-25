<?php

namespace App\Enums;

/**
 * Marital statuses recorded on Indonesian identity cards (KTP).
 */
enum MaritalStatus: string
{
    case BelumKawin = 'belum_kawin';
    case Kawin = 'kawin';
    case CeraiHidup = 'cerai_hidup';
    case CeraiMati = 'cerai_mati';

    /**
     * Get the human readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::BelumKawin => 'Belum kawin',
            self::Kawin => 'Kawin',
            self::CeraiHidup => 'Cerai hidup',
            self::CeraiMati => 'Cerai mati',
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
            fn (self $status): array => ['value' => $status->value, 'label' => $status->label()],
            self::cases(),
        );
    }
}
