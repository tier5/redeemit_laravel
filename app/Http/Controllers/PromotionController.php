<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Illuminate\Http\Response; 
use App\Model\User;
use App\Model\Video;
use App\Model\Directory;
use App\Model\Category;
use App\Model\Offer;
use App\Model\OfferDetail;
use Hash;
use Validator;
use App\Model\Logo;
use App\Helper\vuforiaclient;
use App\Helper\helpers;
use Auth; 
use Session ;
use App\Model\Campaign;
use App\Model\Inventory;
use App\Model\UserBankOffer;
use App\Model\OfferCategory;
use App\Model\OfferInventory;
use App\Model\OfferProduct;
use DB, \Image;


class PromotionController extends Controller {	
	
	
	public function __construct( )
	{
//		if($this->middleware('auth'))		
//		{
//			Auth::logout();
//    		return redirect('auth/login');	
//    	}
//		
	}

	public function getIndex()
	{
		$reedemer_id=Auth::User()->id;
		$offer_list=Offer::select(array('*',DB::raw('DATEDIFF(CAST(end_date as char), NOW()) AS expires')))
					->where('created_by',$reedemer_id)
					->where('status','1')
					->with('categoryDetails','subCategoryDetails','partnerSettings','companyDetail')
					->orderBy('created_at','desc')
					->get();			
		return $offer_list;

	}


