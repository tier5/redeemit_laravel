<?php namespace App\Http\Controllers;
use Auth;
//use App\Model\Logo;
use App\Model\Wptoken;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Illuminate\Http\Response; 
use Redirect;
use Input;
use Session, \Image;
use App\Helper\vuforiaclient;
use App\Model\User;
use App\Model\Inventory;
use App\Model\Campaign;
use App\Model\Logo;
use App\Model\Category;
use Illuminate\Routing\Route;
use App\Helper\helpers;
use App\Model\PasswordReset;

class PartnerController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
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
	// public function __construct()
	// {
	// 	$this->middleware('guest');
	// }

	public function __construct()
	{
		//$this->middleware('auth');
		//$this->menuItems				= $menu->where('active' , '1')->orderBy('weight' , 'asc')->get();
 				
		
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		
		$logo_details=Logo::where('status',1)
						  ->orderBy('id','DESC')
						  ->get();
		
		$category_details=$this->getCategory('0');
		$url=url();

		$logo_details_unused=Logo::where('status',1)
				  ->whereNull('reedemer_id')
				  ->orderBy('id','DESC')
				  ->get();
				 // dd($category_details->toArray());
		return view('partner.registration',[
					'logo_details' =>$logo_details,
					'logo_details_unused' =>$logo_details_unused,
					'category_details' =>$category_details,
					'url' =>$url
			   ]);
		
	}

	

	public function postSearch(Request $request)
	{
		
		//dd($request->all());
		//exit;
		$logo_details=Logo::where('status',1)
					  ->orderBy('id','DESC')
					  ->get();					 
		$url=url();
		
		return view('partner.list',[
						'logo_details' =>$logo_details,
						'url' =>$url
				   ]);
	}

	public function getAdd($logo_id)
	{	
		$logo_details=Logo::where('id',$logo_id)->first();		
		$category_details=$this->getCategory('0');
		//dd($category_details->toArray());
		return view('partner.add',[
						'logo_id' =>$logo_id,
						'logo_details' =>$logo_details,
						'category_details' =>$category_details
				   ]);
	}

	//Listing to show only 
	public function getCategory($parent_id='')
	{		
		if($parent_id!='')
		{
			//$id=$request[0];
			$category = Category::where('parent_id',$parent_id)
						->where('visibility',1)
						->where('status',1)
						->get();
		}
		else
		{
			$category = Category::where('visibility',1)
						->where('status',1)
						->orderBy('id','DESC')
						->get();
		}
		//dd($category->toArray());
		return $category;
	}

	public function postStore(Request $request)
	{	
	//dd($request->all())	;
		$wptoken=$this->getWptoken();
		//dd($wptoken->toArray());
		$logo_id=$request->get('logo_id');
		//dd($logo_id);

		$zipcode=$request->get('zipcode');
		$location_arr=$this->get_lat_lng($zipcode);			
		$lat=$location_arr['lat'];
		$lng=$location_arr['lng'];		
		//dd($lng);
		// Data Array
		$data = array(
			//'logo_id' => urlencode($request->get('logo_id')),
			'company_name' => urlencode($request->get('company_name')),
			'first_name' => urlencode($request->get('first_name')),
			'last_name' => urlencode($request->get('last_name')),
			'address' => urlencode($request->get('address')),
			'zipcode' => urlencode($request->get('zipcode')),
			'lat' => urlencode($lat),
			'lng' => urlencode($lng),
			'email' => urlencode($request->get('user_email')),
			'web_address' => urlencode($request->get('web_address')),
			'password' => urlencode($request->get('user_password')),
			'confirm_user_password' => urlencode($request->get('confirm_user_password')),
			'category_id' => urlencode($request->get('category_id')),
			'subcat_id' => urlencode($request->get('subcat_id')),
			'owner' => urlencode($request->get('owner')),
			'create_offer_permission' => urlencode($request->get('create_offer_permission')),
			'token_value' => $wptoken->token_value
		);

		
		$url = getenv('WEBSERVICE_PATH');
		$result= $this->post_to_url($url, $data);
		//dd($result);
		$result_arr=json_decode($result);
		
		if($result_arr->success=='false')
		{
			return redirect()->back()	
					->withInput($request->only('company_name','address','user_email', 'web_address'))								
					->withErrors([
						'message' => $result_arr->message,
					]);
		}
		else
		{
			$reedemer_id = $result_arr->reedemer_id;

			if($logo_id==0)
			{				
     			return view('partner.addlogo',[
					 'reedemer_id' =>$reedemer_id,
					 'logo_text' =>$request->get('company_name')
			    ]);					
			}
			else
			{			

				$logo=Logo::find($logo_id);
				$logo->reedemer_id 	= $reedemer_id;	
				$logo->save();
				
				Session::flash('message', $result_arr->message);

				return Redirect::back();
			}
		}

		


	}

	public function post_to_url($url, $data) {
	    $fields = '';
	    foreach ($data as $key => $value) {
	        $fields .= $key . '=' . $value . '&';
	    }
	    rtrim($fields, '&');

	    $post = curl_init();

	    curl_setopt($post, CURLOPT_URL, $url);
	    curl_setopt($post, CURLOPT_POST, count($data));
	    curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
	    curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);

	    $result = curl_exec($post);

	    curl_close($post);
	    return $result;
	}

	public function getWptoken() {
		$wptoken=Wptoken::first();
		//dd($wptoken->toArray());
		return $wptoken;
	}

	//This function is use for frontend upload
	public function postUploadlogo(Request $request)
	{
		$extArr = array_reverse(explode("/",$request->image_type));
		$ext=$extArr[0];
		$mainExt  = $ext=='png'?'png':'jpg';
		$alterExt = $ext=='png'?'jpg':'png';
		$alterMethod = $ext=='png'?'jpeg':'png';
		$obj = new helpers();
		
		$newRandName 	= time()."_".rand(10,999999);
		$thumb_path		= base_path()."/uploads/temp_thumb"."/";
		$medium_path	= base_path()."/uploads/temp_medium"."/";
		$original_path	= base_path()."/uploads/temp_original"."/";

		$medium_size = env('MEDIUM_SIZE');
		$thumb_size  = env('THUMB_SIZE');

		$src = $request->logo_image;
		
		$mainOriginalFilePathTmp = $original_path.$newRandName.'_tmp.'.$mainExt;
		$mainOriginalFilePath = $original_path.$newRandName.'.'.$mainExt;
		$mainMediumFilePath   = $medium_path.$newRandName.'.'.$mainExt;
		$mainThumb_path   	  = $thumb_path.$newRandName.'.'.$mainExt;
		
		$obj->base64toiamge($src,$mainOriginalFilePathTmp);
		Image::make($mainOriginalFilePathTmp)->save($mainOriginalFilePath);
		Image::make($mainOriginalFilePath)->resize($medium_size, $medium_size)->save($mainMediumFilePath);
		Image::make($mainOriginalFilePath)->resize($medium_size, $medium_size)->save($mainThumb_path);
		
		$altOriginalFilePath = $original_path.$newRandName.'.'.$alterExt;
		$altMediumFilePath   = $medium_path.$newRandName.'.'.$alterExt;
		$altThumb_path   	  = $thumb_path.$newRandName.'.'.$alterExt;
		$method = 'image'.$alterMethod;
		
		$this->convertImage($mainOriginalFilePath,$altOriginalFilePath,$alterExt);
		$this->convertImage($mainMediumFilePath,$altMediumFilePath,$alterExt);
		$this->convertImage($mainThumb_path,$altThumb_path,$alterExt);
		
		chmod($mainOriginalFilePath,0777);
		chmod($mainMediumFilePath,0777);
		chmod($mainThumb_path,0777);
		chmod($altOriginalFilePath,0777);
		chmod($altMediumFilePath,0777);
		chmod($altThumb_path,0777);
		chmod($mainOriginalFilePathTmp,0777);
		@unlink($mainOriginalFilePathTmp);
		
		$client = new vuforiaclient();
		$send[0] = $newRandName .'.jpg';
		$send[1] = $original_path.$newRandName.'.jpg';
		$send[2] = $send[1];
		$send[3] = 'Redeemar';
		$send[4] = 'Redeemar';		
		$response=$client->addTarget($send);
		$response_arr=json_decode($response);		

		if($response_arr->result_code=="TargetCreated")
		{
			$target_id=$response_arr->target_id;
			$tracking_rating=$this->getVuforiarate($target_id);
			$message=array('success'=>'true','logo_image'=>$newRandName .'.'.$mainExt,'tracking_rating'=>$tracking_rating, 'target_id'=>$target_id);
			return $message;
		}
		return array('success'=>false,'msg'=>'Oops. Something went wrong. Please try again later.');
	}

	//backend logo upload
	//this function use for reedemar logo upload
	public function postUploadlogoback(Request $request)
	{
		$src=$request->input('logo_image');
		$extArr = array_reverse(explode("/",$request->image_type));
		$ext=$extArr[0];
		$mainExt  = $ext=='png'?'png':'jpg';
		$alterExt = $ext=='png'?'jpg':'png';
		$alterMethod = $ext=='png'?'jpeg':'png';
		$obj = new helpers();
		
		$newRandName 	= time()."_".rand(10,999999);
		$thumb_path		= base_path()."/uploads/thumb"."/";
		$medium_path	= base_path()."/uploads/medium"."/";
		$original_path	= base_path()."/uploads/original"."/";
		
		$fileManagerUserfilePath = rtrim(base_path(),'/admin');

		$medium_size = env('MEDIUM_SIZE');
		$thumb_size  = env('THUMB_SIZE');
		
		$fileMng_largeWidth   = env('LOGO_LARGE_WIDTH');
		$fileMng_largeHeight  = env('LOGO_LARGE_HEIGHT');
		$fileMng_mediumWidth  = env('LOGO_MEDIUM_WIDTH');
		$fileMng_mediumHeight = env('LOGO_MEDIUM_HEIGHT');
		$fileMng_smallWidth   = env('LOGO_SMALL_WIDTH');
		$fileMng_smallHeight  = env('LOGO_SMALL_HEIGHT');

		$src = $request->logo_image;
		
		$mainOriginalFilePathTmp = $original_path.$newRandName.'_tmp.'.$mainExt;
		$mainOriginalFilePath = $original_path.$newRandName.'.'.$mainExt;
		$mainMediumFilePath   = $medium_path.$newRandName.'.'.$mainExt;
		$mainThumb_path   	  = $thumb_path.$newRandName.'.'.$mainExt;
		
		$fileMngrSmallPath  =  rtrim(base_path(),'/admin') .'/filemanager/userfiles/small/'.$newRandName.'.png';
		$fileMngrMediumPath =  rtrim(base_path(),'/admin') .'/filemanager/userfiles/medium/'.$newRandName.'.png';
		$fileMngrLargePath  =  rtrim(base_path(),'/admin') .'/filemanager/userfiles/large/'.$newRandName.'.png';
				
		try{
		$obj->base64toiamge($src,$mainOriginalFilePathTmp);
		Image::make($mainOriginalFilePathTmp)->save($mainOriginalFilePath);
		Image::make($mainOriginalFilePath)->resize($medium_size, $medium_size)->save($mainMediumFilePath);
		Image::make($mainOriginalFilePath)->resize($thumb_size, $thumb_size)->save($mainThumb_path);
			
		$altOriginalFilePath = $original_path.$newRandName.'.'.$alterExt;
		$altMediumFilePath   = $medium_path.$newRandName.'.'.$alterExt;
		$altThumb_path   	 = $thumb_path.$newRandName.'.'.$alterExt;
		$method = 'image'.$alterMethod;
		
		$this->convertImage($mainOriginalFilePath,$altOriginalFilePath,$alterExt);
		$this->convertImage($mainMediumFilePath,$altMediumFilePath,$alterExt);
		$this->convertImage($mainThumb_path,$altThumb_path,$alterExt);
		
		Image::make($original_path.$newRandName.'.png')->resize($fileMng_largeWidth, $fileMng_largeHeight, function($constraint) { $constraint->aspectRatio(); })->save($fileMngrLargePath);
		Image::make($original_path.$newRandName.'.png')->resize($fileMng_mediumWidth, $fileMng_mediumHeight, function($constraint) { $constraint->aspectRatio(); })->save($fileMngrMediumPath);
		Image::make($original_path.$newRandName.'.png')->resize($fileMng_smallWidth, $fileMng_smallHeight, function($constraint) { $constraint->aspectRatio(); })->save($fileMngrSmallPath);
		
		chmod($fileMngrLargePath,0777);
		chmod($fileMngrMediumPath,0777);
		chmod($fileMngrSmallPath,0777);
				
		}catch(\Exception $e){
			return array('status'=>'error','msg'=>'Please provide a valid logo');
		}
		chmod($mainOriginalFilePath,0777);
		chmod($mainMediumFilePath,0777);
		chmod($mainThumb_path,0777);
		chmod($altOriginalFilePath,0777);
		chmod($altMediumFilePath,0777);
		chmod($altThumb_path,0777);
		chmod($mainOriginalFilePathTmp,0777);
		@unlink($mainOriginalFilePathTmp);

		$client = new vuforiaclient();
		$send[0] = $newRandName.'.jpg';
		$send[1] = $original_path.$newRandName.'.jpg';
		$send[2] = $send[1];
		$send[3] = 'Redeemar';
		$send[4] = 'Redeemar';		
		$response=$client->addTarget($send);
		$response_arr=json_decode($response);		

		if($response_arr->result_code=="TargetCreated")
		{
			
			$user=Auth::user();
			
			$newLogo = new Logo();
			$newLogo->contact_email = $user->email;
			$newLogo->cat_id        = $user->cat_id ;
			$newLogo->subcat_id     = $user->subcat_id;
			$newLogo->company_name  = $user->company_name;
			
			$newLogo->first_name    = $user->first_name;
			$newLogo->last_name     = $user->last_name;
			$newLogo->address       = $user->address;
			$newLogo->city			= $user->city;
			$newLogo->state			= $user->state;
			$newLogo->zipcode       = $user->zipcode;
			$newLogo->lat           = $user->lat;
			$newLogo->lng			= $user->lng;
			$newLogo->mobile		= $user->mobile;
			$newLogo->web_address	= $user->web_address;
			$newLogo->enhance_logo  = $request->enhance_logo=='true'?1:0;
			$newLogo->status 	    = 1;
			$newLogo->action_id     = 1;
			$newLogo->uploaded_by   = $user->id;
			$newLogo->target_id     = $response_arr->target_id;
			$newLogo->logo_name     = $newRandName.'.'.$mainExt;			
			$newLogo->reedemer_id   = $user->id;
			
			$rating = $this->getVuforiarate($newLogo->target_id);
			$newLogo->tracking_rating  = $rating?$rating:0;
			
			if($newLogo->save())
			{
				$logo_id = $newLogo->id;
				return array('status'=>'ok','target_id'=>$newLogo->target_id,'logo_id'=>$logo_id);
			}else{
				return array('status'=>'error','msg'=>'Oops. Something went wrong. Please try again later.');
			}
		}
		return array('status'=>'error','msg'=>'Oops. Something went wrong. Please try again later.');

		$logo->delete();
		
		if($response_arr->result_code=="UnknownTarget")
		{
			return "UnknownTarget";
		}
		else
		{
			return 'success';
		}
	}
	
	public function convertImage($src,$dst,$type){
		$srcMethod = $type=='png'?'imagecreatefromjpeg':'imagecreatefrompng'  ;
		$src = $srcMethod($src);
		
		$width = imagesx($src);
		$height = imagesy($src);
		$bg = imagecreatetruecolor($width, $height);
		$white = imagecolorallocate($bg, 255, 255, 255);
		imagefill($bg, 0, 0, $white);
		imagecopy(
	        $bg,
	        $src,
	        0, 0, 0, 0,
	        $width,
	        $height
	    );
		$method = $type=='png'?'imagepng':'imagejpeg';
		$method($bg, $dst);
	}
	
	
	// public function getDeletelogo($id)
	// {
	// 	if(!$id) return false;
		
	// 	$logo = Logo::find($id); 		
		
	// 	$logo_original_path="../uploads/thumb/".$logo->logo_name;
	// 	$logo_medium_path="../uploads/thumb/".$logo->logo_name;
	// 	$logo_thumb_path="../uploads/thumb/".$logo->logo_name;
	// 	if(file_exists($logo_original_path))
	// 	{
	// 		@unlink($logo_original_path);
	// 	}
	// 	if(file_exists($logo_medium_path))
	// 	{
	// 		@unlink($logo_medium_path);
	// 	}
	// 	if(file_exists($logo_thumb_path))
	// 	{
	// 		@unlink($logo_thumb_path);
	// 	}
		
	// 	$client = new vuforiaclient();
			
	// 	$response=$client->deleteTargets($logo->target_id);  
		 
	// 	$response_arr=json_decode($response);

	// 	$logo->delete();
		
	// 	if($response_arr->result_code=="UnknownTarget")
	// 	{
	// 		return "UnknownTarget";
	// 	}
	// 	else
	// 	{
	// 		return 'success';
	// 	}		
	// }
	
	public function getLogodetails($logo_id)
	{
		if(!$logo_id) return false;
		return Logo::find($logo_id);
	}
	
	public function postAddreedemar(Request $request)
	{
		$response = array();
		$vf = new vuforiaclient();
		$zipcode=$request->get('zipcode');
		$location_arr=$this->get_lat_lng($zipcode);
		if(!isset($location_arr['lat']) || !$location_arr['lat']){
			return array('status'=>'error','msg'=>'Please enter a valid zip code','track'=>'API not providing lat,lng');
		}
		
		if(!$request->target_id){
			return array('status'=>'error','msg'=>'Please upload/select your logo');
		}
		if(!$request->subcat_id){
			return array('status'=>'error','msg'=>'Please select subcategory');
		}
		
		$isExistEmail = User::where('email',$request->user_email)->count();
		if($isExistEmail){
			return array('status'=>'error','msg'=>'Email id is already exist in our system');
		}

		if($request->email_as_username == 'false'){
			$isExistUsername = User::where('username',$request->username)->count();
			if($isExistUsername){
				return array('status'=>'error','msg'=>'Username is already exist in our system');
			}
		}

		$lat=$location_arr['lat'];
		$lng=$location_arr['lng'];
			
		$location = isset($location_arr['location'])?$location_arr['location']:'';
		
		$logo_name = $request->logo_name;
		$logoNameArr = array_reverse(explode('.',$logo_name));
		$logo_ext = $logoNameArr[0];
		$logoBaseName = str_replace('.'.$logo_ext,'',$logo_name);
		
		$logo = Logo::where('target_id',$request->target_id)->first();
		if(!$logo){
			$logo = new Logo();
			$logo->target_id = $request->get('target_id');
			
			$fileMng_largeWidth   = env('LOGO_LARGE_WIDTH');
			$fileMng_largeHeight  = env('LOGO_LARGE_HEIGHT');
			$fileMng_mediumWidth  = env('LOGO_MEDIUM_WIDTH');
			$fileMng_mediumHeight = env('LOGO_MEDIUM_HEIGHT');
			$fileMng_smallWidth   = env('LOGO_SMALL_WIDTH');
			$fileMng_smallHeight  = env('LOGO_SMALL_HEIGHT');
			
			$fileMngrSmallPath  =  rtrim(base_path(),'/admin') .'/filemanager/userfiles/small/'.$logoBaseName.'.png';
			$fileMngrMediumPath =  rtrim(base_path(),'/admin') .'/filemanager/userfiles/medium/'.$logoBaseName.'.png';
			$fileMngrLargePath  =  rtrim(base_path(),'/admin') .'/filemanager/userfiles/large/'.$logoBaseName.'.png';
			
			$thumb_path= base_path() . "/uploads/thumb/".$logoBaseName;
			$medium_path= base_path() . "/uploads/medium/".$logoBaseName;
			$original_path= base_path() . "/uploads/original/".$logoBaseName;
	
	
			$temp_thumb_path= base_path() . "/uploads/temp_thumb/".$logoBaseName;
			$temp_medium_path= base_path() . "/uploads/temp_medium/".$logoBaseName;
			$temp_original_path= base_path() . "/uploads/temp_original/".$logoBaseName;
			
			try{
				copy($temp_thumb_path.'.jpg',$thumb_path.'.jpg');
				copy($temp_medium_path.'.jpg',$medium_path.'.jpg');
				copy($temp_original_path.'.jpg',$original_path.'.jpg');
				
				copy($temp_thumb_path.'.png',$thumb_path.'.png');
				copy($temp_medium_path.'.png',$medium_path.'.png');
				copy($temp_original_path.'.png',$original_path.'.png');
				
				chmod($thumb_path.'.jpg',0777);
				chmod($medium_path.'.jpg',0777);
				chmod($original_path.'.jpg',0777);
				
				chmod($thumb_path.'.png',0777);
				chmod($medium_path.'.png',0777);
				chmod($original_path.'.png',0777);
				
				chmod($temp_thumb_path.'.jpg',0777);
				chmod($temp_medium_path.'.jpg',0777);
				chmod($temp_original_path.'.jpg',0777);
				
				chmod($temp_thumb_path.'.png',0777);
				chmod($temp_medium_path.'.png',0777);
				chmod($temp_original_path.'.png',0777);
				
				Image::make($temp_original_path.'.png')->resize($fileMng_largeWidth, $fileMng_largeHeight, function($constraint) { $constraint->aspectRatio(); })->save($fileMngrLargePath);
				Image::make($temp_original_path.'.png')->resize($fileMng_mediumWidth, $fileMng_mediumHeight, function($constraint) { $constraint->aspectRatio(); })->save($fileMngrMediumPath);
				Image::make($temp_original_path.'.png')->resize($fileMng_smallWidth, $fileMng_smallHeight, function($constraint) { $constraint->aspectRatio(); })->save($fileMngrSmallPath);
								
				chmod($fileMngrLargePath,0777);
				chmod($fileMngrMediumPath,0777);
				chmod($fileMngrSmallPath,0777);
			}catch(\Exception $e){
				return array('status'=>'error','msg'=>'Please choose a valid logo');
			}
		}
		$logo->logo_name 		= $logo_name;		
		$logo->logo_text 		= $request->get('company_name');
		$logo->status 			= 1;
		$logo->default_logo 	= 1;
		$logo->target_id		= $request->target_id;
		$logo->tracking_rating 	= $request->get('tracking_rating');
		$logo->cat_id 			= $request->get('category_id');
		$logo->subcat_id 		= $request->get('subcat_id');
		$logo->enhance_logo 	= $request->get('enhance_logo');
		$logo->company_name 	= $request->get('company_name');			
		$logo->first_name 		= $request->get('first_name');
		$logo->last_name 		= $request->get('last_name');
		$logo->address 			= $request->get('address');
		$logo->city 			= $request->get('city');
		$logo->state 			= $request->get('state');
		$logo->zipcode 			= $zipcode;
		$logo->lat 				= $lat;
		$logo->lng 				= $lng;
		$logo->location 		= $location;
		$logo->contact_email 	= $request->user_email;
		$logo->mobile 			= $request->get('mobile');
		$logo->web_address 		= $request->get('web_address');
		$logo->cat_id 			= $request->get('category_id');
		$logo->subcat_id 		= $request->get('subcat_id');
		
		$user = new User();
		
		$user->company_name = $request->get('company_name');			
		$user->first_name 	= $request->get('first_name');
		$user->last_name 	= $request->get('last_name');
		$user->address 		= $request->get('address');
		$user->city 		= $request->get('city');
		$user->state 		= $request->get('state');
		$user->zipcode 		= $zipcode;
		$user->lat 			= $lat;
		$user->lng 			= $lng;
		$user->location		= $location;
		$user->email 		= $request->get('user_email');
		$user->mobile 		= $request->get('mobile');
		$user->web_address 	= $request->get('web_address');		
		$user->password 	= bcrypt($request->get('user_password'));
		$user->cat_id 		= $request->get('category_id');
		$user->subcat_id 	= $request->get('subcat_id');
		$user->owner 		= $request->get('owner');
		$user->offer_permission 		= $request->get('offer_permission');
		$user->status 		= 1;
		$user->approve 		= 1;
		$user->type 		= 2;			
		$user->source 		= 1;  //1:Web, 2:Android, 3:IOS
		
		if($request->email_as_username == 'true'){
			$user->username = $user->email;
		}else{
			$user->username = $request->username;
		}
		
		if($user->save())
		{
			// Create user with same email if user checked
			if($request->get('create_user')==1)
			{
				$reedemar_user=new User();
				$reedemar_user->first_name 	= $request->get('first_name');
				$reedemar_user->last_name  	= $request->get('last_name');
				$reedemar_user->email 	   	= $request->get('user_email');
				$reedemar_user->mobile 		= $request->get('mobile');
				$reedemar_user->password   	= bcrypt($request->get('user_password'));
				$reedemar_user->username   	= $user->username;
				$reedemar_user->status 	   	= 1;
				$reedemar_user->approve    	= 1;
				$reedemar_user->type 	   	= 3;
				$reedemar_user->save();
			}

			$logo->reedemer_id = $user->id;
			$logo->uploaded_by = $user->id;
			
			try{
				$upload_dir = base_path()."/uploads/";
				$copy_base_path = rtrim(base_path(),'admin/') . '/filemanager/userfiles/'.$user->id;
				$upload_path=$upload_dir."/".$user->id;
				
				if(!file_exists($upload_path))
				{
					helpers::createDir($upload_path, 0777);
					helpers::createDir($upload_path.'/Store', 0777);
				}
				if(!file_exists($upload_path.'/Store'))
				{
					helpers::createDir($upload_path, 0777);
				}		
				if(!file_exists($copy_base_path))
				{
					helpers::createDir($copy_base_path, 0777);
					helpers::createDir($copy_base_path.'/Store', 0777);
				}
				if(!file_exists($copy_base_path.'/Store'))
				{
					helpers::createDir($copy_base_path.'/Store', 0777);
				}
				$logo->save();
				@unlink($temp_thumb_path.'.png');
				@unlink($temp_medium_path.'.png');
				@unlink($temp_original_path.'.png');
				
				@unlink($temp_thumb_path.'.jpg');
				@unlink($temp_medium_path.'.jpg');
				@unlink($temp_original_path.'.jpg');
				return array('status'=>'ok','msg'=>'Registration successful');
			}catch(\Exception $e){
				$user->delete();
				$reedemar_user->delete();
				@unlink($thumb_path.'.png');
				@unlink($medium_path.'.png');
				@unlink($original_path.'.png');
				
				@unlink($thumb_path.'.jpg');
				@unlink($medium_path.'.jpg');
				@unlink($original_path.'.jpg');
				
				@unlink($fileMngrLargePath);
				@unlink($fileMngrMediumPath);
				@unlink($fileMngrSmallPath);
				
				if($logo->target_id){
					$vf->deleteTargets($logo->target_id);
				}

				return array('status'=>'error','track'=>$e->getMessage(),'msg'=>'Oops. Something went wrong. Please try again later.');
			}

		}
		
		return array('status'=>'error','msg'=>'Oops. Something went wrong. Please try again later.');

	}
	
	
	public function getUpdatelocation(){
		$users = User::all();
		foreach($users as $user){
			if($user->zipcode && !$user->location){
				$location_arr=$this->get_lat_lng($user->zipcode);
				$location = isset($location_arr['location'])?$location_arr['location']:'';
				$user->location = $location;
				$user->save();
			}
		}
		echo 'Process is completed';
	}


	public function postAddlogo(Request $request)
	{
		//Upload logo to server		
		if($_FILES['logo_image']['name']!="")
		{
			if($_FILES['logo_image']['type']=="image/jpeg" || $_FILES['logo_image']['type']=="image/jpg")
			{
				if((($_FILES['logo_image']['size']/1024)/1024) <=2)
				{
					$destinationPath ='../uploads/original/'; // upload path			
					$extension = Input::file('logo_image')->getClientOriginalExtension(); // getting image extension
					$fileName = time()."_".rand(111111111,999999999).'.'.$extension;
					Input::file('logo_image')->move($destinationPath, $fileName); // uploading file to given path
				}
				else
				{
					return redirect()->back()	
					->withInput()								
					->withErrors([
						'message' => 'Unable to upload your logo. Please try again',
					]);
				}
			}
			else
			{
					return redirect()->back()	
					->withInput()								
					->withErrors([
						'message' => 'Upload only jpg file within 2MB size',
					]);
			}
		}
		else
		{
			return redirect()->back()	
			->withInput()								
			->withErrors([
				'message' => 'Upload only jpg file within 2MB size',
			]);
		}


		$client = new vuforiaclient();
		
		$send[0] = $fileName;
		$send[1] = '../uploads/original/'.$fileName;
		$send[2] = '../uploads/original/'.$fileName;
		$send[3] = 'Redeemar';
		$send[4] = 'Redeemar';		
		$response=$client->addTarget($send);
		$response_arr=json_decode($response);		
		
		$reedemer_id = $request->get('reedemer_id');
		$target_id = $response_arr->target_id;
		$logo_text = $request->get('logo_text');
		
		 $logo = new Logo();
		 $logo->reedemer_id=$reedemer_id;
		 $logo->logo_name=$fileName;
		 $logo->logo_text=$logo_text;
		 $logo->status=0;
		 $logo->default_logo=1;
		 $logo->target_id=$target_id;
		 $logo->uploaded_by=$reedemer_id;
		 $logo->tracking_rating=-1;
		 
		 if($logo->save())
		 {
		 	//Add demo inventory item autometically
		 	$inventory_image=$this->add_default_inventory($reedemer_id);

		 	//Add demo campaign item autometically
		 	$campaign=$this->add_default_campaign($reedemer_id);
		 	
		 	Session::flash('message', "Your account created successfully. We will notify you via email after it activated.");

			//return Redirect::to('partner/msg')->with('reedemer_id',$reedemer_id);
			//Redirect::route('partner/msg', [$reedemer_id]);
			//dd($reedemer_id);
			//return Redirect::route('partner.msg')->with('reedemer_id', $reedemer_id);
			//return redirect()->intended( '/partner/msg' )->with('reedemer_id', $reedemer_id);
			//return Redirect::url('partner/msg')->with('reedemer_id', $reedemer_id);

			//return Redirect::to('partner/msg')->with('reedemer_id','555')	;
			return redirect('partner/msg/');
		 }
		 else
		 {

			return redirect()->back()	
					->withInput()								
					->withErrors([
						'message' => 'Unable to upload your logo. Please try again',
					]);
		 }
	}


	public function getVuforiarate($target_id,$logo_id='')
	{
		//dd($logo_id);
		$client = new vuforiaclient();
		//$target_id=$target_id->target_id;

		$target_res_details=$client->getTarget($target_id); 
		//$response_arr=json_decode($target_res_details);
		$response_arr=json_decode($target_res_details);
		$tracking_rating=$response_arr->target_record->tracking_rating;
		
		return $tracking_rating;		
	}

	public function getMsg()
	{
//		$user=User::find($id);

		//Use Crypt::decrypt();
//		$value = Crypt::decrypt($user->password);
//
//		dd($value);
		//$this->getVuforiarate();
		return view('partner.msg');
	}

	//Listing to show only 
	public function getSubcategory($parent_id='')
	{	
		//dd($parent_id)	;
		if($parent_id!='')
		{
			//$id=$request[0];
			$category = Category::where('parent_id',$parent_id)
						->where('visibility',1)
						->where('status',1)
						->get();
		}
		else
		{
			$category = Category::where('visibility',1)
						->where('status',1)
						->orderBy('id','DESC')
						->get();
		}
		//dd($category->toArray());
		return $category;
	}

	//Delete logo from admin panel
	public function postDeletelogo(Request $request){
		// dd($request->all());
		$id = $request->logo_id;
		if(!$id) return false;
		
		$logo = Logo::find($id);

		$logo_name = $logo->logo_name;
		$logoNameArr = array_reverse(explode('.',$logo_name));
		$logo_ext = $logoNameArr[0];
		$mainExt  = $logo_ext=='png'?'png':'jpg';
		$alterExt = $logo_ext=='png'?'jpg':'png';

		$logo_original_path="../uploads/small/".$logo->logo_name;
		$logo_medium_path="../uploads/medium/".$logo->logo_name;
		$logo_thumb_path="../uploads/large/".$logo->logo_name;

		$fileMngrSmallPath  =  rtrim(base_path(),'/admin') .'/filemanager/userfiles/small/'.$logo->logo_name;
		$fileMngrMediumPath =  rtrim(base_path(),'/admin') .'/filemanager/userfiles/medium/'.$logo->logo_name;
		$fileMngrLargePath  =  rtrim(base_path(),'/admin') .'/filemanager/userfiles/large/'.$logo->logo_name;

		$alt_logo_original_path="../uploads/small/".$logoNameArr[1].'.'.$alterExt;
		$alt_logo_medium_path="../uploads/medium/".$logoNameArr[1].'.'.$alterExt;
		$alt_logo_thumb_path="../uploads/large/".$logoNameArr[1].'.'.$alterExt;

		$alt_fileMngrSmallPath  =  rtrim(base_path(),'/admin') .'/filemanager/userfiles/small/'.$logoNameArr[1].'.'.$alterExt;
		$alt_fileMngrMediumPath =  rtrim(base_path(),'/admin') .'/filemanager/userfiles/medium/'.$logoNameArr[1].'.'.$alterExt;
		$alt_fileMngrLargePath  =  rtrim(base_path(),'/admin') .'/filemanager/userfiles/large/'.$logoNameArr[1].'.'.$alterExt;


		if(file_exists($logo_original_path))
		{
			@unlink($logo_original_path);
		}
		if(file_exists($logo_medium_path))
		{
			@unlink($logo_medium_path);
		}
		if(file_exists($logo_thumb_path))
		{
			@unlink($logo_thumb_path);
		}

		if(file_exists($fileMngrSmallPath))
		{
			@unlink($fileMngrSmallPath);
		}
		if(file_exists($fileMngrMediumPath))
		{
			@unlink($fileMngrMediumPath);
		}
		if(file_exists($fileMngrLargePath))
		{
			@unlink($fileMngrLargePath);
		}


		if(file_exists($alt_logo_original_path))
		{
			@unlink($alt_logo_original_path);
		}
		if(file_exists($alt_logo_medium_path))
		{
			@unlink($alt_logo_medium_path);
		}
		if(file_exists($alt_logo_thumb_path))
		{
			@unlink($alt_logo_thumb_path);
		}

		if(file_exists($alt_fileMngrSmallPath))
		{
			@unlink($alt_fileMngrSmallPath);
		}
		if(file_exists($alt_fileMngrMediumPath))
		{
			@unlink($alt_fileMngrMediumPath);
		}
		if(file_exists($alt_fileMngrLargePath))
		{
			@unlink($alt_fileMngrLargePath);
		}
		
		$client = new vuforiaclient();
			
		$response=$client->deleteTargets($logo->target_id);  
		 
		$response_arr=json_decode($response);

		$logo->delete();
		
		if($response_arr->result_code=="UnknownTarget")
		{
			return "UnknownTarget";
		}
		else
		{
			return 'success';
		}		
	}

	function get_lat_lng($zip)
	{
		$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($zip)."&sensor=false&key=".env('GOOGLE_GEO_API_KEY');
		$result_string = file_get_contents($url);
		$result = json_decode($result_string, true);
		
		$result1[]= (isset($result['results']) && isset($result['results'][0]))?$result['results'][0]:0;
		$result2[]= (isset($result1[0]) && isset($result1[0]['geometry']))?$result1[0]['geometry']:0;
		$result3[]= (isset($result2[0]) && isset($result2[0]['location']))?$result2[0]['location']:0;
		
		
		if(isset($result1[0]['address_components']) && isset($result1[0]['address_components'][1]) && isset($result1[0]['address_components'][1]['long_name']) )
		{
			$result3[0]['location'] = $result1[0]['address_components'][1]['long_name'];
		}
		
		return $result3[0];
	}
	
	function add_default_campaign($reedemer_id)
	{		
		$start_date=date('Y-m-d');
		$end_date=date('Y-m-d', strtotime("+30 days"));

		$campaign = new Campaign();
		$campaign->campaign_name="Default Campaign";
		$campaign->start_date=$start_date;
		$campaign->end_date=$end_date;
		$campaign->status=1;
		$campaign->created_by=$reedemer_id;
		if($campaign->save())
		{
			return $campaign->id;
		}
		else
		{
			return 'error';
		}

	}

	function add_default_inventory($reedemer_id)
	{
		$base_image_path='../uploads/default_pizza.jpg'; // upload path			
		$destinationPath ='../uploads/inventory/original/'; // upload path			
		$destinationPathThumb ='../uploads/inventory/thumb/'; // upload path			
		$destinationPathMedium ='../uploads/inventory/medium/'; // upload path	
		
		$extension="jpg";
		$fileName = "demo_".time()."_".rand(111111111,999999999).'.'.$extension;
		$original_image=$destinationPath.$fileName;
		$thumb_image=$destinationPathThumb.$fileName;
		$medium_image=$destinationPathMedium.$fileName;


		copy($base_image_path, $original_image);
		copy($base_image_path, $thumb_image);
		copy($base_image_path, $medium_image);		
		
		$inventory = new Inventory();
		$inventory->inventory_name="Default Product";
		$inventory->inventory_image=$fileName;
		$inventory->sell_price="100";
		$inventory->cost="40";
		$inventory->status=1;
		$inventory->created_by=$reedemer_id;
		if($inventory->save())
		{
			return $inventory->id;
		}
		else
		{
			return 'error';
		}

	}

	public function getForgotpassword()
	{
		return view('auth.forgetpassword');
	}
	
	public function getResetpassword($token)
	{
		if($token){
			$userData = PasswordReset::where('token', $token)->get();
			// dd($userData->toArray());
			if($userData){
				return view('auth.changepassword',$userData[0]);
			} else {
				return view('/auth/login');
			}
		} else {
			return view('/auth/login');
		}
		//return view('auth.changepassword');
	}

	function create_thumb($src, $dest, $desired_width)
	{
		/* read the source image */
		$source_image = imagecreatefromjpeg($src);
		$width = imagesx($source_image);
		$height = imagesy($source_image);
		
		/* find the "desired height" of this thumbnail, relative to the desired width  */
		$desired_height = floor($height * ($desired_width / $width));
		
		/* create a new, "virtual" image */
		$virtual_image = imagecreatetruecolor($desired_width, $desired_height);
		
		/* copy source image at a resized size */
		imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
		
		/* create the physical thumbnail image to its destination */
		imagejpeg($virtual_image, $dest);
	}


}
