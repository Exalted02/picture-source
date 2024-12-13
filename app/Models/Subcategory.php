<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;
	protected $fillable = [
        'category_id',
        'sub_category_name',
        'status',
    ];
	
	public function get_category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
}
