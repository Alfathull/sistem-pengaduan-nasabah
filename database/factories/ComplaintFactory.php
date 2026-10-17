<?php

namespace Database\Factories;

use App\Enums\AccountType;
use App\Enums\ComplaintPriority;
use App\Enums\ComplaintStatus;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Complaint>
 */
class ComplaintFactory extends Factory
{
    protected $model = Complaint::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory();

        return [
            'ticket_number' => 'PGD-'.now()->format('Ymd').'-'.Str::padLeft((string) fake()->unique()->numberBetween(1, 9999), 4, '0'),
            'user_id' => $user,
            'category_id' => Category::factory(),
            'department_id' => null,
            'reporter_name' => fake()->name(),
            'reporter_email' => fake()->safeEmail(),
            'reporter_phone' => fake()->numerify('08##########'),
            'account_type' => fake()->randomElement(AccountType::cases())->value,
            'subject' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => ComplaintStatus::Baru->value,
            'priority' => fake()->randomElement(ComplaintPriority::cases())->value,
            'notes' => null,
            'rejection_reason' => null,
        ];
    }

    public function status(ComplaintStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status->value,
        ]);
    }
}
