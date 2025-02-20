<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Delivery_address;
use App\Models\Orders;
use App\Models\Order_items;
use App\Models\Media;
use App\Models\Wistlists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use File;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
	public function index(Request $request)
	{
		//----------------
		$has_search  = 0;
		if($request->all() && count($request->all()) > 0)
		{
			$has_search  = 1;
		}
		$data['has_search'] = $has_search;
		//--- search ---
		//$dataArr = Orders::with('user_details');
		$dataArr = Orders::query();
		//$dataArr->select('user_id');
		if($request->order_search_daterange && $request->order_search_daterange != 'MM/DD/YYYY - MM/DD/YYYY') {
			// Explode the date range into start and end dates
			$dates = explode(' - ', $request->order_search_daterange);

			// Convert the start date and end date to Y-m-d format
			$start_date = \Carbon\Carbon::createFromFormat('m/d/Y', $dates[0])->startOfDay()->format('Y-m-d');
			$end_date = \Carbon\Carbon::createFromFormat('m/d/Y', $dates[1])->endOfDay()->format('Y-m-d');
			//$contactArr->whereBetween('address_since', [$start_date, $end_date]);
			$dataArr->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date);
		}
		
		if($request->search_status)
		{
			$dataArr->where('status', 'like', '%' . $request->search_status . '%');
		}
		
		$dataArr->where('order_type',0);
		//$dataArr->groupBy('user_id');
		$orders = $dataArr->get();
		///with('order_details')->
		//echo "<pre>";print_r($orders);die;
		$data['orders'] = $orders;
		return view('order/order',$data);
	}
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
	public function view_order($id='')
	{
		$data['order_dtls'] = Orders::with('order_details')->where('id', $id)->where('order_type',0)->get();
		//echo "<pre>";print_r($order_dtls); die;
		return view('order/order_view',$data);
	}
	public function change_order_status(Request $request)
	{
		$order_id = $request->id;
		$order_status = $request->status;
		Orders::where('id', $order_id)->update(['status'=>$order_status]);
		
		//-----send mail ---
		$status = $order_status == 1 ? 'Pending' : 
          ($order_status == 2 ? 'Shipped' : 
          ($order_status == 3 ? 'Cancel' : 
          ($order_status == 4 ? 'Deliver' : '')));

		$user_id = Orders::where('id', $request->id)->first()->user_id;
		$email_address = User::where('id', $user_id)->first()->email;
		$get_email = get_email(5);
			$data = [
			'subject' => $get_email->message_subject,
			'body' => str_replace(array("[status]"), array($status), $get_email->message),
			'toEmails' => [$email_address],
		];
		send_email($data);
	}
	public function order_wistlist_list(Request $request)
	{
		
		$has_search  = 0;
		if($request->all() && count($request->all()) > 0)
		{
			$has_search  = 1;
		}
		$data['has_search'] = $has_search;
		
		$dataArr = Wistlists::query();
		//$dataArr->select('user_id');
		if($request->order_wishlist_search_daterange && $request->order_wishlist_search_daterange != 'MM/DD/YYYY - MM/DD/YYYY') {
			// Explode the date range into start and end dates
			$dates = explode(' - ', $request->order_wishlist_search_daterange);

			// Convert the start date and end date to Y-m-d format
			$start_date = \Carbon\Carbon::createFromFormat('m/d/Y', $dates[0])->startOfDay()->format('Y-m-d');
			$end_date = \Carbon\Carbon::createFromFormat('m/d/Y', $dates[1])->endOfDay()->format('Y-m-d');
			//$contactArr->whereBetween('address_since', [$start_date, $end_date]);
			$dataArr->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date);
		}
		
		if($request->search_status)
		{
			$dataArr->where('status', 'like', '%' . $request->search_status . '%');
		}
		
		//$dataArr->where('order_type',1);
		//$dataArr->groupBy('user_id');
		$orders = $dataArr->get();
		$data['orders'] = $orders;
		return view('order/order_wistlist',$data);
	}
	public function view_order_wistlist($id='')
	{
		//$data['order_dtls'] = Orders::with('order_details')->where('id', $id)->where('order_type',1)->get();
		//echo "<pre>";print_r($order_dtls); die;
		//$ids = Orders::select('id')->where('usid', $id)->where('order_type',1)->get();
		
		//$data['wistlists']   = Wistlists::where('order_id',$id)->get();
		$data['order_dtls'] = Wistlists::with('wishlist_details')->where('id', $id)->where('order_type',1)->get();
		//echo "<pre>";print_r($wistlist);die;
		//Wistlists
		return view('order/order_wistlist_view',$data);
	}
	public function change_wishlist_status(Request $request)
	{
		$order_id = $request->id;
		$order_status = $request->status;
		Wistlists::where('id', $order_id)->update(['status'=>$order_status]);
		
		//-----send mail ---
		$status = $order_status == 1 ? 'Pending' : 
          ($order_status == 2 ? 'Shipped' : 
          ($order_status == 3 ? 'Cancel' : 
          ($order_status == 4 ? 'Deliver' : '')));

		$user_id = Wistlists::where('id', $request->id)->first()->user_id;
		$email_address = User::where('id', $user_id)->first()->email;
		$get_email = get_email(5);
			$data = [
			'subject' => $get_email->message_subject,
			'body' => str_replace(array("[status]"), array($status), $get_email->message),
			'toEmails' => [$email_address],
		];
		send_email($data);
	}
}
