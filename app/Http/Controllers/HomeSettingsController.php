<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Home_gallery_images;

class HomeSettingsController extends Controller
{
    public function index()
    {
      $data[] = '';
      $data['images'] = Home_gallery_images::get();
      return view('home.index', $data);
    }
    public function save_data(Request $request)
    {
		$image = $request->file('file');
		//echo "<pre>";print_r($image);die;
	
		$dest_path = public_path('uploads/').'home/';
		$dest_thumb_path = public_path('uploads/').'home/thumbs/';
		$details_path = public_path('uploads/').'home/details/';
		
		$width 				= '360';
		$height  			= '270';
		
		$imageName = uploadResizeImage($details_path, $dest_path, $dest_thumb_path, $width, $height, $image);
		
		$galley = new Home_gallery_images();
		$galley->name  = 	$imageName;
		$galley->created_at		=	date('Y-m-d H:i:s');
		$galley->save();
    }
    public function delete_home_image(Request $request)
    {
		$id = $request->id;
		$imageName = $request->image_name;
		$imagePath = public_path('uploads/home/' . $imageName);
		
		$imagePaththumbs = public_path('uploads/home/thumbs/' . $imageName);
		$imagedetails = public_path('uploads/home/details/' . $imageName);

   
		if(file_exists($imagePath)) {
			unlink($imagePath);
		}
		
		if(file_exists($imagePaththumbs)) {
			unlink($imagePaththumbs);
		}
		
		if(file_exists($imagedetails)) {
			unlink($imagedetails);
		}
		
		//-------------------------------------
		Home_gallery_images::where('id',$request->id)->delete();
		echo 1;
    }
}
