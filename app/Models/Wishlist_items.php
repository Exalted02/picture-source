<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist_items extends Model
{
    use HasFactory;
	
	public function order_color()
    {
        return $this->belongsTo(Color::class, 'color_id', 'id');
    }
	public function order_size()
    {
         return $this->belongsTo(Size::class, 'size_id', 'id');
    }
}
