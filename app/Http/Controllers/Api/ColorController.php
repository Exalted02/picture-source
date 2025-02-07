<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use File;

class ColorController extends Controller
{
    public function color_list(Request $request)
    {
		$color_data = Color::where('status',1)->get();
		$data = [];
		foreach($color_data as $color)
		{
			$data[] = [
				'id' => $color->id,
				'color' => $color->color,
			];
		}
		$response = [
			'status' => 200,
			'data' => $data,
		];
		return $response;
    }
	
}
