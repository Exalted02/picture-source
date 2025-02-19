<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Orders;
use App\Models\Order_items;
use App\Models\Delivery_address;
use App\Models\Notifications;
use App\Models\Products;
use App\Models\Media;
use App\Models\Wistlists;
use App\Models\Wishlist_items;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
//use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use DB;
use Carbon\Carbon;

use Illuminate\Validation\Rules;
use File;

class OrderController extends Controller
{
	public function place_order(Request $request)
	{
		if(Auth::guard('sanctum')->check()) 
		{
			$user_id = Auth::guard('sanctum')->user()->id;
			
			$delivery_address = Delivery_address::where('id',$request->address_id)->first();
			$retailer = User::where('user_type',2)->select('*', 
						DB::raw("
							ROUND( ( 6371 * acos( cos( radians($delivery_address->latitude) ) 
							* cos( radians( latitude ) ) 
							* cos( radians( longitude ) - radians($delivery_address->longitude) ) 
							+ sin( radians($delivery_address->latitude) ) 
							* sin( radians( latitude ) ) ) ), 1 ) AS distance
						"),
					)
					->havingNotNull('distance')
					->orderBy('distance', 'asc') // Sort by closest distance
					->get();
			if ($retailer->isEmpty()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'No retailer found for this order',
                ]);
            }		
			// \Log::info($retailer[0]->id); 		
			//dd($retailer);
			
			$ordermodel = new Orders();
			$ordermodel->user_id = $user_id ?? null;
			$ordermodel->retailer_id = $retailer[0]->id ?? null;
			$ordermodel->delivery_address_id = $request->address_id ?? null;
			$ordermodel->discount_amount = null;
			$ordermodel->coupon_discount = null;
			$ordermodel->shipping_charge = null;
			$ordermodel->order_total = $request->total_amount  ?? null;
			$ordermodel->final_amount = $request->total_amount  ?? null;
			$ordermodel->order_type = 0;
			$ordermodel->status = 1; // default pending
			$ordermodel->created_at = now();
			$ordermodel->save();
			$order_id = $ordermodel->id;
			
			foreach ($request->cart_items as $product) {
				$productId = $product['product_id'];
				
				$productExist = Products::where('id', $productId)->exists();
				if($productExist)
				{
					// $productData = Products::where('id', $productId)->first();
					$orderItemModel = new Order_items();
					$orderItemModel->order_id = $order_id ?? null;
					$orderItemModel->product_id = $productId ?? null;
					$orderItemModel->product_name = $product['product_name'] ?? null;
					$orderItemModel->product_code = null;
					$orderItemModel->quantity = $product['quantity'] ?? null;
					$orderItemModel->price = $product['price'] ?? null;
					$orderItemModel->color_id = null;
					$orderItemModel->size_id = null;
					$orderItemModel->image_url = $product['image_url'] ?? null;
					$orderItemModel->created_at = now();
					$orderItemModel->save();
				}
			}
			
			$consumer_name = Auth::guard('sanctum')->user()->name;
			$consumer_email = Auth::guard('sanctum')->user()->email;
			$order_date = Carbon::now()->format('d-m-Y');
			
			$notification = new Notifications();
			$notification->order_id = $order_id ?? null;
			$notification->customer_id = $user_id ?? null;
			$notification->retailer_id = $retailer[0]->id ?? null;
			$notification->wishlist_email = null;
			$notification->message = '#'.$order_id.' order placed by '.$consumer_name.' on '.$order_date;
			$notification->save();
			
			/*
			//-----send mail ---
				//Retailer email
				$get_email = get_email(7);
				$data = [
					'subject' => $get_email->message_subject,
					'body' => str_replace(array("[ORDER_ID]", "[CONSUMER_NAME]", "[ORDER_DATE]"), array($order_id, $consumer_name, $order_date), $get_email->message),
					'toEmails' => array($retailer[0]->email),
				];
				send_email($data);
				
				//Consumer email
				$get_email = get_email(8);
				$data = [
					'subject' => $get_email->message_subject,
					'body' => str_replace(array("[ORDER_ID]"), array($order_id), $get_email->message),
					'toEmails' => array($consumer_email),
				];
				send_email($data);
			*/
			
			$response = [
				'status' => 200,
				'order_id' => $order_id,
				'data' => 'Order placed successfully',
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
    public function my_order()
	{
		if(Auth::guard('sanctum')->check()) 
		{
			$myOrder = [];
			$APP_URL = env('APP_URL');
			$pimage = '';
			
			$user_id = Auth::guard('sanctum')->user()->id;
			
			$OrderExists  = Orders::where('user_id',$user_id)->where('order_type',0)->exists();
			if($OrderExists)
			{
				$orders = Orders::with('order_details.order_color', 'order_details.order_size')->where('user_id',$user_id)->where('order_type',0)->get();
				//echo "<pre>";print_r($orders);die;
				foreach($orders as $order)
				{
					//$existsMedia = Media::where('media_source_id',$order->order_details[0]->product_id)->where('media_type',3)->exists();
					if(!empty($order->order_details) && isset($order->order_details[0]->product_id)) {
						$existsMedia = Media::where('media_source_id', $order->order_details[0]->product_id)->where('media_type', 3)->exists();
					} else {
						$existsMedia = false; // or handle the case when order_details is empty
					}
					if($existsMedia)
					{
						$pimage = Media::where('media_source_id',$order->order_details[0]->product_id)->where('media_type',3)->first()->image;
						//$image = 
					}
					$myOrder[] = [
						'final_amount' => $order->final_amount,
						'order_date' => $order->created_at->diffForHumans(),
						'image' => $pimage ? $APP_URL.'/uploads/product/'. $order->order_details[0]->product_id .'/gallery/thumbs/'.$pimage : null,
					];
				}
			}
			
			$response = [
					'status' => 200,
					'data' => $myOrder,
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
	public function my_order_details(Request $request)
	{
		
		if(Auth::guard('sanctum')->check()) 
		{
			$myOrder = [];
			$myOrderDtls = [];
			$APP_URL = env('APP_URL');
			
			$user_id = Auth::guard('sanctum')->user()->id;
			$order_id = $request->order_id;
			$OrderExists  = Orders::where('user_id',$user_id)->where('id', $order_id)->where('order_type',0)->exists();
			if($OrderExists)
			{
				$orders = Orders::with('order_details.order_color', 'order_details.order_size')->where('user_id',$user_id)->where('id',$order_id)->where('order_type',0)->first();
				//echo "<pre>";print_r($orders);die;
				$myOrder = [
							'final_amount' => $orders->final_amount,
							'order_date' => date('d/m/Y',strtotime($orders->created_at)),
						];
				
				foreach($orders->order_details as $items)
				{
					$existsMedia = Media::where('media_source_id',$items->product_id)->where('media_type',3)->exists();
					if($existsMedia)
					{
						$pimage = Media::where('media_source_id',$items->product_id)->where('media_type',3)->first()->image;
					}
						
					$myOrderDtls[] = [
						'product_name' => $items->product_name,
						'quantity' => $items->quantity,
						'price_per_quantity' => $items->price,
						'color' => $items->order_color->color,
						'size' => $items->order_size->size,
						'image' => $pimage ? $APP_URL.'/uploads/product/'. $items->product_id .'/gallery/thumbs/'.$pimage : null,
					
					];
				}
				$myOrder['products'] = $myOrderDtls;
			}
			
			$response = [
					'status' => 200,
					'data' => $myOrder,
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
	public function my_wistlist()
	{
		if(Auth::guard('sanctum')->check()) 
		{
			$myOrder = [];
			$APP_URL = env('APP_URL');
			
			$user_id = Auth::guard('sanctum')->user()->id;
			
			$OrderExists  = Orders::where('user_id',$user_id)->where('order_type',1)->exists();
			if($OrderExists)
			{
				$orders = Orders::with('order_details.order_color', 'order_details.order_size')->where('user_id',$user_id)->where('order_type',1)->get();
				//echo "<pre>";print_r($orders);die;
				foreach($orders as $order)
				{
					$existsMedia = Media::where('media_source_id',$order->order_details[0]->product_id)->where('media_type',3)->exists();
					if($existsMedia)
					{
						//echo $order->order_details[0]->product_id;die;
						$pimage = Media::where('media_source_id',$order->order_details[0]->product_id)->where('media_type',3)->first()->image;
						//$image = 
					}
					$myOrder[] = [
						'final_amount' => $order->final_amount,
						'order_date' => date('d/m/Y',strtotime($order->created_at)),
						
						'image' => $pimage ? $APP_URL.'/uploads/product/'. $order->order_details[0]->product_id .'/gallery/thumbs/'.$pimage : null,
					];
				}
			}
			
			$response = [
					'status' => 200,
					'data' => $myOrder,
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
	public function my_wistlist_order_details(Request $request)
	{
		
		if(Auth::guard('sanctum')->check()) 
		{
			$myOrder = [];
			$myOrderDtls = [];
			$myOrderWistlist = [];
			$APP_URL = env('APP_URL');
			
			$user_id = Auth::guard('sanctum')->user()->id;
			$order_id = $request->order_id;
			$OrderExists  = Orders::where('user_id',$user_id)->where('id', $order_id)->where('order_type',1)->exists();
			if($OrderExists)
			{
				$orders = Orders::with('order_details.order_color', 'order_details.order_size')->where('user_id',$user_id)->where('id',$order_id)->where('order_type',1)->first();
				//echo "<pre>";print_r($orders);die;
				$myOrder = [
							'final_amount' => $orders->final_amount,
							'order_date' => date('d/m/Y',strtotime($orders->created_at)),
						];
				
				// for wistlist 
				$OrderWistlistExists  = Wistlists::where('user_id',$user_id)->where('id', $order_id)->exists();
				if($OrderWistlistExists)
				{
					$fetch = Wistlists::where('user_id',$user_id)->where('id', $order_id)->first();
					$myOrder = array_merge($myOrder, [
						'wistlist_email_address' => $fetch->email_address ?? null,
						'wistlist_relationship' => $fetch->relationship ?? null,
						'wistlist_birthdate' => $fetch->birthdate ? date('d/m/Y', strtotime($fetch->birthdate)) : null,
						'wistlist_aniversary' => $fetch->aniversary ? date('d/m/Y', strtotime($fetch->aniversary)) : null,
					]);
				}
						
				foreach($orders->order_details as $items)
				{
					$existsMedia = Media::where('media_source_id',$items->product_id)->where('media_type',3)->exists();
					if($existsMedia)
					{
						$pimage = Media::where('media_source_id',$items->product_id)->where('media_type',3)->first()->image;
					}
						
					$myOrderDtls[] = [
						'product_name' => $items->product_name,
						'quantity' => $items->quantity,
						'price_per_quantity' => $items->price,
						'color' => $items->order_color->color,
						'size' => $items->order_size->size,
						'image' => $pimage ? $APP_URL.'/uploads/product/'. $items->product_id .'/gallery/thumbs/'.$pimage : null,
					
					];
				}
				$myOrder['products'] = $myOrderDtls;
			}
			
			$response = [
					'status' => 200,
					'data' => $myOrder,
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
	public function view_order(Request $request)
	{
		\Log::info(json_encode($request->all()));
		if(Auth::guard('sanctum')->check()) 
		{
			$data = [];
			$orderdata = [];
			$orderItemsdata = [];
			
			$user_id = Auth::guard('sanctum')->user()->id;
			$order_id = $request->order_id;
			$OrderExists  = Orders::where('user_id',$user_id)->where('id', $order_id)->exists();
			if($OrderExists)
			{
				$orders = Orders::with('order_details.order_color', 'order_details.order_size')->where('user_id',$user_id)->where('id',$order_id)->first();
				
				//-- delivery address --
				
				$delivery_address_type = null;
				$delivery_address_phone = null;
				$delivery_address_username = null;
				$delivery_address_details = null;
				
				$delivery_address = Delivery_address::where('id',$orders->delivery_address_id)->first();
				if(isset($delivery_address))
				{
					$isUser = User::where('id',$delivery_address->user_id)->first();
					if(isset($isUser))
					{
						$delivery_address_username = $isUser->name ?? null;
					}
					$delivery_address_type = $delivery_address->address_type ?? null;
					$delivery_address_phone = $delivery_address->phone_number ?? null;
					$delivery_address_details = $delivery_address->address ?? null;
					
				}
				
				// customer details 
				
				$customers_details = User::where('id',$orders->user_id)->first();
				if(isset($customers_details))
				{
					$customer_addr ='';
					$comma = '';
					$customers_address = null;
					if(isset($customers_details->city))
					{
						$customer_addr = $customers_details->city;
						$comma = ',';
					}
					if(isset($customers_details->state))
					{
						$customer_addr .= $comma.$customers_details->state;
						$comma = ',';
					}
					if(isset($customers_details->country))
					{
						$customer_addr .= $comma.$customers_details->country;
						$comma = ',';
					}
					if(isset($customers_details->zipcode))
					{
						$customer_addr .= $comma.$customers_details->zipcode;
					}
					
					$customers_name = $customers_details->name ?? null;
					$customers_email = $customers_details->email ?? null;
					$customers_phone = $customers_details->phone_number ?? null;
					$customers_address = $customers_details->address.' '.$customer_addr ;
				}
				//-------retailer ------------
				$retailers_details = User::where('id',$orders->retailer_id)->first();
				if(isset($retailers_details))
				{
					$retailer_addr ='';
					$comma = '';
					$retailer_address = null;
					if(isset($retailers_details->city))
					{
						$retailer_addr = $retailers_details->city;
						$comma = ',';
					}
					if(isset($retailers_details->state))
					{
						$retailer_addr .= $comma.$retailers_details->state;
						$comma = ',';
					}
					if(isset($retailers_details->country))
					{
						$retailer_addr .= $comma.$retailers_details->country;
						$comma = ',';
					}
					if(isset($retailers_details->zipcode))
					{
						$retailer_addr .= $comma.$retailers_details->zipcode;
					}
					
					$retailer_name = $retailers_details->name ?? null;
					$retailer_email = $retailers_details->email ?? null;
					$retailer_phone = $retailers_details->phone_number ?? null;
					$retailer_address = $retailers_details->address.' '.$retailer_addr ;
				}
				
				//----------------------------
				
				// order items 
				$orderItemExists  = Order_items::where('order_id',$order_id)->exists();
				if($orderItemExists)
				{
					foreach($orders->order_details as $items)
					{
						$orderItemsdata[] = [
							'product_name' => $items->product_name ?? null,
							'product_code' => $items->product_code ?? null,
							'quantity' => $items->quantity ?? null,
							'price' => $items->price ?? null,
							'color' => $items->order_color->color ?? null,
							'size' => $items->order_size->size ?? null,
							'image_url' => $items->image_url ?? null,
						 
						];
					}
				}
				
				$data = [
					'order_id' => $orders->id,
					'discount_amount' => $orders->discount_amount,
					'coupon_discount' => $orders->coupon_discount,
					'shipping_charge' => $orders->shipping_charge,
					'order_total' => $orders->order_total,
					'final_amount' => $orders->final_amount,
					'order_type' => $orders->order_type == 0 ? 'Normal Order' : 'wistlist',
					'status' => $orders->status==1 ? 'pending': ($orders->status==2 ? 'shipped' : ($orders->status==3 ? 'cancel': 'deliver')),
					'delivery_address_username' => $delivery_address_username ?? null,
					'delivery_address_type' => $delivery_address_type ?? null,
					'delivery_address_phone' => $delivery_address_phone ?? null,
					'delivery_address_details' => $delivery_address_details ?? null,
					'customers_name' => $customers_name ?? null,
					'customers_email' => $customers_email ?? null,
					'customers_phone' => $customers_phone ?? null,
					'customers_address' => $customers_address ?? null,
					'retailer_name' => $retailer_name ?? null,
					'retailer_email' => $retailer_email ?? null,
					'retailer_phone' => $retailer_phone ?? null,
					'retailer_address' => $retailer_address ?? null,
					
					'created_at' => $orders->created_at->diffForHumans(),
					'order_items'  => $orderItemsdata,
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
	public function view_profile_orders_wishlist()
	{
		if(Auth::guard('sanctum')->check()) 
		{
			$user_id = Auth::guard('sanctum')->user()->id;
			$data = [];
			$orderdata = [];
			$orderImage = [];
			$wishlistImage = [];
			
			// my order 
			$exists = Orders::where('user_id',$user_id)->exists();
			if($exists)
			{
				$countOrder = Orders::where('user_id',$user_id)->count();
				$myordercount = $countOrder;
				
				$orders = Orders::with(['order_details' => function ($query) {
					$query->orderBy('created_at', 'desc')->limit(5);
				}])->where('user_id', $user_id)->get();
				foreach($orders as $order)
				{
					foreach($order->order_details as $item)
					{
						if(!empty($item->image_url))
						{
							$orderImage[] = [$item->image_url];
						}
					}
				}
			}
			
			$data['my_order'] = [
			  'count' => $myordercount,
			  'image_url' =>$orderImage,
			];
			
			
			//-- my wishlist
			$wishlistexists = Wistlists::where('user_id',$user_id)->exists();
			if($wishlistexists)
			{
				$countWishlist = Wistlists::where('user_id',$user_id)->count();
				$myWishlistcount = $countWishlist;
				
				$wishlists = Wistlists::with(['wishlist_details' => function ($query) {
					$query->orderBy('created_at', 'desc')->limit(5);
				}])->where('user_id', $user_id)->get();
				foreach($wishlists as $wishlist)
				{
					foreach($wishlist->wishlist_details as $wList)
					{
						if(!empty($wList->image_url))
						{
							$wishlistImage[] = [$wList->image_url];
						}
					}
				}
			}
			//echo "<pre>";print_r($wishlistImage);die;
			$data['my_wishlist'] = [
			  'count' => $myWishlistcount,
			  'image_urls' =>$wishlistImage,
			];
			
			
			$response = [
					'data' => $data,
					'status' => 200,
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
