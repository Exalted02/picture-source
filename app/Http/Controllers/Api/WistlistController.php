<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Wistlists;
use App\Models\Wishlist_items;
use App\Models\Products;
use App\Models\Notifications;
use App\Models\User;
use App\Models\Email_settings;
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
			
			$retailer_id =  null;
			$exsits = User::where('email',$request->email_address)->first();
			if(isset($exsits))
			{
				$retailer_id = $exsits->id;
			}
			
			$notification = new Notifications();
			$notification->order_id = $order_id ?? null;
			$notification->customer_id = $user_id ?? null;
			$notification->retailer_id = $retailer_id;
			$notification->wishlist_email = $request->email_address ?? null;
			$notification->message = '#'.$order_id.' wishlist create by '.$consumer_name.' on '.$order_date;
			$notification->save();
			
			
			//-----send mail ---
				//Retailer email
				$settings = Email_settings::find(1);
				$get_email = get_email(9);
				$data = [
					'subject' => $get_email->message_subject,
					'body' => str_replace(array("[CONSUMER_NAME]", "[ORDER_DATE]", "[ORDER_ID]"), array($consumer_name, $order_date, $order_id), $get_email->message),
					'toEmails' => array($request->email_address),
					'ccEmails' => array($settings->admin_email),
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
	
	public function view_wishlist(Request $request)
	{
		//\Log::info(json_encode($request->all()));
		if(Auth::guard('sanctum')->check()) 
		{
			$data = [];
			$wistlistdata = [];
			$wishlistItemsdata = [];
			
			$user_id = Auth::guard('sanctum')->user()->id;
			$wishlist_id = $request->wishlist_id;
			$wishlistExists  = Wistlists::where('id', $wishlist_id)->exists();
			if($wishlistExists)
			{
				$wistlists = Wistlists::with('wishlist_details.order_color', 'wishlist_details.order_size')->where('id',$wishlist_id)->first();
				
				
				// customer details 
				
				$customers_details = User::where('id',$wistlists->user_id)->first();
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
				$retailers_exists = Wistlists::where('id',$wistlists->email_address)->first();
				if(isset($retailers_exists))
				{
					$retailers_details = User::where('email',$retailers_exists->email_address)->first();
					if(isset($retailers_details))
						
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
				
				// wishlist items
				
				$wishlistItemExists  = Wishlist_items::where('wishlist_id',$wishlist_id)->exists();
				if($wishlistItemExists)
				{
					
					foreach($wistlists->wishlist_details as $items)
					{
						
						$wishlistItemsdata[] = [
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
					'wistlist_id' => $wistlists->id,
					'discount_amount' => $wistlists->discount_amount ?? null,
					'coupon_discount' => $wistlists->coupon_discount ?? null,
					'shipping_charge' => $wistlists->shipping_charge ?? null,
					'order_total' => $wistlists->order_total ?? null,
					'final_amount' => $wistlists->final_amount ?? null,
					'order_type' => $wistlists->order_type == 0 ? 'Normal Order' : 'wistlist',
					'email_address' => $wistlists->email_address ?? null,
					'birthdate' => date('d/m/Y',$wistlists->birthdate),
					'aniversary' => date('d/m/Y',$wistlists->aniversary),
					'relationship' => $wistlists->relationship,
					'status' => $wistlists->status,
					
					'customers_name' => $customers_name ?? null,
					'customers_email' => $customers_email ?? null,
					'customers_phone' => $customers_phone ?? null,
					'customers_address' => $customers_address ?? null,
					'retailer_name' => $retailer_name ?? null,
					'retailer_email' => $retailer_email ?? null,
					'retailer_phone' => $retailer_phone ?? null,
					'retailer_address' => $retailer_address ?? null,
					'created_at' => $wistlists->created_at->diffForHumans(),
					'wistlist_items'  => $wishlistItemsdata,
				];
				
				$response = [
					'data' => $data,
					'status' => 200,
				];
				
			}
			else
			{
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
