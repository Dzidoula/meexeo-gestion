<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'quantity_total'];

    protected function casts(): array
    {
        return ['quantity_total' => 'integer'];
    }
}
