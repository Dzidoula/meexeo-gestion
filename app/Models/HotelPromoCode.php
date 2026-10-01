<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelPromoCode extends Model
{
    protected $fillable = [
        'code', 'discount_type', 'discount_value', 'min_total', 'max_uses',
        'uses_count', 'starts_at', 'expires_at', 'is_active',
        'external_source', 'external_id',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'float',
            'min_total' => 'float',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }
}
