<?php

namespace App\Http\Requests;

use App\Enums\ComplaintPriority;
use App\Enums\ComplaintStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateComplaintStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isCs() ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_column(ComplaintStatus::cases(), 'value'))],
            'priority' => ['nullable', Rule::in(array_column(ComplaintPriority::cases(), 'value'))],
            'department_id' => ['nullable', 'integer', Rule::exists('departments', 'id')],
            'follow_up' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'rejection_reason' => ['nullable', 'string', 'max:2000', 'required_if:status,'.ComplaintStatus::Ditolak->value],
            'notify' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'status' => 'status',
            'priority' => 'prioritas',
            'department_id' => 'departemen',
            'follow_up' => 'tindak lanjut',
            'notes' => 'catatan',
            'rejection_reason' => 'alasan penolakan',
        ];
    }
}
