<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use File;

class CategoryController extends Controller
{
    
	public function getCategories(Request $request)
	{
		/*$categories = Category::where('status', '!=', 2)
			->with(['images' => function ($query) {
				$query->where('media_type', 1);
			}])
			->get();
		$response = [
			'category_list' => $categories,
			'status' => 200,
		];
		return $response;*/ 
      
		$interval = config('custom.API_CATEGORY_INTERVAL');
		$APP_URL = env('APP_URL');
		//$paginate = $request->page ==1 ? ($request->page-1) : $request->page;
      	$page = $request->page ?? 1;
      	$offset = ($page - 1) * $interval;
		$data = [];
      
		//$categories = Category::where('status', '=', 1)->get();
		$exists = Category::where('status', '=', 1)->exists();
		if($exists)
		{
			$categories = Category::where('status', '=', 1)->skip($offset)->take($interval)->get();
          
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

		
	}
	public function get_subcategory_list(Request $request)
	{
		//echo 'helllo '.$request->category_id;die;
		$APP_URL = env('APP_URL');
		$category_id = $request->category_id;
		if($category_id != '')
		{
			$exists = Subcategory::where('category_id',$category_id)->exists();
			if($exists)
			{
				$subcategories = Subcategory::where('category_id',$category_id)->where('status', '=', 1)->get();
				foreach ($subcategories as $val) {
				
					$media = Media::where('media_source_id', $val->id)
						->where('media_type', 2)
						->inRandomOrder()
						->first();

					$data[] = [
						'category_id' => $category_id,
						'subcategory_id' => $val->id,
						'subcategory_name' => $val->sub_category_name,
						'image' => $media ? $APP_URL.'/uploads/subcategory/'. $val->id .'/gallery/thumbs/'.$media->image : $APP_URL.'/noimage.png',
					];
				}
				
				$response = [
					'data' => $data,
					'status' => 200,
				];
			}
			else{
				$response = [
					'message' => 'No record found',
					'status' => 400,
				];
			}
		}
		else{
			$response = [
				'message' => 'No record found',
				'status' => 400,
			];
		}
		return $response;
	}
	public function get_category_by_id(Request $request)
	{
		$category_id = $request->category_id;
		if($category_id != '')
		{
			$exists = Category::where('id',$category_id)->exists();
			if($exists)
			{
				$categories = Category::where('status', '!=', 2)->where('id',$category_id)->first();
				$media_ids = Media::select('id')->where('media_source_id', $category_id)->where('media_type', 1)->inRandomOrder()->take(5)->pluck('id');

				
				$randMedia = rand(1,12);
				
				$media = Media::where('media_source_id',$category_id)->where('media_type',1)->where('id', $media_ids[0])->first();
				
				$data['categories']['id'] = $categories['id'];
				$data['categories']['name'] = $categories['name'];
				$data['categories']['image'] = $media->image;
				
				
				$response = [
					'data' => $data,
					'status' => 200,
				];
			}
			else{
				$response = [
					'message' => 'Category not exist',
					'status' => 400,
				];
			}
		}
		else{
			$response = [
				'message' => 'Please enter category Id',
				'status' => 400,
			];
		}
		return $response;
	}
	
}
