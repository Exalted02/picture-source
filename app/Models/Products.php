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
        'size',
        'color',
        'orientation',
        'length',
        'width',
        'depth',
		'name',
		'price',
		'wholesale_price',
        'image',
        'description',
        'moulding_description',
        'status',
    ];
	
	public function get_category()
	{
		return $this->hasMany(Category::class, 'id','category');
	}
	public function get_subcategory()
	{
		return $this->hasMany(Subcategory::class, 'id','subcategory');
	}
	public function get_artist()
	{
		return $this->hasMany(Artists::class, 'id','artist_id');
	}
	public function get_orientation()
	{
		return $this->hasOne(Orientation::class, 'id','orientation');
	}
	public function get_size()
	{
		return $this->hasMany(Size::class, 'id','size');
	}
	public function get_color()
	{
		return $this->hasMany(Color::class, 'id','color');
	}
}