	public function getList()
	{
		$user_id=Auth::User()->id;

		$userbankoffer=UserBankOffer::where('user_id',$user_id)->with('userDetail')->lists('offer_id');

			$offer_list=Offer::select(array('*',DB::raw('DATEDIFF(CAST(end_date as char), NOW()) AS expires')))->whereNotIn('status',array(2,4))->where('max_redeemar','>',0)->whereIn('id',$userbankoffer)->with('categoryDetails','subCategoryDetails','partnerSettings','companyDetail','myofferDetails')->orderBy('created_at','desc')->get();		
		return $offer_list;	
		

	}
	
	
	public function postCreateoffer(Request $request){
		
		$user=Auth::User();
		$offer_details = $request->offer_details;
		$consumer_deal = $request->consumer_deal;
		$offer_calculation = $request->offer_calculation;
		$product_data = $request->product_data;
		$inventory_data = $request->inventory_data;
		$msg = "";
		
		if($offer_details['offer_description'] == '' || $offer_details['offer_full_description'] == ''
		   || $offer_details['start_date'] == '' || $offer_details['end_date'] == ''
		   ){
			$resp =  array('status'=>'error','msg'=>'Something is wrong please try with valid data');
			return $resp;
			exit;
		}
		
		if($request->offer_id){
			$offer = Offer::find($request->offer_id);
			OfferCategory::where('offer_id',$request->offer_id)->delete();
			OfferProduct::where('offer_id',$request->offer_id)->delete();
			OfferInventory::where('offer_id',$request->offer_id)->delete();
			$msg = 'Your offer has been successfully modified';
			if($request->published == "true"){
			
				$msg = 'Your offer has been successfully created';
			}

		} else {
			$offer = new Offer();
			$msg = 'Your offer has been successfully created';
		}
		
		$offer->created_by = $user->id;
		$offer->cat_id = $user->cat_id;
		$offer->subcat_id = $user->subcat_id;
		$offer->campaign_id = $offer_details['campaign_id'];
		$offer->start_date = date('Y-m-d',strtotime($offer_details['start_date']));
		$offer->end_date = date('Y-m-d',strtotime($offer_details['end_date']));
		$offer->max_redeemar = $offer_details['total_redmeem'];
		$offer->more_information = $offer_details['tags'];
		$offer->offer_description = $offer_details['offer_description'];
		$offer->what_you_get 	  = $offer_details['offer_full_description'];
		$offer->pay_value 		  = $consumer_deal['pay_value'];
		$offer->retails_value	= $offer_calculation['retails_value'];
		$offer->discount 		= $offer_calculation['discount'];
		$offer->value_calculate = $offer_calculation['value_calculate'];
		$offer->value_text = $offer_calculation['value_text'];
		$offer->optional_margin_profit_ratio = $offer_calculation['optional_margin_profit_ratio'];
		$offer->is_only_on_inventory = $offer_calculation['is_only_for_inventory'];
		$offer->validate_after = $offer_details['validated_hour'];
		$offer->zipcode		= $user->zipcode;
		$offer->latitude	= $user->lat;
		$offer->longitude	= $user->lng;
		$offer->published	= $request->published;
		$offer->save();
		
		$offerId = $offer->id;
		
		if($user->subcat_id){
			$secondLevelSubCat = new OfferCategory();
			$secondLevelSubCat->offer_id = $offerId;
			$secondLevelSubCat->cat_id = $user->subcat_id;
			$secondLevelSubCat->save();
		}
		
		if(is_array($offer_details['categories']) && count($offer_details['categories'])){
			foreach($offer_details['categories'] as $cat){
				$offerCat = new OfferCategory();
				$offerCat->offer_id = $offerId;
				$offerCat->cat_id = $cat;
				$offerCat->save();
			}
		}
		
		
		foreach($product_data as $product){
			if(isset($product['productData']) &&
			   isset($product['productData'][0] ) && isset($product['productData'][0]['id'])){
				$offerProduct = new OfferProduct();
				$offerProduct->offer_id 			= $offerId;
				$offerProduct->product_id 			= $product['productData'][0]['id'];
				$offerProduct->product_name			= $product['productData'][0]['product_name'];
				$offerProduct->quantity   			= $product['noofproduct'];
				$offerProduct->cost   				= $product['productData'][0]['cost'];
				$offerProduct->sell_price  			= $product['productData'][0]['sell_price'];
				$offerProduct->retail_price  		= $product['productData'][0]['retail_price'];
				$offerProduct->image_path 			= str_replace('uploads/','filemanager/userfiles/',ltrim($product['productData'][0]['product_image'],'././'));
				$offerProduct->selectedImage 		= $product['selectedImage'];
				$offerProduct->save();
				if($product['selectedImage'] == 1){
					
					
					if(isset($product['productData'][0]['product_image']) && isset($product['productData'][0]['is_file_exist']) && $product['productData'][0]['product_image'] && $product['productData'][0]['is_file_exist']){
						
						$logo=Logo::where('reedemer_id',$user->id)->where('default_logo',1)->first();
						
						$logo_image_path=base_path()."/uploads/original/".$logo->logo_name;
						//$logo_image_path=env("FILE_PATH")."original/Logo_1470660585_908678.png";
						// dd($logo_image_path);
						$newFilename = rand(0,9999). time().'.jpg';
						$offer->offer_image = $newFilename;
						$offer->offer_image_path        = 'filemanager/userfiles/'. $user->id .'/offer_small_image/'. $newFilename;
						$offer->offer_medium_image_path = 'filemanager/userfiles/'. $user->id .'/offer_medium_image/'. $newFilename;
						$offer->offer_large_image_path  = 'filemanager/userfiles/'. $user->id .'/offer_large_image/'. $newFilename;
						
						$offerSelectedImgPath = str_replace('uploads/','filemanager/userfiles/', ltrim($product['productData'][0]['product_image'],'././'));
						
						$uploadSmallSoucePath = base_path().'/'.str_replace('filemanager/userfiles/','uploads/', $offerSelectedImgPath);
						$uploadLargeSoucePath = str_replace('/small_image/','/large_image/', $uploadSmallSoucePath);
						$uploadMediumSoucePath= str_replace('/small_image/','/medium_image/', $uploadSmallSoucePath);
					
						//copy image
						$fileMngrBasePath = rtrim(base_path(),'admin/') . '/filemanager/userfiles/'. $user->id;
						$uploadsBasePath = base_path() . '/uploads/'. $user->id;
						
						if(!is_dir($fileMngrBasePath)){
							helpers::createDir($fileMngrBasePath, 0777);
						}
						
						
						if(!is_dir($fileMngrBasePath.'/offer_small_image/')){ helpers::createDir($fileMngrBasePath.'/offer_small_image/', 0777); }
						if(!is_dir($fileMngrBasePath.'/offer_medium_image/')){ helpers::createDir($fileMngrBasePath.'/offer_medium_image/', 0777); }
						if(!is_dir($fileMngrBasePath.'/offer_large_image/')){ helpers::createDir($fileMngrBasePath.'/offer_large_image/', 0777);}
						if(!is_dir($uploadsBasePath.'/offer_medium_image/')){ helpers::createDir($uploadsBasePath.'/offer_medium_image/', 0777); }
						
						copy($uploadSmallSoucePath, $fileMngrBasePath.'/offer_small_image/'.$newFilename);
						copy($uploadMediumSoucePath, $fileMngrBasePath.'/offer_medium_image/'.$newFilename);
						copy($uploadLargeSoucePath, $fileMngrBasePath.'/offer_large_image/'.$newFilename);
						copy($uploadMediumSoucePath, $uploadsBasePath.'/offer_medium_image/'.$newFilename);
						
						chmod($fileMngrBasePath.'/offer_small_image/'.$newFilename,0777);
						chmod($fileMngrBasePath.'/offer_medium_image/'.$newFilename,0777);
						chmod($fileMngrBasePath.'/offer_large_image/'.$newFilename,0777);
						chmod($uploadsBasePath.'/offer_medium_image/'.$newFilename,0777);
						
						$offer->save();
					}//isset
					
				}//selected img
			}
			
		}
		
		foreach($inventory_data as $inventory){
			if(isset($inventory['InventoryData']) && isset($inventory['InventoryData'][0]) && isset($inventory['InventoryData'][0]['id'])){
				$offerInventory = new OfferInventory();
				$offerInventory->offer_id 			= $offerId;
				$offerInventory->inventory_id 		= $inventory['InventoryData'][0]['id'];
				$offerInventory->inventory_name 	= $inventory['InventoryData'][0]['inventory_name'];
				$offerInventory->quantity 	  		= $inventory['noofinventory'];
				$offerInventory->cost   			= $inventory['InventoryData'][0]['cost'];
				$offerInventory->sell_price  		= $inventory['InventoryData'][0]['sell_price'];
				$offerInventory->retail_price  		= $inventory['InventoryData'][0]['retail_price'];
				$offerInventory->image_path   		= str_replace('uploads/','filemanager/userfiles/',ltrim($inventory['InventoryData'][0]['inventory_image'],'././'));
				$offerInventory->save();
			}
		}
		
		
		$resp =  array('status'=>'success','msg'=>$msg);
		return $resp;
		exit;
		
		
		
	}

	

