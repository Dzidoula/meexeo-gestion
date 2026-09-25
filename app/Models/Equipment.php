<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'quantity_total'];

    protected function casts(): array
    {
        return ['quantity_total' => 'integer'];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(EventEquipmentReservation::class);
    }
}
