<?php

namespace App\Services;

use App\Enums\ComplaintPriority;
use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\ComplaintHistory;
use App\Models\User;
use App\Notifications\ComplaintStatusUpdated;
use Illuminate\Support\Facades\DB;

/**
 * Logika bisnis tiket pengaduan: pembuatan, perubahan status,
 * eskalasi, dan pencatatan riwayat tindak lanjut.
 */
class ComplaintService
{
    public function __construct(
        private readonly TicketNumberGenerator $ticketNumberGenerator,
    ) {}

    /**
     * Buat pengaduan baru dari nasabah dan catat riwayat awal.
     *
     * @param  array{category_id:int, subject:string, description:string, account_type:string}  $data
     */
    public function create(User $nasabah, array $data): Complaint
    {
        return DB::transaction(function () use ($nasabah, $data): Complaint {
            $complaint = Complaint::create([
                'ticket_number' => $this->ticketNumberGenerator->generate(),
                'user_id' => $nasabah->id,
                'category_id' => $data['category_id'],
                'reporter_name' => $nasabah->name,
                'reporter_email' => $nasabah->email,
                'reporter_phone' => $nasabah->phone,
                'account_type' => $data['account_type'],
                'subject' => $data['subject'],
                'description' => $data['description'],
                'status' => ComplaintStatus::Baru,
            ]);

            $this->recordHistory(
                complaint: $complaint,
                actor: $nasabah,
                status: ComplaintStatus::Baru,
                followUp: 'Pengaduan dibuat oleh nasabah.',
            );

            return $complaint;
        });
    }

    /**
     * CS memvalidasi tiket: menetapkan prioritas dan status baru.
     *
     * @param  array{status:string, priority?:string|null, follow_up?:string|null, notes?:string|null, department_id?:int|null, rejection_reason?:string|null, notify?:bool}  $data
     */
    public function updateStatus(Complaint $complaint, User $officer, array $data): Complaint
    {
        return DB::transaction(function () use ($complaint, $officer, $data): Complaint {
            $newStatus = ComplaintStatus::from($data['status']);

            $attributes = ['status' => $newStatus];

            if (array_key_exists('priority', $data) && $data['priority'] !== null && $data['priority'] !== '') {
                $attributes['priority'] = ComplaintPriority::from($data['priority']);
            }

            if (array_key_exists('notes', $data)) {
                $attributes['notes'] = $data['notes'];
            }

            if ($newStatus === ComplaintStatus::Dieskalasi && ! empty($data['department_id'])) {
                $attributes['department_id'] = (int) $data['department_id'];
            }

            if ($newStatus === ComplaintStatus::Ditolak) {
                $attributes['rejection_reason'] = $data['rejection_reason'] ?? null;
            }

            $complaint->update($attributes);

            $this->recordHistory(
                complaint: $complaint,
                actor: $officer,
                status: $newStatus,
                followUp: $this->buildFollowUp($newStatus, $data),
                departmentId: $attributes['department_id'] ?? null,
            );

            if (($data['notify'] ?? false) === true) {
                $this->notifyReporter($complaint);
            }

            return $complaint->refresh();
        });
    }

    /**
     * Catat satu entri riwayat perubahan tiket.
     */
    public function recordHistory(
        Complaint $complaint,
        ?User $actor,
        ComplaintStatus $status,
        ?string $followUp = null,
        ?int $departmentId = null,
    ): ComplaintHistory {
        return $complaint->histories()->create([
            'user_id' => $actor?->id,
            'department_id' => $departmentId,
            'status' => $status,
            'follow_up' => $followUp,
        ]);
    }

    /**
     * Kirim notifikasi in-app ke nasabah pemilik tiket.
     */
    private function notifyReporter(Complaint $complaint): void
    {
        $message = "Status pengaduan {$complaint->ticket_number} diperbarui menjadi \"{$complaint->status->label()}\".";

        $complaint->user->notify(new ComplaintStatusUpdated($complaint, $message));
    }

    /**
     * Susun teks tindak lanjut untuk riwayat berdasarkan status.
     *
     * @param  array<string, mixed>  $data
     */
    private function buildFollowUp(ComplaintStatus $status, array $data): ?string
    {
        if ($status === ComplaintStatus::Ditolak && ! empty($data['rejection_reason'])) {
            return 'Tiket ditolak. Alasan: '.$data['rejection_reason'];
        }

        return $data['follow_up'] ?? null;
    }
}