	public function postImageid(Request $request)
	{
		$user_id=Auth::User()->id;
		//dd($user_id);
		//dd($request->get('file_name'));
		dd($request->all());
		$str=explode("filemanager/userfiles/",$request->get('file_name'));
		$str_value=explode("/",$str[1]);	
		//dd($str_value)	;
		// Get name of image
		$image_name=array_pop($str_value);

		//$last_ele_len=strlen($image_name);
		//$base_dir=substr($str[1], 0, $last_ele_len-4);
		//$base=env('UPLOADS')."/".$base_dir;
		//dd($str[1]);
		$str_wot_name=rtrim(str_replace($image_name, '', $str[1]),"/");
		$base=env('UPLOADS')."/".$str_wot_name;
		dd($base);
		$directory=Directory::where('created_by',$user_id)
		   		   ->where('file_name',$image_name)
		 		   ->where('updated_at',$base)
		 		   ->first();
		$id=$directory->id;
		//dd($id);
		return $id;
	}

	// public function postStoreoffer(Request $request)
	// {		
	// 	//dd($request->all());
	// 	$user_id=Auth::User()->id;

	// 	$campaign_id=$request->get('campaign_id');		
	// 	$offer_description=$request->get('offer_description');
	// 	$total_redeemar=$request->get('total_redeemar');
	// 	$total_redeemar_price=$request->get('total_redeemar_price');
	// 	$c_s_date_user=explode("/",$request->get('c_s_date'));
	// 	$c_s_date=$c_s_date_user[2]."-".$c_s_date_user[0]."-".$c_s_date_user[1];
	// 	$c_e_date_user=explode("/",$request->get('c_e_date'));
	// 	$c_e_date=$c_e_date_user[2]."-".$c_e_date_user[0]."-".$c_e_date_user[1];
		
		
	// 	$total_payment=$request->get('total_payment');
	// 	if($total_payment==0.65)
	// 	{
	// 		$pay=1;
	// 	}
	// 	else
	// 	{
	// 		$pay=2;	
	// 	}
	// 	$what_you_get=$request->get('what_you_get');
	// 	$more_information=$request->get('more_information');
	// 	$created_by=Auth::user()->id;
	// 	$pay_value=$request->get('pay_value');
	// 	$retails_value=$request->get('retails_value');
	// 	$include_product_value=$request->get('include_product_value');
	// 	$discount=$request->get('discount');
	// 	$value_calculate=$request->get('value_calculate');
	// 	$product_id_arr=explode(",",$request->get('product_id_str'));
	// 	$camp_img_id=$request->get('camp_img_id');
	// 	$validate_after=$request->get('validate_after');
	// 	$validate_within=$request->get('validate_within');		
	// 	$choose_image=$request->get('choose_image');

