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
	public function view_customer_old($id='')
	{
		//$data['order_dtls'] = Orders::with('order_details')->where('user_id', $id)->get();
		$data['order_dtls'] = Orders::with('order_details')->where('id', $id)->get();
		$order_delivery_addr = Orders::where('id', $id)->first();
		
		$data['delivery_address'] = '';
		if(isset($order_delivery_addr->delivery_address_id))
		{
			$data['delivery_address'] = Delivery_address::where('id', $order_delivery_addr->delivery_address_id)->get();
		}
		//echo "<pre>";print_r($order_dtls);die;
		
		$data['wistlists'] = Wistlists::where('user_id', $id)->get();
		//echo "<pre>";print_r($reviews); die;
		return view('customer/customer_view',$data);
	}
	public function view_customer($id='')
	{
		//echo 'hello';die;
		$data['order_dtls'] = Orders::with('order_details')->where('user_id', $id)->get();
		
		$order_delivery_addr = Orders::where('user_id', $id)->first();
		
		$data['delivery_address'] = '';
		if(isset($order_delivery_addr->delivery_address_id))
		{
			$data['delivery_address'] = Delivery_address::where('id', $order_delivery_addr->delivery_address_id)->where('user_id', $id)->get();
		}
		else{
			$data['delivery_address'] = Delivery_address::where('user_id', $id)->get();
		}
		$data['wistlists'] = Wistlists::where('user_id', $id)->get();
		$data['customer_id'] = $id;
		//echo "<pre>";print_r($reviews); die;
		return view('customer/customer_view',$data);
	}
	public function view_customer_order_details($id='')
	{
		$data['order_dtls'] = array();
		$exists = Orders::where('id', $id)->exists();
		if($exists)
		{
			$data['order_dtls'] = Orders::with('order_details','delivery_address')->where('id', $id)->get();
			
			$order_delivery_addr = Orders::where('id', $id)->first();
			if(isset($order_delivery_addr->delivery_address_id))
			{
				$delivery_address_id = $order_delivery_addr->delivery_address_id;
			}
			else
			{
				$delivery_address_id = Orders::where('retailer_id', $id)->first()->delivery_address_id;
			}
			$data['delivery_address'] = Delivery_address::where('id',$delivery_address_id)->first();
		}
		$data['retailer_id'] = $order_delivery_addr->retailer_id;
		$data['customer_id'] = $order_delivery_addr->user_id;
		//$data['']
		return view('customer/customer_order_details_view',$data);
	}
}
