<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request_account_remove extends Model
{
    use HasFactory;
	protected $fillable = [
        'email',
        'address_type',
        'region',
        'status',
    ];
}
