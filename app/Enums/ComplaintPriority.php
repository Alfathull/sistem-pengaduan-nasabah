<?php

namespace App\Enums;

/**
 * Prioritas tiket pengaduan yang ditetapkan CS saat validasi.
 */
enum ComplaintPriority: string
{
    case Rendah = 'rendah';
    case Sedang = 'sedang';
    case Tinggi = 'tinggi';

    /**
     * Label Bahasa Indonesia untuk ditampilkan di UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Rendah => 'Rendah',
            self::Sedang => 'Sedang',
            self::Tinggi => 'Tinggi',
        };
    }

    /**
     * Warna badge Flux untuk prioritas ini.
     */
    public function color(): string
    {
        return match ($this) {
            self::Rendah => 'zinc',
            self::Sedang => 'amber',
            self::Tinggi => 'red',
        };
    }

    /**
     * Opsi untuk dropdown: value => label.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
