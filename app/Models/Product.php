<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'description', 'price', 'stock_quantity'];

    protected function casts(): array
    {
        return ['price' => 'integer', 'stock_quantity' => 'integer'];
    }

    protected static function booted(): void
    {
        // Supprimer les fichiers physiques avant que la contrainte de clé
        // étrangère ne supprime les enregistrements ProductPhoto en cascade —
        // sinon les fichiers restent orphelins sur le disque.
        static::deleting(function (Product $product) {
            foreach ($product->photos as $photo) {
                Storage::disk('public')->delete($photo->path);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ProductPhoto::class)->orderBy('position')->orderBy('id');
    }
}
