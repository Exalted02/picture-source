<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Artists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use File;

class ArtistController extends Controller
{
    public function get_artist_list(Request $request)
	{
		$interval = config('custom.API_ARTIST_INTERVAL');
		$APP_URL = env('APP_URL');
		$data = [];
		$page = $request->page ?? 1;
      	$offset = ($page - 1) * $interval;
		
		$exists = Artists::where('status', '=', 1)->exists();
		if($exists)
		{
			$artists = Artists::where('status', '=', 1)->skip($offset)->take($interval)->get();
			foreach ($artists as $val) {
				$data[] = [
						'artist_id' => $val->id,
						'name' => $val->name,
						'image' => $val->image ? $APP_URL.'/uploads/artist/'. $val->id .'/'.$val->image : $APP_URL.'/noimage.png',
					];
				}

			$response = [
				'data' => $data,
				'status' => 200,
			];
		}
		else{
			$response = [
				'response' => 'No records found',
				'status' => 400,
			];
		}

		return $response;
	}
}
