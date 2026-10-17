<?php

namespace Database\Factories;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\ComplaintHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ComplaintHistory>
 */
class ComplaintHistoryFactory extends Factory
{
    protected $model = ComplaintHistory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'complaint_id' => Complaint::factory(),
            'user_id' => User::factory(),
            'department_id' => null,
            'status' => ComplaintStatus::Baru->value,
            'follow_up' => fake()->sentence(),
        ];
    }
}
