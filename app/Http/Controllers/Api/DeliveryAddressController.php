<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Delivery_address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
//use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

use Illuminate\Validation\Rules;
use File;

class DeliveryAddressController extends Controller
{
	public function delivery_address_list()
	{
		if(Auth::guard('sanctum')->check()) 
		{
			$user_id = Auth::guard('sanctum')->user()->id;
			$listing = Delivery_address::where('user_id', $user_id)->get();
			foreach($listing as $list)
			{
				$data[] = [
					'id' => $list->id,
					'address_type' => $list->address_type,
					'phone_number' => $list->phone_number,
					'address' => $list->address,
				];
			}
			
			$response = [
				'status' => 200,
				'data' => $data,
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
    public function add(Request $request)
    {
		if(Auth::guard('sanctum')->check()) 
		{
			
			$user_id = Auth::guard('sanctum')->user()->id;
			$validator = Validator::make($request->all(), [
				'address_type' => 'required',
				'phone_number' => 'required|numeric|digits:10',
				'address' => 'required',
			], [
				'phone_number.required' => 'The phone number field is required.',
				'phone_number.numeric' => 'The phone number must be a valid number.',
				'phone_number.digits' => 'The phone number must be exactly 10 digits.',
			]);
			if($validator->fails()){
				return response()->json([
					'errors'=>$validator->errors(),
					'status'=>'600',
				]);
			}
			
			$model = new Delivery_address();
			$model->user_id = $user_id;
			$model->address_type = $request->address_type;
			$model->phone_number = $request->phone_number;
			$model->address = $request->address;
			$model->save();
			
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
	public function edit(Request $request)
    {
		$id = $request->id;
		if(Auth::guard('sanctum')->check()) 
		{
			
			$user_id = Auth::guard('sanctum')->user()->id;
			$validator = Validator::make($request->all(), [
				'address_type' => 'required',
				'phone_number' => 'required|numeric|digits:10',
				'address' => 'required',
			], [
				'phone_number.required' => 'The phone number field is required.',
				'phone_number.numeric' => 'The phone number must be a valid number.',
				'phone_number.digits' => 'The phone number must be exactly 10 digits.',
			]);
			
			if($validator->fails()){
				return response()->json([
					'errors'=>$validator->errors(),
					'status'=>'600',
				]);
			}
			
			$exists = Delivery_address::where('id',$id)->exists();
			if($exists)
			{
				$model = Delivery_address::find($id);
				//$model->user_id = $user_id;
				$model->address_type = $request->address_type;
				$model->phone_number = $request->phone_number;
				$model->address = $request->address;
				$model->save();
				
				$response = [
					'status' => 200,
					'data' => 'Record updated successfully',
				];
			}
			else{
				$response = [
					'status' => 401,
					'data' => 'Record not found',
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
	public function delete(Request $request)
	{
		$id = $request->id;
		if(Auth::guard('sanctum')->check()) 
		{
			$exists = Delivery_address::where('id',$id)->exists();
			if($exists)
			{
				Delivery_address::where('id',$id)->delete();
				$response = [
						'status' => 401,
						'data' => 'Record deleted successfully',
					];
			}
			else{
				$response = [
					'status' => 401,
					'data' => 'Record not found',
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
