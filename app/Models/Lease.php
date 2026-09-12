<?php
// app/Models/Lease.php
namespace App\Models;

use App\Enums\LeaseStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lease extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id', 'tenant_id', 'start_date', 'expected_end_date', 'actual_end_date',
        'monthly_rent', 'deposit_paid', 'due_day', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'expected_end_date' => 'date',
            'actual_end_date' => 'date',
            'monthly_rent' => 'integer',
            'deposit_paid' => 'integer',
            'due_day' => 'integer',
            'status' => LeaseStatus::class,
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderByDesc('paid_on');
    }

    /** Durée d'occupation en mois, arrêtée à la date de fin réelle ou à aujourd'hui. */
    protected function durationInMonths(): Attribute
    {
        // Carbon 3's diffInMonths() returns a float; cast to keep whole months in the UI.
        return Attribute::get(fn () => (int) $this->start_date->diffInMonths($this->actual_end_date ?? now()));
    }
}