	// 	$logo=Logo::where('reedemer_id',$user_id)->first();
	// 	if($choose_image==1)
	// 	{
	// 		//dd($camp_img_id."A");
	// 		$directory=Directory::find($camp_img_id);

			
	// 		$directory_base_path=$directory->directory_base_path;

	// 		$logo_image_path=env("UPLOADS")."/thumb/".$logo->logo_name;
	// 		$offer_image_old=$directory->file_name;
	// 		$offer_image_path_old=$directory_base_path."/".$offer_image_old;

	// 		$ext = pathinfo($offer_image_path_old, PATHINFO_EXTENSION);

	// 		$desired_width=env("OFFER_IMAGE_SIZE");
	// 		$dest_dir=$directory_base_path."/thumb/";

	// 		if(!file_exists($dest_dir))
	// 		{
	// 			//create base folder
	// 			$rootDir = 
	// 			helpers::createDir($dest_dir, 0777);
	// 		}
	// 		$dest=$dest_dir.$offer_image_old;			
	// 		$thumb_name=$this->create_thumb($offer_image_path_old, $dest, $desired_width);			
	// 		$source_file=$dest;			
	// 		$offer_image_name="offer_".time().rand(99,99999).$user_id.".".$ext;

	// 		$output_file_path="../uploads/offer/".$offer_image_name;	
	// 		//dd($logo_image_path)		
	// 		$this->watermark($source_file, $output_file_path, $logo_image_path);

	// 		$offer_image=$offer_image_name;
	// 		$offer_image_path=env("IMAGE_URL")."uploads/offer/".$offer_image;			
	// 	}
	// 	else
	// 	{
	// 		//dd("B");
	// 		$offer_image=$logo->logo_name;
	// 		$offer_image_path=env("IMAGE_URL")."uploads/original/".$offer_image;			
	// 	}
		
	// 	$user=User::find($logo->reedemer_id);



	// 	// echo $campaign_id."A<br>";
	// 	// echo $offer_description."B<br>";
	// 	// echo $total_redeemar."C<br>";
	// 	// echo $total_redeemar_price."D<br>";
	// 	// //echo $c_s_date_user."E<br>";
	// 	// echo $c_s_date."F<br>";
	// 	// //echo $c_e_date_user."G<br>";
	// 	// echo $c_e_date."H<br>";
		
		
	// 	// echo $total_payment."I<br>";
	// 	// echo $pay."J<br>";
	// 	// echo $what_you_get."K<br>";
	// 	// echo $more_information."L<br>";
	// 	// echo $created_by."M<br>";
	// 	// echo $pay_value."N<br>";
	// 	// echo $retails_value."O<br>";
	// 	// echo $include_product_value."P<br>";
	// 	// echo $discount."Q<br>";
	// 	// echo $value_calculate."R<br>";
	// 	// print_r($product_id_arr)."S<br>";
	// 	// echo $camp_img_id."T<br>";
	// 	// echo $validate_after."U<br>";
	// 	// echo $validate_within."V<br>";
	// 	// echo $choose_image."W<br>";
	// 	// die();
	// 	//dd($user->lat);
	// 	$zipcode = $user->zipcode;
	// 	$latitude =$user->lat;
	// 	$longitude =$user->lng;

	// 	$category_id =$logo->cat_id;
	// 	$subcat_id =$logo->subcat_id;

