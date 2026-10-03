<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenantMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'sender', 'user_id', 'parent_id',
        'subject', 'body', 'read_at',
    ];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function author(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function parent(): BelongsTo { return $this->belongsTo(self::class, 'parent_id'); }
    public function replies(): HasMany   { return $this->hasMany(self::class, 'parent_id')->oldest(); }

    /** Racines de fil : un message sans parent. */
    public function scopeThreads(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /** Non lu ne concerne que ce qui vient du gestionnaire : le locataire a lu ce qu'il écrit. */
    public function scopeUnreadByTenant(Builder $query): Builder
    {
        return $query->where('sender', 'manager')->whereNull('read_at');
    }

    public function isFromManager(): bool
    {
        return $this->sender === 'manager';
    }

    public function getAuthorNameAttribute(): string
    {
        return $this->isFromManager()
            ? ($this->author?->name ?? 'MEEXEO IMMOBILIER')
            : ($this->tenant?->fullName ?? 'Vous');
    }
}
