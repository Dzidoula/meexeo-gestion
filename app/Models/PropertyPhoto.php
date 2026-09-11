<?php
// app/Models/PropertyPhoto.php
namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PropertyPhoto extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'path', 'is_primary', 'position'];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean', 'position' => 'integer'];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /** Toujours via Storage::url() : le disque public est servi sous /storage. */
    protected function url(): Attribute
    {
        return Attribute::get(fn () => Storage::disk('public')->url($this->path));
    }
}
