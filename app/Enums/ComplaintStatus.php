<?php

namespace App\Enums;

/**
 * Status siklus hidup tiket pengaduan.
 *
 * Alur: Baru -> Divalidasi -> Diproses -> Dieskalasi -> Selesai.
 * Ditolak dapat terjadi dari Baru atau Divalidasi (wajib alasan).
 */
enum ComplaintStatus: string
{
    case Baru = 'baru';
    case Divalidasi = 'divalidasi';
    case Diproses = 'diproses';
    case Dieskalasi = 'dieskalasi';
    case Selesai = 'selesai';
    case Ditolak = 'ditolak';

    /**
     * Label Bahasa Indonesia untuk ditampilkan di UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Baru => 'Baru',
            self::Divalidasi => 'Divalidasi',
            self::Diproses => 'Diproses',
            self::Dieskalasi => 'Dieskalasi',
            self::Selesai => 'Selesai',
            self::Ditolak => 'Ditolak',
        };
    }

    /**
     * Warna badge Flux untuk status ini.
     */
    public function color(): string
    {
        return match ($this) {
            self::Baru => 'sky',
            self::Divalidasi => 'blue',
            self::Diproses => 'amber',
            self::Dieskalasi => 'purple',
            self::Selesai => 'green',
            self::Ditolak => 'red',
        };
    }

    /**
     * Apakah status ini bersifat final (tiket sudah ditutup)?
     */
    public function isClosed(): bool
    {
        return in_array($this, [self::Selesai, self::Ditolak], true);
    }

    /**
     * Daftar status yang dapat dipilih dari status saat ini oleh CS.
     *
     * @return array<int, self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Baru => [self::Divalidasi, self::Ditolak],
            self::Divalidasi => [self::Diproses, self::Ditolak],
            self::Diproses => [self::Dieskalasi, self::Selesai],
            self::Dieskalasi => [self::Selesai],
            self::Selesai, self::Ditolak => [],
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
