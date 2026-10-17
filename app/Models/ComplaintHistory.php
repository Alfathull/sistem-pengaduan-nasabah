<?php

namespace App\Models;

use App\Enums\ComplaintStatus;
use Database\Factories\ComplaintHistoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $complaint_id
 * @property int|null $user_id
 * @property int|null $department_id
 * @property ComplaintStatus $status
 * @property string|null $follow_up
 * @property Carbon $created_at
 */
class ComplaintHistory extends Model
{
    /** @use HasFactory<ComplaintHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'user_id',
        'department_id',
        'status',
        'follow_up',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ComplaintStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Complaint, $this>
     */
    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    /**
     * Petugas yang mencatat perubahan ini.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
