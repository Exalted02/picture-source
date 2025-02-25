<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Notifications;
use App\Models\Wishlist_items;
use App\Models\Order_items;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use File;

class NotificationController extends Controller
{
    public function get_notifications(Request $request)
	{
		//\Log::info(json_encode($request->all()));
		
		$interval = config('custom.API_NOTIFICATION_INTERVAL');
		$APP_URL = env('APP_URL');
		$data = [];
		$allData  = [];
		$readData  = [];
		$unreadData  = [];
		$all 	= $request->all==1 ? 1 : '';
		$unread = $request->unread==1 ? 1 : '';
		$read 	= $request->read==1 ? 1 : '';
      	
      	$page = $request->page ?? 1;
		$offset = ($page - 1) * $interval;
		
		$paginate = $request->page ==1 ? ($request->page-1) : $request->page;
		if(Auth::guard('sanctum')->check()) 
		{
			$retailer_id = Auth::guard('sanctum')->user()->id;
			$exists = Notifications::where('retailer_id',$retailer_id)->exists();
			if($exists)
			{
				$retailer_id = Auth::guard('sanctum')->user()->id;
				if($all==1)
				{
					$notifications = Notifications::where('retailer_id',$retailer_id)->skip($offset)->take($interval)->get();
					
					foreach ($notifications as $val) {
						
						$image_url = null;
						if($val->wishlist_email==null)
						{
							$imageExist = Order_items::where('order_id',$val->order_id)->first();
							if(isset($imageExist))
							{
								$image_url = $imageExist->image_url;
							}
						}
						else{
							$imageExist = Wishlist_items::where('wishlist_id',$val->order_id)->first();
							if(isset($imageExist))
							{
								$image_url = $imageExist->image_url;
							}
						}
						
						$allData[] = [
								'order_id' => $val->order_id,
								'wishlist_email' => $val->wishlist_email,
								'message' => $val->message,
								'status' => $val->status,
								'created_at' => $val->created_at->diffForHumans(),
								'image_url' => $image_url,
							];
					}
				}
				
				if($unread==1)
				{
					$notifications = Notifications::where('retailer_id',$retailer_id)->where('status',0)->skip($offset)->take($interval)->get();
					foreach ($notifications as $val) {
						
						$image_url = null;
						if($val->wishlist_email==null)
						{
							$imageExist = Order_items::where('order_id',$val->order_id)->first();
							if(isset($imageExist))
							{
								$image_url = $imageExist->image_url;
							}
						}
						else{
							$imageExist = Wishlist_items::where('wishlist_id',$val->order_id)->first();
							if(isset($imageExist))
							{
								$image_url = $imageExist->image_url;
							}
						}
						
						$unreadData[] = [
								'order_id' => $val->order_id,
								'wishlist_email' => $val->wishlist_email,
								'message' => $val->message,
								'status' => $val->status,
								'created_at' => $val->created_at->diffForHumans(),
								'image_url' => $image_url,
							];
					}
				}
				
				if($read==1)
				{
					$notifications = Notifications::where('retailer_id',$retailer_id)->where('status',1)->skip($offset)->take($interval)->get();
					foreach ($notifications as $val) {
						
						$image_url = null;
						if($val->wishlist_email==null)
						{
							$imageExist = Order_items::where('order_id',$val->order_id)->first();
							if(isset($imageExist))
							{
								$image_url = $imageExist->image_url;
							}
						}
						else{
							$imageExist = Wishlist_items::where('wishlist_id',$val->order_id)->first();
							if(isset($imageExist))
							{
								$image_url = $imageExist->image_url;
							}
						}
						
						$readData[] = [
								'order_id' => $val->order_id,
								'wishlist_email' => $val->wishlist_email,
								'message' => $val->message,
								'status' => $val->status,
								'created_at' => $val->created_at->diffForHumans(),
								'image_url' => $image_url,
							];
					}
				}
				
				if($all==1)
				{
					$data['all'] = $allData;
				}
				
				if ($unread == 1) {
					$data['unread'] = $unreadData;
				}

				if ($read == 1) {
					$data['read'] = $readData;
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
				'status' => 400,
				'message' => 'Please login',
			];
			
		}
		return $response;
	}
}
