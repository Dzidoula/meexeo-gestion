<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelPayment extends Model
{
    use HasFactory;

    protected $fillable = ['hotel_stay_id', 'amount', 'paid_on', 'method'];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'paid_on' => 'date',
        ];
    }

    public function stay(): BelongsTo
    {
        return $this->belongsTo(HotelStay::class, 'hotel_stay_id');
    }
}
