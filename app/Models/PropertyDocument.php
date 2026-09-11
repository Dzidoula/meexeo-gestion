<?php
// app/Models/PropertyDocument.php
namespace App\Models;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PropertyDocument extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'type', 'path', 'original_name', 'verified'];

    protected function casts(): array
    {
        return ['type' => DocumentType::class, 'verified' => 'boolean'];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    protected function url(): Attribute
    {
        return Attribute::get(fn () => Storage::disk('public')->url($this->path));
    }
}
