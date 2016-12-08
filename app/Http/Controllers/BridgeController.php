<?php namespace App\Http\Controllers;
use Auth;
use DB;
use App\Model\Wptoken;
use App\Model\Demotest;
use App\Model\Pp;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response; 
use Redirect;
use Input;
use Session;
use App\Helper\vuforiaclient;
use App\Model\User;
use App\Model\StoreImage;
use App\Model\Video;
use App\Model\Logo;
use App\Model\Category;
use App\Model\UserPassedOffer;
use App\Model\UserBankOffer;
use App\Model\RedeemptionOffer;
use App\Model\Offer;
use App\Model\Feedback;
use App\Model\FrontUser;
use App\Model\Beacon;
use Illuminate\Http\Request;
use Hash;
use Illuminate\Encryption\Encrypter;

class BridgeController extends Controller {

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
	public function postIndex(Request $request)
	{		
		$data=json_decode($request->get('data'));
		$target_id=$data->target_id;
		$webservice_name=$data->webservice_name;

		// Put all response to database for testing purpose
		// Will have to remove this later
		
	 	$demotest=new Demotest();
	 	$demotest->target_id=$target_id;
	 	$demotest->save();
	 	//dd("B");
		if($webservice_name=='')
		{
			$response['status']='failure';
			$response['messageCode']='R0001'; //Webservice name is missing
		}
		if($target_id=='')
		{
			$response['status']='failure';
			$response['messageCode']='R0002'; //Target ID is missing
		}
		
		$base_path=getenv('WEBSERVICE');
		$webservice_name=$webservice_name;
		$target_id=$target_id;

		 $data = array(
		   	'target_id' => urlencode($target_id)
		 );		

		switch ($webservice_name) {

		case "check_target":
			$url=$base_path."checktarget";
		break;

		case "userregister":
			$url=$base_path."userregister";
		break;

		case "userlogin":
			$url=$base_path."userlogin";
		break;

		case "userdetail":
			$url=$base_path."userdetail";
		break;

		case "showoffers":
			$url=$base_path."offerlist";
		break;	
		case "offerdetail":
			$url=$base_path."offerdetail";
		break;

		case "validateofferdetail":
			$url=$base_path."validateofferdetail";
		break;


		case "alloffers":
			$url=$base_path."alloffers";
		break;

		case "mapalloffers":
			$url=$base_path."mapalloffers";
		break;	

		case "myoffer":
			$url=$base_path."myoffer";
		break;	

		case "bankoffer":
			$url=$base_path."bankoffer";
		break;

		case "passoffer":
			$url=$base_path."passoffer";
		break;	

		case "mypassedoffer":
			$url=$base_path."mypassedoffer";
		break;

		case "redeempasscode":
			$url=$base_path."redeempasscode";
		break;	

		case "redeemption":
			$url=$base_path."redeemption";
		break;	

		case "socialsignup":
			$url=$base_path."socialsignup";
		break;	

		case "multicat":
			$url=$base_path."multicat";
		break;	

		case "userprofile":
			$url=$base_path."userprofile";
		break;	

		case "sendfeedback":
			$url=$base_path."sendfeedback";
		break;	



		case "updateprofile":
			$url=$base_path."updateprofile";
		break;	


		default:
			$url=$base_path."not_found";
		}

		$response= $this->post_to_url($url, $data);
		$json = json_decode($response, true);		

		return $response;		
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


	// User Registration

	public function postUserregister(Request $request)
	{


		$today = date("Y-m-d H:i:s");

 		$status = 1;
 		$approve = 1;
 		$type = 3;
		$offer_id = 0;

 		$email = "";
 		$device_token = "";
 		$password = "";
 		$source = "";

 		$data=json_decode($request->get('data'));

 		if(isset($data->email))
 			$email = $data->email;

 		if(isset($data->device_token))
 			$device_token = $data->device_token;
 		
 		if(isset($data->password))
 			$password = bcrypt($data->password);

 		if(isset($data->source))
 			$source = $data->source;


 		if(isset($data->offer_id))
 			$offer_id = $data->offer_id;


 		if($email != "" && $password != "") {

	 		$encrypter = app('Illuminate\Encryption\Encrypter');
			$encrypted_token = $encrypter->encrypt(csrf_token());

			$userdata=User::where('email', $email)->first();

			if(count($userdata)>0)
			{
				$datalist['messageCode']="R01002";
				$datalist['data']="User with this email id already exist.";

			}
			else
			{

				$userdatalist = User::create(['email' => $email, 'password' => $password, 'status' => $status , 'approve' => $approve ,  'device_token' => $device_token , 'source' => $source , 'type' => 3]);


				if(isset($userdatalist->id) && $offer_id  > 0) {

					$frontUserData = FrontUser::where('user_login', $email)->first();

					// If duplicate user is not already exisiting in front_users (WP) table
					if(count($frontUserData) == 0) {

						// If successfully entered into the user table then now insert into front_users (WP) table
						$frontuserdatalist = FrontUser::create(['user_login' => $email, 'user_pass' => md5($password), 'user_email' => $email , 'user_registered' => $today]);


					}


					// Add any offer in the queue to banked offer list
					$user_id = $userdatalist->id;
					$this->addToBankedOffer($offer_id, $user_id);

				}

				$datalist['messageCode']="R01001";
				$datalist['data']=$userdatalist;

	       }
 		}
 		else {
	 		$datalist['messageCode']="R01004";
			$datalist['data']="Either email or password is not found.";

 		}

	   return $datalist;

	}

	//function for social signup
	public function postSocialsignup(Request $request)
	{


		$status = 1;
 		$approve = 1;
 		$type = 3;
		$offer_id = 0;

 		$email = "";
 		$device_token = "";
 		$password = "";
 		$source = "";
 		$social_type = "";
 		$facebook_id = "";
 		$google_id = "";

 		$first_name = "";
 		$last_name = "";



		$seed = str_split('abcdefghijklmnopqrstuvwxyz'.'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.'0123456789!@#'); 
		shuffle($seed); 
		foreach (array_rand($seed, 5) as $k) $password .= $seed[$k];

 		$data=json_decode($request->get('data'));

 		if(isset($data->email))
 			$email = $data->email;

 		if(isset($data->first_name))
 			$first_name = $data->first_name;

 		if(isset($data->last_name))
 			$last_name = $data->last_name;

 		if(isset($data->device_token))
 			$device_token = $data->device_token;
 		
 		if(isset($data->source))
 			$source = $data->source;

 		if(isset($data->social_type))
 			$social_type = $data->social_type;

 		if($password != "")
 			$password = bcrypt($password);

 		if(isset($data->offer_id))
 			$offer_id = $data->offer_id;

 		if(isset($data->facebook_id))
 			$facebook_id = $data->facebook_id;

 		if(isset($data->google_id))
 			$google_id = $data->google_id;

 		$encrypter = app('Illuminate\Encryption\Encrypter');
		$encrypted_token = $encrypter->encrypt(csrf_token());


		if($email != "" && $social_type != "") {

			$userdata=User::where('email',$email)
					->where('type', 3)
					->first();

			if(count($userdata)>0)
			{
	 
				$datalist['messageCode']="R01002";
				$datalist['data']=$userdata;

				if($social_type=='facebook')
				{
					if($facebook_id != "")					
						$updateSocialType = User::where('id',$userdata->id)->update(array('facebook_token'=> $facebook_id,'status' => 1 , 'approve' => 1 ,'source' => $source , 'device_token' => $device_token));
					else {
						$datalist['messageCode']="R01007";
						$datalist['data']="Facebook id not found.";
					}

				}
				else
				{
					if($google_id != "")
						$updateSocialType = User::where('id',$userdata->id)->update(array('googleplus_token'=> $google_id ,'status' => 1 , 'approve' => 1 ,'source' => $source , 'device_token' => $device_token));
					else {
						$datalist['messageCode']="R01008";
						$datalist['data']="Google id not found.";
					}
				}
			}
			else
			{
				if($social_type=='facebook')
				{
					if($facebook_id != "")
						$userdatalist = User::create(['email' => $email, 'password' => $password, 'status' => $status , 'approve' => $approve ,  'device_token' => $device_token , 'source' => $source , 'type' => 3 ,'facebook_token'=> $facebook_id, 'first_name' => $first_name, 'last_name' => $last_name]);
					else {
						$datalist['messageCode']="R01004";
						$datalist['data']="Facebook id not found.";
					}
						
				}
				elseif($social_type=='google')
				{
					
					if($google_id != "")
						$userdatalist = User::create(['email' => $email, 'password' => $password, 'status' => $status , 'approve' => $approve ,  'device_token' => $device_token , 'source' => $source , 'type' => 3 ,'googleplus_token'=> $google_id,  'first_name' => $first_name, 'last_name' => $last_name]);
					else {
						$datalist['messageCode']="R01005";
						$datalist['data']="Google id not found.";
					}
								
				}
				else
				{
	              	$userdatalist = User::create(['email' => $email, 'password' => $password, 'status' => $status , 'approve' => $approve ,  'device_token' => $device_token , 'source' => $source , 'type' => 3 , 'first_name' => $first_name, 'last_name' => $last_name]);

				}

				if(isset($userdatalist) && count($userdatalist) > 0) {

					$datalist['messageCode']="R01001";

					$datalist['data']=$userdatalist;

					$this->sendRegisterEmail($email,$password);

					if(isset($userdatalist->id)) {
						$user_id = $userdatalist->id;
						$this->addToBankedOffer($offer_id, $user_id);

					}


				}
				else {
					// Generic error code
					if(!isset($datalist['messageCode']) || $datalist['messageCode'] == "")
						$datalist['messageCode'] = "R01009";

				}

	       }
	   } 
	   else {
	   		$datalist['messageCode']="R01003";
			$datalist['data']="Email and/or social type is not found.";
	   }
		
	   return $datalist;

	}



	// User Login

	public function postUserlogin(Request $request)
	{
		$offer_id = 0;
		$email = "";
		$password = "";

		$data = json_decode($request->get('data'));

		if(isset($data->email) && $data->email != "" && isset($data->password) && $data->password != "") {



			$credentials['email'] = $data->email;

			$credentials['password'] = $data->password;

			if(isset($data->offer_id))
				$offer_id = $data->offer_id;


			if (Auth::attempt($credentials)) {

				$user = Auth::user();


				if($user->status==0)
				{

					$userdetail['messageCode']="R01003";
					$userdetail['data']='You account is deactivated.';

				}
				else if($user->approve==0)
				{

					$userdetail['messageCode']="R01005";
					$userdetail['data']='You account is not approve yet.';

				}

				else
				{



					if($user->status==1 && $user->type==3)
					{

						// Insert Offer Id

						//Get Count of Bank offer
						$user_id = $user->id;

						$this->addToBankedOffer($offer_id, $user_id);

						$currentdate=date('Y-m-d h:i:s');

						$userbankoffer=UserBankOffer::where('user_id',$user->id)->where('validate_within','>=', $currentdate)->get();

						$totalbankoffer=count($userbankoffer);


						$dataArr=array('user_id'=>$user->id, 'first_name' => $user->first_name, 'last_name' => $user->last_name, 'email' => $user->email, 'phone' => $user->phone, 'totalbankoffer' => $totalbankoffer, 'userStatus' => 1, 'device_token'=>$user->device_token);

						$userdata=json_encode($dataArr);

						$userdetail['messageCode']="R01001";

						$userdetail['data']=$userdata;

					}
					else if($user->type==1)
					{

						$userdetail['messageCode']="R01002";
						$userdetail['data']='You are not allowed to login from mobile device, login from web';

					}

				}
			}
			else
			{

				$userdetail['messageCode']="R01004";
				$userdetail['data']='Invalid Username and Password.';

			}

		}
		else
		{

			$userdetail['messageCode']="R01006";
			$userdetail['data']='Email and/or password not found.';

		}
		

		return $userdetail;
	}


	// Show user detail


	public function postUserdetail(Request $request)
	{
      
      $data=json_decode($request->get('data'));

      $device_token=$data->device_token;

      $source=$data->source;

      $userdata=User::where('source',$source)->where('device_token',$device_token)->first();

      if(count($userdata)>0)
		{

		$userdetail['messageCode']="R01001";

		$userdetail['data']=$userdata;

	    }
	    else
	    {

	    $userdetail['messageCode']="R01002";

		$userdetail['data']="No record found.";

	    }


		return $userdetail;

	}


	// Show Offer List 

	public function postAlloffers(Request $request)
	{
      
		$data=json_decode($request->get('data'));

		$now=date('Y-m-d h:i:s');

		$lat=$data->lat;

		$long=$data->long;

		$radius=$data->radius;

		$user_id=$data->user_id;

		$zipcodes= $this->getDistance($lat, $long ,$radius);

		$zipval=[];

		foreach($zipcodes as $zip)
		{

		$zipval[]=$zip->zipcode;

		 }


		 // Offer List


		if($user_id>0)
		{
		// Get passed users list offer

		$userbankoffer=UserBankOffer::where('user_id',$user_id)->with('userDetail')->lists('offer_id');

		$userpassedoffer=UserPassedOffer::where('user_id',$user_id)->with('userDetail')->lists('offer_id');

		$offer_list=Offer::select(array('*',DB::raw('DATEDIFF(CAST(end_date as char), NOW()) AS expires')))->where('max_redeemar','>',0)->whereIn('zipcode',$zipval)->whereNotIn('status',array(2,4))->where('end_date','>=',$now)->whereNotIn('id',$userbankoffer)->whereNotIn('id',$userpassedoffer)->with('categoryDetails','subCategoryDetails','partnerSettings','companyDetail')->orderBy('created_at','desc')->get();

		}
		else
		{

			$offer_list=Offer::select(array('*',DB::raw('DATEDIFF(CAST(end_date as char), NOW()) AS expires')))->where('max_redeemar','>',0)->whereIn('zipcode',$zipval)->whereNotIn('status',array(2,4))->where('end_date','>=',$now)->with('categoryDetails','subCategoryDetails','partnerSettings','companyDetail')->orderBy('created_at','desc')->get();


		}



		// $offer_list=Offer::select(array('*',DB::raw('DATEDIFF(CAST(end_date as char), NOW()) AS expires')))->whereIn('zipcode',$zipval)->whereNotIn('status',array(2,4))->where('end_date','>=',$now)->with('categoryDetails','subCategoryDetails','partnerSettings','companyDetail')->orderBy('created_at','desc')->get();


		if(count($offer_list)>0)
		{

			$datalist['messageCode']="R01001";

			$datalist['data']=$offer_list;

	    }
	    else
	    {

		    $datalist['messageCode']="R01002";

			$datalist['data']="No record found.";

	    }


		return $datalist;

	}

	// Map Offer

	public function postMapalloffers(Request $request)
	{
      
		$data=json_decode($request->get('data'));

		$now=date('Y-m-d h:i:s');

		$lat=$data->lat;

		$long=$data->long;


		//$users=User::where('type', 2)->with('profile')->get();

		$users = User::where('type', 2)->with('profile', 'storeImage')->get();




		if(count($users)>0)
		{

			$datalist['messageCode']="R01001";
			$datalist['data']=$users;

	    }
	    else
	    {

		    $datalist['messageCode']="R01002";
			$datalist['data']="No record found.";

	    }

		return $datalist;

	}



	 function getPostcode($lat, $lng , $radius) {

			$returnValue = NULL;
			$ch = curl_init();
			$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&sensor=false";
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$result = curl_exec($ch);
			$json = json_decode($result, TRUE);

			if (isset($json['results'])) {
			foreach    ($json['results'] as $result) {
			foreach ($result['address_components'] as $address_component) {
			$types = $address_component['types'];
			if (in_array('postal_code', $types) && sizeof($types) == 1) {
			$returnValue = $address_component['short_name'];
			}
			}
			}
			}
			return $returnValue;
	}

	// Show Offer List of User
	// Show Offer List of User
	public function postOfferlist(Request $request)
	{
      
		$data=json_decode($request->get('data'));
		$today = date("Y-m-d H:i:s");
		$offer_list = array();
		$banked_offer_list = array();
		$passed_offer_list = array();
		$datalist = array();
		$type = "0"; // (0 => Fetch all records, 1 => Records filtered by redeemar id, 2 => Records filtered by campaign id, 3 => Records filtered by camapaign id, 3 => Records filtered by category id)
		$lat = "0";
		$long = "0";
		$lat1 = "0";
		$long1 = "0";
		$radius = "100"; // (Default is 100)
		$user_id = "0";
		$campaign_id = "0";
		$keyword = "";
		$reedemar_id = "0";
		$category_id = "0";
		$category_level = "0"; // (0 => Parent Category, 1 => Sub Category, 2 = Sub Sub category)
		$now=date('Y-m-d h:i:s');
		if(isset($data->type))
			$type = $data->type;
		if(isset($data->user_id))
			$user_id = $data->user_id;		
		if(isset($data->reedemar_id))
			$reedemar_id = $data->reedemar_id;		
		
		if(isset($data->campaign_id))
			$campaign_id = $data->campaign_id;
		if(isset($data->category_id))
			$category_id = $data->category_id;	
		if(isset($data->category_level))
			$category_level = $data->category_level;		
		// Location you are searching for
		if(isset($data->lat))
        	$lat=$data->lat;
		if(isset($data->long))        
			$long=$data->long;
		// Your current location
		if(isset($data->selflat))
        	$lat1=$data->selflat;
		if(isset($data->selflong))        
			$long1=$data->selflong;
		
		if(isset($data->radius))  
			$radius=$data->radius;
		
		if(isset($data->keyword))  
			$keyword=$data->keyword;
		if($lat != "" && $long != "" &&  $radius != "") {
			$zipcodes  = $this->getDistance($lat, $long, $radius);
			
			$zipval=[];
			$distanceval=[];
			foreach($zipcodes as $zip)
			{
				$zipval[]=$zip->zipcode;
				/*if($lat1 == $lat && $long1 == $long)
					$distanceval[$zip->zipcode]=$zip->distance;
				else {
					$distanceval[$zip->zipcode]=$this->haversineGreatCircleDistance($lat1, $long1, $lat, $long);
				}*/
			}
			$zipval = array_unique($zipval);
			if($user_id > 0)
			{
				// Get passed users list offer
				//$datalist['messageCode']="R01001";
				$banked_offer_list = UserBankOffer::where('user_id',$user_id)->where('validate_within', '>=', $today)->with('userDetail')->lists('offer_id');
				$passed_offer_list = UserPassedOffer::where('user_id',$user_id)->with('userDetail')->lists('offer_id');
			}
			
			// Show all
			if($type == 0)  {
				// End Date 2016-10-31 00:00:00
				// Example Date: 2016-10-30 04:00:00 is consaidered expired
				//$offer_list = Offer::select(array('*', DB::raw('DATEDIFF(CAST(end_date as char), NOW()) AS expires')))->where('max_redeemar','>',0)->whereIn('zipcode',$zipval)->whereNotIn('status',array(2,4))->where('end_date','>=',$today)->whereNotIn('id',$banked_offer_list)->where('published', 'true')->whereNotIn('id',$passed_offer_list)->with('categoryDetails','subCategoryDetails','partnerSettings','companyDetail','logoDetails')->orderBy('created_at','desc')->get();
				if($keyword != "") {
					$offer_list = Offer::select(array('*', DB::raw('DATEDIFF(CAST(end_date as char), NOW()) AS expires')))
						->where('max_redeemar','>',0)
						->whereIn('zipcode',$zipval)
						->whereNotIn('status',array(2,4))
						->where('end_date','>=',$today)
						->whereNotIn('id',$banked_offer_list)
						->where('published', 'true')
						->whereNotIn('id',$passed_offer_list)
						->where(function($query) use ($keyword) {
						        $query->where('offer_description', 'LIKE', '%'.$keyword.'%');
						        $query->orWhere('more_information', 'LIKE', '%'.$keyword.'%');
						        $query->orWhere('what_you_get', 'LIKE', '%'.$keyword.'%');
						    })
						->with('categoryDetails','subCategoryDetails','partnerSettings','companyDetail','logoDetails')->distinct('created_by')->groupBy('created_by')
						->orderBy('created_at','asc')->get();
					}
				else {
					$offer_list = Offer::select(array('*', DB::raw('DATEDIFF(CAST(end_date as char), NOW()) AS expires')))->where('max_redeemar','>',0)
					->whereIn('zipcode',$zipval)->whereNotIn('status',array(2,4))->where('end_date','>=',$today)->whereNotIn('id',$banked_offer_list)
					->where('published', 'true')->whereNotIn('id',$passed_offer_list)
					->with('categoryDetails','subCategoryDetails','partnerSettings','companyDetail','logoDetails')
					->groupBy('created_by')->orderBy('created_by','asc')->orderBy('created_at','desc')->get();



				}
			}
			// Redeemar Id
			else if($type == 1) {
				$offer_list = Offer::select(array('*', DB::raw('DATEDIFF(CAST(end_date as char), NOW()) AS expires')))->where('created_by', $reedemar_id)
				->where('max_redeemar','>',0)->whereIn('zipcode',$zipval)->where('published', 'true')
				->whereNotIn('status',array(2,4))->where('end_date','>=',$now)
				->whereNotIn('id',$banked_offer_list)
				->whereNotIn('id',$passed_offer_list)->with('categoryDetails','subCategoryDetails','partnerSettings','companyDetail','logoDetails')
				->orderBy('created_at','desc')->get();
			}
			// Campaign Id
			else if($type == 2) {
				$offer_list = Offer::select(array('*', DB::raw('DATEDIFF(CAST(end_date as char), NOW()) AS expires')))
				->where('campaign_id', $campaign_id)
				->where('max_redeemar','>',0)->whereIn('zipcode',$zipval)
				->where('published', 'true')
				->whereNotIn('status',array(2,4))
				->where('end_date','>=',$now)
				->whereNotIn('id',$banked_offer_list)
				->whereNotIn('id',$passed_offer_list)
				->with('categoryDetails','subCategoryDetails','partnerSettings','companyDetail','logoDetails','campaignDetails')->orderBy('created_at','desc')->get();
			}
			// Category Id
			else if($type == 3) {
				if($keyword != "") {
					$offer_list = Offer::select(array('*', DB::raw('DATEDIFF(CAST(end_date as char), NOW()) AS expires')))
					->where(function($query) use ($category_id) {
				        $query->where('cat_id', $category_id);
				        $query->orWhere('subcat_id', $category_id);

				    })
					->where('max_redeemar','>',0)
					->whereIn('zipcode',$zipval)
					->where('published', 'true')
					->whereNotIn('status',array(2,4))
					->where('end_date','>=',$now)
					->whereNotIn('id',$banked_offer_list)
					->whereNotIn('id',$passed_offer_list)
					->where(function($query) use ($keyword) {
					        $query->where('offer_description', 'LIKE', '%'.$keyword.'%');
					        $query->orWhere('more_information', 'LIKE', '%'.$keyword.'%');
					        $query->orWhere('what_you_get', 'LIKE', '%'.$keyword.'%');
					    })
					->with('categoryDetails','subCategoryDetails','partnerSettings','companyDetail','logoDetails','offerCategory')
					->orderBy('created_at','desc')->distinct('created_by')->groupBy('created_by')->get();

					
				}
				else {
					$offer_list = Offer::select(array('*', DB::raw('DATEDIFF(CAST(end_date as char), NOW()) AS expires')))->where('subcat_id', $category_id)->where('max_redeemar','>',0)->whereIn('zipcode',$zipval)->where('published', 'true')->whereNotIn('status',array(2,4))->where('end_date','>=',$now)->whereNotIn('id',$banked_offer_list)->whereNotIn('id',$passed_offer_list)->with('categoryDetails','subCategoryDetails','partnerSettings','companyDetail','logoDetails','offerCategory')->orderBy('created_at','desc')->distinct('created_by')->groupBy('created_by')->get();					
				}
			}
			// On Demand Offers
			else if($type == 4) {
				$offer_list = Offer::select(array('*', DB::raw('DATEDIFF(CAST(end_date as char), NOW()) AS expires')))->where('on_demand', 1)
				->where('max_redeemar','>',0)->whereIn('zipcode',$zipval)->where('published', 'true')->whereNotIn('status',array(2,4))
				->where('end_date','>=',$now)
				->whereNotIn('id',$banked_offer_list)->whereNotIn('id',$passed_offer_list)
				->with('categoryDetails','subCategoryDetails','partnerSettings','companyDetail','logoDetails')->orderBy('created_at','desc')
				->distinct('created_by')->groupBy('created_by')->get();
			}
			$p = 0;
			if(isset($offer_list)) {
				foreach($offer_list as $offer) {
					
					$zip = $offer->zipcode;
					$mlat = $offer->latitude;
					$mlng = $offer->longitude;
					if(isset($mlat) && isset($mlng) && $mlat != "" && $mlng != "") {
						$distval = $this->haversineGreatCircleDistance($lat1, $long1, $mlat, $mlng);
						$offer_list[$p]['distance'] = number_format($distval, 0, '.', '');
						
					}
					else
						$offer_list[$p]['distance'] = "";
					$p++;
				}
				//	Typecast the object to array
				$offer_list1 =  (array) $offer_list;
				// Sort the records based on distance in ascending order
				usort($offer_list1, function($a, $b) {
				    return $a['distance'] - $b['distance'];
				});
				if(isset($offer_list1[0]))
					$datalist['data']=$offer_list1[0];
		
				$datalist['messageCode']="R01001";
			}
			else
				$datalist['messageCode']="R01000";
		}
		else
			$datalist['messageCode']="R01002";
		
		return $datalist;
	}

	//Get All offers for a user for a particular company for ios

	public function postBankedsingleofferlist(Request $request)
	{
		$data = json_decode($request->get('data'));
		$user_id 		= $data->user_id;
		$redeemar_id 	= $data->redeemar_id;
		$cur_lat 		= $data->cur_lat;
		$cur_long 		= $data->cur_long;

		$offer_list = array();
		$datalist = array();
		
		$getListofOffers = UserBankOffer::where('user_id', $user_id)->where('redeemar_id', $redeemar_id)->get();

		if(count($getListofOffers)){
			$offer_list['alloffers'] = array();

			//$companyDetails = User::find($redeemar_id);
			$companyDetails = Logo::where('reedemer_id', $redeemar_id)->where('default_logo', 1)->where('status', 1)->get();
			$offer_list['company_details'] = $companyDetails;
			$zip = $companyDetails[0]->zipcode;
			$mlat = $companyDetails[0]->lat;
			$mlng = $companyDetails[0]->lng;
			foreach ($getListofOffers as $key => $singleData) {
				$singleOffer = Offer::where('id', $singleData->offer_id)->where('status', 1)->get();
				if(count($singleOffer)){
					if(isset($mlat) && isset($mlng) && $mlat != "" && $mlng != "") {
						$distval = $this->haversineGreatCircleDistance($cur_lat, $cur_long, $mlat, $mlng);

						$singleOffer[0]['distance'] = number_format($distval, 0, '.', '');
						
					}
					else
						$singleOffer[0]['distance'] =  "";
					
					array_push( $offer_list['alloffers'], $singleOffer[0]);
				}
			}

			$datalist = $offer_list;

		}

		return $datalist;

	}

	//Get All offers for a user group by company for ios

	public function postBankedofferlist(Request $request)
	{
		$data 				 	= json_decode($request->data);
		$user_id 			 	= $data->user_id;
		$cur_lat 				= $data->cur_lat;
		$cur_long 				= $data->cur_long;


		$banked_offer_info = DB::table('reedemer_user_bank_offer')
				 ->where('user_id', $user_id)
				 ->where('status', 1)
                 ->select('redeemar_id', DB::raw('count(*) as total'))
                 ->groupBy('redeemar_id')
                 ->get();

        if(count($banked_offer_info)){
			$datalist = array();
        	foreach ($banked_offer_info as $key => $singleData) {
				$detailed_list 			= array();
				$offer_on_demand 		= array();
				$all_offer 				= array();
				$offer_on_demand_count 	= 0;
				$all_offer_count 		= 0;
				$detailed_list['logo_details'] 	= "";
				$detailed_list['distance'] 		= "";

				$logo_details 			= Logo::where('reedemer_id', $singleData->redeemar_id)->where('default_logo', 1)->get();

				$zip = $logo_details[0]->zipcode;
				$mlat = $logo_details[0]->lat;
				$mlng = $logo_details[0]->lng;

				if(isset($mlat) && isset($mlng) && $mlat != "" && $mlng != "") {
					$distval = $this->haversineGreatCircleDistance($cur_lat, $cur_long, $mlat, $mlng);

					$detailed_list['distance'] = number_format($distval, 0, '.', '');
					
				}
				else
					$detailed_list['distance'] = "";

                $offer_on_demand = DB::table('reedemer_offer')
				 ->where('created_by', $singleData->redeemar_id)
				 ->where('on_demand', 1)
				 ->where('status', 1)
				 ->where('published', 'true')
                 ->select(DB::raw('count(*) as total'))
                 ->get();

                $all_offer = DB::table('reedemer_offer')
				 ->where('created_by', $singleData->redeemar_id)
				 ->where('status', 1)
				 ->where('published', 'true')
                 ->select(DB::raw('count(*) as total'))
                 ->get();

                $offer_on_demand_count 	= $offer_on_demand[0]->total;
                $all_offer_count 		= $all_offer[0]->total;
				$banked_offers_count 	= $singleData->total;

				$detailed_list['offer_on_demand_count'] = $offer_on_demand_count;
				$detailed_list['all_offer_count'] = $all_offer_count;
				$detailed_list['banked_offers_count'] = $banked_offers_count;
				$detailed_list['logo_details'] = $logo_details;

				array_push($datalist, $detailed_list);

        	}
        }

		return $datalist;

	}

    // Show User Bank Offer

	public function postMyoffer(Request $request)
	{

		$user_id = 0; $lat = ""; $long = ""; $radius = ""; $category_id = ""; $redeemar_id = "";

		// Get banked list offer
		$data=json_decode($request->get('data'));

		if(isset($data->user_id)) {
			$user_id=$data->user_id;
		}

		if(isset($data->lat)) {
			$lat=$data->lat;
		}

		if(isset($data->long)) {
			$long=$data->long;
		}

		if(isset($data->radius)) {
			$radius=$data->radius;
		}
		
		if(isset($data->category_id)) {
			$category_id=$data->category_id;
		}
		
		if(isset($data->redeemar_id)) {
			$redeemar_id=$data->redeemar_id;
		}
		


		$userbankoffer = UserBankOffer::where('user_id', $user_id)->with('userDetail')->lists('offer_id');

		if($lat != "" && $long != "" && $radius != "") {

			$zipcodes = $this->getDistance($lat, $long, $radius);

			
			$zipval=[];
			$distanceval=[];

			foreach($zipcodes as $zip)
			{
				$mzip = trim($zip->zipcode);
				$zipval[] = $mzip;
				$distanceval[$mzip] = trim($zip->distance);
			}

		}



		if($category_id != "") {

			$offer_list = Offer::select(array('*', DB::raw('DATEDIFF(CAST(end_date as char), NOW()) AS expires')))
			->whereNotIn('status', array(2,4))
			//->where('max_redeemar','>',0)
			->whereIn('id', $userbankoffer)
			->where(function($query) use ($category_id) {
					        $query->where('cat_id', $category_id);
					        $query->orWhere('subcat_id', $category_id);
			})
			->with(array('categoryDetails','subCategoryDetails', 'partnerSettings' => function($query) {
		        $query->select('*');
		    }))->with(array('companyDetail' => function($query1) {
		    	$query1->select('*');
		    }))->with(array('logoDetails' => function($query2) {
		    	$query2->select("*");
		    }))->orderBy('created_by', 'asc')->get();

		}
		else if($redeemar_id != "" && $redeemar_id != "0") {


			$offer_list = Offer::select(array('*', DB::raw('DATEDIFF(CAST(end_date as char), NOW()) AS expires')))
			->whereNotIn('status', array(2,4))
			//->where('max_redeemar','>',0)
			->whereIn('id', $userbankoffer)
			->where('created_by', $redeemar_id)
			->with(array('categoryDetails','subCategoryDetails', 'partnerSettings' => function($query) {
		        $query->select('*');
		    }))->with(array('companyDetail' => function($query1) {
		    	$query1->select('*');
		    }))->with(array('logoDetails' => function($query2) {
		    	$query2->select("*");
		    }))->orderBy('created_by', 'asc')->get();

		}
		else {

			$offer_list = Offer::select(array('*', DB::raw('DATEDIFF(CAST(end_date as char), NOW()) AS expires')))
			->whereNotIn('status', array(2,4))
			//->where('max_redeemar','>',0)
			->whereIn('id', $userbankoffer)
			->with(array('categoryDetails','subCategoryDetails', 'partnerSettings' => function($query) {
		        $query->select('*');
		    }))->with(array('companyDetail' => function($query1){
		    	$query1->select('*');
		    }))->with(array('logoDetails' => function($query2){
		    	$query2->select("*");
		    }))->orderBy('created_by', 'asc')->get();
		}


		
		if(count($offer_list) > 0)
		{
			$p = 0;
			foreach($offer_list as $offer) {

				$company_id = $offer->created_by;
				$zip = $offer->zipcode;

				if(isset($distanceval[$zip])) {

					$offer_list[$p]['distance'] = number_format($distanceval[$zip], 0, '.', '');					
				}
				else
					$offer_list[$p]['distance'] = "";

				
				$counters = Offer::select(DB::raw('count(*) as offers_count, sum(on_demand) AS deals_count'))->where('created_by', $company_id)->first();


				if(isset($counters)) {
					$offer_list[$p]['counters'] = $counters;
				}

				$p++;

			}

			$datalist['messageCode']="R01001";
			$datalist['data']=$offer_list;

		}
		else
		{
			$datalist['messageCode']="R01002";
			$datalist['data']="No record found.";
		}



		return $datalist;

	}



	public function postFindbeacon(Request $request)
	{

		// Find and returns the beacon

		$data=json_decode($request->get('data'));

		$datalist = array();


		$uuid = ""; $major = ""; $minor = "";

		if(isset($data->uuid))
			$uuid = $data->uuid;

		if(isset($data->major))
			$major = $data->major;

		if(isset($data->minor))
			$minor = $data->minor;

		/*echo $uuid."<br>";
		echo $major."<br>";
		echo $minor."<br>";
		exit;*/

		if($uuid != "" && $major != "" && $minor != "") {

			// Check where already bank offer
			$beacon = Beacon::where('uuid', $uuid)->where('major', $major)->where('minor', $minor)->where('beacon_type', 'proximity')->where('active', 1)->first();

			//dd($beacon);
			
			if(isset($beacon->id) && $beacon->id > 0)
			{
				
				$datalist['messageCode']="R01001";
				$datalist['data'] = $beacon;
			}
			else
			{
				$datalist['messageCode']="R01002";
				$datalist['data']="No matching beacon found.";
				

			}


		}
		else
		{
			$datalist['messageCode']="R01003";
			$datalist['data'] = "Error receiving parameters.";

		}

		return $datalist;

	}



	public function postFindsticker(Request $request)
	{

		// Find and returns the beacon

		$data=json_decode($request->get('data'));

		$datalist = array();


		$identifier = "";

		if(isset($data->identifier))
			$identifier = $data->identifier;


		if($identifier != "") {

			// Check where already bank offer
			$beacon = Beacon::where('identifier', $identifier)->where('beacon_type', 'interactive')->where('active', 1)->first();


			
			if(isset($beacon->id) && $beacon->id > 0)
			{
				
				$datalist['messageCode']="R01001";
				$datalist['data'] = $beacon;
			}
			else
			{
				$datalist['messageCode']="R01002";
				$datalist['data']="No matching beacon found.";
				

			}


		}
		else
		{
			$datalist['messageCode']="R01003";
			$datalist['data'] = "Error receiving parameters.";

		}

		return $datalist;

	}


	public function postBankoffer(Request $request)
	{

		// Get passed users list offer

		$data=json_decode($request->get('data'));

		$user_id = ""; $offer_id = "";

		if(isset($data->user_id))
			$user_id = $data->user_id;

		if(isset($data->offer_id))
			$offer_id = $data->offer_id;

		if($user_id != "" && $offer_id != "") {

			// Check where already bank offer
			$userbankoffer=UserBankOffer::where('user_id', $user_id)->where('offer_id', $offer_id)->get();

			
			if(count($userbankoffer) > 0)
			{
				$datalist['data']="You already banked this offer.";
				$datalist['messageCode']="R01002";
			}
			else
			{
				$resp = $this->addToBankedOffer($offer_id, $user_id);

				/*
				**	4 possible values of $resp:						**
				**	---------------------------						**
				**	1) Successfully banked_offer_list 				**
				**	2) User has already banked this offer 			**
				**	3) Unable to calculate redeem/validate after 	**
				**	4) Offer end date is not available.  			**
				*/



				if($resp == 1)
					$datalist['messageCode']="R01001";

				else if($resp == 2)
					$datalist['messageCode']="R01002";

				else if($resp == 3)
					$datalist['messageCode']="R01003";

				else if($resp == 4)
					$datalist['messageCode']="R01004";

			}


		}

		return $datalist;

	}

	public function postPassoffer(Request $request)
	{

			// Get passed users list offer

			$data=json_decode($request->get('data'));

			$user_id=$data->user_id;

			$offer_id=$data->offer_id;

			//Check already pass offer

			$userpassoffer=UserPassedOffer::where('user_id',$user_id)->where('offer_id',$offer_id)->get();

           if(count($userpassoffer)>0)
			{
				$datalist['data']="You already passed this offer.";

				$datalist['messageCode']="R01002";
			}else
			{

			$offer_detail=Offer::where('id',$offer_id)->first();

            $max_redeemar=$offer_detail['max_redeemar'] - 1;

            $redeem_offer=$offer_detail['redeem_offer'] + 1;

			$datalist = UserPassedOffer::create(['user_id' => $user_id, 'offer_id' => $offer_id]);

			//$updateOffer = Offer::where('id',$offer_id)->update(array('max_redeemar'=> $max_redeemar));

			//$redeemOffer = Offer::where('id',$offer_id)->update(array('redeem_offer'=> $redeem_offer));

			$datalist['messageCode']="R01001";
             
            }

			return $datalist;

	}



	// User Offer Redeemption
	public function postRedeempasscode(Request $request)
	{

		$user_id = 0; $offer_id = 0; $redemption_code = ""; 

		$data=json_decode($request->get('data'));

		if(isset($data->user_id))
			$user_id = $data->user_id;

		if(isset($data->offer_id))
			$offer_id = $data->offer_id;

		if(isset($data->redemption_code))
			$redemption_code = $data->redemption_code;




    	if($offer_id > 0 && $user_id > 0 && $redemption_code != "") {

    		$offer_detail = Offer::where('id', $offer_id)->select('id','max_redeemar','redeem_offer','created_by')->first();

    		$redemption_codes = $offer_detail->userOfferDetails()->select('redemption_code')->first();

			if(count($offer_detail) > 0) {

				// Check redemption code 

				if(isset($redemption_codes->redemption_code) && $redemption_codes->redemption_code == $redemption_code) {


					if($offer_detail['max_redeemar'] > 0) {

						$max_redeemar = $offer_detail['max_redeemar'];

						$redeemed = RedeemptionOffer::where('user_id', $user_id)->where('offer_id', $offer_id)->select('id')->first();

						// User has not redeemed this offer yet, go ahead and redeem it then
						if(count($redeemed) == 0) {

							$redeem_offer=$offer_detail['redeem_offer'] + 1;

							$updateOffer = Offer::where('id',$offer_id)->update(array('max_redeemar'=> $max_redeemar, 'redeem_offer'=> $redeem_offer));

							$datalist = RedeemptionOffer::create(['user_id' => $user_id, 'offer_id' => $offer_id, 'source'=> 2]);

							if(count($datalist) > 0) {
								$affectedRows = UserBankOffer::where('user_id', $user_id)->where('offer_id', $offer_id)->delete();
								$datalist['messageCode']="R01001";
							}

							
						}
						else {

							// Show message back to user that he cannot redeem same offer more than once
							$datalist['messageCode']="R01004";

						}
					} 
					else {

						// Show message back to user that he cannot redeem same offer more than once
						$datalist['messageCode']="R01003";

					}
				}
				else
				{
					// Redemption code does not match
		         	$datalist['messageCode']="R01005";

				}


			}
			else
			{
				// Offer not found
	         	$datalist['messageCode']="R01002";

			}

    	}
    	else {
    		// Offer or user id or passcode is not found
    		$datalist['messageCode']="R01006";
    	}

    	

		return $datalist;

	}

	// User Offer Redeemption
	public function postRedeemption(Request $request)
	{

		$user_id = 0; $target_id = 0; $offer_id = 0; $reedemer_id = 0; 
		$data=json_decode($request->get('data'));

		if(isset($data->user_id))
			$user_id = $data->user_id;

		if(isset($data->target_id))
			$target_id = $data->target_id;

		if(isset($data->offer_id))
			$offer_id = $data->offer_id;


		$logo=Logo::where('target_id',$target_id)->where('status', 1)->where('action_id',4)->first();	

		// If logo is found in the database
		if(isset($logo->id) && $logo->id != "")
		{

			// Update number of times the logo has been scanned
			$updateScanned = Logo::where('id', $logo->id)->increment('num_scanned');


			if(isset($logo->reedemer_id)) {
				
				$reedemer_id = $logo->reedemer_id;
	        	if($offer_id > 0 && $reedemer_id > 0) {

	        		$offer_detail=Offer::where('id',$offer_id)->where('created_by', $reedemer_id)->where('max_redeemar','>','0')->whereNotIn('status',array(2,4))->first();

					if(count($offer_detail) > 0) {

						$redeemed = RedeemptionOffer::where('user_id', $user_id)->where('offer_id', $offer_id)->first();

						// User has not redeemed this offer yet, go ahead and redeem it then
						//if(count($redeemed) == 0) {
						$max_redeemar=$offer_detail['max_redeemar'] - 1;

						$redeem_offer=$offer_detail['redeem_offer'] + 1;

						$updateOffer = Offer::where('id',$offer_id)->update(array('max_redeemar'=> $max_redeemar, 'redeem_offer'=> $redeem_offer));

						$datalist = RedeemptionOffer::create(['user_id' => $user_id, 'offer_id' => $offer_id, 'source'=> 1]);

						if(count($datalist) > 0) {
							$affectedRows = UserBankOffer::where('user_id', $user_id)->where('offer_id', $offer_id)->delete();
							$datalist['messageCode']="R01001";
						}

							
						/*}
						else {

							// Show message back to user that he cannot redeem same offer more than once
							$datalist['messageCode']="R01004";

						}*/
					}
					else
					{
						// You are scannaing an image not associated with validation or this offer has reached max redemption limit
			         	$datalist['messageCode']="R01002";

					}


	        	}
	        	else {
	        		// Offer or redeemer id is not found
	        		$datalist['messageCode']="R01005";
	        	}

				
			}
			else
			{
				// You are scannaing a image not associated with validation
	            $datalist['messageCode']="R01003";

			}

		}
		else {
			// Unable to locate the target
	        $datalist['messageCode']="R01003";
		}

		return $datalist;

	}



	// User Offer Redeemption
	public function postBeaconredeemption(Request $request)
	{

		$user_id = 0; $offer_id = 0; $redeemar_id = 0; 
		$data=json_decode($request->get('data'));

		if(isset($data->user_id) && $data->user_id != "")
			$user_id = $data->user_id;

		if(isset($data->offer_id) && $data->offer_id != "")
			$offer_id = $data->offer_id;

		if(isset($data->redeemar_id) && $data->redeemar_id != "")
			$redeemar_id = $data->redeemar_id;


		// If all 3 parameters are passed, viz, User Id, Redeemar Id and Offer Id then proceed 
		if($user_id != 0 && $offer_id != 0 && $redeemar_id != 0)
		{


    		$offer_detail = Offer::where('id', $offer_id)->where('created_by', $redeemar_id)->where('max_redeemar','>','0')->whereNotIn('status',array(2,4))->first();

			/*$offer_detail = UserBankOffer::where('user_id', $user_id)->where('redeemar_id', $redeemar_id)->where('offer_id', $offer_id)->first();   */
			
			//dd($offer_detail);

			// TODO: Check whether the user has already banked this offer.
			// Same checking should also exists in postRedeemption()

			if(count($offer_detail) > 0) {

				// Check whether the user can redeem the offer
				// NOTE: THIS OPTION HAS NOW BEEN DISABLED, BECAUSE USERS ARE ALLOWED
				// TO BANK SAME OFFERS MULTIPLE TIMES, HENCE THEY SHOULD BE ALLOWED TO 
				// VALIDATE THE SAME MULTIPLE TIMES
				$redeemed = RedeemptionOffer::where('user_id', $user_id)->where('offer_id', $offer_id)->first();


				//  Reduce max redeemar by 1
				$max_redeemar=$offer_detail['max_redeemar'] - 1;

				// Increment redeem offer by 1
				$redeem_offer=$offer_detail['redeem_offer'] + 1;

				$updateOffer = Offer::where('id', $offer_id)->update(array('max_redeemar'=> $max_redeemar, 'redeem_offer'=> $redeem_offer));

				// Add a new row in the redemption offer table
				$datalist = RedeemptionOffer::create(['user_id' => $user_id, 'offer_id' => $offer_id, 'source'=> 1]);

				// Finally, remove the row from user bank
				if(count($datalist) > 0) {
					$affectedRows = UserBankOffer::where('user_id', $user_id)->where('offer_id', $offer_id)->delete();
					$datalist['messageCode']="R01001";
				}

			}
			else
			{
				// You are trying to redeem an different offer
	         	$datalist['messageCode']="R01002";

			}


    	}
    	else {
    		// Offer or redeemer id is not found
    		$datalist['messageCode']="R01005";
    	}

    	return $datalist;
		

	}




	// Show User Passed Offer

	public function postMypassedoffer(Request $request)
	{

		// Get passed users list offer

		$data=json_decode($request->get('data'));


		$user_id=$data->user_id;

		$userpassedoffer=UserPassedOffer::where('user_id',$user_id)->with('userDetail')->lists('offer_id');

		$offer_list=Offer::select(array('*',DB::raw('DATEDIFF(CAST(end_date as char), NOW()) AS expires')))->whereNotIn('status',array(2,4))->where('max_redeemar','>',0)->whereIn('id',$userpassedoffer)->with('categoryDetails','subCategoryDetails','partnerSettings','companyDetail')->orderBy('created_at','desc')->get();

		$datalist['messageCode']="R01001";

		$datalist['data']=$offer_list;

		return $datalist;

	}

   // Show Offer Details

	public function postOfferdetail(Request $request)
	{

		$data=json_decode($request->get('data'));
		$offer_id=$data->offer_id;
		$user_id=$data->user_id;

		// check offer bank or passed

		$userbankoffer=UserBankOffer::where('user_id',$user_id)->where('offer_id',$offer_id)->get();
		$userpassoffer=UserPassedOffer::where('user_id',$user_id)->where('offer_id',$offer_id)->get();

		if(count($userbankoffer)>0)
		{
			$datalist['messageCode']="R01002";
		}
		elseif(count($userpassoffer)>0)
		{

			$datalist['messageCode']="R01003";

		}
		else
		{
         $datalist['messageCode']="R01001";

		}
		$now=date('Y-m-d h:i:s');


		// Fetch all information pertaining to the offer		
		$offer_list=Offer:: select(array('*',DB::raw('DATEDIFF(CAST(end_date as char), NOW()) AS expires')))->where('end_date','>=',$now)->where('id',$offer_id)->with('categoryDetails','subCategoryDetails','partnerSettings','companyDetail', 'logoDetails')->orderBy('created_at','desc')->get();

		$datalist['data']=$offer_list;

		return $datalist;

	}


	public function postValidateofferdetail(Request $request)
	{
		$user_id = 0; $offer_id = 0;

		$data=json_decode($request->get('data'));
		
		if(isset($data->offer_id))
			$offer_id=$data->offer_id;

		if(isset($data->user_id))
			$user_id=$data->user_id;

		$now=date('Y-m-d h:i:s');

		if($user_id > 0 && $offer_id > 0) {
			$userbankoffer=UserBankOffer::where('user_id',$user_id)->where('offer_id',$offer_id)->with('userDetail')->lists('offer_id');

			$userbankoffer1=UserBankOffer::where('user_id',$user_id)->where('offer_id',$offer_id)->first();

			$offer_list=Offer::select(array('*',DB::raw('DATEDIFF(CAST(end_date as char), NOW()) AS expires')))->whereNotIn('status',array(2,4))->where('max_redeemar','>',0)->whereIn('id',$userbankoffer)->with('categoryDetails','subCategoryDetails','partnerSettings','companyDetail', 'logoDetails')->orderBy('created_at','desc')->first();
			if(count($offer_list) > 0) {
				$datalist['data']=$offer_list;
				$datalist['data']['myoffer_details']=$userbankoffer1;
				$datalist['messageCode']="R01001";
			}
			else {
				// No offer details found
				$datalist['messageCode']="R01003";
			}
			
		}
		else {
			// User id and/or offer id is not found
			$datalist['messageCode']="R01002";
		}



		return $datalist;

	}


	public function postChecktarget(Request $request)
	{
	    //$target_id=$request->get('target_id');
		$data=json_decode($request->get('data'));
		$target_id = "";
		$redeemar_id = "";

		if(isset($data->target_id))
			$target_id=$data->target_id;

		if(isset($data->redeemar_id))
			$redeemar_id=$data->redeemar_id;

		//dd($target_id);
		//dd($redeemar_id);
		
		if($target_id != "" || $redeemar_id != "") {

			if($target_id != "")
				$logo=Logo::where('target_id',$target_id)
				->where('status',1)
				->get()
				->first();
		
			else if($redeemar_id != "")
				$logo=Logo::where('reedemer_id',$redeemar_id)
				->where('status',1)
				->get()
				->first();

			// If logo is found in the database
			if(isset($logo->id) && $logo->id != "")
			{

				// Update number of times the logo has been scanned
				$updateScanned = Logo::where('id', $logo->id)->increment('num_scanned');

				if(isset($logo->reedemer_id) && $logo->reedemer_id != "")
				{
					$company_name=User::where('id', $logo->reedemer_id)
								->first()
								->company_name;
					$logo_name= $logo->logo_name;

					// Get Default Logo
					$defaultlogo=Logo::where('reedemer_id',$logo->reedemer_id)
								->where('default_logo',1)
								->where('status',1)
								->first();	

					$offer_id = 0;
					$campaign_id = 0;

					// In case of campaigns
					if($logo->action_id==2)
					{
						if(isset($logo->particular_id) && $logo->particular_id > 0)
							$campaign_id = $logo->particular_id;

					}
					
					// In case of particular offers
					else if($logo->action_id==3)
					{
						
						if(isset($logo->particular_id) && $logo->particular_id > 0)
							$offer_id = $logo->particular_id;

					}
					// get video links 
					$video_list=Video::where('uploaded_by',$logo->reedemer_id)
								->orderBy('default_video','desc')
								->where('status',1)
								->get();
					$dataArr=array(
						'reedemer_id'=>$logo->reedemer_id,
						'companyName' => $company_name, 
						'default_logo' => $defaultlogo->logo_name , 
						'logoImage' => $logo_name, 
						'videoList' => $video_list,
						'action_id' => $logo->action_id,
						'campaign_id' => $campaign_id,
						'offer_id' => $offer_id
					);

					$dataStr=json_encode($dataArr);
					$response['status']='success';
					$return['messageCode']="R01001";
					$return['data'] = $dataStr;

					// Put all response to database for testing purpose
					// Will have to remove this later
					$pp=new Pp();
					$pp->val=$dataStr;
					$pp->save();
				}
				else
				{
					$response['status']='success';
					$this->sendEmail($logo->contact_email,$logo->logo_text);
			 		$return['messageCode']="R01002";
				}

			}
			else
			{
				$response['data']='Logo not found';
				$return['messageCode']="R01003";

			}	

		}
		else
		{
			$response['data']='Target Id or Redeemar Id not found';
			$return['messageCode']="R01004";

		}
		
	 	
		return $return;
	}


	/*
	 * postSendfeedback
	 *
	 * This service saves user feedback in the database and also send emails to admin
	 *
	 * @param (string) (user_id) User Id of the user (optional, could be null)
	 * @param (string) (email) Email address of the user
	 * @param (string) (feedback) Feedback text
	 * @param (string) (rating) Rating in float
	 
	 * @return (array)
	 */

	public function postSendfeedback(Request $request)
	{


		$data=json_decode($request->get('data'));

		$user_id = 0;
		$email = "";
		$feedback = "";
		$rating = 0;
		$source = "web";


		if(isset($data->user_id))
			$user_id=$data->user_id;

		if(isset($data->email))
			$email=$data->email;

		if(isset($data->feedback))
			$feedback=$data->feedback;

		if(isset($data->rating))
			$rating=$data->rating;

		if(isset($data->source))
			$source=$data->source;



		if($email != "" && ($feedback != "" || floatval($rating) > 0)) {

			if($user_id > 0)
				$datalist = Feedback::create(['user_id' => $user_id, 'email' => $email, 'feedback' => $feedback, 'rating' => $rating, 'source' => $source]);
			else
				$datalist = Feedback::create(['email' => $email, 'feedback' => $feedback, 'rating' => $rating, 'source' => $source]);

			$this->sendFeedbackEmail($email, $feedback, $rating, $source);


			$datalist['messageCode']="R01001";

		}
		else {

			// Either email and feedback or rating has not been submitted
			$datalist['messageCode']="R01002";
		}
			

		return $datalist;

	}




	/*
	 * postUserprofile
	 *
	 * This service saves user feedback in the database and also send emails to admin
	 *
	 * @param (string) (user_id) User Id of the user
	 * @return (array)
	 */

	public function postUserprofile(Request $request)
	{
      
		$data=json_decode($request->get('data'));

		$user_id = $data->user_id;

		$userdata=User::where('id', $user_id)->first();

		if(count($userdata)>0)
		{
			
			$userdetail['messageCode']="R01001";
			$userdetail['data']=$userdata;
		}
		else
		{
			$userdetail['messageCode']="R01002";
			$userdetail['data']="No record found.";
		}


		return $userdetail;

	}




	/*
	 * postUpdateprofile
	 *
	 * This service saves user feedback in the database and also send emails to admin
	 *
	 * @param (string) (user_id) User Id of the user
	 * @param (string) (first_name) First Name of the user
	 * @param (string) (last_name) Last Name of the user
	 * @param (string) (email) Email address of the user
	 * @param (string) (phone) Phone number of the user
	 
	 * @return (array)
	 */

	public function postUpdateprofile(Request $request)
	{


		$data=json_decode($request->get('data'));

		$user_id = 0;
		$email = "";
		$first_name = "";
		$last_name = "";
		$phone = "";


		if(isset($data->user_id))
			$user_id=$data->user_id;

		if(isset($data->email))
			$email=$data->email;

		if(isset($data->first_name))
			$first_name=$data->first_name;

		if(isset($data->last_name))
			$last_name=$data->last_name;

		if(isset($data->phone))
			$phone=$data->phone;

		if($email != "" && $user_id > 0) {

			$updateProfile = User::where('id',$user_id)->update(array('first_name'=> $first_name, 'last_name' => $last_name, 'email' => $email, 'mobile' => $phone));
			$datalist['messageCode']="R01001";

		}

		else {

			// Either email and/or user id or rating has not been submitted
			$datalist['messageCode']="R01002";
		}
			

		return $datalist;

	}



	public function postMulticat(Request $request)
	{
		

		$categories = Category::where('parent_id', 0)->where('status', 1)->where('visibility', 1)->with('children')->orderBy('cat_name', 'asc')->get();

		if(count($categories) > 0) {

			$datalist['messageCode']="R01001";
			$datalist['data']=array('categories' => $categories);
		}

		else
			$datalist['messageCode']="R01000";
		
		
	   	return $datalist;

	
	}

	/* Returns JSON response for all categories */
	public function postCategories(Request $request)
	{
		

		$categories = Category::where('status', 1)->where('visibility', 1)->orderBy('cat_name', 'asc')->get();

		if(count($categories) > 0) {

			$datalist['messageCode']="R01001";
			$datalist['data']=array('categories' => $categories);
		}

		else
			$datalist['messageCode']="R01000";
		
		
	   	return $datalist;

	
	}


	/* Searches the User Model for specified conditions and then returns JSON response for all matching fields */
	public function postLocations(Request $request)
	{

		$data=json_decode($request->get('data'));

		$location = "";


		if(isset($data->keyword))
			$location=$data->keyword;

		if($location != "") {
			$locations = User::distinct('city')->where('status', 1)
							->where('type', 2)
							->where(function ($query) use($location) {
							    $query->where('address', 'LIKE', '%'.$location.'%')
							->orWhere('city', 'LIKE', '%'.$location.'%')
							->orWhere('state', 'LIKE', '%'.$location.'%')
							->orWhere('zipcode', 'LIKE', '%'.$location.'%')
							->orWhere('state_code', 'LIKE', '%'.$location.'%')
							->orWhere('location', 'LIKE', '%'.$location.'%');
							})->select('id', 'city', 'state', 'state_code', 'zipcode', 'location', 'lat', 'lng')->groupBy('city','zipcode')->get();

			if(count($locations) > 0) {

				$datalist['messageCode']="R01001";
				$datalist['data']=$locations;
			}

			else
				$datalist['messageCode']="R01000";

		}
		else
			$datalist['messageCode']="R01002";
		
		
	   	return $datalist;

	
	}


	/* Searches the User Model for specified conditions and then returns JSON response for all matching fields */
	public function postSearch(Request $request)
	{
		$data=json_decode($request->get('data'));

		$keyword = "";
		$datalist = array();
		$arr = array();
		$arr3 = array();



		if(isset($data->keyword)) {

			$keyword=$data->keyword;
			


			/*$result = Offer::select('more_information','cat_name')
				->whereRaw('more_information LIKE "%'.$keyword.'%"')
				->join('reedemer_category','reedemer_offer.cat_id','=','reedemer_category.id')
				->orderBy('reedemer_offer.created_at','desc')->get();*/


			$result = Offer::select('more_information','cat_id','cat_name')
				->whereRaw('more_information LIKE "%'.$keyword.'%"')
				->join('reedemer_category','reedemer_offer.cat_id','=','reedemer_category.id')->groupBy('reedemer_offer.cat_id')->distinct()->get();

			
			$arr3 = array();
			foreach($result as $r){
				$arr = array();
			    $val = strtolower($r['more_information']);
			    $cat_id = $r['cat_id'];
			    $cat_name = $r['cat_name'];
			    $arr = explode(",", $val);

				foreach($arr as $elem) {



					if(strpos($elem, $keyword) !== FALSE) {

						$arr2 = array();
						
						$arr2['keyword'] 	= $elem;
						$arr2['id']			= $cat_id;
						$arr2['name']		= $cat_name;
						$arr2['type'] 		= "1";

						$arr3[] = $arr2;
					}
					
					

				}

			}
			
			
			$result_company = Offer::select('users.id','company_name')
				->whereRaw('company_name LIKE "%'.$keyword.'%"')
				->join('users','reedemer_offer.created_by','=','users.id')->distinct()->get();

			foreach($result_company as $r){
				$arr2 = array();
				$arr2['keyword'] 	= $r['company_name'];
				$arr2['id'] 		= $r['id'];
				$arr2['name'] 		= $r['company_name'];
				$arr2['type'] 		= "2";
				$arr3[] = $arr2;
			
			}
			
			
			


			$count = Offer::select('offer_description','what_you_get')
				->whereRaw('offer_description LIKE "%'.$keyword.'%"')
				->orWhereRaw('what_you_get LIKE "%'.$keyword.'%"')->count();

			
			$arr2['count'] = $count;
			

			

			if(count($arr3) > 0) {

				$datalist['messageCode']="R01001";
				$datalist['data'] = $arr3;
			}

			else
				$datalist['messageCode']="R01000";
			
		
		}
	
		else
			$datalist['messageCode']="R01000";
			

		

		
	   	return $datalist;

	
	}

	public function sendEmail($contactemail,$logo_text)
	{


		$user_email=$contactemail;
		$admin_email="redeemar@mailinator.com";
		
		$user_name=$logo_text;
		$data = array('user_name' => $user_name, 'user_email' => $user_email);
		\Mail::send('emails.nopartner', $data, function($message) use ($admin_email,$user_email,$user_name){ 
			$subject="Join with Redeemar";
			$message->from($admin_email, $user_name);
			$message->to($user_email)->subject($subject);
		}); 
	}
	


	public function sendRegisterEmail($contactemail,$registerpassword)
	{


		$user_email=$contactemail;
		$admin_email="admin@mailinator.com";
		
		$data = array('user_email' => $user_email,'registerpassword' => $registerpassword);
		\Mail::send('emails.register', $data, function($message) use ($admin_email,$user_email,$registerpassword){ 
			$subject="Thank You For Joining Redeemar";
			$message->from($admin_email, $user_email);
			$message->to($user_email)->subject($subject);
		}); 
	}


	public function sendFeedbackEmail($email, $feedback, $rating, $source)
	{

		$admin_email = "redeemar@mailinator.com";

		$data = array('admin_email', $admin_email, 'email' => $email,'feedback' => $feedback,'rating' => $rating,'source' => $source);
		\Mail::send('emails.feedback', $data, function($message) use ($admin_email, $email, $feedback, $rating, $source){ 
			$subject="You have got a new feedback";
			$message->from($admin_email, $email);
			$message->to($admin_email)->subject($subject);
		}); 
	}

	private function getValidationDates($validate_after_hours) {

		$date = date("Y-m-d H:i:s");
		

		$validate_after = '+'.intval($validate_after_hours).' hours';


		$ret['validate_after'] = date("Y-m-d H:i:s", strtotime($validate_after));
		

		return $ret;
	}

	

	private function addToBankedOffer($offer_id, $user_id) {

		$now = date("Y-m-d H:i:s");

		$offer_detail=Offer::where('id', $offer_id)->first();

		$redeemar_id = 0;

		
		if(isset($offer_detail['created_by']) && $offer_detail['created_by'] <> "") {
			$redeemar_id = $offer_detail['created_by'];
			
		}



		$userbankoffer=UserBankOffer::where('user_id', $user_id)->where('offer_id',$offer_id)->get();

		if(count($userbankoffer) == 0)
		{

			if(isset($offer_detail['validate_after']))
				$validate_after_hours = $offer_detail['validate_after'];
			else
				$validate_after_hours = 0;


			$validate_after = '+'.intval($validate_after_hours).' hours';
			$validate_after = date("Y-m-d H:i:s", strtotime($validate_after));
		
			// Validate within is always the offer end date
			if(isset($validate_after) && $validate_after != "") {
				if(isset($offer_detail['end_date']) && $offer_detail['end_date']!= "" && $offer_detail['end_date'] != "0000-00-00 00:00:00" && strlen($offer_detail['end_date']) > 10) {
					
					// Push the end date to the day end
					$end_datepart = substr($offer_detail['end_date'], 0, 10);
					$validate_within = $end_datepart." 23:59:50";


					$datalist = UserBankOffer::create(['user_id' => $user_id, 'offer_id' => $offer_id , 'redeemar_id' => $redeemar_id, 'validate_within' => $validate_within, 'validate_after' => $validate_after]);
					return 1; // In case of successfully adding the offer to my offer

				}
				else 
					return 4; // In case of offer end date is not available

			}
			else 
				return 3; // In case of validate after value cannot be calculated

		}
		else
			return 2; // In case the user has already banked the offer
	}


	function getDistance($lat, $lng , $radius) {

		// Returns array of zip code and distances in miles that fall within the radius
		// Earth radius is 3,959 in miles and 6,371 in Km 

		$sql=DB::select("SELECT zipcode, ( 3959 * acos( cos( radians( {$lat} ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( {$lng} ) ) + sin( radians( {$lat} ) ) * sin( radians( latitude)))) AS distance FROM reedemer_offer  HAVING distance <= {$radius} ORDER BY distance");
		
		//dd($sql);

		return $sql;
	}

	/**
	 * Calculates the great-circle distance between two points, with
	 * the Haversine formula.
	 * @param float $latitudeFrom Latitude of start point in [deg decimal]
	 * @param float $longitudeFrom Longitude of start point in [deg decimal]
	 * @param float $latitudeTo Latitude of target point in [deg decimal]
	 * @param float $longitudeTo Longitude of target point in [deg decimal]
	 * @param float $earthRadius Mean earth radius in [m]
	 * @return float Distance between points in [m] (same as earthRadius)
	 */

	function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 3959)
	{
	  // convert from degrees to radians
	  $latFrom = deg2rad($latitudeFrom);
	  $lonFrom = deg2rad($longitudeFrom);
	  $latTo = deg2rad($latitudeTo);
	  $lonTo = deg2rad($longitudeTo);

	  $latDelta = $latTo - $latFrom;
	  $lonDelta = $lonTo - $lonFrom;

	  $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
	    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
	  return $angle * $earthRadius;
	}



	function super_unique($array, $key1, $key2)
	{

	   $uniq_array = array();
	   

	       
	   if(isset($array[$key1])) {

	   	$pa1 = $array[$key1];

	   	print_r($pa1);

	       if(!in_array($pa1[$key1], $uniq_array))
	       	$uniq_array['mi'][] = $pa1[$key1];

	   }

	   $array = array_values($uniq_array);

	   return $array;

	}

	function object_to_array($object) {
    	return (array) $object;
	}

	function array_to_object($array) {
    	return (object) $array;
	}
}
