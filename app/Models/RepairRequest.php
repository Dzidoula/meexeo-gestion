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

    private bool $pendingTicket = false;

    protected static function booted(): void
    {
        // ticket_no is NOT NULL + unique, so the insert needs a value that cannot
        // collide; the real number is derived from the row id right after, which
        // concurrent submissions cannot duplicate the way read-then-increment would.
        static::creating(function (self $repair) {
            $repair->pendingTicket = ! $repair->ticket_no;

            if ($repair->pendingTicket) {
                $repair->ticket_no = 'TMP-'.bin2hex(random_bytes(6));
            }
        });

        static::created(function (self $repair) {
            if ($repair->pendingTicket) {
                $repair->ticket_no = 'REP-'.str_pad((string) $repair->id, 3, '0', STR_PAD_LEFT);
                $repair->saveQuietly();
            }
        });
    }

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function lease(): BelongsTo  { return $this->belongsTo(Lease::class); }
}
