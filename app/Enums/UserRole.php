<?php

namespace App\Enums;

/**
 * Role pengguna sistem SiPeNa.
 */
enum UserRole: string
{
    case Nasabah = 'nasabah';
    case Cs = 'cs';
    case Pimpinan = 'pimpinan';

    /**
     * Label Bahasa Indonesia untuk ditampilkan di UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Nasabah => 'Nasabah',
            self::Cs => 'Customer Service',
            self::Pimpinan => 'Pimpinan',
        };
    }

    /**
     * Daftar nilai semua role.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $role): string => $role->value, self::cases());
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
