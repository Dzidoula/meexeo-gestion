<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHotelRoomTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('hotel_room_types', 'name')->ignore($this->route('hotelRoomType'))],
            'description' => ['nullable', 'string'],
            'base_price' => ['nullable', 'integer', 'min:0'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'bed_count' => ['nullable', 'integer', 'min:0'],
            'bath_count' => ['nullable', 'integer', 'min:0'],
            'area' => ['nullable', 'integer', 'min:0'],
            'amenities' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Donnez un nom au type de chambre.',
            'name.unique' => 'Ce type de chambre existe déjà.',
            'rating.max' => 'La note ne peut pas dépasser 5.',
            'rating.min' => 'La note ne peut pas être négative.',
        ];
    }

    /** Convertit le textarea "une ligne par équipement" en tableau pour le modèle. */
    public function validated($key = null, $default = null): array
    {
        $data = parent::validated($key, $default);

        if (array_key_exists('amenities', $data)) {
            $data['amenities'] = $this->parseAmenities($data['amenities']);
        }

        return $data;
    }

    private function parseAmenities(?string $raw): ?array
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }

        return collect(explode("\n", $raw))
            ->map(fn ($line) => trim($line))
            ->filter(fn ($line) => $line !== '')
            ->values()
            ->all();
    }
}
