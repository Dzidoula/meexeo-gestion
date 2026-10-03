<?php
// app/Models/PortalDocument.php
namespace App\Models;

use App\Enums\PortalDocumentCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortalDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'lease_id', 'category',
        'path', 'original_name', 'size', 'period', 'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'category'  => PortalDocumentCategory::class,
            'period'    => 'date',
            'issued_at' => 'datetime',
            'size'      => 'integer',
        ];
    }

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function lease(): BelongsTo  { return $this->belongsTo(Lease::class); }

    /**
     * Pas d'attribut url() ici, contrairement à PropertyDocument : une quittance
     * est une pièce financière nominative, servie par une route authentifiée
     * qui vérifie la propriété, jamais par une URL publique devinable.
     */
    public function getSizeLabelAttribute(): string
    {
        if ($this->size <= 0) {
            return '—';
        }

        return $this->size < 1048576
            ? number_format($this->size / 1024, 1, '.', ' ').' KB'
            : number_format($this->size / 1048576, 1, '.', ' ').' MB';
    }
}
