<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_name' => ['required', 'string', 'max:255'],
            'client_phone' => ['required', 'string', 'max:30'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'venue' => ['required', 'string', 'max:255'],
            'budget_total' => ['required', 'integer', 'min:0'],
            'deposit_amount' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_name.required' => 'Indiquez le nom du client.',
            'client_phone.required' => 'Indiquez le téléphone du client.',
            'end_date.after_or_equal' => 'La date de fin ne peut pas être avant la date de début.',
            'budget_total.min' => 'Le budget ne peut pas être négatif.',
            'deposit_amount.min' => "L'acompte ne peut pas être négatif.",
        ];
    }
}
