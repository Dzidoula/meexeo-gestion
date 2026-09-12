<?php
namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Rôle filtré par le middleware de la route.
    }

    public function rules(): array
    {
        return [
            'lease_id' => ['required', 'exists:leases,id'],
            'month' => ['required', 'date'],
            'paid_on' => ['required', 'date', 'before_or_equal:today'],
            // Le franc CFA n'a pas de décimales : entier obligatoire, strictement positif.
            'amount' => ['required', 'integer', 'min:1'],
            'method' => ['required', Rule::enum(PaymentMethod::class)],
            'reference' => ['nullable', 'string', 'max:255'],
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'lease_id.required' => 'Choisissez le bail concerné.',
            'amount.integer' => 'Le montant doit être un nombre entier de francs CFA.',
            'amount.min' => 'Le montant doit être supérieur à zéro.',
            'paid_on.before_or_equal' => 'La date de paiement ne peut pas être future.',
            'proof.required' => 'La preuve de paiement est obligatoire.',
        ];
    }
}
