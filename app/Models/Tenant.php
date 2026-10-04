<?php

namespace App\Models;

use App\Enums\LeaseStatus;
use App\Enums\MaritalStatus;
use App\Enums\TenantStatus;
use App\Support\PhoneNumber;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Tenant extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'last_name', 'first_names', 'birth_date', 'id_number', 'marital_status',
        'occupation', 'workplace', 'phone1', 'phone2', 'email',
        'emergency_name', 'emergency_phone', 'spouse_name', 'spouse_phone',
        'status', 'photo_path', 'notes',
    ];

    // Credential fields are deliberately not fillable: they are only ever written
    // by the OTP methods below, via forceFill, never from request input.

    protected $hidden = ['otp_code', 'remember_token'];

    protected function casts(): array
    {
        return [
            'birth_date'        => 'date',
            'marital_status'    => MaritalStatus::class,
            'status'            => TenantStatus::class,
            'otp_expires_at'    => 'datetime',
            'phone_verified_at' => 'datetime',
        ];
    }

    public function generateOtp(): string
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->forceFill([
            'otp_code'       => $code,
            'otp_expires_at' => now()->addMinutes(10),
        ])->save();

        return $code;
    }

    public function verifyOtp(string $code): bool
    {
        if ($this->otp_expires_at === null || $this->otp_expires_at->isPast()) {
            return false;
        }

        if (! hash_equals((string) $this->otp_code, $code)) {
            return false;
        }

        $this->invalidateOtp();

        return true;
    }

    public function invalidateOtp(): void
    {
        $this->forceFill(['otp_code' => null, 'otp_expires_at' => null])->save();
    }

    /**
     * Stocké canonique (+225XXXXXXXXXX) quel que soit le format saisi — formulaire
     * admin, import, ou seeder. Le même format est recherché à la connexion
     * (LoginController), donc les deux ne peuvent pas diverger.
     */
    protected function phone1(): Attribute
    {
        return Attribute::set(fn (string $value) => PhoneNumber::ivoirianE164($value));
    }

    protected function reference(): Attribute
    {
        return Attribute::get(fn () => 'LOC-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT));
    }

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

    /** Documents publiés au locataire dans son portail — distincts de documents(). */
    public function portalDocuments(): HasMany
    {
        return $this->hasMany(PortalDocument::class)->latest('issued_at');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TenantMessage::class)->latest();
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class)->orderByDesc('start_date');
    }

    public function activeLease(): HasOne
    {
        return $this->hasOne(Lease::class)->where('status', LeaseStatus::Active->value);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Lease::class)->orderByDesc('paid_on');
    }
}
