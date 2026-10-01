<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHotelGalleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'title' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
        ];
    }
}
