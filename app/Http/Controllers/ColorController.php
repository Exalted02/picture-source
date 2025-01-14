<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use File;

class colorController extends Controller
{
    public function index(Request $request)
    {
		//--- search ---
		$dataArr = Color::query();
		if($request->search_name)
		{
			$dataArr->where('color', 'like', '%' . $request->search_name . '%');
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
		
		$dataArr->orderBy('color', 'ASC'); 
		
		$color = $dataArr->get();
		$data['colors'] = $color;
		return view('color/color',$data);
    }
	public function save_color(Request $request)
	{
		$existingStage = Color::where('color', $request->post('color'))->where('status', '!=', 2)
        ->when($request->post('id'), function ($query) use ($request) {
			if($request->post('id') !='')
			{
				$query->where('id', '!=', $request->post('id'));
			}
        })
        ->first();
		
		if ($existingStage) {
			return response()->json([
				'success' => false,
				'message' => 'color already exists.'
			]);
		}
		
		if($request->post('id')>0)
		{
			$model= Color::find($request->post('id'));
			$model->color		=	$request->post('color');
			$model->save();
		}
		else{
			$model=new Color();
			$model->color		=	$request->post('color');
			$model->status		=	1;
			$model->save();
		}
		
		return response()->json([
			'success' => true,
			'message' => 'color saved successfully.'
		]);
	}
	public function edit_color(Request $request)
	{
		$color = Color::where('id', $request->id)->first();
		//$media = Media::where('media_source_id', $request->id)->get();
		//echo "<pre>";print_r($media);die;
		$data = array();
		$data['id']  = $color->id ;
		$data['color']  = $color->color ;
		return $data;
	}
	public function delete_color(Request $request)
	{
		$name = Color::where('id', $request->id)->first()->color;
		echo json_encode($name);
	}
	public function delete_color_list(Request $request)
	{
		$check  = Color::where('id', $request->id)->exists();
		if($check){
			$del = Color::where('id', $request->id)->update(['status'=>2]);
			
			$data['result'] ='success';
		}else{
			$data['result'] ='error';
		}
		echo json_encode($data);
	}
	public function update_status(Request $request)
	{
		$status = Color::where('id', $request->id)->first()->status;
		$change_status = $status == 1 ? 0 : 1;
		$update = Color::where('id', $request->id)->update(['status'=> $change_status]);
		
		$data['result'] = $change_status;
		echo json_encode($data);
	}
}
