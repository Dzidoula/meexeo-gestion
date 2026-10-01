<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelTestimonial extends Model
{
    protected $fillable = [
        'author_name', 'author_subtitle', 'author_image', 'rating', 'title',
        'content', 'is_active', 'external_source', 'external_id',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'rating' => 'float'];
    }
}
