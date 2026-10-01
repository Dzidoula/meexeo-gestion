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
        'hotel_room_id', 'type', 'guest_name', 'guest_phone', 'guest_email', 'arrival_date', 'departure_date',
        'guests', 'total_amount', 'deposit_amount', 'status', 'confirmation_status', 'checked_in_at', 'checked_out_at', 'notes',
        'special_requests', 'external_source', 'external_id',
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

    /** True for a whole-property privatisation stay, which has no single room. */
    public function isPrivatisation(): bool
    {
        return $this->type === 'privatisation';
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
