<?php

namespace App\Policies;

use App\Models\Complaint;
use App\Models\User;

class ComplaintPolicy
{
    /**
     * Nasabah hanya boleh melihat pengaduan miliknya sendiri.
     * CS dan Pimpinan boleh melihat semua pengaduan.
     */
    public function view(User $user, Complaint $complaint): bool
    {
        if ($user->isCs() || $user->isPimpinan()) {
            return true;
        }

        return $user->isNasabah() && $complaint->user_id === $user->id;
    }

    /**
     * Hanya nasabah yang boleh membuat pengaduan.
     */
    public function create(User $user): bool
    {
        return $user->isNasabah();
    }

    /**
     * Hanya CS yang boleh mengelola (mengubah status/eskalasi) tiket.
     * Tiket yang sudah ditutup tidak bisa diubah lagi.
     */
    public function manage(User $user, Complaint $complaint): bool
    {
        return $user->isCs() && ! $complaint->status->isClosed();
    }

    /**
     * Siapa yang boleh melihat daftar tiket (area pengelolaan CS).
     */
    public function manageAny(User $user): bool
    {
        return $user->isCs();
    }

    /**
     * Pimpinan (dan CS) boleh melihat laporan rekapitulasi.
     */
    public function viewReports(User $user): bool
    {
        return $user->isPimpinan() || $user->isCs();
    }
}
