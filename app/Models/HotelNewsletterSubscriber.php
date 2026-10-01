<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelNewsletterSubscriber extends Model
{
    protected $fillable = ['email', 'external_source', 'external_id'];
}
