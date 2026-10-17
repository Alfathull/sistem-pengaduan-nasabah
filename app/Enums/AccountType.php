<?php

namespace App\Enums;

/**
 * Jenis rekening nasabah pada form pengaduan.
 */
enum AccountType: string
{
    case Tabungan = 'tabungan';
    case Giro = 'giro';
    case Deposito = 'deposito';
    case Pinjaman = 'pinjaman';
    case Lainnya = 'lainnya';

    /**
     * Label Bahasa Indonesia untuk ditampilkan di UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Tabungan => 'Tabungan',
            self::Giro => 'Giro',
            self::Deposito => 'Deposito',
            self::Pinjaman => 'Pinjaman',
            self::Lainnya => 'Lainnya',
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
