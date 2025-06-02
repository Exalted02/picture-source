<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Products;
use App\Models\Artists;
use App\Models\Temp_media_galleries;
use App\Models\Media;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Reviews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use File;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function get_product_list(Request $request)
	{
		//echo "<pre>";print_r($request->all());die;
		$APP_URL = env('APP_URL');
		$data = [];

		if($request->category_id !='' || $request->subcategory_id!='' || $request->artist_id !='')
		{
			$exists = Products::where('status', '=', 1)
				->where(function ($query) use ($request) {
					$query->when(!empty($request->category_id), function ($query) use ($request) {
						// $query->Where('category', $request->category_id);
						$query->whereRaw("FIND_IN_SET(?, category)", [$request->category_id]);
					})
					->when(!empty($request->artist_id), function ($query) use ($request) {
						$query->Where('artist_id', $request->artist_id);
					})
					->when(!empty($request->subcategory_id), function ($query) use ($request) {
						$query->Where('subcategory', $request->subcategory_id);
					});
				})
				->exists();



			if($exists)
			{
				$products = Products::where('status', '=', 1)
				->where(function ($query) use ($request) {
					$query->when(!empty($request->category_id), function ($query) use ($request) {
						// $query->Where('category', $request->category_id);
						$query->whereRaw("FIND_IN_SET(?, category)", [$request->category_id]);
					})
					->when(!empty($request->artist_id), function ($query) use ($request) {
						$query->Where('artist_id', $request->artist_id);
					})
					->when(!empty($request->subcategory_id), function ($query) use ($request) {
						$query->Where('subcategory', $request->subcategory_id);
					});
				})
				->get();
				
				//echo "<pre>";print_r($products);die;
				
				foreach($products as $val) {
					$media = Media::where('media_source_id', $val->id)
						->where('media_type', 3)
						->inRandomOrder()
						->first();

					$data[] = [
						'product_id' => $val->id,
						'category_id' => $val->get_category[0]->id,
						'category_name' => $val->get_category[0]->name,
						'subcategory_id' => $val->get_subcategory[0]->id,
						'subcategory_name' => $val->get_subcategory[0]->sub_category_name,
						'artist_id' => $val->get_artist[0]->id,
						'artist_name' => $val->get_artist[0]->name,
						// 'size_id' => $val->get_size[0]->id,
						// 'size_name' => $val->get_size[0]->size,
						'size_id' => '',
						'size_name' => '',
						'color_id' => $val->get_color[0]->id,
						'color_name' => $val->get_color[0]->color,
						'name' => $val->name,
						'product_code' => $val->product_code,
						'price' => $val->price,
						'moulding_description' => strip_tags($val->moulding_description),
						'image' => $media ? $APP_URL.'/uploads/product/'. $val->id .'/gallery/thumbs/'.$media->image : null,
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
		}
		else
		{
			$response = [
				'response' => 'No records found',
				'status' => 400,
			];
		}

		return $response;
	}
	public function get_single_product(Request $request)
	{
		$APP_URL = env('APP_URL');
		$data = [];
		$responseData = [];
		//echo $request->input('product_id'); die;
		//echo $request->product_id;die;
		
		$exists = Products::where('status', '=', 1)->where('id', $request->product_id)->exists();
		if($exists)
		{
			$product = Products::where('status', '=', 1)->where('id', $request->product_id)->first();
			
			$medias = Media::where('media_source_id', $request->product_id)
				->where('media_type', 3)->get();
			$imageArr = array();
			/*foreach($medias as $media)
			{
				$imageArr[] = $APP_URL.'/uploads/product/'. $request->product_id .'/gallery/thumbs/'.$media->image;
			}*/
			
			foreach($medias as $media)
			{
				$imageArr[] = 
				[
					'id'    =>  (string) $media->id,
					'file_path' => $APP_URL.'/uploads/product/'. $request->product_id .'/gallery/thumbs/'.$media->image,
				];
			}
			
			// total rating 
			$total_rating = (int) Reviews::where('product_id', $request->product_id)->whereNotNull('rating')->sum('rating');
			
			//----total review-------
			$count_reviews = (int) Reviews::where('product_id', $request->product_id)
			->whereNotNull('comment')->count();
			//-------total avg rating product------
			$average_rating =  Reviews::where('product_id', $request->product_id)
				->whereNotNull('rating')->avg('rating');
				$average_rating = round($average_rating, 1);
			/*if (($average_rating * 10) % 10 >= 5) {
				$average_rating = ceil($average_rating);
			}
			else{
				$average_rating = ceil($average_rating);
			}*/
			//----------------------
			$existRatings = [];
			$products = Reviews::select('product_id', 'rating', DB::raw('COUNT(*) as count'))
			->where('product_id', $request->product_id)->groupBy('product_id', 'rating') // Group by product_id and rating
			->get();
            
			// Prepare results with percentages
				$result = [];
			foreach ($products as $productVal) {
				$totalRatings = Reviews::where('product_id', $productVal->product_id)
				->count();

				if ($totalRatings > 0) {
					$percentage = round(($productVal->count / $totalRatings) * 100, 1);
					
					if (($percentage * 10) % 10 >= 5) {
						$percentage = ceil($percentage);
					}
					
					$result[$productVal->product_id][] = [
						'rating' => $productVal->rating,
						'percentage' => $percentage,
					];
				}
				
				$existRatings[] = $productVal->rating; // add for not exist rating
			}
			
			
			foreach ($result as $productId => $ratings) {
				$ratingDetails = [];
				foreach ($ratings as $rating) {
					$percentage = $rating['percentage'];
					if(!empty($rating['rating']))
					{
						$ratingDetails[] = [
							'rating' => $rating['rating'],
							'percentage' => (int)$percentage,
						];
					}
				}
			}
			// rating are not present
			$notexistRating = [];
			for($i=1;$i<=5;$i++)
			{
				if(!in_array($i,$existRatings))
				{
					$ratingDetails[] = [
							'rating' => $i,
							'percentage' => 0,
						];
				}
			}
			
			usort($ratingDetails, function ($a, $b) {
				return $a['rating'] <=> $b['rating'];
			});
			
			
			//$percentage_rating_one = $ratingDetails[0]['percentage'] == 0 ? 0.00 :
			
            //----------------------
          	$my_rating = $my_review = '';
          	if(Auth::guard('sanctum')->check()) {
              $my_login_id = Auth::guard('sanctum')->user()->id;
              $my_review_details = Reviews::where('product_id', $request->product_id)->where('user_id', $my_login_id)->first();
              $my_rating = $my_review_details->rating ?? 0;
              $my_review = $my_review_details->comment ?? '';
            }
			
			/*$width = $product->get_size[0]->width * 2.5;
			$height = $product->get_size[0]->height * 2.5;			
			if($width > 300){
				$width_ratio = $width / 300;
				$final_height = $height / $width_ratio;
				$final_width = 300;
			}else if($height > 400 || $final_height > 400){
				$height_ratio = $height / 400;
				$final_width = $width / $height_ratio;
				$final_height = 400;
			}else{
				$final_width = $width;
				$final_height = $height;
			}*/
			// $width = $product->get_size[0]->width * 2.5;
			// $height = $product->get_size[0]->height * 2.5;
			$width = $product->width * 2.5;
			$height = $product->length * 2.5;

			$max_width = 300;
			$max_height = 300;

			$width_ratio = $width / $max_width;
			$height_ratio = $height / $max_height;
			$scale_ratio = max($width_ratio, $height_ratio, 1); // never scale up

			$final_width = $width / $scale_ratio;
			$final_height = $height / $scale_ratio;
			
			$orientation = '';
			if($product->orientation == 1){
				$orientation = 'Portrait';
			}else if($product->orientation == 2){
				$orientation = 'Landscape';
			}else if($product->orientation == 3){
				$orientation = 'Square';
			}
			
			$data = [
				'product_id' => $product->id,
				'category_id' => $product->get_category[0]->id,
				'category_name' => $product->get_category[0]->name,
				//'subcategory_id' => $product->get_subcategory[0]->id,
				//'subcategory_name' => $product->get_subcategory[0]->sub_category_name,
				'artist_id' => $product->get_artist[0]->id,
				'artist_name' => $product->get_artist[0]->name,
				// 'size_id' => $product->get_size[0]->id,
				'size_id' => '',
				// 'size_name' => $product->get_size[0]->size,
				'size_name' => '',
				'orientation' => $orientation,
				'adjust_size' => $product->length.'inch x '.$product->width.'inch x '.$product->depth.'inch',
				// 'size_height' => $product->get_size[0]->height,
				// 'size_width' => $product->get_size[0]->width,
				'size_height' => round($final_height),
				'size_width' => round($final_width),
				'color_id' => $product->get_color[0]->id,
				'color_name' => $product->get_color[0]->color,
				'name' => $product->name,
				'product_code' => $product->product_code,
				'price' => '$ '.$product->price,
				'moulding_description' => $product->moulding_description,
				'total_rating' => $total_rating,
				'total_reviews' => $count_reviews,
				'average_rating' => (string) $average_rating,
				//'percentage_rating' => $ratingDetails,
				'percentage_rating_one' => $ratingDetails[0]['percentage'],
				'percentage_rating_two' => $ratingDetails[1]['percentage'],
				'percentage_rating_three' => $ratingDetails[2]['percentage'],
				'percentage_rating_four' => $ratingDetails[3]['percentage'],
				'percentage_rating_five' => $ratingDetails[4]['percentage'],
              	'my_rating' => (int) $my_rating,
              	'my_review' => $my_review,
				'files' => $imageArr,
			];
			

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
	public function get_product_search(Request $request)
	{
		$interval = config('custom.API_PRODUCT_INTERVAL');
		$size_ids = $request->sizes != '' ? explode(',', $request->sizes) : [];
		$color_ids = $request->colors != '' ? explode(',', $request->colors) : [];
		
		//\Log::info(json_encode($request->all())); 
			
		$data = [];
		$APP_URL = env('APP_URL');
		$page = $request->page ?? 1;
      	$offset = ($page - 1) * $interval;
		$dataArr = Products::query();
		if($request->keyword)
		{
			$dataArr->where('name', 'like', '%' . $request->keyword . '%');
		}
		if($request->category_id)
		{
			// $dataArr->where('category', $request->category_id);
			$dataArr->whereRaw("FIND_IN_SET(?, category)", [$request->category_id]);
		}
		if($request->artist_id)
		{
			$dataArr->where('artist_id', $request->artist_id);
		}
		
		if(!empty($size_ids))
		{
			$dataArr->whereIn('size', $size_ids);
		}
		
		if(!empty($color_ids))
		{
			$dataArr->whereIn('color', $color_ids);
		}
		
		$dataArr->where('status', '=', 1);
		$dataArr->orderBy('name', 'ASC'); 
		$productdata = $dataArr->skip($offset)->take($interval)->get();
		//$productdata = $dataArr->limit($paginate,$interval)->get();
		//$productdata = $dataArr->get();
		//echo "<pre>";print_r($productdata);die;
		//$imageArr = array();
		foreach($productdata as $product)
		{
			$imageArr = array();
			$image_name  = '';
			/*$medias = Media::where('media_source_id', $product->id)
						->where('media_type', 3)->get();
			foreach($medias as $media)
			{
				$imageArr[] = $APP_URL.'/uploads/product/'. $product->id .'/gallery/thumbs/'.$media->image;
			}*/
			
			$medias = Media::where('media_source_id', $product->id)
						->where('media_type', 3)->inRandomOrder()
					->first();
			if(isset($medias->image) && $medias->image != null){
				$image_name = $APP_URL.'/uploads/product/'. $product->id .'/gallery/thumbs/'.$medias->image;
			}else{
				$image_name = $APP_URL.'/noimage.png';
			}
			
			$data[] = [
				'product_id' => $product->id,
				//'category_id' => $product->get_category[0]->id,
				//'category_name' => $product->get_category[0]->name,
				//'subcategory_id' => $product->get_subcategory[0]->id,
				//'subcategory_name' => $product->get_subcategory[0]->sub_category_name,
				'artist_id' => $product->get_artist[0]->id,
				'artist_name' => $product->get_artist[0]->name,
				//'size_id' => $product->get_size[0]->id,
				// 'size_name' => $product->get_size[0]->size,
				'size_name' => '',
				//'color_id' => $product->get_color[0]->id,
				'color_name' => $product->get_color[0]->color,
				'name' => $product->name,
				'product_code' => $product->product_code,
				'price' => $product->price,
				'moulding_description' => strip_tags($product->moulding_description),
				'image' => $image_name,
			];
		}
		
		$response = [
				'data' => $data,
				'status' => 200,
			];
		return $response;
	}
}
