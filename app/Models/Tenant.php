<?php
// app/Models/Tenant.php
namespace App\Models;

use App\Enums\MaritalStatus;
use App\Enums\TenantStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'last_name', 'first_names', 'birth_date', 'id_number', 'marital_status',
        'occupation', 'workplace', 'phone1', 'phone2', 'email',
        'emergency_name', 'emergency_phone', 'spouse_name', 'spouse_phone',
        'status', 'photo_path', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'marital_status' => MaritalStatus::class,
            'status' => TenantStatus::class,
        ];
    }

    protected function reference(): Attribute
    {
        return Attribute::get(fn () => 'LOC-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT));
    }

    /** Prénoms puis nom, comme sur les documents administratifs ivoiriens. */
    protected function fullName(): Attribute
    {
        return Attribute::get(fn () => trim("{$this->first_names} {$this->last_name}"));
    }

    protected function initials(): Attribute
    {
        return Attribute::get(fn () => Str::upper(
            Str::substr($this->first_names, 0, 1).Str::substr($this->last_name, 0, 1)
        ));
    }

    public function documents(): HasMany
    {
        return $this->hasMany(TenantDocument::class)->orderBy('type')->orderBy('id');
    }
}
