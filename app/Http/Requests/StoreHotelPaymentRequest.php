<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHotelPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer', 'min:0'],
            'paid_on' => ['required', 'date'],
            'method' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Indiquez le montant du paiement.',
            'amount.integer' => 'Le montant doit être un entier en francs CFA.',
            'amount.min' => 'Le montant ne peut pas être négatif.',
            'paid_on.required' => 'Indiquez la date du paiement.',
        ];
    }
}
