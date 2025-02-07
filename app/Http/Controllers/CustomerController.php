<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Orders;
use App\Models\Delivery_address;
use App\Models\Wistlists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use File;

class CustomerController extends Controller
{
    public function customer_list()
	{
		$dataArr = User::query();
		$dataArr->where('user_type',1);
		$dataArr->where('status','!=',2);
		$dataArr->orderBy('name', 'ASC'); 
		$customers = $dataArr->get();
		$data['customers'] = $customers;
		return view('customer/customer',$data);
	}
	public function retailer_list()
	{
		$dataArr = User::query();
		$dataArr->where('user_type',2);
		$dataArr->where('status','!=',2);
		$dataArr->orderBy('name', 'ASC'); 
		$customers = $dataArr->get();
		$data['customers'] = $customers;
		return view('retailer/retailer',$data);
	}
	public function delete_customer(Request $request)
	{
		$name = User::where('id', $request->id)->first()->name;
		echo json_encode($name);
	}
	public function delete_customer_list(Request $request)
	{
		$check = User::where('id', $request->id)->exists();
		if($check){
			$del = User::where('id', $request->id)->update(['status'=>2]);
			
			$data['result'] ='success';
		}else{
			$data['result'] ='error';
		}
		echo json_encode($data);
	}
	public function update_status(Request $request)
	{
		$status = User::where('id', $request->id)->first()->status;
		$change_status = $status == 1 ? 0 : 1;
		$update = User::where('id', $request->id)->update(['status'=> $change_status]);
		
		$data['result'] = $change_status;
		echo json_encode($data);
	}
	public function view_customer($id='')
	{
		$data['order_dtls'] = Orders::with('order_details')->where('user_id', $id)->get();
		$data['delivery_address'] = Delivery_address::where('user_id', $id)->get();
		$data['wistlists'] = Wistlists::where('user_id', $id)->get();
		//echo "<pre>";print_r($reviews); die;
		return view('customer/customer_view',$data);
	}
}
