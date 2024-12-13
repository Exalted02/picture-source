<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\Models\Areatrade_contact;
use App\Models\Product_code;
use App\Models\Product_group;
use App\Models\Product_name_master;
use App\Models\Prospect;
use App\Models\Resource;
use App\Models\Outsources;
use App\Models\ReferralCategory;
use App\Models\States;
use App\Models\Cities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Lang;
class CommonController extends Controller
{
	public function change_multi_status(Request $request)
	{
		$modelClass = $request->model;
		$cat_ids = explode(',',$request->id);
		$module = '';
		if($modelClass == 'App\Models\Leads'){
			$chk = $modelClass::where('id', $cat_ids[0])->first();
			if($chk->move_to == 0){
				$module = 1;
			}else if($chk->move_to == 1){
				$module = 2;
			}
		}else if($modelClass == 'App\Models\Referral'){
			$module = 7;
		}else if($modelClass == 'App\Models\Inventory'){
			$module = 6;
		}
		if($module != ''){
			if(!check_module_option_permission($module, 'status')){
				return response()->json([
					'success' => false,
					'message' => 'No Permission!',
				], 403);
			}			
		}
		
		$updated = $modelClass::whereIn('id',$cat_ids)
				->update(['status'=>$request->status]);
				
		if($request->additional_table){
			foreach($request->additional_table as $request_table){
				if($request_table == 'product_code'){
					Product_code::where('id', $updated->id)
						->update(['status'=>$request->status]);
				}
			}
		}
		
        if($updated){
			$request->session()->flash('message','Status has been updated successfully.');
			return response()->json(['success'=>'Status has been updated successfully.']);
		}else{
			return response()->json(['success'=>'Status not updated.']);
		}
	}
	public function delete_multi_data(Request $request)
	{
		$modelClass = $request->model;
		$cat_ids = explode(',',$request->id);
		$module = '';
		if($modelClass == 'App\Models\Leads'){
			$chk = $modelClass::where('id', $cat_ids[0])->first();
			if($chk->move_to == 0){
				$module = 1;
			}else if($chk->move_to == 1){
				$module = 2;
			}
		}else if($modelClass == 'App\Models\Referral'){
			$module = 7;
		}else if($modelClass == 'App\Models\Inventory'){
			$module = 6;
		}else if($modelClass == 'App\Models\Outsources'){
			$module = 8;
		}else if($modelClass == 'App\Models\Resource'){
			$module = 11;
		}
		if($module != ''){
			if(!check_module_option_permission($module, 'delete')){
				return response()->json([
					'success' => false,
					'message' => 'No Permission!',
				], 403);
			}			
		}
		
		//check the value is used in another movule
		if($modelClass == 'App\Models\Product_code'){
			$find_values = get_used_value('product_code');
			$result = array_diff($cat_ids, $find_values);
			$cat_ids = $result;
		}else if($modelClass == 'App\Models\Product_group'){
			$find_values = get_used_value('product_group');
			$result = array_diff($cat_ids, $find_values);
			$cat_ids = $result;
		}else if($modelClass == 'App\Models\Product_name_master'){
			$find_values = get_used_value('product_name');
			$result = array_diff($cat_ids, $find_values);
			$cat_ids = $result;
		}else if($modelClass == 'App\Models\Prospect'){
			$find_values = get_used_value('stage');
			$result = array_diff($cat_ids, $find_values);
			$cat_ids = $result;
		}else if($modelClass == 'App\Models\Resource'){
			$find_values = get_used_value('resource');
			$result = array_diff($cat_ids, $find_values);
			$cat_ids = $result;
		}else if($modelClass == 'App\Models\Outsources'){
			$find_values = get_used_value('outsource');
			$result = array_diff($cat_ids, $find_values);
			$cat_ids = $result;
		}else if($modelClass == 'App\Models\ReferralCategory'){
			$find_values = get_used_value('referral_category');
			$result = array_diff($cat_ids, $find_values);
			$cat_ids = $result;
		}
		
		$updated = $modelClass::whereIn('id',$cat_ids)
				->update(['status'=>2]);
		
		if($request->additional_table){
			foreach($request->additional_table as $request_table){
				if($request_table == 'product_code'){
					Product_code::where('id', $updated->id)
						->update(['status'=>2]);
				}
			}
		}
		
        if($updated){
			$request->session()->flash('message','Data deleted successfully.');
			return response()->json(['success'=>'Data deleted successfully.']);
		}else{
			return response()->json(['success'=>'Data not deleted.']);
		}
	}
	public function get_state_by_country(Request $request)
	{
		$country_id = $request->country_id;
		$state_list = States::query()->where('country_id', $country_id)->orderBy('name')->get();
		$html = '';
		if($request->page && $request->page == 'search'){
			$html .='<option value>'.Lang::get('state').'</option>';
		}else{
			$html .='<option value>'.Lang::get('please_select').'</option>';
		}
		foreach($state_list as $val)
		{
		$html .='<option value='.$val->id.'>'.$val->name.'</option>';
		}
		echo json_encode($html);
	}
	public function get_city_by_state(Request $request)
	{
		$state_id = $request->state_id;
		$city_list = Cities::query()->where('state_id', $state_id)->orderBy('name')->get();
		$html = '';
		if($request->page && $request->page == 'search'){
			$html ='<option value>'.Lang::get('city').'</option>';
		}else{
			$html .='<option value>'.Lang::get('please_select').'</option>';
		}
		foreach($city_list as $val)
		{
		$html .='<option value='.$val->id.'>'.$val->name.'</option>';
		}
		echo json_encode($html);
	}
}
