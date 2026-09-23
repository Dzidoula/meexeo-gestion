<?php
namespace App\Models;

use App\Enums\HotelStayStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HotelStay extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_room_id', 'guest_name', 'guest_phone', 'arrival_date', 'departure_date',
        'total_amount', 'deposit_amount', 'status', 'checked_in_at', 'checked_out_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'arrival_date' => 'date',
            'departure_date' => 'date',
            'total_amount' => 'integer',
            'deposit_amount' => 'integer',
            'status' => HotelStayStatus::class,
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(HotelRoom::class, 'hotel_room_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(HotelPayment::class);
    }
}
