<?php

namespace App\Services;

use App\Models\Orientation;

class OrientationService
{
    public function orientationInsertIfNotExists($name)
    {
		$exists = Orientation::where('name', $name)->where('status', '!=', 2)->first();
		if ($exists) {
			return $exists->id;
		}else{
			$insert=new Orientation();
			$insert->name		=	$name;
			$insert->status		=	1;
			$insert->save();
			return $insert->id;
		}
    }
}
