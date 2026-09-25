<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventPayment extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'amount', 'paid_on', 'method'];

    protected function casts(): array
    {
        return ['amount' => 'integer', 'paid_on' => 'date'];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
