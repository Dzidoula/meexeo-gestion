<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelGallery extends Model
{
    protected $fillable = ['image_path', 'title', 'category', 'external_source', 'external_id'];

    public function getDisplayUrlAttribute(): string
    {
        if ($this->external_source === 'residence_touvalem') {
            return 'https://residencetouvalem.com/storage/' . $this->image_path;
        }

        return asset('storage/' . $this->image_path);
    }
}