	// 	//dd($category_id);
	// 	$offer = new Offer();
	// 	$offer->campaign_id				= $campaign_id;			
	// 	$offer->cat_id 					= $category_id;	
	// 	$offer->subcat_id 				= $subcat_id;	
	// 	$offer->offer_description 		= $offer_description;	
	// 	$offer->max_redeemar 			= $total_redeemar;	
	// 	$offer->price 					= $total_redeemar_price;	
	// 	$offer->pay 					= $pay;	
	// 	$offer->start_date 				= $c_s_date;
	// 	$offer->end_date 				= $c_e_date;			
	// 	$offer->what_you_get 			= $what_you_get;		
	// 	$offer->more_information 		= $more_information;
	// 	$offer->pay_value 				= $pay_value;
	// 	$offer->retails_value 			= $retails_value;
	// 	$offer->include_product_value 	= $include_product_value;
	// 	$offer->discount 				= $discount;
	// 	$offer->validate_after 			= $validate_after;
	// 	$offer->validate_within 		= $validate_within;
	// 	$offer->zipcode 				= $zipcode;
	// 	$offer->latitude 				= $latitude;
	// 	$offer->longitude 				= $longitude;
	// 	$offer->value_calculate 		= $value_calculate;
	// 	$offer->offer_image 			= $offer_image;	
	// 	$offer->offer_image_path 		= $offer_image_path;	
	// 	$offer->created_by 				= $created_by;	
	// 	if($offer->save())
	// 	{
	// 		$offer_id = $offer->id;
	// 		//dd($offer_id);
	// 		foreach($product_id_arr as $product_id)
	// 		{
	// 			//dd($product_id);
	// 			$data[] = array('offer_id'=>$offer_id, 'inventory_id'=>$product_id, 'created_at'=>date("Y-m-d H:i:s"), 'updated_at'=>date("Y-m-d H:i:s"));
	// 		}

	// 		OfferDetail::insert($data); // Eloquent

	// 		return 'success';
	// 	}
	// 	else
	// 	{
	// 		return 'error';
	// 	}
	// }

	function watermark($source_file_path, $output_file_path, $stamp,$stampWidth,$stampHeight)
	{
		//$imagePath = $this->resize_image($stamp, 200, 200);
		$tempStampPath = $this->resize_image_l5($stamp,$stampWidth,$stampHeight);
		$watermark_overlay_opacity = 80;
		$watermark_output_quality = 100;
		
		list($source_width, $source_height, $source_type) = getimagesize($source_file_path);
		list($tmp_width, $tmp_height, $tmp_type) = getimagesize($tempStampPath);
	    if ($source_type === NULL) {
	        return false;
	    }
	    switch ($source_type) {
	        case IMAGETYPE_GIF:
	        //dd("A");
	            $source_gd_image = imagecreatefromgif($source_file_path);
	            break;
	        case IMAGETYPE_JPEG:
	        //dd("B");
	            $source_gd_image = imagecreatefromjpeg($source_file_path);
	            break;
	        case IMAGETYPE_PNG:
	        //dd("C");
	            $source_gd_image = imagecreatefrompng($source_file_path);
	            break;
	        default:
	            return false;
	    }
		
	    $overlay_gd_image = imagecreatefrompng($tempStampPath);
		$overlay_width = imagesx($overlay_gd_image);
		$overlay_height = imagesy($overlay_gd_image);
		
		$x_position = (($source_width -($tmp_width)-($source_width*2/100)));
		$y_position = (($source_height*4)/100);
		if($source_width>800){
			$x_position = 10;
			$y_position = 10;
		}
		
		imagecopy(
	        $source_gd_image,
	        $overlay_gd_image,
	        $x_position,  //x position
	        $y_position,  //y position 
	        0,
	        0,
	        $overlay_width,
	        $overlay_height
	    );
		
	    imagejpeg($source_gd_image, $output_file_path, $watermark_output_quality);
	    imagedestroy($source_gd_image);
	    imagedestroy($overlay_gd_image);
	}
	
