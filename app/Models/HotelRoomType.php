<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class HotelRoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'base_price', 'rating', 'capacity',
        'bed_count', 'bath_count', 'area', 'image', 'images', 'amenities',
        'external_source', 'external_id',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'integer',
            'rating' => 'float',
            'images' => 'array',
            'amenities' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (HotelRoomType $type) {
            $type->slug = Str::slug($type->name);
        });
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(HotelRoom::class);
    }
}
