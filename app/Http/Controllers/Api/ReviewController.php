<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Reviews;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function give_review(Request $request)
	{
		//echo 'hello '; die;
		//echo "<pre>";print_r($request->all());die;
		if(Auth::guard('sanctum')->check()) {
			$user_id = Auth::guard('sanctum')->user()->id;
			
			$validator = Validator::make($request->all(), [
				'product_id' => ['required'],
			]);
			
			if ($validator->fails()) {
				return response()->json([
					'errors' => $validator->errors(),
					'status' => 600,
				]);
			}
			
			$exists = Products::where('id',$request->product_id)->where('status',1)->exists();
			if($exists)
			{
				$reviewExists = Reviews::where('product_id',$request->product_id)->where('user_id',$user_id)->exists();
				if($reviewExists)
				{
					if($request->rating <=5)
					{
						$id = Reviews::where('product_id',$request->product_id)->where('user_id',$user_id)->first()->id;
						$model = Reviews::find($id);
						$model->user_id = $user_id;
						$model->product_id = $request->product_id;
						if(!empty($request->rating))
						{
							$model->rating = $request->rating;
						}
						
						if(!empty($request->comment))
						{
							$model->comment = $request->comment;
						}
						$model->updated_at = now();
						$model->save();
						
                      	if($request->rating){
                          $response = [
                              'status' => 200,
                              'message' => 'You rated successfully',
                          ];
                        }else{
                          $response = [
                              'status' => 200,
                              'message' => 'You have successfully submitted your review',
                          ];
                        }
					}
					else{
						$response = [
							'status' => 400,
							'message' => 'Rating less than or equal to 5',
						];
					}
					
				}
				else{
					if($request->rating <=5)
					{
						$model = new Reviews();
						$model->user_id = $user_id;
						$model->product_id = $request->product_id;
						$model->rating = $request->rating ?? null;
						$model->comment = $request->comment ?? null;
						$model->created_at = now();
						$model->save();
						
						if($request->rating){
                          $response = [
                              'status' => 200,
                              'message' => 'You rated successfully',
                          ];
                        }else{
                          $response = [
                              'status' => 200,
                              'message' => 'You have successfully submitted your review',
                          ];
                        }
					}
					else{
						$response = [
							'status' => 400,
							'message' => 'Rating less than or equal to 5',
						];
					}
				}
				
				
			}
			else{
				$response = [
					'status' => 400,
					'message' => 'Product not found',
				];
			}
		}
		else 
		{
			return response()->json(['status'=> 401 , 'message' => 'Please login']);
		}
		
		return $response;
	}
	public function count_total_rating(Request $request)
	{
		$exists = Reviews::where('product_id', $request->product_id)->exists();
		if($exists)
		{
		    // total rating
			$total_rating = Reviews::where('product_id', $request->product_id)->whereNotNull('rating')->sum('rating');
			
			$response = [
					'status' => 200,
					'total_rating' => $total_rating,
				];
		}
		else{
			$response[] = [
					'status'  => 400,
					'message' => 'No record found',
				];
		}
		return $response;
	}
	public function count_total_review(Request $request)
	{
		$exists = Reviews::where('product_id', $request->product_id)->exists();
		if($exists)
		{
			$count_reviews = Reviews::where('product_id', $request->product_id)
			->whereNotNull('comment')->count();
			$response = [
					'status' => 200,
					'total_reviews' => $count_reviews,
				];
		}
		else{
			$responseData[] = [
					'status'  => 400,
					'message' => 'No record found',
				];
		}
		return $response;
	}
	public function total_avg_rating_product(Request $request)
	{
		$exists = Reviews::where('product_id', $request->product_id)->exists();
		if($exists)
		{
			$average_rating = Reviews::where('product_id', $request->product_id)
				->whereNotNull('rating')->avg('rating');

			//$average_rating = round($average_rating, 2);
			//$average_rating = ceil($average_rating, 2);
			/*if ($average_rating) {
				$fraction = $average_rating - floor($average_rating);
				if ($fraction > 0.5) {
					$average_rating = ceil($average_rating);
				} elseif ($fraction == 0.5) {
					$average_rating = floor($average_rating) + 0.5;
				} else {
					$average_rating = floor($average_rating) + 0.1 * round($fraction * 10); 
				}
			}*/
			
			$average_rating = round($average_rating, 1);
			if (($average_rating * 10) % 10 >= 5) {
				$average_rating = ceil($average_rating);
			}
			
			$response = [
						'status' => 200,
						'average_rating' => $average_rating,
					];
		}
		else{
			$responseData[] = [
					'status'  => 400,
					'message' => 'No record found',
				];
		}
		return $response;

	}
	public function percentage_cal_rating_products(Request $request)
	{
		$exists = Reviews::where('product_id', $request->product_id)->exists();
		$responseData = array();
		if($exists)
		{
			$products = Reviews::select('product_id', 'rating', DB::raw('COUNT(*) as count'))
			->where('product_id', $request->product_id)->groupBy('product_id', 'rating') // Group by product_id and rating
			->get();

			// Prepare results with percentages
				$result = [];
			foreach ($products as $product) {
				$totalRatings = Reviews::where('product_id', $product->product_id)
				->count();

				if ($totalRatings > 0) {
					$percentage = round(($product->count / $totalRatings) * 100, 1);
					
					if (($percentage * 10) % 10 >= 5) {
						$percentage = ceil($percentage);
					}
					
					$result[$product->product_id][] = [
						'rating' => $product->rating,
						'percentage' => $percentage,
					];
				}
			}
			
			
			foreach ($result as $productId => $ratings) {
				$ratingDetails = [];
				foreach ($ratings as $rating) {
					$percentage = $rating['percentage'];

					$ratingDetails[] = [
						'rating' => $rating['rating'],
						'percentage' => $percentage .'%',
					];
				}

				$responseData[] = [
					'status'  => 200,
					//'product_id' => $productId,
					'ratings' => $ratingDetails,
				];
			}
			;
		}
		else{
			$responseData[] = [
					'status'  => 400,
					'message' => 'No record found',
				];
		}
		
		return $responseData;
	}
}
