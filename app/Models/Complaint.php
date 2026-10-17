<?php

namespace App\Models;

use App\Enums\AccountType;
use App\Enums\ComplaintPriority;
use App\Enums\ComplaintStatus;
use Database\Factories\ComplaintFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $ticket_number
 * @property int $user_id
 * @property int $category_id
 * @property int|null $department_id
 * @property string $reporter_name
 * @property string $reporter_email
 * @property string|null $reporter_phone
 * @property AccountType $account_type
 * @property string $subject
 * @property string $description
 * @property ComplaintStatus $status
 * @property ComplaintPriority|null $priority
 * @property string|null $notes
 * @property string|null $rejection_reason
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Complaint extends Model
{
    /** @use HasFactory<ComplaintFactory> */
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'category_id',
        'department_id',
        'reporter_name',
        'reporter_email',
        'reporter_phone',
        'account_type',
        'subject',
        'description',
        'status',
        'priority',
        'notes',
        'rejection_reason',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ComplaintStatus::class,
            'priority' => ComplaintPriority::class,
            'account_type' => AccountType::class,
        ];
    }

    /**
     * Nasabah pemilik pengaduan.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * @return HasMany<ComplaintHistory, $this>
     */
    public function histories(): HasMany
    {
        return $this->hasMany(ComplaintHistory::class)->latest();
    }

    /**
     * Batasi query hanya untuk pengaduan milik user tertentu.
     *
     * @param  Builder<Complaint>  $query
     * @return Builder<Complaint>
     */
    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }
}
