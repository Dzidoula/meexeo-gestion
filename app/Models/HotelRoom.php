<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelRoom extends Model
{
    use HasFactory;

    protected $fillable = ['hotel_room_type_id'];

    public function hotelRoomType(): BelongsTo
    {
        return $this->belongsTo(HotelRoomType::class);
    }
}
