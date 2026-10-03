<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'lease_id', 'type', 'description',
        'urgency', 'status', 'ticket_no', 'photos', 'video_path', 'notes',
    ];

    protected function casts(): array
    {
        return ['photos' => 'array'];
    }

    protected static function booted(): void
    {
        static::creating(function (self $repair) {
            if (! $repair->ticket_no) {
                $last  = self::orderByDesc('id')->first();
                $next  = $last ? ((int) substr($last->ticket_no, 4)) + 1 : 1;
                $repair->ticket_no = 'REP-'.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function lease(): BelongsTo  { return $this->belongsTo(Lease::class); }
}
