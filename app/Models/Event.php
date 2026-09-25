<?php
namespace App\Models;

use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name', 'client_phone', 'start_date', 'end_date', 'venue',
        'budget_total', 'deposit_amount', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'budget_total' => 'integer',
            'deposit_amount' => 'integer',
            'status' => EventStatus::class,
        ];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(EventEquipmentReservation::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(EventPayment::class);
    }
}
