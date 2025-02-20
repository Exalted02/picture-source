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
	
	public function wishlist_details()
	{
		return $this->hasMany(Wishlist_items::class, 'wishlist_id', 'id');
	}
	public function user_details()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}
}
