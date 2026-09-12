<?php
namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_id', 'month', 'paid_on', 'amount', 'method', 'reference', 'proof_path', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'date',
            'paid_on' => 'date',
            'amount' => 'integer',
            'method' => PaymentMethod::class,
        ];
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    protected function url(): Attribute
    {
        return Attribute::get(fn () => Storage::disk('public')->url($this->proof_path));
    }
}
