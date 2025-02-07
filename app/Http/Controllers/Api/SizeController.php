<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use File;

class SizeController extends Controller
{
    public function size_list(Request $request)
    {
		$size_data = Size::where('status',1)->get();
		$data = [];
		foreach($size_data as $size)
		{
			$data[] = [
				'id' => $size->id,
				'size' => $size->size,
			];
		}
		$response = [
			'status' => 200,
			'data' => $data,
		];
		return $response;
    }
	
}
