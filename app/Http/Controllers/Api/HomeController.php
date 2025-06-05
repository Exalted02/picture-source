<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Artists;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Media;
use App\Models\Home_gallery_images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use File;

class HomeController extends Controller
{
    
	public function category_list(Request $request)
	{
		$APP_URL = env('APP_URL');
		$interval = config('custom.API_HOME_CATEGORY_INTERVAL');
		$data = [];
		$peginate = ($request->peginate -1);
		$exists = Category::where('status', '=', 1)->exists();
		if($exists)
		{
			//$categories = Category::where('status', '=', 1)->get();
			$categories = Category::where('status', '=', 1)->orderBy('name', 'ASC')
			->limit($interval)->get();
			foreach ($categories as $val) {
				// Retrieve a random media associated with the current category
				$media = Media::where('media_source_id', $val->id)
					->where('media_type', 1)
					->inRandomOrder()
					->first();

				$data[] = [
					'id' => $val->id,
					'name' => $val->name,
					'featured' => $val->is_feature==1 ? true : false,
					'image' => $media ? $APP_URL.'/uploads/category/'. $val->id .'/gallery/thumbs/'.$media->image : $APP_URL.'/noimage.png', // Handle cases where no media is found
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

		//
	}
	public function artist_list(Request $request)
	{
		$APP_URL = env('APP_URL');
		$data = [];
		$interval = config('custom.API_HOME_ARTIST_INTERVAL');
		
		$exists = Artists::where('status', '=', 1)->exists();
		if($exists)
		{
			$artists = Artists::where('status', '=', 1)->orderBy('name', 'ASC')->limit($interval)->get();
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
	/*public function home_image_list(Request $request)
	{
		$APP_URL = env('APP_URL');
		$data = [];
		
		$exists = Home_gallery_images::exists();
		if($exists)
		{
			$artists = Home_gallery_images::get();
			foreach ($artists as $val) {
				$data[] = [
					'name' => $val->name,
					'image' => $val->name ? $APP_URL.'/uploads/home/'.$val->name : $APP_URL.'/noimage.png',
				];
			}

			$response = [
				'data' => $data,
				'status' => 200,
			];
		}
		else{
			$data[] = [
				'name' => 'noimage.png',
				'image' => $APP_URL.'/noimage.png',
			];
			$response = [
				'data' => $data,
				'status' => 200,
			];
		}

		return $response;
	}*/
	public function home_image_list(Request $request)
	{
		$APP_URL = env('APP_URL');
		$data = [];

		// Get up to 9 images from DB
		$images = Home_gallery_images::take(9)->get();

		foreach ($images as $val) {
			$data[] = [
				'name' => $val->name,
				'image' => $val->name ? $APP_URL . '/uploads/home/' . $val->name : $APP_URL . '/noimage.png',
			];
		}

		// If less than 9, fill the rest with noimage
		$remaining = 9 - count($data);
		for ($i = 0; $i < $remaining; $i++) {
			$data[] = [
				'name' => 'noimage.png',
				'image' => $APP_URL . '/noimage.png',
			];
		}

		return [
			'data' => $data,
			'status' => 200,
		];
	}

}
