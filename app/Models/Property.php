<?php
// app/Models/Property.php
namespace App\Models;

use App\Enums\LeaseStatus;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'type', 'status', 'city', 'commune', 'district',
        'lot_number', 'block_number', 'rooms', 'area_sqm',
        'monthly_rent', 'deposit', 'latitude', 'longitude', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => PropertyType::class,
            'status' => PropertyStatus::class,
            'rooms' => 'integer',
            'area_sqm' => 'integer',
            'monthly_rent' => 'integer',
            'deposit' => 'integer',
        ];
    }

    /** Dérivée de l'identifiant : jamais stockée, donc jamais désynchronisée. */
    protected function reference(): Attribute
    {
        return Attribute::get(fn () => 'BIEN-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT));
    }

    protected function fullAddress(): Attribute
    {
        return Attribute::get(function () {
            $place = collect([$this->district, $this->commune, $this->city])
                ->filter()
                ->implode(', ');

            $plot = collect([
                $this->lot_number ? "Lot {$this->lot_number}" : null,
                $this->block_number ? "Îlot {$this->block_number}" : null,
            ])->filter()->implode(', ');

            return $plot === '' ? $place : "{$place} — {$plot}";
        });
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PropertyPhoto::class)->orderBy('position')->orderBy('id');
    }

    public function primaryPhoto(): HasOne
    {
        return $this->hasOne(PropertyPhoto::class)->where('is_primary', true);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PropertyDocument::class)->orderBy('type');
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class)->orderByDesc('start_date');
    }

    public function activeLease(): HasOne
    {
        return $this->hasOne(Lease::class)->where('status', LeaseStatus::Active->value);
    }
}
