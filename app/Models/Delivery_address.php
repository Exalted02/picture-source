<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery_address extends Model
{
    use HasFactory;
	protected $fillable = [
        'user_id',
        'address_type',
        'phone_number',
        'latitude',
        'longitude',
        'address',
    ];
}
