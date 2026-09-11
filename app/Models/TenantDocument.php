<?php
// app/Models/TenantDocument.php
namespace App\Models;

use App\Enums\TenantDocumentType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TenantDocument extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_id', 'type', 'path', 'original_name', 'verified'];

    protected function casts(): array
    {
        return ['type' => TenantDocumentType::class, 'verified' => 'boolean'];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    protected function url(): Attribute
    {
        return Attribute::get(fn () => Storage::disk('public')->url($this->path));
    }
}
