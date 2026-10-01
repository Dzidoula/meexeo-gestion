<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelContactMessage extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'subject', 'message',
        'is_read', 'external_source', 'external_id',
    ];

    protected function casts(): array
    {
        return ['is_read' => 'boolean'];
    }
}
