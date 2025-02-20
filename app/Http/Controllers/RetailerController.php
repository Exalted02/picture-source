<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Orders;
use App\Models\Delivery_address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use File;
use Illuminate\Support\Facades\Mail;

class RetailerController extends Controller
{
    
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
	public function delete_retailer(Request $request)
	{
		$name = User::where('id', $request->id)->first()->name;
		echo json_encode($name);
	}
	public function delete_retailer_list(Request $request)
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
		
		// send mail to retailer
		if($status==0)
		{
			$email_address = User::where('id', $request->id)->first()->email;
			$get_email = get_email(4);
				$data = [
				'subject' => $get_email->message_subject,
				'body' => $get_email->message,
				'toEmails' => [$email_address],
			];
			send_email($data);
		}
		echo json_encode($data);
	}
	public function retailer_tax_download(Request $request)
	{
		//echo $request->retailer_id;die;
		$fileName = User::where('id', $request->retailer_id)->where('user_type', 2)->first()->upload_tax_lisence;
		//echo $fileName; die;
		//$fileName = 'taxinvoice.txt';
		
			//$filePath = storage_path('app/public/' . $fileName);
			$filePath =  public_path('uploads/retailer/' . $fileName); 

			if (file_exists($filePath)) {
				return response()->download($filePath, $fileName, [
					'Content-Type' => 'text/plain',
				]);
			} else {
				return response()->json(['error' => 'File not found.'], 404);
			}
	}
	public function view_retailer_list($id='')
	{
		$data['order_dtls'] = array();
		$exists = Orders::where('retailer_id', $id)->exists();
		if($exists)
		{
			$data['order_dtls'] = Orders::where('retailer_id', $id)->get();
			
			$delivery_address_id = Orders::where('retailer_id', $id)->first()->delivery_address_id;
			$data['delivery_address'] = Delivery_address::where('id',$delivery_address_id)->first();
		}
		$data['retailer_id'] = $id;
		return view('retailer/retailer_view_list',$data);
		//return view('retailer/retailer_view',$data);
	}
	public function view_retailer($id='')
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
		return view('retailer/retailer_view',$data);
	}
	public function view_retailer_old($id='')
	{
		$data['order_dtls'] = array();
		$exists = Orders::where('retailer_id', $id)->exists();
		if($exists)
		{
			$data['order_dtls'] = Orders::with('order_details','delivery_address')->where('retailer_id', $id)->get();
			
			$delivery_address_id = Orders::where('retailer_id', $id)->first()->delivery_address_id;
			$data['delivery_address'] = Delivery_address::where('id',$delivery_address_id)->first();
		}
		$data['retailer_id'] = $id;
		return view('retailer/retailer_view',$data);
	}
}
