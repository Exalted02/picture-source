<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
	protected $fillable = [
        'name',
        'is_feature',
        'status',
    ];
	public function images()
	{
		return $this->hasMany(Media::class, 'media_source_id', 'id');
	}
}
