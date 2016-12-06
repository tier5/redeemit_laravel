<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\Logo;
use App\Model\Price;
use App\Model\Partnersetting;
use App\Model\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Hash;
use Validator; 
use Input; /* For input */
use App\Helper\helpers;
use Auth;
use App\Helper\vuforiaclient;
use \Image;
use stdClass;
use App\Model\StoreImage;
use App\Model\Campaign;
use App\Model\Offer;
use App\Model\Action;
use App\Model\Beacons;
use App\Model\ReedemerBeaconsRequestCount;
use App\Model\Requestbeacon;
use DB;
//use App\Helper\gettarget;
//use App\Helper\signaturebuilder;

class DashboardController extends Controller {

	//protected $dashboard;
	
	//public function __construct(  )
	//{
		//$this->dashboard = $dashboard;
	//	$this->middleware('auth');
	//	dd("Ag");
	//}	


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	 
	public function getIndex()
	{
		return view('admin.dashboard.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//dd("a");
		//
		//dd($request->all());
		//return 'c';
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getShow()
	{
		//dd("dashboard->show");
		//Auth::logout();

    	//return redirect()->back();
	}

	public function postReedemarlistandlogo(Request $request)
	{
		$userdetails=User::where('type',2)->get();
		$logo_arr=array();

		foreach($userdetails as $user) {
			$id = $user['id'];
			$logo_details = Logo::where('reedemer_id',$id)
							->get();
			$user['logo_details'] = $logo_details;

		}
		$user_json=json_encode($userdetails);		
		return $user_json;
	}

	public function postShow(Request $request)
	{
		//dd($request->all());

		$id=Auth::user()->id;
		$created_by=Auth::user()->id;

		// Get current logged in user TYPE
		$type=Auth::user()->type;
		if($request[0]!="")
		{
			$id=$request[0];
			$user=User::where('status',1)
						  ->where('id',$id)						 
						  ->get();	
		}
		else
		{
			if($type==1)
			{	
				$user=User::where('type',2)->orderBy('id','DESC')->get();			
			}
			else
			{
				$user=User::where('id',$id)->orderBy('id','DESC')->get();	
			}
		}


		// $id=Auth::user()->id;
		// $type=Auth::user()->type;

		// dd($id);
		// if($type!=1)
		// {
		// 	$user=User::where('id',$id)->orderBy('id','DESC')->get();	
		// }
		// else
		// {
		// 	$user=User::where('type',2)->orderBy('id','DESC')->get();		
					
		// }
		 return $user;
	}

	public function postStatusupdate(Request $request)
	{	
		$approve 	= $request->status;
		$id 		= $request->redeemar_id;

		if($approve==1)
		{
			$new_status=0;
		}
		else
		{
			$new_status=1;	
		}
		$user = User::find($id);
		$user->approve=$new_status;
		if($user->save()){
			return 'success';
		} else {
			return 'error';
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	function get_lat_lng($zip)
	{
		$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($zip)."&sensor=false&key=".env('GOOGLE_GEO_API_KEY');
		
		$result_string = file_get_contents($url);
		$result = json_decode($result_string, true);
		
		$result3 = array();
		if(isset($result['results']) && $result['status'] == 'OK' &&  isset($result['results'][0])){
		 $result1[]=$result['results'][0];
		 $result2[]=$result1[0]['geometry'];
		 $result3[]=$result2[0]['location'];
		 $result3 = $result3[0];
		}
		
		return $result3;
	}
	public function postStorereedemer(Request $request)
	{
		$zipcode=$request[0]['postal_code'];
		$location_arr=$this->get_lat_lng($zipcode);
		
		$lat=$location_arr['lat'];
		$lng=$location_arr['lng'];
		$c_c=strtolower($request[0]['company_name']);
		$user_check = User::where('company_name',$c_c)->count();
		//dd($user_check);
		if($user_check >0)
		{
			return 'already_company_exists';
			exit;
		}
		if($request[0]['address']=='' || $request[0]['web_address']=='' || $request[0]['company_name']=='' || $request[0]['email']=='')
		{
		 	return 'error';
		 	exit;
		}

		$src = $request[0]['image_name'];
		$extArr = array_reverse(explode("/",$request[0]['image_type']));
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

		$src = $request[0]['image_data'];
		
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

		$loggeduser=Auth::user();

		if($response_arr->result_code=="TargetCreated")
		{
			
			$newLogo = new Logo();
			$newLogo->contact_email = $request[0]['email'];
			$newLogo->cat_id        = $request[0]['category_id'];
			$newLogo->subcat_id     = $request[0]['subcat_id'];
			$newLogo->company_name  = $request[0]['company_name'];
			$newLogo->first_name    = $request[0]['first_name'];
			$newLogo->last_name     = $request[0]['last_name'];
			$newLogo->address       = $request[0]['address'];
			$newLogo->city			= $request[0]['city'];
			$newLogo->state			= $request[0]['state'];
			$newLogo->zipcode       = $zipcode;
			$newLogo->lat           = $lat;
			$newLogo->lng			= $lng;
			$newLogo->mobile		= $request[0]['mobile'];
			$newLogo->web_address	= $request[0]['web_address'];
			$newLogo->enhance_logo  = $request->enhance_logo=='true'?1:0;
			$newLogo->status 	    = 1;
			$newLogo->action_id     = 1;
			$newLogo->uploaded_by   = $loggeduser->id;
			$newLogo->default_logo 	= 1;
			$newLogo->target_id     = $response_arr->target_id;
			$newLogo->logo_name     = $newRandName.'.'.$mainExt;			
			// $newLogo->reedemer_id   = NULL;
			
			if($newLogo->save())
			{
				return 'success';		
				exit;
			}else{
				return 'error';
			}
		} else {
			return 'error';		
			exit;
		}
	}

	/*public function getCreatereedemer()
	{
		return view('admin.reedemer.add');
	}*/

	public function postUploadlogo(Request $request)
	{		
		dd($request->all());
		$obj = new helpers();
		$folder_name=env('UPLOADS');
		$file_name=$_FILES[ 'file' ][ 'name' ];
		$temp_path = $_FILES[ 'file' ][ 'tmp_name' ];

		

		if (!file_exists($folder_name)) {			
			$create_folder= helpers::createDir($folder_name, 0777);
			$thumb_path= helpers::createDir($folder_name."/thumb", 0777);
			$medium_path= helpers::createDir($folder_name."/medium", 0777);
			$original_path= helpers::createDir($folder_name."/original", 0777);
		}
		else
		{			
			$thumb_path= env('UPLOADS')."/thumb"."/";
			$medium_path= env('UPLOADS')."/medium"."/";
			$original_path= env('UPLOADS')."/original"."/";
		}

		//echo "PP".$file_name;
		//die();
		$extension = pathinfo($file_name, PATHINFO_EXTENSION);
		$new_file_name = time()."_".rand(111111111,999999999).'.'.$extension; // renameing image

		$file_ori = $_FILES[ 'file' ][ 'tmp_name' ];
		
		move_uploaded_file($file_ori, "$original_path$new_file_name");
		
		//$obj->createThumbnail($original_path,$thumb_path,env('THUMB_SIZE'));
		//$obj->createThumbnail($original_path,$medium_path,env('MEDIUM_SIZE'));		
		
		return $new_file_name;

	}

	public function getVuforiarate($target_id,$logo_id,$contact_email='')
	{
		//dd($logo_id);
		$client = new vuforiaclient();
		//$target_id=$target_id->target_id;

		$target_res_details=$client->getTarget($target_id); 
		//$response_arr=json_decode($target_res_details);
		$response_arr=json_decode($target_res_details);
		$tracking_rating=$response_arr->target_record->tracking_rating;
		
		$logo = Logo::find($logo_id);

		$logo->tracking_rating = $tracking_rating;
		$logo->contact_email = $contact_email;

		if($logo->save())
		{
			//$logo_id = $logo->id;
			return array('response'=>'success','rating'=>$tracking_rating);
		}
	}

	public function getAddlogo($logo_text='',$image_name,$enhance_logo=0)
	{	
	
		$id=Auth::user()->id;
		$type=Auth::user()->type;	

		$prev_logo =Logo::where("reedemer_id",$id)->first();	
		//dd($prev_logo->cat_id);
		if($type==2)
		{
			$user_details=User::find($id);
			//dd($user_details->company_name);
			$reedemer_id=$id;
			//dd()
			$status=0;
			$logo_text=$user_details->company_name;
		}
		else
		{
			$reedemer_id=null;
			$status=1;
			//$logo_text="";
				
		}

		$client = new vuforiaclient();
		$rand=rand(111111,999999);
		$send[0] = "Logo_".time()."_".$rand;
		$send[1] = '../uploads/original/'.$image_name;
		$send[2] = '../uploads/original/'.$image_name;
		$send[3] = 'Redeemar';
		$send[4] = 'Redeemar';		
		$response=$client->addTarget($send);
		$response_arr=json_decode($response);		

		if($response_arr->result_code=="TargetCreated")
		{
			//dd("A");
			$target_id=$response_arr->target_id;					
			$logo = new Logo();
			$logo->reedemer_id 		= $reedemer_id;	
			$logo->target_id 		= $target_id;
			$logo->logo_name 		= $image_name;	
			$logo->logo_text 		= $logo_text;
			$logo->cat_id 			= $prev_logo->cat_id;
			$logo->subcat_id 		= $prev_logo->subcat_id;
			$logo->status 			= $status;			
			$logo->enhance_logo 	= $enhance_logo;
			$logo->uploaded_by 		= $id;
			if($logo->save())
			{
				$logo_id = $logo->id;
				return array('response'=>'success','target_id'=>$target_id,'logo_id'=>$logo_id);
			}			
		}
		else
		{
			return array('response'=>'image_problem','target_id'=>'');			
		}
	}

	public function getUpdatedefault($id='')
	{		
		$uploaded_by=Auth::user()->id;

		Logo::where('uploaded_by', $uploaded_by)->update(array('default_logo' => 0));

		$logo = Logo::find($id);
		$logo->default_logo	= 1;
		if($logo->save())
		{		
			return 'success';
		}
		else
		{
			return 'success'; 	
		}
	}
	
	
	public function postAddlogo(Request $request)
	{
		/// fileupload 
		$extArr = array_reverse(explode("/",$request[0]['image_type']));
		$ext=$extArr[0];
		$mainExt  = $ext=='png'?'png':'jpg';
		$alterExt = $ext=='png'?'jpg':'png';
		$alterMethod = $ext=='png'?'jpeg':'png';
		$obj = new helpers();
		
		$newRandName = time()."_".rand(111111,999999);
		$thumb_path		= base_path()."/uploads/thumb"."/";
		$medium_path	= base_path()."/uploads/medium"."/";
		$original_path	= base_path()."/uploads/original"."/";

		
		$largeWidth   = env('LOGO_LARGE_WIDTH');
		$largeHeight  = env('LOGO_LARGE_HEIGHT');
		$mediumWidth  = env('LOGO_MEDIUM_WIDTH');
		$mediumHeight = env('LOGO_MEDIUM_HEIGHT');
		$smallWidth   = env('LOGO_SMALL_WIDTH');
		$smallHeight  = env('LOGO_SMALL_HEIGHT');

		$src=$request[0]['image_data'];
		
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
		Image::make($mainOriginalFilePath)->resize($mediumWidth, $mediumHeight, function($constraint) { $constraint->aspectRatio(); })->save($mainMediumFilePath);
		Image::make($mainOriginalFilePath)->resize($smallWidth, $smallHeight, function($constraint) { $constraint->aspectRatio(); })->save($mainThumb_path);
		
		$altOriginalFilePath = $original_path.$newRandName.'.'.$alterExt;
		$altMediumFilePath   = $medium_path.$newRandName.'.'.$alterExt;
		$altThumb_path   	  = $thumb_path.$newRandName.'.'.$alterExt;
		$method = 'image'.$alterMethod;
		
		$this->convertImage($mainOriginalFilePath,$altOriginalFilePath,$alterExt);
		$this->convertImage($mainMediumFilePath,$altMediumFilePath,$alterExt);
		$this->convertImage($mainThumb_path,$altThumb_path,$alterExt);
		
		Image::make($original_path.$newRandName.'.png')->resize($largeWidth, $largeHeight, function($constraint) { $constraint->aspectRatio(); })->save($fileMngrLargePath);
		Image::make($original_path.$newRandName.'.png')->resize($mediumWidth, $mediumHeight, function($constraint) { $constraint->aspectRatio(); })->save($fileMngrMediumPath);
		Image::make($original_path.$newRandName.'.png')->resize($smallWidth, $smallHeight, function($constraint) { $constraint->aspectRatio(); })->save($fileMngrSmallPath);
		
		chmod($fileMngrLargePath,0777);
		chmod($fileMngrMediumPath,0777);
		chmod($fileMngrSmallPath,0777);
		
		chmod($mainOriginalFilePath,0777);
		chmod($mainMediumFilePath,0777);
		chmod($mainThumb_path,0777);
		chmod($altOriginalFilePath,0777);
		chmod($altMediumFilePath,0777);
		chmod($altThumb_path,0777);
		chmod($mainOriginalFilePathTmp,0777);
		@unlink($mainOriginalFilePathTmp);
		/// end fileupload
		}catch(\Exception $e){
			return array('response'=>'image_problem','target_id'=>'');	
				
		}
		$logo_text=$request[0]['company_name'];
		$enhance_logo=0;
		$cat_id=$request[0]['category_id'];
		$subcat_id=$request[0]['subcat_id'];
		$contact_email=$request[0]['contact_email'];
		
		$user = Auth::user();
		
		$id=$user->id;
		$type=$user->type;		
		if($type==2)
		{
			$user_details=User::find($id);			
			$reedemer_id=null;
			
			$status=0;
			$logo_text=$user_details->company_name;
		}
		else
		{
			if($request[0]['redeemar_id']){
				$reedemer_id = $request[0]['redeemar_id'];
				$status = 1;
			} else {
				$reedemer_id = null;
				$status = 1;
			}
		}

		$client = new vuforiaclient();
		
		$send[0] = $newRandName;
		$send[1] = '../uploads/medium/'.$newRandName.'.jpg';
		$send[2] = '../uploads/medium/'.$newRandName.'.jpg';
		$send[3] = 'Redeemar';
		$send[4] = 'Redeemar';		
		$response=$client->addTarget($send);
		$response_arr=json_decode($response);		

		if($response_arr->result_code=="TargetCreated")
		{
						
			if($request[0]['postal_code'])
			{
				$zipcode=$request[0]['postal_code']	;
				$location_arr=$this->get_lat_lng($zipcode);		
				$lat=isset($location_arr['lat'])?$location_arr['lat']:'';
				$lng=isset($location_arr['lng'])?$location_arr['lng']:'';	
			}
			else
			{
				$zipcode='';				
				$lat='';
				$lng='';
			}
			//dd("A");
			
			$target_id=$response_arr->target_id;
			$rating = $this->getVuforiaRatebyTargetId($target_id);
			$logo = new Logo();
			$logo->reedemer_id 		= $reedemer_id;	
			$logo->target_id 		= $target_id;
			$logo->logo_name 		= $newRandName.'.'.$mainExt;	
			$logo->logo_text 		= $logo_text;
			$logo->contact_email	= $contact_email;
			$logo->cat_id 			= $cat_id;
			$logo->subcat_id 		= $subcat_id;
			$logo->status 			= $status;			
			$logo->enhance_logo 	= $enhance_logo;
			$logo->uploaded_by 		= $id;
			$logo->company_name 	= $request[0]['company_name'];
			$logo->first_name 		= $request[0]['first_name'];
			$logo->last_name 		= $request[0]['last_name'];
			$logo->address 			= $request[0]['address'];
			$logo->city 			= $request[0]['city'];
			$logo->state 			= $request[0]['state'];
			$logo->zipcode 			= $zipcode;
			$logo->lat 				= $lat;
			$logo->lng 				= $lng;
			$logo->mobile 			= $request[0]['mobile'];
			$logo->tracking_rating  = $rating?$rating:0;
			$logo->web_address 		= $request[0]['web_address'];			
			if($logo->save())
			{
				$logo_id = $logo->id;
				return array('response'=>'success','target_id'=>$target_id,'logo_id'=>$logo_id);
			}			
		}
		else
		{
			return array('response'=>'image_problem','target_id'=>'');			
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
	
	public function getVuforiaRatebyTargetId($target_id)
	{
		
		$client = new vuforiaclient();
		$target_res_details=$client->getTarget($target_id); 
		$response_arr=json_decode($target_res_details);
		$tracking_rating=$response_arr->target_record->tracking_rating;
		return $tracking_rating;		
	}


	
	public function getAlllogo()
	{
		$id=Auth::user()->id;
		$type=Auth::user()->type;		
		if($type==2)
		{
			$logo_details = Logo::orderBy('id','DESC')
						->where('status',1)
						->whereNull('reedemer_id')
						->get();	
		}
		else
		{
			$logo_details = Logo::orderBy('id','DESC')						
						->get();
				
		}		

		$logo_arr=array();	
		$company_name="N/A";
		$target_id=NULL;
		foreach($logo_details as $logo_details)
		{	

			if($logo_details['reedemer_id'] >0)
			{
				$company_details=User::find($logo_details['reedemer_id']);
				$company_name=$company_details['company_name'];
			}			

			$logo_arr[]=array(
						'id'=>$logo_details['id'],
						'reedemer_id'=>$logo_details['reedemer_id'],
						'tracking_rating'=>$logo_details['tracking_rating'],
						'reedemer_company'=>$company_name,
						'logo_name'=>$logo_details['logo_name'],
						'logo_text'=>$logo_details['logo_text'],
						'status'=>$logo_details['status'],
						'uploaded_by'=>Auth::user()->id,
						'created_at'=>$logo_details['created_at'],
						'updated_at'=>$logo_details['updated_at'],
					  );
		}
		$logo_json=json_encode($logo_arr);		
		return $logo_json;		
			
	}

	public function getLogodetails($logo_id)
	{
		
		$logo_details = Logo::where('id',$logo_id)->get();
		//dd($logo_details);
		$logo_arr=array();	
		$company_name="N/A";
		$target_id=NULL;
		foreach($logo_details as $logo_details)
		{	

			if($logo_details['reedemer_id'] >0)
			{
				$company_details=User::find($logo_details['reedemer_id']);
				$company_name=$company_details['company_name'];
			}			

			$logo_arr[]=array(
						'id'=>$logo_details['id'],
						'reedemer_id'=>$logo_details['reedemer_id'],
						'tracking_rating'=>$logo_details['tracking_rating'],
						'target_id'=>$logo_details['target_id'],
						'reedemer_company'=>$company_name,
						'logo_name'=>$logo_details['logo_name'],
						'logo_text'=>$logo_details['logo_text'],
						'status'=>$logo_details['status'],
						'uploaded_by'=>Auth::user()->id,
						'created_at'=>$logo_details['created_at'],
						'updated_at'=>$logo_details['updated_at'],
					  );
		}
		$logo_json=json_encode($logo_arr);		
		return $logo_json;		
			
	}

	public function getRate()
	{
		
		 $rand=rand(1,5)	;
		return $rand;		
			
	}

	public function postDeletereedemer(Request $request)
	{
		$user = User::find($request->redeemar_id);
		$created_by = StoreImage::where('user_id', $user->id)->get();

		if($created_by){
			StoreImage::where('user_id', $user->id)->delete();
		}
		
		$userEmail = $user->email;
		$user = User::where('email',$userEmail)->delete();
		if($user)
		{

			$logo_details = Logo::where('reedemer_id',$request->redeemar_id)
							->get();
			$newReqInst = new Request();
			foreach($logo_details as $logo) {

				$this->postDeletelogo( $newReqInst,$logo->id);

			}

			return 'success';
		}

		if(Logo::where('reedemer_id',$request->redeemar_id)->count()){
			return 'error';
		} else {
			return 'success';
		}
	}

	public function postDeletelogo(Request $request,$logoid){
		// dd($request->all());
		if(!$logoid){
			$id = $request->logo_id;
		} else {
			$id = $logoid;
		}

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

	public function postUserdetails()
	{		
		$user_id=Auth::user()->id;
		$type=Auth::user()->type;
		$user_details=User::findOrFail($user_id);
		if($type==2)
		{

			$logoDetails=Logo::where('reedemer_id',$user_details->id)
						->get();

          	if(count($logoDetails)>0)
			{

				$logo_Details=Logo::where('reedemer_id',$user_details->id)
							  ->where('default_logo',1)
							  ->get();

				if(count($logo_Details)>0)
				{
                  $logo_details=Logo::where('reedemer_id',$user_details->id)
                  				->where('default_logo',1)
                  				->first()
                  				->logo_name;

				}
				else
				{
					$logo_details=Logo::where('reedemer_id',$user_details->id)
									->first()
									->logo_name;

				}

           
			}else
			
			{  

				$logo_details="no_logo.gif";
			
			}			
		}
		//dd($logo_details);
		$user_arr=array();
		$user_arr['company_name']=$user_details->company_name;
		$user_arr['email']=$user_details->email;
		$user_arr['type']=$user_details->type;
		$user_arr['location']=$user_details->location;
		if($type==2)
		{
			$user_arr['logo_image']=$logo_details;
		}
		
		return $user_arr;
	}
	
	public function postUpdatestatus(Request $request)
	{

		$id=$request->get('user_logo_id');
		$target_id=$request->get('user_logo_target_id');
		$reedemer_id=Auth::user()->id;
		$default_logo=0;
		//dd($target_id);
		//dd($reedemer_id);
		$check_default=$this->check_default($reedemer_id);
		if($check_default==0)
		{
			$default_logo=1;
		}
		$logo = Logo::find($id);
		$logo->reedemer_id=$reedemer_id;
		$logo->uploaded_by=$reedemer_id;
		$logo->default_logo=$default_logo;
		//dd($check_default);
		//die();
		if($logo->save())
		{
			return 'success';
		}
	}

	public function postCategory(Request $request)
	{
		//dd($request->all());
		if($request[0]['sub_cat'])
		{
			$category = Category::where('parent_id',$request[0]['parent_id'])
						->where('visibility',1)
						->orderBy('id','DESC')
						->get();
		}
		else
		{
			$id=null;
			if($request[0])
			{
				$id=$request[0];
				$category = Category::where('id',$id)
							->where('visibility',1)
							->orderBy('id','DESC')
							->get();
			}
			else
			{
				$category = Category::where('parent_id',0)
							->where('visibility',1)
							->orderBy('id','DESC')
							->get();
			}
		}
		//dd($category->toArray());
		return $category;
	}

	//Listing to show only 
	public function getCategory($parent_id='')
	{
		//dd($parent_id);
		//$id=null;
		if($parent_id<=0)
		{
			
			//$id=$request[0];
			$category = Category::where('parent_id',$parent_id)
						->where('visibility',1)
						->get();

			//dd($parent_id);
		}
		else
		{
			$category = Category::where('visibility',1)
						->orderBy('id','DESC')
						->get();
		}
		//dd($category->toArray());
		return $category;
	}

	public function getAllcategory($parent_id='')
	{
		
	   $category = Category::where('visibility',1)
						->get();

			
		return $category;
	}
	
	public function getCategories(){
		
		$categories = Category::where('visibility',1)->where('parent_id',0)->get();
		
		$data = $this->catTree($categories);
		return $data;
		
	}
	
	public function catTree( $cats){
		
		$data = [];
		foreach($cats as $key=>$category)
		{
		  $data[] =  $category->toArray() + ['children' => $this->catTree($category->children)];
		}
		return $data;
	
	}

	public function getOwncategory($parent_id='')
	{
		$category = Category::where('parent_id',$parent_id)
					->where('visibility',1)
					->get();

		return $category;
	}

	public function getSwapcatstatus($cat_id){
    if(!$cat_id) return array('status'=>'error','msg'=>'Parameter is missing');
    
    $cat = Category::find($cat_id);
    if($cat->status == 1)
    {
      $cat->status = 0;
    }else{
      $cat->status = 1;
    }
    if($cat->save()){
      $newStatus = $cat->status==1?'Active':'Inactive';
      return array('status'=>'success','msg'=>'Status has been changed to '.$newStatus);
    }
    return array('status'=>'error','msg'=>'Oops, something is wrong, please try again');
  }

	public function postStorecategory(Request $request)
	{	
		//dd($request->all());
		$cat_name=$request->get('cat_name');
    if(!$request->cat_name || $request->status=='') return array('status'=>'error','msg'=>'Some fields are missing');
		if($request->get('parent_id'))
		{
			$parent_id=$request->get('parent_id');
		}
		else
		{
			$parent_id=0;
		}

		$category = new Category();
		$isExist = Category::where('cat_name',$cat_name)->where('visibility',1)->count();
		$category->cat_name 		= $cat_name;	
		$category->parent_id 		= $parent_id;
		$category->status 		  = $request->status;
		
		if($isExist>0){
			return array('status'=>'error','msg'=>'This category name is already exist, try an another name');
		}
		if($category->save())
		{
			return array('status'=>'success','msg'=>'Category has been created successfully');
		}
		else
		{
			return array('status'=>'error','msg'=>'Oops, something is wrong, please try again');
		}
		//dd(4)
		//dd($request->get('cat_name'));
	}
	
	public function postUpdatecategory(Request $request){
		if($request->id =='' || !$request->cat_name || $request->status==='') return array('status'=>'error','msg'=>'Some fields are missing');
		
		$existCount = Category::where('cat_name',$request->cat_name)->where('id','!=',$request->id)->where('visibility',1)->get()->count();
		
		if(!$existCount){
			$cat = Category::find($request->id);
			$cat->cat_name = $request->cat_name;
			$cat->status = $request->status;
			$cat->save();
			return array('status'=>'success','msg'=>'Category has been updated');
		}else{
			return array('status'=>'error','msg'=>'This category name is already exist, try an another name');
		}
	}


	public function getDeletecategory($id)
	{
		$category = Category::find($id); 
		
		$chk_subcat=Category::where('parent_id',$category->id)->where('visibility','1')->count();
	
		if($chk_subcat >0)
		{
			return array('status'=>'error','msg'=>'Please delete first its sub categories');
		}
		else
		{
			$category->visibility = 0;
			if($category->save())
			{
				return array('status'=>'success','msg'=>'Category successfully deleted');
			}
			else
			{
				return array('status'=>'error','msg'=>'Unable to delete this category');
			}
		}
	}

	
	//Listing to show only 
	public function postSubcategory(Request $request)
	{	
		//dd($parent_id)	;
		if($request->parent_id != '')
		{
			//$id=$request[0];
			$category = Category::where('parent_id',$request->parent_id)
						->where('visibility',1)
						->get();
		}
		else
		{
			return 'subcat_not_exists';
		}
		//dd($category->toArray());
		return $category;
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


	public function postLogobyuser()
	{
		$reedemer_id=Auth::user()->id;
		//dd($reedemer_id);
		$logo=Logo::where('reedemer_id',$reedemer_id)
			  ->with('action')
			  ->orderBy('id','DESC')
			  ->get();
		//return 'vvv';
		//dd($logo->toArray());
		return $logo;
	}

	function check_default($reedemer_id)
	{
		$logo=Logo::where('reedemer_id',$reedemer_id)->get();
		$prev_logo_count=$logo->count();

		return $prev_logo_count;
	}

	public function postChecklogin()
	{
		//dd("A");
		//$user_id=Auth::User()->id;
		if (Auth::check())
		{
			return 'login';
			
		}
		else
		{
			return 'logout';
		}
		//return 'a';
	}
	
	public function getTest(){
		phpinfo();
		$whitejpg = base_path().'/uploads/test/white.jpg';
		$whitepng = base_path().'/uploads/test/white.png';
		
		$Image = new Imagick($whitejpg);
		$BackgroundColors = array(
			'TopLeft' => array(1, 1),
			'TopRight' => array($Image->getimagewidth(), 1),
			'BottomLeft' => array(1, $Image->getimageheight()),
			'BottomRight' => array($Image->getimagewidth(), $Image->getimageheight())
		);
		
		foreach ($BackgroundColors as $Key => $BG) {
			$pixel = $Image->getImagePixelColor($BG[0], $BG[1]);
			$colors = $pixel->getColor();
			$ExcludedColors[] = rgb2hex(array_values($colors));
			$Image->floodfillPaintImage('none', 9000, $pixel, $BG[0] - 1, $BG[1] - 1, false);
			//Comment the line above and uncomment the below line to achieve the effects of the second Vette
			//$Image->transparentPaintImage($pixel, 0, 9000, false);
		}
		$Image->writeImage($whitepng);
		
	}
	public function transparent_background($src_path,$dest_path, $color) 
	{
		$img = imagecreatefromjpeg($src_path); //or whatever loading function you need
		$colors = explode(',', $color);
		$remove = imagecolorallocate($img, $colors[0], $colors[1], $colors[2]);
		imagecolortransparent($img, $remove);
		imagepng($img, $dest_path);
		

	}

	public function postUpdatepartnerdata(Request $request)
	{
		//dd($request->all());
		$zipcode=$request->zipcode;
		$location_arr=$this->get_lat_lng($zipcode);
		
		$lat=$location_arr['lat'];
		$lng=$location_arr['lng'];

		if($request->address == '' || $request->web_address == '' || $request->company_name=='' || $request->email=='')
		{
		 	return 'error';
		 	exit;
		}

		// update the data for our user
		$user=User::find($request->input('id'));
		$user->company_name = $request->input('company_name');			
		$user->first_name 	= $request->input('first_name');	
		$user->last_name 	= $request->input('last_name');	
		$user->address 		= $request->input('address');
		$user->zipcode 		= $zipcode;
		$user->lat 			= $lat;
		$user->lng 			= $lng;
		$user->type 		= $request->input('type');
		$user->approve 		= $request->input('approve');
		$user->email 		= $request->input('email');
		$user->web_address 	= $request->input('web_address');
		$user->cat_id 		= $request->input('cat_id');
		$user->subcat_id 	= $request->input('subcat_id');
		$user->state 		= $request->input('state');
		$user->city 		= $request->input('city');
		$user->location 	= $request->input('location');
		$user->save();
		
		return 'success';		
		exit;
		
	}

	public function getUnusedlogos(){
		$allLogos = array();
		$logoDetails = Logo::where('reedemer_id', NULL)->get();
		if($logoDetails){
			return $logoDetails;
		} else {
			return $allLogos;
		}
	}

	public function postUpdatepartnerlogodata(Request $request){
		// dd($request->image_changed);
		$logo = Logo::find($request->id);
		if($request->image_changed){

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

			$src = $request->image_name;
			$extArr = array_reverse(explode("/",$request[0]['image_type']));
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

			$src = $request->image_data;
			
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
				$logo->target_id     = $response_arr->target_id;
				$logo->logo_name     = $newRandName.'.'.$mainExt;
			} else {
				return 'error';
			}
		}

		$zipcode=$request->zipcode;
		$location_arr=$this->get_lat_lng($zipcode);
		
		$lat=$location_arr['lat'];
		$lng=$location_arr['lng'];

		$logo->logo_text 		= $request->company_name;
		$logo->contact_email	= $request->contact_email;
		$logo->cat_id 			= $request->cat_id;
		$logo->subcat_id 		= $request->subcat_id;
		$logo->status 			= $request->status;
		$logo->company_name 	= $request->company_name;
		$logo->first_name 		= $request->first_name;
		$logo->last_name 		= $request->last_name;
		$logo->address 			= $request->address;
		$logo->city 			= $request->city;
		$logo->state 			= $request->state;
		$logo->zipcode 			= $zipcode;
		$logo->lat 				= $lat;
		$logo->lng 				= $lng;
		$logo->mobile 			= $request->mobile;
		$logo->web_address 		= $request->web_address;

		if($logo->save()){
			return 'success';
		} else {
			return 'error';
		}
	}

	public function postLogostatusupdate(Request $request){
		$status 	= $request->status;
		$id 		= $request->logo_id;

		if($status==1)
		{
			$new_status=0;
		}
		else
		{
			$new_status=1;	
		}
		$logo = Logo::find($id);
		$logo->status=$new_status;
		if($logo->save()){
			return 'success';
		} else {
			return 'error';
		}
	}

	public function getUsedbeacondetails()
	{
		$beaconDetails = Beacons::where('redeemar_id','<>', '')->get();
		if(count($beaconDetails)){
			$Allaction = Action::all();
			foreach ($beaconDetails as $beacon) {
				$action = new stdClass();
				$user = User::find($beacon->redeemar_id);
				$beacon['partner'] = $user;
				if($beacon->beacon_type == 'proximity'){
					foreach ($Allaction as $act) {
						if($act->id == $beacon->action_id){
							$action->action_name = $act->action_name;
						}
					}
				} else {
					foreach ($Allaction as $act) {
						if($act->id == $beacon->action_id){
							$action->proximity_action_name = $act->action_name;
						}

						if($act->id == $beacon->action_id_2){
							$action->interactive_action_name = $act->action_name;
						}
					}
				}
				
				$beacon['action'] = $action;
			}
			
			return $beaconDetails;
		} else {
			$beaconDetails = [];
			return $beaconDetails;
		}
	}

	public function getUnusedbeacondetails()
	{
		$beaconDetails = Beacons::where('redeemar_id', '')->get();
		if(count($beaconDetails)){
			$Allaction = Action::all();
			foreach ($beaconDetails as $beacon) {
				$action = new stdClass();
				if($beacon->beacon_type == 'proximity'){
					foreach ($Allaction as $act) {
						if($act->id == $beacon->action_id){
							$action->action_name = $act->action_name;
						}
					}
				} else {
					foreach ($Allaction as $act) {
						if($act->id == $beacon->action_id){
							$action->proximity_action_name = $act->action_name;
						}

						if($act->id == $beacon->action_id_2){
							$action->interactive_action_name = $act->action_name;
						}
					}
				}
				
				$beacon['action'] = $action;
			}
			
			return $beaconDetails;
		} else {
			$beaconDetails = [];
			return $beaconDetails;
		}
	}

	public function postAddbeacon(Request $request)
	{
		//dd($request->all());
		$beacon = new Beacons();
		$beacon->redeemar_id = '';
		$beacon->request_id = '';
		$beacon->name = $request->name;
		$beacon->beacon_type = $request->beacon_type;
		if($beacon->beacon_type == 'proximity'){
			$beacon->uuid = $request->uuid;
			$beacon->major = $request->major;
			$beacon->minor = $request->minor;
			$beacon->color = $request->color;
		} else {
			$beacon->identifier = $request->identifier;
			$beacon->category = $request->category;
			$beacon->action_id_2 = 1;
		}

		if($beacon->save())
		{
			$action=Action::find(1);
			$array=array(
					'success'=>true,
					'action_name'=>$action->action_name
				   );
		}
		else
		{			
			$array=array(
					'success'=>'error',
					'action_name'=>''
				   );
		}
		return $array;
	}

	public function postDeletebeacon($id)
	{
		if(!$id) return false;
		
		$beacon = Beacons::find($id);
		if($beacon->delete())
		{
			$action=Action::find(1);
			$array=array(
					'success'=>true
				   );
		}
		else
		{			
			$array=array(
					'success'=>'error'
				   );
		}
		return $array;
	}

	public function postUpdatebeacon(Request $request)
	{
		//dd($request->category);
		$beacon = Beacons::find($request->id);
		$beacon->name = $request->name;

		if($beacon->beacon_type === 'proximity'){
			$beacon->uuid = $request->uuid;
			$beacon->major = $request->major;
			$beacon->minor = $request->minor;
			$beacon->color = $request->color;
		} else {
			$beacon->identifier = $request->identifier;
			$beacon->category = $request->category;
		}

		if($beacon->save())
		{
			$array=array(
					'success'=>true
				   );
		}
		else
		{			
			$array=array(
					'success'=>'error'
				   );
		}
		return $array;
	}

	public function postUpdatebeaconstatus(Request $request)
	{
		$beacon = Beacons::find($request->beacon_id);
		if($beacon->active){
			$beacon->active = 0;
		} else {
			$beacon->active = 1;
		}

		if($beacon->save())
		{
			$array=array(
					'success'=>true
				   );
		}
		else
		{			
			$array=array(
					'success'=>'error'
				   );
		}
		return $array;
	}

	public function getChangebeaconaction($beacon_id,$action_id)
	{

		$user_id=Auth::User()->id;
		$user=User::find($user_id);
		$user_level=$user->membership_level;
		$beacon=Beacons::where('redeemar_id',$user_id)->first();
		$action_id=$beacon->action_id;
		$action_list=Action::where('status',1)
					->where('min_level','<=',$user_level)
					->orderBy('action_name','ASC')
					->get();

		$array=array(
			'success'=>true,
			'action_list'=>$action_list,
			'action_id'=>$action_id,
			'beacon_id'=>$beacon_id
		);
		return $array;
	}	

	public function getAllbeaconrequest()
	{
		$alldata 	= [];
		$beacon 	= [];
		$data = ReedemerBeaconsRequestCount::all();
		if(count($data)){
			foreach ($data as $key => $request_maker) {
				$all_request = Requestbeacon::where('redeemar_id', $request_maker->redeemar_id)->get();
				foreach ($all_request as $key => $request_beacon) {
					$time = explode(" ", $request_beacon->created_at)[1];
	                $date  = date("F jS, Y", strtotime($request_beacon->created_at));
	                $datentime = $date.' '.$time;
	                $request_beacon->creation_time = $datentime;
				}

				$beacon['all_request'] = $all_request;
				$user=User::find($request_maker->redeemar_id);
				$beacon['partner'] = $user;
				array_push($alldata, $beacon);
			}
		}
		
		return $alldata;
	}

	public function postAssignbeacon(Request $request)
	{
		$beacon = Beacons::find($request->beacon_id);
		$beacon->redeemar_id = $request->redeemar_id;
		$beacon->request_id  = $request->reqId;
		$req = Requestbeacon::find($request->reqId);
		$req->assigned_count = $req->assigned_count+1;
		$req->status = 'approved';
		if($beacon->save() && $req->save()){
			return 'success';
		} else {
			return 'err';
		}
	}

	public function getRemoveassignedpartnerforbeacon($id)
	{
		$beacon = Beacons::find($id);
		$beacon->redeemar_id = '';
		$req = Requestbeacon::find($beacon->request_id);
		$req->assigned_count = $req->assigned_count-1;
		if($req->assigned_count == 0){
			$req->status = 'pending';
		}

		$beacon->request_id = '';
		if($beacon->save() && $req->save()){
			return 'success';
		} else {
			return 'err';
		}
	}

	public function getActionlist()
	{
		return Action::all();
	}

	public function getOffersbyuser($redeemar_id, $campaign_id)
	{
		$offer_list=Offer::select(array('*',DB::raw('DATEDIFF(CAST(end_date as char), NOW()) AS expires')))
					->where('created_by',$redeemar_id)
					->where('campaign_id',$campaign_id)
					->where('status','1')
					->with('categoryDetails','subCategoryDetails','partnerSettings','companyDetail')
					->orderBy('created_at','desc')
					->get();

		return $offer_list;
	}

	public function getCampaignbyuser($redeemar_id)
	{
		$campaign = [];
		$user_id  = $redeemar_id;
		$camp = Campaign::where('created_by',$user_id)->get();
		if(count($camp)){
			$campaign = $camp;
		}

		return $campaign;
	}

	public function postUpdatebeaconaction(Request $request)
	{
		if($request->beacon_type == 'interactive'){
			$beacon = Beacons::find($request->beacon_id);
			if($request->action_id != ""){
				$beacon->action_id = $request->action_id;
			}

			if($request->offer_id != ""){
				$beacon->particular_id = $request->offer_id;
			}

			if($request->action_id2 != ""){
				$beacon->action_id_2 = $request->action_id2;
			}

			if($request->offer_id2 != ""){
				$beacon->particular_id_2 = $request->offer_id2;
			}

			if($beacon->save()){
				return 'success';
			} else {
				return 'err';
			}
		} else {
			$beacon = Beacons::find($request->beacon_id);
			if($request->action_id != ""){
				$beacon->action_id = $request->action_id;
			}

			if($request->offer_id != ""){
				$beacon->particular_id = $request->offer_id;
			}

			if($beacon->save()){
				return 'success';
			} else {
				return 'err';
			}
		}
	}

	public function getDeleteallrequest($id)
	{
		$count = 1;
		$req = Requestbeacon::where('redeemar_id', $id)->get();
		$record_count = count($req);
		
		$rec = ReedemerBeaconsRequestCount::where('redeemar_id', $id)->get();
		$rec = ReedemerBeaconsRequestCount::find($rec[0]->id);
		if(!$record_count){
			if($rec->delete()){
				return 'success';
			} else {
				return 'err';
			}
		} else {
			$rec->delete();
			foreach ($req as $key => $single_request) {
				$ret = Requestbeacon::find($single_request->id);
				if($count == $record_count){
					if($ret->delete()){
						return 'success';
					} else {
						return 'err';
					}
				} else {
					$ret->delete();
					$count++;
				}
			}
		}
		
	}

	public function getDeletesinglerequest($id)
	{
		$req = Requestbeacon::find($id);
		$rec = ReedemerBeaconsRequestCount::where('redeemar_id', $req->redeemar_id)->get();
		$rec = ReedemerBeaconsRequestCount::find($rec[0]->id);
		$rec->request_count = $rec->request_count - 1;
		if($rec->save()){
			if($req->delete()){
				return 'success';
			} else {
				return 'err';
			}
		} else {
			return 'err';
		}
	}
}
