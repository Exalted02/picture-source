<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Temp_media_galleries;
use App\Models\Media;
use File;

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
		return view('subcategory/sub-category',$data);
    }
	public function save_subcategory(Request $request)
	{
		
		//echo "<pre>";print_r($request->all());die;
		$existingStage = Subcategory::where('category_id', $request->post('category_id'))->where('sub_category_name', $request->post('sub_category_name'))->where('status', '!=', 2)
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
				'message' => 'Subcategory already exists'
			]);
		}
		
		if($request->post('id')>0)
		{
			$model= Subcategory::find($request->post('id'));
			$model->category_id		=	$request->post('category_id');
			$model->sub_category_name		=	$request->post('sub_category_name');
			$model->save();
			
			//-------------------Picture move--------------------
			$tempRecords = Temp_media_galleries::where('unique_id', $request->post('unique_number'))->get();
			
			foreach ($tempRecords as $tempRecord) {
				$newRecord = new Media();
				$newRecord->media_source_id = $request->post('id');
				$newRecord->media_type = 2;
				$newRecord->image = $tempRecord->name;
				$newRecord->status = 1;
				$newRecord->save();
			}
			Temp_media_galleries::where('unique_id', $request->post('unique_number'))->delete();
			
			$sourceDirectory = public_path('uploads/').'/subcategory/tmp/'.$request->post('unique_number').'/';
			$destinationDirectory = public_path('uploads/').'/subcategory/'.$request->post('id').'/';
			// $sourceDirectory = '/source/directory';
			// $destinationDirectory = '/destination/directory';
			$this->copyDirectory($sourceDirectory, $destinationDirectory);
			
			$removeDirectory = public_path('uploads/subcategory/tmp/');
			if (File::exists($removeDirectory)) {
				File::deleteDirectory($removeDirectory);
			}
		}
		else{
			$model=new Subcategory();
			$model->category_id		=	$request->post('category_id');
			$model->sub_category_name		=	$request->post('sub_category_name');
			$model->status		=	1;
			$model->save();
			
			$lastId = $model->id;
			//-------------------Picture move--------------------
			$tempRecords = Temp_media_galleries::where('unique_id', $request->post('unique_number'))->get();
			
			foreach ($tempRecords as $tempRecord) {
				$newRecord = new Media();
				$newRecord->media_source_id = $lastId;
				$newRecord->media_type = 2;
				$newRecord->image = $tempRecord->name;
				$newRecord->status = 1;
				$newRecord->save();
			}
			
			Temp_media_galleries::where('unique_id', $request->post('unique_number'))->delete();
			
			$sourceDirectory = public_path('uploads/').'/subcategory/tmp/'.$request->post('unique_number').'/';
			$destinationDirectory = public_path('uploads/').'/subcategory/'.$lastId.'/';
			// $sourceDirectory = '/source/directory';
			// $destinationDirectory = '/destination/directory';
			$this->copyDirectory($sourceDirectory, $destinationDirectory);
			
			$removeDirectory = public_path('uploads/subcategory/tmp/');
			if (File::exists($removeDirectory)) {
				File::deleteDirectory($removeDirectory);
			}
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
		
		$data['medias'] = Media::where('media_source_id', $request->id)->where('media_type',2)->get();
		//echo "<pre>";print_r($medias);die;
		$data['app_url'] = env('APP_URL');
		
		$data['subcategory_image_count'] = Media::where('media_source_id',$request->id)->where('media_type',2)->count();
		return $data;
	}
	public function delete_subcategory(Request $request)
	{
		$name = Subcategory::where('id', $request->id)->first()->sub_category_name;
		echo json_encode($name);
	}
	public function delete_subcategory_list(Request $request)
	{
		$check = Subcategory::where('id', $request->id)->exists();
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
	
	public function dropzone_store(Request $request)
    {
		
        $image = $request->file('file');
		//echo "<pre>";print_r($image);die;
		$unique_number = $request->unique_number;
	
		$dest_path = public_path('uploads/').'subcategory/tmp/'.$unique_number.'/gallery/';
		$dest_thumb_path = public_path('uploads/').'subcategory/tmp/'.$unique_number.'/gallery/thumbs/';
		$details_path = public_path('uploads/').'subcategory/tmp/'.$unique_number.'/details/';
		
		$width 				= '360';
		$height  			= '270';
		
		$imageName = uploadResizeImage($details_path, $dest_path, $dest_thumb_path, $width, $height, $image);
		
		$galley = new Temp_media_galleries();
		$galley->unique_id =  $unique_number;
		$galley->name  = 	$imageName;
		$galley->created_at		=	date('Y-m-d H:i:s');
		$galley->save();
        //return response()->json(['success'=>$imageName]);
    }
	public function delete_media(Request $request)
	{
		$subcategory_id = $request->subcategory_id;
		
		$imageName = $request->image_name;
		$imagePath = public_path('uploads/subcategory/' . $request->subcategory_id . '/gallery/' . $imageName);
		
		$imagePaththumbs = public_path('uploads/subcategory/' . $request->subcategory_id . '/gallery/thumbs/' . $imageName);
		$imagedetails = public_path('uploads/subcategory/' . $request->subcategory_id . '/details/' . $imageName);

   
		if(file_exists($imagePath)) {
			unlink($imagePath);
		   /*if (unlink($imagePath)) {
				return response()->json(['message' => 'Image deleted successfully.']);
			} else {
				return response()->json(['message' => 'Failed to delete image.'], 500);
			}*/
		} else {
			return response()->json(['message' => 'Image not found.'], 404);
		}
		
		if(file_exists($imagePaththumbs)) {
			unlink($imagePaththumbs);
		}
		
		if(file_exists($imagedetails)) {
			unlink($imagedetails);
		}
		
		//-------------------------------------
		Media::where('id',$request->id)->delete();
		$data['app_url'] = env('APP_URL');
		$data['medias']  = Media::where('media_source_id',$subcategory_id)->where('media_type',2)->get();
		$subcategory_image_count = Media::where('media_source_id',$subcategory_id)->where('media_type',2)->count();
		
		$data['subcategory_remain']  = 12 - $subcategory_image_count;
		return $data;
	}
	
	function copyDirectory($source, $destination) {
	   if (!is_dir($destination)) {
		  mkdir($destination, 0755, true);
	   }
	   if(is_dir($source)){
		   $files = scandir($source);
		   foreach ($files as $file) {
			  if ($file !== '.' && $file !== '..') {
				 $sourceFile = $source . '/' . $file;
				 $destinationFile = $destination . '/' . $file;
				 if (is_dir($sourceFile)) {
					$this->copyDirectory($sourceFile, $destinationFile);
				 } else {
					copy($sourceFile, $destinationFile);
				 }
			  }
		   }
	   }
	}
}
