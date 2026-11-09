<?php

namespace App\Http\Requests;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'customer_id' => ['required', 'integer', Rule::exists('customers', 'id')],
            'subject' => ['required', 'string', 'min:5', 'max:160'],
            'description' => ['required', 'string', 'min:10', 'max:10000'],
            'priority' => ['required', Rule::enum(TicketPriority::class)],
            'assignee_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
        ];

        if (! $this->isMethod('post')) {
            $rules += [
                'status' => ['required', Rule::enum(TicketStatus::class)],
                'resolution' => ['nullable', 'string', 'max:4000'],
                'note' => ['nullable', 'string', 'max:2000'],
                'version' => ['required', 'integer', 'min:0'],
            ];
        }

        return $rules;
    }
}
