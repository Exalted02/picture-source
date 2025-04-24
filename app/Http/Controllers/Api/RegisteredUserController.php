<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Email_settings;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\Notifications;

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
		//echo "<pre>";print_r($user);die;
        if($user){
			if(!Hash::check($password, $user->password)){
				$response['status']=400;
				$response['message']="Password are not matched";
			}
			else if($user->email_verified_at == null)
			{				
				$otp = mt_rand(1000, 9999);
				$user->otp = $otp;
				$user->save();
				
				//-----send mail ---
				$get_email = get_email(3);
				$data = [
					'subject' => $get_email->message_subject,
					'body' => str_replace(array("[OTP]"), array($otp), $get_email->message),
					'toEmails' => array($user->email),
					// 'bccEmails' => array('exaltedsol06@gmail.com','exaltedsol04@gmail.com'),
					// 'ccEmails' => array('exaltedsol04@gmail.com'),
					// 'files' => [public_path('images/logo.jpg'), public_path('css/app.css'),],
				];
				send_email($data);
				
				$response['status']=400;
				$response['message']="User is not verified";
			}
			else if($user->status != 1)
			{
				$response['status']=400;
				$response['message']="User is inactive";
			}
			else
			{
				$exists = Notifications::where('wishlist_email',$email)->exists();
				if($exists)
				{
					$hasRetialerId = Notifications::where('wishlist_email',$email)->first();
					if(isset($hasRetialerId) && $hasRetialerId->retailer_id==null)
					{
						Notifications::where('wishlist_email',$email)->update(['retailer_id'=>$user->id]);
						/*$model = Notifications::find($hasRetialerId->id);
						$model->retailer_id = $user->id;
						$model->save();*/
					}
				}
				$msg = 'Successfully logged in';
				return $this->authResponse($user, $msg);
			}
        }else{
			$response['status']=400;
			$response['message']="User does not exist";
		}
		return $response;
    }
	public function googleLogin(Request $request){
		//  \Log::info('Socialite user: ' . $request->user['displayName']);
		$email =  $request->user['email'];
		$name = $request->user['displayName'] ?? '';
		$auth_provider_id = $request->user['id'] ?? '';
		$request_usertype = $request->user['userType'];
		$provider = $request->route('provider');
		if($email) {
			if($request_usertype == 1){
				$chk_user = User::where('email',$email)->where('user_type', 2)->first();
				if($chk_user){
					$response['status']=400;
					$response['message']='Same account already used in retailer.';
					return $response;
				}
			}else if($request_usertype == 2){
				$chk_user = User::where('email',$email)->where('user_type', 1)->first();
				if($chk_user){
					$response['status']=400;
					$response['message']='Same account already used in consumer.';
					return $response;
				}
			}
			$user = User::where('email',$email)->first();
			if($user){
				$user->name = $name;
				$user->save();
				
				$authUser = $user;
			}else{
				try {
					$default_password = '12345678';
					$model = new User();
					$model->name = $name;
					$model->first_name = $name;
					// $model->last_name = $request->last_name ?? null;
					$model->email = $email ?? null;
					$model->password = Hash::make($default_password);
					// $model->company_name = $request->company_name ?? null;
					// $model->address = $request->address ?? null;
					// $model->city = $request->city ?? null;
					// $model->country = $request->country ?? null;
					// $model->state = $request->state ?? null;
					// $model->zipcode = $request->zipcode ?? null;
					// $model->phone_number = $request->phone_number ?? null;
					$model->status = 1;
					$model->user_type = $request->user['userType'] ?? null;
					$model->auth_provider = $provider;
					$model->auth_provider_id = $auth_provider_id;
					$model->email_verified_at = Now();
					
					if ($model->save()) {
						$authUser = $model;
						
						//-----send mail ---
							$get_email = get_email(6);
							$data = [
							'subject' => $get_email->message_subject,
							'body' => str_replace(array("[USERNAME]", "[PASSWORD]"), array($email, $default_password), $get_email->message),
							'toEmails' => array($request->email),
						];
						send_email($data);
					}
				} catch (\Exception $exception) {
					// Redirect to homepage with error
					//return redirect(route('home'))->with('error', $exception->getMessage());
					$response['status']=500;
					$response['message']=$exception->getMessage();
					return $response;
				}
			}
			
			$exists = Notifications::where('wishlist_email',$email)->exists();
			if($exists)
			{
				$hasRetialerId = Notifications::where('wishlist_email',$email)->first();
				if(isset($hasRetialerId) && $hasRetialerId->retailer_id==null)
				{
					Notifications::where('wishlist_email',$email)->update(['retailer_id'=>$user->id]);
					
					/*$model = Notifications::find($hasRetialerId->id);
					$model->retailer_id = $user->id;
					$model->save();*/
				}
			}
			
			
			$msg = 'Successfully logged in';
			return $this->authResponse($authUser, $msg);
		}else{
			$response['status']=400;
			$response['message']='Email not provided by Google.';
			return $response;
		}
    }
	protected function authResponse($user, $msg){
        $token = $user->createToken('API Token')->plainTextToken;
        //echo "<pre>";print_r($user);die;
		$APP_URL = env('APP_URL');
		$upload_tax_lisence = null;
		$profile_image = null;
		if(!empty($user->upload_tax_lisence))
		{
			$upload_tax_lisence = $user->upload_tax_lisence;
			$user->upload_tax_lisence = $upload_tax_lisence ? $APP_URL.'/uploads/retailer/'.$upload_tax_lisence : null;
		}
		
		if(!empty($user->profile_image))
		{
			$profile_image = $user->profile_image;
			$imagedata = $profile_image ? $APP_URL.'/uploads/profile/'.$user->id.'/'.$profile_image : null;
			$user->profile_image = $profile_image ? $APP_URL.'/uploads/profile/'.$user->id.'/'.$profile_image : null;
		}
		
		//echo $imagedata; die;
		//echo "<pre>";print_r($user);die;
		//'image' => $pimage ? $APP_URL.'/uploads/product/'. $items->product_id .'/gallery/thumbs/'.$pimage : null
		
        return response()->json([
            'status' => 200,
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
				'status' => 600,
			]);
		}
		
		$model = new User();
		$model->name = $request->first_name . ' ' . $request->last_name;
		$model->first_name = $request->first_name ?? null;
		$model->last_name = $request->last_name ?? null;
		$model->email = $request->email ?? null;
		$model->password = Hash::make($request->password);
		$model->company_name = $request->company_name ?? null;
		$model->address = $request->address ?? null;
		$model->city = $request->city ?? null;
		$model->country = $request->country ?? null;
		$model->state = $request->state ?? null;
		$model->zipcode = $request->zipcode ?? null;
		$model->phone_number = $request->phone_number ?? null;
		$model->referring_retailer = $request->retailer ?? null;
		$model->status = 1;
		$model->user_type = 1 ?? null; // customer

		if ($model->save()) {
			$lastid = $model->id;
			$otp = mt_rand(1000, 9999);
			$muser = User::find($lastid);
			$muser->otp = $otp;
			$muser->save();
			
			//-----send mail ---
				$get_email = get_email(3);
				$data = [
					'subject' => $get_email->message_subject,
					'body' => str_replace(array("[OTP]"), array($otp), $get_email->message),
					'toEmails' => array($request->email),
					// 'bccEmails' => array('exaltedsol06@gmail.com','exaltedsol04@gmail.com'),
					// 'ccEmails' => array('exaltedsol04@gmail.com'),
					// 'files' => [public_path('images/logo.jpg'), public_path('css/app.css'),],
				];
			send_email($data);
			/*try {
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
                return response()->json(['status' => 200, 'message' => 'Check to your email.']);
			} catch (\Exception $e) {
		       \Log::error('Mail send error: ' . $e->getMessage());

				return response()->json([
					'status' => 500,
					'message' => 'Failed to send email. Please try again later.',
				], 500);
			}*/
			
			//-----
			$response['status'] = 200;
			$response['message'] = "Customer added successfully";
		} else {
			$response['status'] = 500;
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
				'status' => 600,
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
		$model->company_name = $request->company_name ?? null;
		$model->address = $request->address ?? null;
		$model->city = $request->city ?? null;
		$model->country = $request->country ?? null;
		$model->state = $request->state ?? null;
		$model->zipcode = $request->zipcode ?? null;
		$model->phone_number = $request->phone_number ?? null;
		$model->user_type = 2 ?? null; // retailer
		$model->upload_tax_lisence = $filename ?? null;
		$model->status = 0;
		
        //echo  "<pre>";print_r($request->all());die;
		if ($model->save()) {
			$lastid = $model->id;
			$otp = mt_rand(1000, 9999);
			$muser = User::find($lastid);
			$muser->otp = $otp;
			$muser->save();
			
			//-----send mail ---
			$get_email = get_email(3);
			$data = [
				'subject' => $get_email->message_subject,
				'body' => str_replace(array("[OTP]"), array($otp), $get_email->message),
				'toEmails' => array($request->email),
				// 'bccEmails' => array('exaltedsol06@gmail.com','exaltedsol04@gmail.com'),
				// 'ccEmails' => array('exaltedsol04@gmail.com'),
				// 'files' => [public_path('images/logo.jpg'), public_path('css/app.css'),],
			];
			send_email($data);
			//-----send mail ---
			$settings = Email_settings::find(1);
			$get_email = get_email(11);
			$data = [
				'subject' => $get_email->message_subject,
				'body' => str_replace(array("[USER_NAME]","[USER_EMAIL]"), array($model->name, $model->email), $get_email->message),
				'toEmails' => array($settings->admin_email),
				// 'bccEmails' => array('exaltedsol06@gmail.com','exaltedsol04@gmail.com'),
				// 'ccEmails' => array('exaltedsol04@gmail.com'),
				'files' => [public_path('uploads/retailer/').$filename,],
			];
			send_email($data);
			
			$response['status'] = 200;
			$response['message'] = "Retailer added successfully";
		} else {
			$response['status'] = 500;
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
				'status' => 600,
			]);
		}
        $email = $request->input('email');
        $user = User::where('email', $email)->first();
		
		if (!$user) {
            return response()->json(['status' => 400, 'message' => 'User not found'], 404);
        }
		
        $otp = mt_rand(1000, 9999); // Generating a 6-digit OTP
		//echo $otp;die;
        if ($user) {
			$user->otp = $otp;
			$user->save();
			
			$token = Str::random(64);
			// Send email with OTP
			
			 /*try {
					Mail::send('email.forgetPassword', ['token' => $token], function ($message) use ($request) {
						$message->to($request->input('email'));
						$message->subject('Reset Password');
					});

					// If no exception, return success response
					return response()->json([
						'status' => 200,
						'message' => 'Password reset instructions sent to your email.',
					]);
				} catch (\Exception $e) {
					
					\Log::error('Mail send error: ' . $e->getMessage());

					return response()->json([
						'status' => 500,
						'message' => 'Failed to send email. Please try again later.',
					], 500);
				}*/
			try {
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
                return response()->json(['status' => 200, 'message' => 'Password reset instructions sent to your email.']);
			} catch (\Exception $e) {
		       \Log::error('Mail send error: ' . $e->getMessage());

				return response()->json([
					'status' => 500,
					'message' => 'Failed to send email. Please try again later.',
				], 500);
			}
        }
    }
	public function showResetPasswordForm($token) { 

        return view('auth.forgetPasswordLink', ['token' => $token]);

    }
	public function submitResetPasswordForm(Request $request)
     {
         $request->validate([
             'password' => 'required|string|min:6|confirmed',
             'password_confirmation' => 'required'
         ]);
         
         $updatePassword = DB::table('password_reset_tokens')->where(['token' => $request->token])->first();
         if(!$updatePassword){
             return back()->withInput()->with('error', 'Invalid token!');
         }
         $user = User::where('email', $updatePassword->email)->update(['password' => Hash::make($request->password)]);
        // DB::table('password_reset_tokens')->where(['email'=> $updatePassword->email])->delete();
         return view('auth.succee-message')->with('message', 'Your password has been changed!');

     }
	public function forget_password_verify_otp(Request $request){
		$validator = Validator::make($request->all(), [
			'email' => 'required|email',
            'otp' => 'required|numeric|digits:4',
		]);

		if ($validator->fails()) {
			return response()->json([
				'errors' => $validator->errors(),
				'status' => 600,
			]);
		}
    
        $email = $request->input('email');
        $otp = $request->input('otp');
    
        // Retrieve the user by email
        $user = User::where('email', $email)->first();
    
        if (!$user) {
            return response()->json(['status' => 400, 'message' => 'User not found']);
        }
    
        // Check if the provided OTP matches the user's OTP
        if ($user->otp != $otp) {
            return response()->json(['status' => 400, 'message' => 'Invalid OTP']);
        }
        $user->otp = null;
		if($user->email_verified_at == null){
			$user->email_verified_at = Now();
		}
        $user->save();
    
        return response()->json(['status' => 200, 'message' => 'OTP verified successfully']);
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
				'status' => 600,
			]);
		}
    
        $email = $request->input('email');
    
        // Retrieve the user by email
        $user = User::where('email', $email)->first();
    
        if (!$user) {
            return response()->json(['status' => 400, 'message' => 'User not found']);
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
	public function register_verify_otp(Request $request){
		$validator = Validator::make($request->all(), [
			'email' => 'required|email',
            'otp' => 'required|numeric|digits:4',
		]);

		if ($validator->fails()) {
			return response()->json([
				'errors' => $validator->errors(),
				'status' => 600,
			]);
		}
    
        $email = $request->input('email');
        $otp = $request->input('otp');
        
        // Retrieve the user by email
        $user = User::where('email', $email)->first();
    
        if (!$user) {
            return response()->json(['status' => 400, 'message' => 'User not found']);
        }
    
        // Check if the provided OTP matches the user's OTP
        if ($user->otp != $otp) {
            return response()->json(['status' => 400, 'message' => 'Invalid OTP']);
        }
        $user->otp = null;
        $user->email_verified_at = Now();
        $user->save();
    
        return response()->json(['status' => 200, 'message' => 'OTP verified successfully']);
    }
	public function edit_customer_profile(Request $request)
	{   
		if(Auth::guard('sanctum')->check()) 
		{
			$user_id = Auth::guard('sanctum')->user()->id;
			$validator = Validator::make($request->all(), [
				'first_name' => ['required', 'string', 'max:255'],
				'last_name' => ['required', 'string', 'max:255'],
				'email' => ['required','email','unique:users,email,' . $user_id,
				],
			]);
			
			if ($validator->fails()) {
				return response()->json([
					'errors' => $validator->errors(),
					'status' => 600,
				]);
			}
			
			$model = User::find($user_id);
			$model->name = $request->first_name . ' ' . $request->last_name;
			$model->first_name = $request->first_name ?? null;
			$model->last_name = $request->last_name ?? null;
			$model->email = $request->email ?? null;
			//$model->password = Hash::make($request->password);
			$model->company_name = $request->company_name ?? null;
			$model->address = $request->address ?? null;
			$model->city = $request->city ?? null;
			$model->country = $request->country ?? null;
			$model->state = $request->state ?? null;
			$model->latitude = $request->latitude ?? null;
			$model->longitude = $request->longitude ?? null;
			$model->zipcode = $request->zipcode ?? null;
			$model->phone_number = $request->phone_number ?? null;
			$model->dob = date('Y-m-d', strtotime($request->dob)) ?? null;
			$model->gender_id = $request->gender_id ?? null;
			$model->user_type = 1 ?? null; // customer
			$model->save();
			
			$response = [
				'status' => 200,
				'message' => 'Record updated successfully',
				];
		}
		else
		{
			return response()->json(['message' => 'Please login'], 401);
		}
		
		return $response;
	}
	public function edit_retailer_profile(Request $request)
	{   
		if(Auth::guard('sanctum')->check()) 
		{
			$user_id = Auth::guard('sanctum')->user()->id;
			//echo $user_id; die;
			$validator = Validator::make($request->all(), [
				'first_name' => ['required', 'string', 'max:255'],
				'last_name' => ['required', 'string', 'max:255'],
				'email' => ['required','email','unique:users,email,' . $user_id,
				],
			]);
			
			if ($validator->fails()) {
				return response()->json([
					'errors' => $validator->errors(),
					'status' => 600,
				]);
			}
			
			$model = User::find($user_id);
			$model->name = $request->first_name . ' ' . $request->last_name;
			$model->first_name = $request->first_name ?? null;
			$model->last_name = $request->last_name ?? null;
			$model->email = $request->email ?? null;
			//$model->password = Hash::make($request->password);
			$model->company_name = $request->company_name ?? null;
			$model->address = $request->address ?? null;
			$model->city = $request->city ?? null;
			$model->country = $request->country ?? null;
			$model->state = $request->state ?? null;
			$model->latitude = $request->latitude ?? null;
			$model->longitude = $request->longitude ?? null;
			$model->zipcode = $request->zipcode ?? null;
			$model->phone_number = $request->phone_number ?? null;
			$model->dob = date('Y-m-d', strtotime($request->dob)) ?? null;
			$model->gender_id = $request->gender_id ?? null;
			$model->user_type = 2 ?? null; // customer
			$model->save();
	
			$filename = '';
			if ($request->hasFile('upload_tax_lisence')) {
				$file = $request->file('upload_tax_lisence');
				$filename = time().'_'.$file->getClientOriginalName();
				//$ext = $file->getClientOriginalExtension();
				$destinationPath =  public_path('uploads/retailer/');
				
				if (!File::exists($destinationPath)) {
					File::makeDirectory($destinationPath, 0777, true);
				}
				
				$file->move($destinationPath,$filename);
				
				$upload_tax_lisence = User::where('id', $user_id)->first()->upload_tax_lisence;

				//echo $upload_tax_lisence; die;
				if($upload_tax_lisence !=null)
				{					
					$path = public_path('uploads/retailer/'. $upload_tax_lisence);
					if (file_exists($path)) {
						unlink($path);
					}
				}
				
				$usermodel = User::find($user_id);
				$usermodel->upload_tax_lisence = $filename;
				$usermodel->save();
			}
			
			$response = [
				'status' => 200,
				'message' => 'Record updated successfully',
				];
		}
		else
		{
			return response()->json(['message' => 'Please login'], 401);
		}
		
		return $response;
	}
	public function change_password(Request $request)
	{
		if(Auth::guard('sanctum')->check()) 
		{
			$user_id = Auth::guard('sanctum')->user()->id;
			
			$validator = Validator::make($request->all(), [
				'old_password' => ['required'],
				'new_password' => ['required', 'min:8'],
				'confirm_password' => ['required', 'same:new_password'],
			]);
			
			if ($validator->fails()) {
				return response()->json([
					'errors' => $validator->errors(),
					'status' => 600,
				]);
			}
			
			$old_password = $request->old_password;
			$user = User::where('id', $user_id)->first();
			
			if(Hash::check($old_password, $user->password))
			{
				$model = User::find($user_id);
				$model->password = Hash::make($request->new_password);
				$model->save();
				
				$response = [
				'status' => 200,
				'message' => 'Password change successfully',
				];
			}
			else{
				$response = [
				'status' => 400,
				'message' => 'Old password does not match',
				];
			}
			
			
			
		}
		else
		{
			return response()->json(['message' => 'Please login'], 401);
		}
		
		return $response;
		
	}
    public function all_retailer_list(Request $request)
    {
		$retailer_data = User::where('user_type', '!=', 1)->get();
		$data = [];
		foreach($retailer_data as $k=>$retailer)
		{
			if($k == 0){
				$data[] = [
					'id' => 1,
					'name' => env('APP_NAME'),
				];
			}else{
				$data[] = [
					'id' => $retailer->id,
					'name' => $retailer->name,
				];
			}
		}
		$response = [
			'status' => 200,
			'data' => $data,
		];
		return $response;
    }
}
