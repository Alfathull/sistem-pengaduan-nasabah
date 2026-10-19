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
     * Penjelasan singkat arti status untuk nasabah.
     */
    public function description(): string
    {
        return match ($this) {
            self::Baru => 'Pengaduan Anda telah diterima dan menunggu diperiksa oleh petugas.',
            self::Divalidasi => 'Pengaduan Anda telah diperiksa dan dinyatakan valid untuk ditindaklanjuti.',
            self::Diproses => 'Petugas sedang menindaklanjuti pengaduan Anda.',
            self::Dieskalasi => 'Pengaduan Anda diteruskan ke departemen terkait untuk penanganan lanjutan.',
            self::Selesai => 'Pengaduan Anda telah selesai ditangani.',
            self::Ditolak => 'Pengaduan Anda tidak dapat diproses. Silakan lihat alasan penolakan.',
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
     * Urutan tahapan utama alur (tanpa Ditolak) untuk progress stepper.
     *
     * @return array<int, self>
     */
    public static function flow(): array
    {
        return [self::Baru, self::Divalidasi, self::Diproses, self::Dieskalasi, self::Selesai];
    }

    /**
     * Posisi status ini dalam alur utama (0-based). Ditolak mengembalikan -1.
     */
    public function flowIndex(): int
    {
        return match ($this) {
            self::Baru => 0,
            self::Divalidasi => 1,
            self::Diproses => 2,
            self::Dieskalasi => 3,
            self::Selesai => 4,
            self::Ditolak => -1,
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
