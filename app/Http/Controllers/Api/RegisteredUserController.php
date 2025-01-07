<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Http\Request;

class RegisteredUserController extends Controller
{
	public function login(Request $request){
		$validator = Validator::make($request->all(),[
			'email'=>'required',
			'password'=>'required',
		]);
		if($validator->fails()){
			return response()->json([
				'errors'=>$validator->errors(),
				'status'=>'600',
			]);
		}
        $email =  $request->input('email');
        $password = $request->input('password');
 
        $user = User::where('email',$email)->first();
        if($user){
			if(!Hash::check($password, $user->password)){
				$response['status']="400";
				$response['message']="Password are not matched";
			}
			else if($user->email_verified_at == null)
			{
				$response['status']="400";
				$response['message']="User is not verified";
			}
			else
			{
				$msg = 'Successfully logged in';
				return $this->authResponse($user, $msg);
			}
        }else{
			$response['status']="400";
			$response['message']="User does not exist";
		}
		return $response;
    }
	protected function authResponse($user, $msg){
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'status' => '200',
            'message' => $msg,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => null,
            'user' => $user,
        ]);
    }
	
	public function store_customer(Request $request)
	{
		//echo  "<pre>";print_r($request->all());die;
		$validator = Validator::make($request->all(), [
			'first_name' => ['required', 'string', 'max:255'],
			'last_name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
			'confirmed_password' => ['required', 'string', 'min:8', 'same:password'],
		]);

		if ($validator->fails()) {
			return response()->json([
				'errors' => $validator->errors(),
				'status' => '600',
			]);
		}
		
		$model = new User();
		$model->name = $request->first_name . ' ' . $request->last_name;
		$model->first_name = $request->first_name ?? null;
		$model->last_name = $request->last_name ?? null;
		$model->email = $request->email ?? null;
		$model->password = Hash::make($request->password);
		$model->city = $request->city ?? null;
		$model->state = $request->state ?? null;
		$model->zipcode = $request->zipcode ?? null;
		$model->phone_number = $request->phone_number ?? null;
		$model->user_type = 1 ?? null; // customer

		if ($model->save()) {
			$response['status'] = "200";
			$response['message'] = "Customer added successfully";
		} else {
			$response['status'] = "500";
			$response['message'] = "Failed to add customer";
		}
		
		return $response;
	}
	
	public function store_retailer(Request $request)
	{
		//echo  "<pre>";print_r($request->all());die;
		$validator = Validator::make($request->all(), [
			'first_name' => ['required', 'string', 'max:255'],
			'last_name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
			'confirmed_password' => ['required', 'string', 'min:8', 'same:password'],
		]);

		if ($validator->fails()) {
			return response()->json([
				'errors' => $validator->errors(),
				'status' => '600',
			]);
		}
		
		//echo  "<pre>";print_r($request->all());die;
		$filename = '';
		if ($request->hasFile('upload_tax_lisence')) {
			$file = $request->file('upload_tax_lisence');
			$filename = time().'_'.$file->getClientOriginalName();
			$ext = $file->getClientOriginalExtension();
			$destinationPath =  public_path('uploads/retailer');
			$file->move($destinationPath,$filename);
		}
		
		$model = new User();
		$model->name = $request->first_name . ' ' . $request->last_name;
		$model->first_name = $request->first_name ?? null;
		$model->last_name = $request->last_name ?? null;
		$model->email = $request->email ?? null;
		$model->password = Hash::make($request->password);
		$model->city = $request->city ?? null;
		$model->state = $request->state ?? null;
		$model->zipcode = $request->zipcode ?? null;
		$model->phone_number = $request->phone_number ?? null;
		$model->user_type = 2 ?? null; // retailer
		$model->upload_tax_lisence = $filename ?? null;
		
        //echo  "<pre>";print_r($request->all());die;
		if ($model->save()) {
			$response['status'] = "200";
			$response['message'] = "Retailer added successfully";
		} else {
			$response['status'] = "500";
			$response['message'] = "Failed to add retailer";
		}

		return $response;
	}
	public function forgotPassword(Request $request){
		$validator = Validator::make($request->all(), [
			'email' => 'required|email',
		]);

		if ($validator->fails()) {
			return response()->json([
				'errors' => $validator->errors(),
				'status' => '600',
			]);
		}
		
        $email = $request->input('email');
        $user = User::where('email', $email)->first();
		
		if (!$user) {
            return response()->json(['status' => '400', 'message' => 'User not found'], 404);
        }
		
        $otp = mt_rand(1000, 9999); // Generating a 6-digit OTP
		//echo $otp;die;
        if ($user) {
			$user->otp = $otp;
			$user->save();
			
			// Send email with OTP
			$get_email = get_email(2);
			$data = [
				'subject' => $get_email->message_subject,
				'body' => str_replace(array("[OTP]"), array($otp), $get_email->message),
				'toEmails' => array($email),
				// 'bccEmails' => array('exaltedsol06@gmail.com','exaltedsol04@gmail.com'),
				// 'ccEmails' => array('exaltedsol04@gmail.com'),
				// 'files' => [public_path('images/logo.jpg'), public_path('css/app.css'),],
			];
			send_email($data);
            return response()->json(['status' => '200', 'message' => 'Password reset instructions sent to your email.']);
        }
    }
	public function forget_password_verify_otp(Request $request){
		$validator = Validator::make($request->all(), [
			'email' => 'required|email',
            'otp' => 'required|numeric|digits:4',
		]);

		if ($validator->fails()) {
			return response()->json([
				'errors' => $validator->errors(),
				'status' => '600',
			]);
		}
    
        $email = $request->input('email');
        $otp = $request->input('otp');
    
        // Retrieve the user by email
        $user = User::where('email', $email)->first();
    
        if (!$user) {
            return response()->json(['status' => '400', 'message' => 'User not found']);
        }
    
        // Check if the provided OTP matches the user's OTP
        if ($user->otp != $otp) {
            return response()->json(['status' => '400', 'message' => 'Invalid OTP']);
        }
        $user->otp = null;
        $user->save();
    
        return response()->json(['status' => '200', 'message' => 'OTP verified successfully']);
    }
	public function resetpassword(Request $request){
		$validator = Validator::make($request->all(), [
			'email' => 'required|email',
			'password' => 'required|confirmed|min:6',
			'password_confirmation' => 'required','same:password',
		]);
		
		if ($validator->fails()) {
			return response()->json([
				'errors' => $validator->errors(),
				'status' => '600',
			]);
		}
    
        $email = $request->input('email');
    
        // Retrieve the user by email
        $user = User::where('email', $email)->first();
    
        if (!$user) {
            return response()->json(['status' => '400', 'message' => 'User not found']);
        }
        $user->password = Hash::make($request->input('password'));
        $user->save();
		
		$msg = 'Password updated successfully.';
		return $this->authResponse($user, $msg);
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
}
