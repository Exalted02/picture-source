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
		$paginate = $request->page ==1 ? ($request->page-1) : $request->page;
		
		$exists = Artists::where('status', '=', 1)->exists();
		if($exists)
		{
			$artists = Artists::where('status', '=', 1)->skip($paginate)->take($interval)->get();
			foreach ($artists as $val) {
				$data[] = [
						'artist_id' => $val->id,
						'name' => $val->name,
						'image' => $val->image ? $APP_URL.'/uploads/artist/'. $val->id .'/'.$val->image : null,
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
