<?php

namespace App\Notifications;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifikasi in-app (database) ketika status pengaduan nasabah berubah.
 */
class ComplaintStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(
        public Complaint $complaint,
        public string $message,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'complaint_id' => $this->complaint->id,
            'ticket_number' => $this->complaint->ticket_number,
            'status' => $this->complaint->status->value,
            'status_label' => $this->complaint->status->label(),
            'message' => $this->message,
        ];
    }
}
