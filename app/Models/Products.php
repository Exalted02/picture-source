<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;protected $fillable = [
        'product_code',
        'artist_id',
        'category',
        'subcategory',
		'name',
        'image',
        'description',
        'moulding_description',
        'status',
    ];
}
