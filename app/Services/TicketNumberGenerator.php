<?php

namespace App\Services;

use App\Models\Complaint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Menghasilkan nomor tiket unik dengan format PGD-YYYYMMDD-XXXX.
 */
class TicketNumberGenerator
{
    /**
     * Buat nomor tiket unik untuk tanggal tertentu (default: hari ini).
     */
    public function generate(?Carbon $date = null): string
    {
        $date ??= Carbon::now();
        $datePart = $date->format('Ymd');
        $prefix = "PGD-{$datePart}-";

        // Hitung tiket pada tanggal yang sama untuk menentukan urutan berikutnya.
        $count = Complaint::query()
            ->where('ticket_number', 'like', $prefix.'%')
            ->count();

        do {
            $sequence = Str::padLeft((string) (++$count), 4, '0');
            $ticketNumber = $prefix.$sequence;
        } while (Complaint::query()->where('ticket_number', $ticketNumber)->exists());

        return $ticketNumber;
    }
}
