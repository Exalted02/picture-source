<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Orders;
use App\Models\Order_items;
use App\Models\Wistlists;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
//use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

use Illuminate\Validation\Rules;
use File;

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
			$ordermodel->order_id = $request->order_id ?? null;
			$ordermodel->email_address = $request->email_address ?? null;
			$ordermodel->relationship = $request->relationship  ?? null;
			$ordermodel->birthdate = date('Y-m-d', strtotime($request->birthdate))  ?? null;
			$ordermodel->aniversary = date('Y-m-d', strtotime($request->aniversary))  ?? null;
			$ordermodel->status = 1; // default active
			$ordermodel->created_at = now();
			$ordermodel->save();
			$order_id = $ordermodel->id;
			$response = [
				'status' => 200,
				'data' => 'Record inserted successfully',
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
