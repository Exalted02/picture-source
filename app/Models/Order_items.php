<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_items extends Model
{
    use HasFactory;
	protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_code',
        'quantity',
        'price',
        'color_id',
        'size_id',
    ];
	
	public function order_color()
    {
        return $this->belongsTo(Color::class, 'color_id', 'id');
    }
	public function order_size()
    {
         return $this->belongsTo(Size::class, 'size_id', 'id');
    }
}
