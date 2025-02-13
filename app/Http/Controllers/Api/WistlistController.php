<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Wistlists;
use App\Models\Wishlist_items;
use App\Models\Products;
use App\Models\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
//use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

use Illuminate\Validation\Rules;
use File;
use Carbon\Carbon;

class WistlistController extends Controller
{
	public function create_wistlist(Request $request)
	{
		//echo 'hello';die;
		if(Auth::guard('sanctum')->check()) 
		{
			$user_id = Auth::guard('sanctum')->user()->id;
			
			$ordermodel = new Wistlists();
			$ordermodel->user_id = $user_id ?? null;
			$ordermodel->discount_amount = null;
			$ordermodel->coupon_discount = null;
			$ordermodel->shipping_charge = null;
			$ordermodel->order_total = $request->total_amount  ?? null;
			$ordermodel->final_amount = $request->total_amount  ?? null;
			$ordermodel->order_type = 1;
			$ordermodel->email_address = $request->email_address ?? null;
			$ordermodel->relationship = $request->relationship  ?? null;
			$ordermodel->birthdate = isset($request->birthdate) && $request->birthdate != null ? date('Y-m-d', strtotime($request->birthdate)) : null;
			$ordermodel->aniversary = isset($request->aniversary) && $request->aniversary != null ? date('Y-m-d', strtotime($request->aniversary)) : null;
			$ordermodel->status = 1; // default active
			$ordermodel->created_at = now();
			$ordermodel->save();
			$order_id = $ordermodel->id;
			
			foreach ($request->cart_items as $product) {
				$productId = $product['product_id'];
				
				$productExist = Products::where('id', $productId)->exists();
				if($productExist)
				{
					// $productData = Products::where('id', $productId)->first();
					$orderItemModel = new Wishlist_items();
					$orderItemModel->wishlist_id = $order_id ?? null;
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
			$notification->retailer_id = null;
			$notification->wishlist_email = $request->email_address ?? null;
			$notification->message = '#'.$order_id.' wishlist create by '.$consumer_name.' on '.$order_date;
			$notification->save();
			/*
			//-----send mail ---
				//Retailer email
				$get_email = get_email(9);
				$data = [
					'subject' => $get_email->message_subject,
					'body' => str_replace(array("[CONSUMER_NAME]", "[ORDER_DATE]", "[ORDER_ID]"), array($consumer_name, $order_date, $order_id), $get_email->message),
					'toEmails' => array($request->email_address),
				];
				send_email($data);
				
				//Consumer email
				$get_email = get_email(10);
				$data = [
					'subject' => $get_email->message_subject,
					'body' => str_replace(array("[ORDER_DATE]", "[ORDER_ID]"), array($order_date, $order_id), $get_email->message),
					'toEmails' => array($consumer_email),
				];
				send_email($data);
			*/
				
			$response = [
				'status' => 200,
				'order_id' => $order_id,
				'data' => 'Order wishlisted successfully',
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
