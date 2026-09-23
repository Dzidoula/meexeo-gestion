<?php
namespace App\Models;

use App\Enums\HotelRoomStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HotelRoom extends Model
{
    use HasFactory;

    protected $fillable = ['hotel_room_type_id', 'number', 'nightly_rate', 'amenities', 'status'];

    protected function casts(): array
    {
        return [
            'nightly_rate' => 'integer',
            'status' => HotelRoomStatus::class,
        ];
    }

    public function hotelRoomType(): BelongsTo
    {
        return $this->belongsTo(HotelRoomType::class);
    }

    public function stays(): HasMany
    {
        return $this->hasMany(HotelStay::class);
    }
}
