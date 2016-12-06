<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Illuminate\Http\Response; 
use App\Model\User;
use App\Model\Directory;
use App\Model\Token;
use Hash;
use Validator;
use App\Model\Logo;
use App\Helper\vuforiaclient;
use App\Helper\helpers;
use Auth; 
use Session ;


class RedeemarController extends Controller {

	
	/*
	|--------------------------------------------------------------------------
	| User Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	/*public function __construct()
	{
		$this->middleware('guest');
	}*/

	public function __construct( )
	{
		// if($this->middleware('auth'))		
		// {
		// 	Auth::logout();
  //   		return redirect('auth/login');	
  //   	}
		
	}

	public function getGeneratetoken($length = 10)
	{
		$token = Token::first();
		$total_token=Token::count();
		//dd($total_token);

		$characters = '!@#$%^&*()[]{}0123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
		    $randomString .= $characters[rand(0, $charactersLength - 1)];		    
		}		    
		if($total_token >0)
		{
			$token->token_value	= $randomString;	
			$token->status 		= 1;	
			if($token->save())
			{
				return $randomString;				
			}
		}
		else
		{
		    $token = new Token();
			$token->token_value	= $randomString;			
			$token->status 		= 1;	
			if($token->save())
			{
				return 'added';
			}
		}
		//return $response;
	}
	/**
	 * add user to the system.
	 *
	 * @return Response
	*/

	public function getToken($length = 10)
	{
		$token = Token::first();
		return $token->token_value;
	}
	
	
	// Register user as reedemer
	public function postStore(Request $request)
	{		
		$token_value=$request->input('token_value');
		$token_value_db=$this->getToken();
		$error=0;
		if($token_value=="")
		{
			$response['success']='false';
			$response['message']='Token is missing';
			$error=1;
		}
		if($token_value!=$token_value_db)
		{
			$response['success']='false';
			$response['message']='Token not match with our db';
			$error=1;
		}
		//$logo_id = $request->input('logo_id');
		$company_name = $request->input('company_name');
		$first_name   = $request->input('first_name');
		$last_name     = $request->input('last_name');
		$address 	  = $request->input('address');
		$zipcode 	  = $request->input('zipcode');
		$lat 	  	  = $request->input('lat');
		$lng 	  	  = $request->input('lng');
		$email 		  = $request->input('email');
		$web_address  = $request->input('web_address');
		$password     = $request->input('password');
		$confirm_user_password     = $request->input('confirm_user_password');
		$cat_id       = $request->input('category_id');
		$subcat_id    = $request->input('subcat_id');
		$owner 		  = $request->input('owner');
		$create_offer_permission 		  = $request->input('create_offer_permission');
		$type         = 2;
		$approve 	  = 1; //0:Not approve authmetically, 1:Approve autometically
		
		if($company_name=="")
		{
			$response['success']='false';
			$response['message']='Company name is missing';
			$error=1;
		}
		if($first_name=="")
		{
			$response['success']='false';
			$response['message']='First Name is missing.';
			$error=1;
		}
		if($last_name=="")
		{
			$response['success']='false';
			$response['message']='Last Name is missing.';
			$error=1;
		}
		if($address=="")
		{
			$response['success']='false';
			$response['message']='Address is missing';
			$error=1;
		}
		if($zipcode=="")
		{
			$response['success']='false';
			$response['message']='Postal Code is missing.';
			$error=1;
		}
		if($web_address=="")
		{
			$response['success']='false';
			$response['message']='Web address is missing';
			$error=1;
		}
		if (filter_var($web_address, FILTER_VALIDATE_URL) === false)
		{
			$response['success']='false';
			$response['message']='Enter valid url';
			$error=1;
		}
		if(strlen($password) <6)
		{
			$response['success']='false';
			$response['message']='Password must be atleast 6 character long';
			$error=1;
		}
		if($confirm_user_password=="")
		{
			$response['success']='false';
			$response['message']='Retype password again';
			$error=1;
		}
		if($password!=$confirm_user_password)
		{
			$response['success']='false';
			$response['message']='Password not match with Retype password';
			$error=1;
		}
		
		
		if($error==0)
		{
			$check_user = User::where('email',$email)->count();
			$check_company = User::where('company_name',strtolower($company_name))->count();

			if ($check_user >0) {

				$response['success']='false';
				$response['message']='Email already registered with us';			   
			}
			else if ($check_company >0) {

				$response['success']='false';
				$response['message']='Company name already registered with us';			   
			}
			else
			{
				$user = new User();
				$user->company_name	= $company_name;			
				$user->first_name	= $first_name;			
				$user->last_name	= $last_name;			
				$user->address 		= $address;	
				$user->zipcode 		= $zipcode;	
				$user->lat 			= $lat;	
				$user->lng 			= $lng;	
				$user->email 		= $email;	
				$user->web_address 	= $web_address;	
				$user->cat_id 		= $cat_id;	
				$user->subcat_id 	= $subcat_id;	
				$user->owner 		= $owner;	
				$user->create_offer_permission 		= $create_offer_permission;
				$user->type 		= $type;			
				$user->approve 		= $approve;		
				$user->password = bcrypt($password);
				if($user->save())
				{
					$response['success']='true';
					$response['message']='User added successfully';
					$response['reedemer_id']=$user->id;
				}
				else
				{
					$response['success']='false';
					$response['message']='Unable to add user';
				}
			}
		}

		$response['message']=htmlspecialchars(ltrim($response['message'],' & '));	
		
		return $response;
	}

	
	public function getShowaddformfile()
	{
		return view('admin.logo.add_logo');
	}

	public function postStoreaddformfile(Request $request)
	{	
		//dd($_FILES[ 'file' ][ 'tmp_name' ]);
		 $obj = new helpers();
		// $folder_name=env('UPLOADS');
		 if($_FILES[ 'file' ][ 'name' ]=="")
		 {
		 	return 'error';
		 }
		 $file_name=$_FILES[ 'file' ][ 'name' ];
		 $temp_path = $_FILES[ 'file' ][ 'tmp_name' ];

		

		// if (!file_exists($folder_name)) {			
		// 	$create_folder= helpers::createDir($folder_name, 0777);
		// 	$thumb_path= helpers::createDir($folder_name."/inventory/thumb", 0777);
		// 	$medium_path= helpers::createDir($folder_name."/inventory/medium", 0777);
		// 	$original_path= helpers::createDir($folder_name."/inventory/original", 0777);
		// }
		// else
		// {			
		// 	$thumb_path= env('UPLOADS')."/inventory/thumb"."/";
		// 	$medium_path= env('UPLOADS')."/inventory/medium"."/";
		// 	$original_path= env('UPLOADS')."/inventory/original"."/";
		// }


		// $extension = pathinfo($file_name, PATHINFO_EXTENSION);
		// $new_file_name = time()."_".rand(111111111,999999999).'.'.$extension; // renameing image
		 $new_file_name=$_FILES[ 'file' ][ 'name' ];
		 $file_ori = $_FILES[ 'file' ][ 'tmp_name' ];
		
		// //dd($original_path.$new_file_name);
		// move_uploaded_file($file_ori, $original_path.$new_file_name);
		
		// $obj->createThumbnail($original_path,$thumb_path,env('THUMB_SIZE'));
		// $obj->createThumbnail($original_path,$medium_path,env('MEDIUM_SIZE'));
		
		//dd($request->all());
		


		$created_by=Auth::user()->id;
		$upload_dir = env('UPLOADS');

		$base_dir=$upload_dir."/".$created_by."/";
		//dd($base_dir);
		//$directory_name=$request->get('dir_name');
		$dest_dir="../../filemanager/userfiles/".$created_by."/";
		// if($request->get('new_dir_id'))
		// {
		// 	$directory_id=$request->get('new_dir_id');
		// }
		// else
		// {
		// 	$directory_id=0;
		// }
		//check if base folder exists
		if(!file_exists($base_dir))
		{
			//create base folder
			helpers::createDir($base_dir, 0777);
		}
		if(!file_exists($dest_dir))
		{
			//create base folder
			helpers::createDir($dest_dir, 0777);
		}
		$original_path= $dest_dir;
		move_uploaded_file($file_ori, $original_path.$new_file_name);
		copy($original_path.$new_file_name, $base_dir.$new_file_name);

		//$obj->createThumbnail($original_path,$thumb_path,env('THUMB_SIZE'));
		//$obj->createThumbnail($original_path,$medium_path,env('MEDIUM_SIZE'));

		//dd($base_dir);
		// check if folder exists
		// if(!file_exists($base_dir."/".$directory_name))
		// {	
		// 	//create folder		
		// 	helpers::createDir($base_dir."/".$directory_name, 0777);
		// }
		// else
		// {
		// 	return 'folder_exists';
		// }

		// if(!file_exists($dest_dir."/".$directory_name))
		// {	
		// 	//create folder		
		// 	helpers::createDir($dest_dir."/".$directory_name, 0777);
		// }

		$original_name=$_FILES[ 'file' ][ 'name' ];
		$file_name=$_FILES[ 'file' ][ 'name' ];
		$directory_base_path=$base_dir;
		$directory_url=url().$directory_base_path;

		$status=1;	
		//$directory_url=url()."/".$directory_name;

		$directory = new Directory();
		$directory->directory_id 		= 0;			
		$directory->original_name 		= $original_name;	
		$directory->file_name 		= $file_name;				
		$directory->directory_base_path 		= rtrim($directory_base_path,"/");	
		$directory->directory_url 		= $directory_url;
		$directory->directory = 0;
		$directory->status = $status;
		$directory->created_by = $created_by;



			
		//$directory = new Directory();
		//$directory->directory_name 			= $directory_name;			
		//$directory->directory_base_path 	= $base_dir."/".$directory_name;	
		//$directory->status 					= $status;	
		//$directory->created_by 				= $created_by;		
		if($directory->save())
		{
			$last_id=$directory->id;
			return $directory_base_path.$original_name."^^".$last_id;
		}
		else
		{
			return 'error';
		}

	}
}
