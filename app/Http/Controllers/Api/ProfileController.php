<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Orders;
use App\Models\Order_items;
use App\Models\Delivery_address;
use App\Models\Media;
use App\Models\Countries;
use App\Models\Genders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use File;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    
	public function view_profile(Request $request)
	{
		//Auth::guard('sanctum')->check()
		
		// $APP_URL = env('APP_URL');
		$APP_URL = url('');
		$data = [];
		$myOrder = [];
		$myWistlist = [];
		$countryList = [];
		
		if (Auth::guard('sanctum')->check()) {
			$user_id = Auth::guard('sanctum')->user()->id;
			$user_active = User::where(['id'=> $user_id, 'status'=>1])->exists();
			if($user_active)
			{
				// myorder
				$OrderExists  = Orders::where('user_id',$user_id)->where('order_type',0)->exists();
				if($OrderExists)
				{
					$orders = Orders::with('order_details.order_color', 'order_details.order_size')->where('user_id',$user_id)->where('order_type',0)->get();
					//echo "<pre>";print_r($orders);die;
					foreach($orders as $order)
					{
						foreach($order->order_details as $items)
						{
						    $existsMedia = Media::where('media_source_id',$items->product_id)->where('media_type',3)->exists();
							if($existsMedia)
							{
								$pimage = Media::where('media_source_id',$items->product_id)->where('media_type',3)->first()->image;
								$myOrder[] = [
									'image' => $pimage ? $APP_URL.'/uploads/product/'. $items->product_id .'/gallery/thumbs/'.$pimage : null,
									/*'product_name' => $items->product_name,
									'quantity' => $items->quantity,
									'price_per_quantity' => $items->price,
									'color' => $items->order_color->color,
									'size' => $items->order_size->size,
									'total_amount' => $order->final_amount,*/
								];
							}
						}
					}
				}
				
				// mywistlist
				$OrderWistlistExists  = Orders::where('user_id',$user_id)->where('order_type',1)->exists();
				if($OrderWistlistExists)
				{
					$ordersWistlist = Orders::with('order_details.order_color', 'order_details.order_size')->where('user_id',$user_id)->where('order_type',1)->get();
					//echo "<pre>";print_r($orders);die;
					foreach($ordersWistlist as $wistlist)
					{
						foreach($wistlist->order_details as $items)
						{
							$existsMedia = Media::where('media_source_id',$items->product_id)->where('media_type',3)->exists();
							if($existsMedia)
							{
								$wimage = Media::where('media_source_id',$items->product_id)->where('media_type',3)->first()->image;
								$myWistlist[] = [
									'image' => $wimage ? $APP_URL.'/uploads/product/'. $items->product_id .'/gallery/thumbs/'.$wimage : null,
									/*'product_name' => $items->product_name,
									'quantity' => $items->quantity,
									'price_per_quantity' => $items->price,
									'color' => $items->order_color->color,
									'size' => $items->order_size->size,
									'total_amount' => $wistlist->final_amount,*/
								];
							}
						}
					}
				}
				
				//----- country list ---
				if(!empty($request->countrylist))
				{
					$country_data = Countries::all();
					foreach($country_data as $country)
					{
						$countryList[] = [
							'id' => $country->id,
							'country' => $country->name,
						];
					}
				}
				//------
				$user_data = User::where(['id'=> $user_id, 'status'=>1])->first();
				//echo "<pre>";print_r($user_data);die;
				$data = [
						'image' => $user_data->profile_image ? $APP_URL.'/uploads/profile/'. $user_id .'/'.$user_data->profile_image : null,
						'name' => $user_data->name,
						'country' => $user_data->country ?? null,
						'state' => $user_data->state ?? null,
						'myorders' => $myOrder,
						'wishtlist' => $myWistlist,
						'country_list' => $countryList,
					];
					$response = [
					'status' => 200,
					'data' => $data,
					];
			}
			else{
				return response()->json(['status'=> 400 , 'message' => 'User inactive']);
			}
		} else {
			return response()->json(['status'=> 401 , 'message' => 'Please login']);
		}
		
		return $response;
	}
	public function profile_image_upload(Request $request)
	{
		// $APP_URL = env('APP_URL');
		$APP_URL = url('');
      	\Log::info($APP_URL);
		
		if (Auth::guard('sanctum')->check()) {
			$user_id = Auth::guard('sanctum')->user()->id;
			if($request->hasFile('file')) {
				$path = public_path('uploads/profile/' . $user_id . '/');
				
				if (!file_exists($path)) {
					mkdir($path, 0777, true);
				}
				$file = $request->file('file');
				$filename = time() . '.' . $file->getClientOriginalExtension();
				//echo $filename;die;
				$file->move($path, $filename);
				
				$modelimg = User::find($user_id);
				$modelimg->profile_image = $filename;
				$modelimg->save();
			}
			$user_data = User::where(['id'=> $user_id, 'status'=>1])->first();
			$response = [
				'status' => 200,
				'message' => 'Image uploaded successfully',
				'image' => $user_data->profile_image ? $APP_URL.'/uploads/profile/'. $user_id .'/'.$user_data->profile_image : null,
				];
		}
		else {
			return response()->json(['message' => 'Please login'], 401);
		}
		
		return $response;
	}
	public function profile_image_delete()
	{
		if (Auth::guard('sanctum')->check()) {
			$user_id = Auth::guard('sanctum')->user()->id;
			
			$filename = User::where('id', $user_id)->first()->profile_image;
			
			$modelimg = User::find($user_id);
			$modelimg->profile_image = null;
			$modelimg->save();
	
			// unlink image
			$path = public_path('uploads/profile/' . $user_id  . '/' . $filename);
			if (file_exists($path)) {
				unlink($path);
			}
			
			$response = [
				'status' => 200,
				'message' => 'Image deleted successfully',
				];
			
		}
		else
		{
			return response()->json(['message' => 'Please login'], 401);
		}
		
		return $response;
	}
	public function gender_list()
    {
		$gender_data = Genders::all();
		$data = [];
		foreach($gender_data as $gender)
		{
			$data[] = [
				'id' => $gender->id,
				'name' => $gender->name,
			];
		}
		$response = [
			'status' => 200,
			'data' => $data,
		];
		return $response;
    }
	public function profile_verified(Request $request)
    {
		if(Auth::guard('sanctum')->check()) 
		{
			
			$user_id = Auth::guard('sanctum')->user()->id;
			
			$model = User::find($user_id);
			$model->profile_verified = 1;
			$model->stripe_paymethod_id = $request->payment_method;
			$model->save();
			
			$response = [
				'status' => 200,
				'data' => 'Profile verified successfully',
			];
		}
		else 
		{
			$response = [
				'status' => 400,
				'message' => 'Please login',
			];
			
		}
		return $response;
    }
	
	
}
