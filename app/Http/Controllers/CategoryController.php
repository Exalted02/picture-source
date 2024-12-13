<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
		//--- search ---
		$dataArr = Category::query();
		if($request->search_name)
		{
			$dataArr->where('name', 'like', '%' . $request->search_name . '%');
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
		
		$dataArr->orderBy('name', 'ASC'); 
		
		$category = $dataArr->get();
		$data['categories'] = $category;
		return view('category/category',$data);
    }
	public function save_category(Request $request)
	{
		
		//echo "<pre>";print_r($request->all());die;
		
		$existingStage = Category::where('name', $request->post('name'))->where('status', '!=', 2)
        ->when($request->post('id'), function ($query) use ($request) {
            $query->where('id', '!=', $request->post('id'));
        })
        ->first();
		
		if ($existingStage) {
			return response()->json([
				'success' => false,
				'message' => 'Category already exists.'
			]);
		}
		
		if($request->post('id')>0)
		{
			$model= Category::find($request->post('id'));
			$model->name		=	$request->post('name');
			$model->save();
		}
		else{
			$model=new Category();
			$model->name		=	$request->post('name');
			$model->status		=	1;
			$model->save();
		}
		
		return response()->json([
			'success' => true,
			'message' => 'Category saved successfully.'
		]);
	}
	public function edit_category(Request $request)
	{
		$category = Category::where('id', $request->id)->first();
		$data = array();
		$data['id']  = $category->id ;
		$data['name']  = $category->name ;
		return $data;
	}
	public function delete_category(Request $request)
	{
		$name = Category::where('id', $request->id)->first()->name;
		echo json_encode($name);
	}
	public function delete_category_list(Request $request)
	{
		if($check){
			$del = Category::where('id', $request->id)->update(['status'=>2]);
			
			$data['result'] ='success';
		}else{
			$data['result'] ='error';
		}
		echo json_encode($data);
	}
	public function update_status(Request $request)
	{
		$status = Category::where('id', $request->id)->first()->status;
		$change_status = $status == 1 ? 0 : 1;
		$update = Category::where('id', $request->id)->update(['status'=> $change_status]);
		
		$data['result'] = $change_status;
		echo json_encode($data);
	}
}
