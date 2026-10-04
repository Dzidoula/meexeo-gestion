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

        // Posé sur le modèle plutôt que dans un écran : le statut sera changé
        // depuis le back-office comme depuis n'importe quel autre chemin.
        // « Réparé » n'a volontairement pas de message : le cahier des charges
        // (§11) ne prévoit que « en cours » et « clôturé ».
        static::updated(function (self $repair) {
            if (! $repair->wasChanged('status')) {
                return;
            }

            $notice = match ($repair->status) {
                'en_cours' => \App\Notifications\PortalNotice::repairInProgress($repair),
                'cloture'  => \App\Notifications\PortalNotice::repairClosed($repair),
                default    => null,
            };

            if ($notice) {
                $repair->tenant?->notify($notice);
            }
        });
    }

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function lease(): BelongsTo  { return $this->belongsTo(Lease::class); }
}
