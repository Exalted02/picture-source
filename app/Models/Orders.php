<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;
	protected $fillable = [
        'user_id',
        'retailer_id',
        'delivery_address_id',
        'discount_amount',
        'coupon_discount',
        'shipping_charge',
        'order_total',
        'final_amount',
        'order_type',
        'status',
    ];
	
	public function order_details()
	{
		return $this->hasMany(Order_items::class, 'order_id', 'id');
	}
	 
	public function user_details()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}
	
	public function delivery_address()
	{
		return $this->belongsTo(Delivery_address::class, 'delivery_address_id', 'id');
	}
}
