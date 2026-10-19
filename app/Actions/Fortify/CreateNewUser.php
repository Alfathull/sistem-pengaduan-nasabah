<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Enums\AccountType;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user (nasabah).
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'phone' => ['required', 'string', 'regex:/^[0-9]+$/', 'min:8', 'max:15'],
            'account_type' => ['required', Rule::in(array_column(AccountType::cases(), 'value'))],
            'password' => $this->passwordRules(),
        ], [
            'phone.regex' => 'Nomor HP hanya boleh berisi angka.',
        ], [
            'phone' => 'nomor HP',
            'account_type' => 'jenis rekening',
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'account_type' => $input['account_type'],
            'password' => $input['password'],
        ]);

        $user->assignRole(UserRole::Nasabah->value);

        return $user;
    }
}
