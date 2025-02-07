<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wistlists extends Model
{
    use HasFactory;
	protected $fillable = [
        'user_id',
        'order_id',
        'email_address',
        'relationship',
        'birthdate',
        'aniversary',
        'status',
    ];
}
