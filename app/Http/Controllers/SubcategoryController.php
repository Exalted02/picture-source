<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SubcategoryController extends Controller
{
    public function index(Request $request)
    {
		$data['categories'] = Category::where('status', '!=', 2)->get();
		//--- search ---
		$dataArr = Subcategory::query();
		
		if($request->search_category)
		{
			$dataArr->where('category_id', 'like', '%' . $request->search_category . '%');
		}
		
		if($request->search_name)
		{
			$dataArr->where('sub_category_name', 'like', '%' . $request->search_name . '%');
		}
		
		if($request->date_range_phone && $request->date_range_phone != 'MM/DD/YYYY - MM/DD/YYYY') {
			// Explode the date range into start and end dates
			$dates = explode(' - ', $request->date_range_phone);

			// Convert the start date and end date to Y-m-d format
			$start_date = \Carbon\Carbon::createFromFormat('m/d/Y', $dates[0])->startOfDay()->format('Y-m-d');
			$end_date = \Carbon\Carbon::createFromFormat('m/d/Y', $dates[1])->endOfDay()->format('Y-m-d');
			//$contactArr->whereBetween('address_since', [$start_date, $end_date]);
			$dataArr->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date);
		}
		
		if($request->has('search_status') && $request->search_status !== '' && isset($request->search_status))
		{
			$dataArr->where('status', $request->search_status);
		} else {
			$dataArr->where('status', '!=', 2);
		}
		
        $dataArr->orderBy('category_id', 'ASC')->orderBy('sub_category_name', 'ASC'); 
		
		
		$sub_category = $dataArr->with('get_category')->get();
		
		$data['sub_category'] = $sub_category;
		return view('category/sub-category',$data);
    }
	public function save_subcategory(Request $request)
	{
		
		//echo "<pre>";print_r($request->all());die;
		$existingStage = Subcategory::where('category_id', $request->post('category_id'))->where('sub_category_name', $request->post('sub_category_name'))->where('status', '!=', 2)
        ->when($request->post('id'), function ($query) use ($request) {
            $query->where('id', '!=', $request->post('id'));
        })
        ->first();
		
		if ($existingStage) {
			return response()->json([
				'success' => false,
				'message' => 'Subcategory already exists'
			]);
		}
		
		if($request->post('id')>0)
		{
			$model= Subcategory::find($request->post('id'));
			$model->category_id		=	$request->post('category_id');
			$model->sub_category_name		=	$request->post('sub_category_name');
			$model->save();
		}
		else{
			$model=new Subcategory();
			$model->category_id		=	$request->post('category_id');
			$model->sub_category_name		=	$request->post('sub_category_name');
			$model->status		=	1;
			$model->save();
		}
		
		return response()->json([
			'success' => true,
			'message' => 'Subcategory saved successfully.'
		]);
	}
	public function edit_subcategory(Request $request)
	{
		$sub_category = Subcategory::where('id', $request->id)->first();
		$data = array();
		$data['id']  = $sub_category->id ;
		$data['category_id']  = $sub_category->category_id ;
		$data['sub_category_name']  = $sub_category->sub_category_name ;
		return $data;
	}
	public function delete_subcategory(Request $request)
	{
		$name = Subcategory::where('id', $request->id)->first()->sub_category_name;
		echo json_encode($name);
	}
	public function delete_subcategory_list(Request $request)
	{
		if($check){
			$del = Subcategory::where('id', $request->id)->update(['status'=>2]);
			
			$data['result'] ='success';
		}else{
			$data['result'] ='error';
		}
		echo json_encode($data);
	}
	public function update_status(Request $request)
	{
		$status = Subcategory::where('id', $request->id)->first()->status;
		$change_status = $status == 1 ? 0 : 1;
		$update = Subcategory::where('id', $request->id)->update(['status'=> $change_status]);
		
		$data['result'] = $change_status;
		echo json_encode($data);
	}
}
