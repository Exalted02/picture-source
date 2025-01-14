<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Products;
use App\Models\Artists;
use App\Models\Temp_media_galleries;
use App\Models\Media;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use File;

class ProductController extends Controller
{
    public function index(Request $request)
    {
		//--- search ---
		$dataArr = Products::query();
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
		
		$Product = $dataArr->get();
		$data['products'] = $Product;
		$data['categories'] = Category::where('status','!=', 2)->get();
		$data['subcategories'] = Subcategory::where('status','!=', 2)->get();
		$data['artists'] = Artists::where('status','!=', 2)->get();
		return view('Product/Product',$data);
    }
	public function save_Product(Request $request)
	{
		$existingStage = Products::where('name', $request->post('name'))->where('status', '!=', 2)
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
				'message' => 'Products already exists.'
			]);
		}
		
		if($request->post('id')>0)
		{
			$model= Products::find($request->post('id'));
			$model->product_code		=	$request->post('product_code') ?? '';
			$model->artist_id			=	$request->post('artist_id') ?? '';
			$model->category			=	$request->post('category') ?? null;
			$model->subcategory			=	$request->post('artist_id') ?? null;
			$model->name				=	$request->post('name') ?? '';
			$model->image				=	 '';
			$model->description			=	$request->post('description') ?? '';
			$model->moulding_description		=	$request->post('moulding_description') ?? '';
			$model->save();
			
			//-------------------Picture move--------------------
			$tempRecords = Temp_media_galleries::where('unique_id', $request->post('unique_number'))->get();
			
			foreach ($tempRecords as $tempRecord) {
				$newRecord = new Media();
				$newRecord->media_source_id = $request->post('id');
				$newRecord->media_type = 3;
				$newRecord->image = $tempRecord->name;
				$newRecord->status = 1;
				$newRecord->save();
			}
			Temp_media_galleries::where('unique_id', $request->post('unique_number'))->delete();
			
			$sourceDirectory = public_path('uploads/').'/product/tmp/'.$request->post('unique_number').'/';
			$destinationDirectory = public_path('uploads/').'/product/'.$request->post('id').'/';
			// $sourceDirectory = '/source/directory';
			// $destinationDirectory = '/destination/directory';
			$this->copyDirectory($sourceDirectory, $destinationDirectory);
			
			$removeDirectory = public_path('uploads/product/tmp/');
			if (File::exists($removeDirectory)) {
				File::deleteDirectory($removeDirectory);
			}
		}
		else{
			$model=new Products();
			$model->product_code		=	$request->post('product_code') ?? '';
			$model->artist_id			=	$request->post('artist_id') ?? '';
			$model->category			=	$request->post('category') ?? null;
			$model->subcategory			=	$request->post('artist_id') ?? null;
			$model->name				=	$request->post('name') ?? '';
			$model->image				=	 '';
			$model->description			=	$request->post('description') ?? '';
			$model->moulding_description		=	$request->post('moulding_description') ?? '';
			$model->status				=	1;
			$model->save();
			$lastId = $model->id;
			//-------------------Picture move--------------------
			$tempRecords = Temp_media_galleries::where('unique_id', $request->post('unique_number'))->get();
			
			foreach ($tempRecords as $tempRecord) {
				$newRecord = new Media();
				$newRecord->media_source_id = $lastId;
				$newRecord->media_type = 3;
				$newRecord->image = $tempRecord->name;
				$newRecord->status = 1;
				$newRecord->save();
			}
			
			Temp_media_galleries::where('unique_id', $request->post('unique_number'))->delete();
			
			$sourceDirectory = public_path('uploads/').'/product/tmp/'.$request->post('unique_number').'/';
			$destinationDirectory = public_path('uploads/').'/product/'.$lastId.'/';
			// $sourceDirectory = '/source/directory';
			// $destinationDirectory = '/destination/directory';
			$this->copyDirectory($sourceDirectory, $destinationDirectory);
			
			$removeDirectory = public_path('uploads/product/tmp/');
			if (File::exists($removeDirectory)) {
				File::deleteDirectory($removeDirectory);
			}
		}
		
		return response()->json([
			'success' => true,
			'message' => 'Products saved successfully.'
		]);
	}
	public function edit_Product(Request $request)
	{
		$Product = Products::where('id', $request->id)->first();
		//$media = Media::where('media_source_id', $request->id)->get();
		//echo "<pre>";print_r($media);die;
		$data = array();
		$data['id']  = $Product->id ;
		$data['name']  = $Product->name ;
		$data['product_code']  = $Product->product_code ;
		$data['artist_id']  = $Product->artist_id ;
		$data['category']  = $Product->category;
		$data['subcategory']  = $Product->subcategory;
		$data['description']  = $Product->description;
		$data['moulding_description']  = $Product->moulding_description;
		
		
		$data['medias']  = Media::where('media_source_id', $request->id)->where('media_type',3)->get();
		$data['app_url'] = env('APP_URL');
		
		$data['category_image_count'] = Media::where('media_source_id',$request->id)->where('media_type',3)->count();
		return $data;
	}
	public function delete_Product(Request $request)
	{
		$name = Products::where('id', $request->id)->first()->name;
		echo json_encode($name);
	}
	public function delete_Product_list(Request $request)
	{
		$check  = Products::where('id', $request->id)->exists();
		if($check){
			$del = Products::where('id', $request->id)->update(['status'=>2]);
			
			$data['result'] ='success';
		}else{
			$data['result'] ='error';
		}
		echo json_encode($data);
	}
	public function update_status(Request $request)
	{
		$status = Products::where('id', $request->id)->first()->status;
		$change_status = $status == 1 ? 0 : 1;
		$update = Products::where('id', $request->id)->update(['status'=> $change_status]);
		
		$data['result'] = $change_status;
		echo json_encode($data);
	}
	public function dropzone_store(Request $request)
    {
		
        $image = $request->file('file');
		//echo "<pre>";print_r($image);die;
		$unique_number = $request->unique_number;
	
		$dest_path = public_path('uploads/').'product/tmp/'.$unique_number.'/gallery/';
		$dest_thumb_path = public_path('uploads/').'product/tmp/'.$unique_number.'/gallery/thumbs/';
		$details_path = public_path('uploads/').'product/tmp/'.$unique_number.'/details/';
		
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
	public function get_subcategory(Request $request)
	{
		$category_id = $request->id;
		$subArr = SubCategory::where('status','!=',2)->where('category_id', $category_id)->get();
		$html = '<option value="">Please select</option>';
		foreach($subArr as $val)
		{
			$html .='<option value="'.$val->id.'">'.$val->sub_category_name.'</option>';
		}
		
		echo json_encode($html);
		
	}
	public function delete_product_media(Request $request)
	{
		$product_id = $request->product_id;
		
		$imageName = $request->image_name;
		$imagePath = public_path('uploads/product/' . $request->product_id . '/gallery/' . $imageName);
		
		$imagePaththumbs = public_path('uploads/product/' . $request->product_id . '/gallery/thumbs/' . $imageName);
		$imagedetails = public_path('uploads/product/' . $request->product_id . '/details/' . $imageName);

   
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
		$data['medias']  = Media::where('media_source_id',$product_id)->where('media_type',3)->get();
		$category_image_count = Media::where('media_source_id',$product_id)->where('media_type',3)->count();
		
		$data['category_remain']  = 12 - $category_image_count;
		return $data;
	}
}
