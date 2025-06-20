<?php

namespace App\Services;

use App\Models\Artists;

class ArtistService
{
    public function artistInsertIfNotExists($name)
    {
		$exists = Artists::where('name', $name)->where('status', '!=', 2)->first();
		if ($exists) {
			return $exists->id;
		}else{
			$insert=new Artists();
			$insert->name		=	$name;
			$insert->status		=	1;
			$insert->save();
			return $insert->id;
		}
    }
}
