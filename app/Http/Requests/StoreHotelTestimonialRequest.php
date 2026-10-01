<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHotelTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'author_name' => ['required', 'string', 'max:255'],
            'author_subtitle' => ['nullable', 'string', 'max:255'],
            'author_image' => ['nullable', 'url', 'max:255'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $data = parent::validated($key, $default);
        $data['is_active'] = $this->boolean('is_active');

        return $data;
    }
}
