<?php
// app/Http/Requests/StoreTenantRequest.php
namespace App\Http\Requests;

use App\Enums\MaritalStatus;
use App\Enums\TenantStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Rôle filtré par le middleware de la route.
    }

    public function rules(): array
    {
        $marital = MaritalStatus::tryFrom((string) $this->input('marital_status'));
        // Le brief exige le conjoint et son contact dès que le locataire est en couple.
        $spouseRule = $marital?->requiresSpouse() ? ['required', 'string', 'max:255'] : ['nullable', 'string', 'max:255'];

        return [
            'last_name' => ['required', 'string', 'max:255'],
            'first_names' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'id_number' => ['nullable', 'string', 'max:60'],
            'marital_status' => ['nullable', Rule::enum(MaritalStatus::class)],
            'occupation' => ['nullable', 'string', 'max:255'],
            'workplace' => ['nullable', 'string', 'max:255'],
            'phone1' => ['required', 'string', 'max:30'],
            'phone2' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'emergency_name' => ['nullable', 'string', 'max:255'],
            'emergency_phone' => ['nullable', 'string', 'max:30'],
            'spouse_name' => $spouseRule,
            'spouse_phone' => $marital?->requiresSpouse() ? ['required', 'string', 'max:30'] : ['nullable', 'string', 'max:30'],
            'status' => ['required', Rule::enum(TenantStatus::class)],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'last_name.required' => 'Renseignez le nom du locataire.',
            'first_names.required' => 'Renseignez les prénoms.',
            'phone1.required' => 'Un premier numéro de téléphone est obligatoire.',
            'spouse_name.required' => "Le nom de l'épouse ou du conjoint est obligatoire pour un locataire en couple.",
            'spouse_phone.required' => 'Le contact du conjoint est obligatoire pour un locataire en couple.',
        ];
    }
}