	function resize_image_l5($file, $width=200, $height=200,$ext='png'){
		
		$extArr = array_reverse(explode('.',$file));
		$fileDest = base_path().'/uploads/tmp/'.rand(0,9999).time().'.'.$extArr[0];
		$newFileDest =  base_path().'/uploads/tmp/'.rand(0,9999).time().rand(0,999).'.png';
		Image::make($file)->resize($width, $height)->save($fileDest);
		
		if(strtolower($extArr[0]) != 'png'){
		 imagepng(imagecreatefromstring(file_get_contents($fileDest)), $newFileDest);
		 $fileDest = $newFileDest;
		}
		chmod($fileDest,0777);
		return $fileDest;
		
	}

	function resize_image($file, $width, $height, $crop=FALSE) {
		
	    $image_properties = getimagesize($file);
		$image_width = $image_properties[0];
		$image_height = $image_properties[1];
		$image_ratio = $image_width / $image_height;
		$type = $image_properties["mime"];
	    $upldLogoPng =  base_path() . '/uploads/tmp/tmp.png';
	    if($type == "image/jpeg") {
			header('Content-type: image/jpeg');
			$thumb = imagecreatefromjpeg($file);
		} elseif($type == "image/png") {
			header('Content-type: image/png');
			$thumb = imagecreatefrompng($file);
		} else {
			return false;
		}

		$temp_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($temp_image, $thumb, 0, 0, 0, 0, $width, $height, $image_width, $image_height);
		
		$thumbnail = imagecreatetruecolor($width, $height);
		imagecopyresampled($thumbnail, $temp_image, 0, 0, 0, 0, $width, $height, $width, $height);

		if($type == "image/jpeg") {
			imagejpeg($thumbnail);
		} else {
			imagepng($thumbnail);
		}

		imagedestroy($temp_image);
		imagedestroy($thumbnail);
		
		return ($upldLogoPng);
	}

	

	public function getFolderid()
	{
		
		$id=Auth::User()->id;

		return $id;
	}

	public function postDefaultlogo()
	{
		$user_id=Auth::User()->id;
		
		$logo = Logo::where('reedemer_id',$user_id)->where('default_logo', 1)->first();
		//dd($logo->logo_name);
		return $logo->logo_name;
	}

	public function postLogodetails()
	{
		$user_id=Auth::User()->id;
		
		$logo=Logo::where('reedemer_id',$user_id)->first();
		//dd($logo->toArray());
		$logo_arr=array(
			'target_id'=>$logo->target_id,
			'original_cat_id'=>$logo->cat_id,
			'cat_id'=>Category::where('id',$logo->cat_id)->first()->cat_name,
			'original_subcat_id'=>$logo->subcat_id,
			'subcat_id'=>$logo->subcat_id >0 ? Category::where('id',$logo->subcat_id)->first()->cat_name:'Not Applicable'
		);
		return $logo_arr;
	}

	public function postSoftdeloffer(Request $request)
	{
		//dd($request[0]);
		$id=$request[0];
		$promotion=Offer::findOrFail($id);
		$promotion->status 	= 4; //Soft Delete			
		//$campaign->created_by 		= $created_by;
		$promotion->save();
		//dd($promotion);


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

		//return $dest;
	}

	public function getCampaignbyuser()
	{
	
		$user_id=Auth::User()->id;
		$campaign=Campaign::where('created_by',$user_id)->get();

		return $campaign;
	}
	
	
	public function getCategories(){
		
		$cat_id=Auth::User()->subcat_id;
		$categories = Category::where('id',$cat_id)->where('status',1)->where('visibility',1)->get();
		
		$cactArr = array();
		$data = $this->catTree($categories,$cactArr);
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
	
	 public function postUpdateproductimage(Request $request){
		if(!$request->product_id || !$request->selected_image ) return 'error';
			$product = Product::find($request->product_id);
			$product->product_image = $request->selected_image;
			if($product->save()){
			return 'success';
			} else {
			return 'error';
		}
	}

	public function postDeleteoffer(Request $request){
		//dd($request->all());
		if(!$request->offer_id) return 'error';
		$promotion=Offer::findOrFail($request->offer_id);
		$promotion->status 	= 4; //Soft Delete			
		//$campaign->created_by 		= $created_by;
		if($promotion->save()){
			return 'success';
		} else {
			return 'error';
		}
	}
	
}
