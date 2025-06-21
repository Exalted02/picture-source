<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function categoryInsertIfNotExists($name)
    {
		$exists = Category::where('name', $name)->where('status', '!=', 2)->first();
		if ($exists) {
			return $exists->id;
		}else{
			$insert=new Category();
			$insert->name		=	$name;
			$insert->is_feature		=	0;
			$insert->status		=	1;
			$insert->save();
			return $insert->id;
		}
    }
}
