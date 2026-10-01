<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelFaq extends Model
{
    protected $fillable = ['question', 'answer', 'is_active', 'order', 'external_source', 'external_id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
