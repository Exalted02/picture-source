<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    use HasFactory;
	protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'comment',
    ];
	
	public function get_reviwer()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
