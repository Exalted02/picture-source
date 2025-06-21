<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;
	protected $table = 'media';
	protected $fillable = [
        'media_source_id',
        'image',
        'media_type',
        'status',
    ];
}
