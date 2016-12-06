<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Illuminate\Http\Response; 
use App\Model\User;
use Hash;
use Validator;
use App\Model\Logo;
use App\Helper\vuforiaclient;
use App\Helper\helpers;
use Auth; 
use Session ;
use App\Model\Campaign;
use App\Model\Offer;
use App\Model\OfferProduct;
use App\Model\OfferInventory;


class CampaignController extends Controller {
	
	

	public function __construct( )
	{
		
	}
	
	public function postList(Request $request)
	{		
		//dd($request[0]);
		// Get current logged in user ID
		$created_by=Auth::user()->id;
		$campaign_arr = [];

		// Get current logged in user TYPE
		$type=Auth::user()->type;
		if($request[0]!="")
		{
			$id=$request[0];
			$campaign=Campaign::where('status',1)
						  ->where('id',$id)						 
						  ->get();	
		}
		else
		{
			if($type==1)
			{
				$campaign=Campaign::where('status',1)
						  ->orderBy('id','DESC')
						  ->get();		
			}
			else
			{
				$campaign=Campaign::where('status',1)
						  ->where('created_by',$created_by)
						  ->orderBy('id','DESC')
						  ->get();		
			}
		}
		foreach($campaign as $camp)
		{
			$offer_deatils=Offer::where('campaign_id',$camp->id)
						   	      ->where('status','!=','4')
						   	      ->orderBy('id','DESC')
						   	      ->get();

			//dd($offer_deatils[0]['offer_description']);
			$campaign_arr[]=array(
				"id" => $camp->id,
			    "campaign_name" => $camp->campaign_name,
			    "campaign_image" => $camp->campaign_image,
			    "start_date" => $camp->start_date,
			    "end_date" => $camp->end_date,
			    "status" => $camp->status,
			    "created_by" => $camp->created_by,
			    "created_at" => $camp->created_at,
			    "updated_at" => $camp->updated_at,
			    "total_offer" => $offer_deatils->count()
			);
		}
		//dd($campaign_arr);
		return $campaign_arr;	
	}

	public function getDelete($id = Null)
	{		
		$campaign = Campaign::find($id);		
		
		// $thubm_path=env('UPLOADS')."/campaign/thumb/";
		// $medium_path=env('UPLOADS')."/campaign/medium/";
		// $original_path=env('UPLOADS')."/campaign/original/";

		// if(file_exists($thubm_path.$campaign->campaign_image))
		// {
		// 	unlink($thubm_path.$campaign->campaign_image);
		// } 
		// if(file_exists($medium_path.$campaign->campaign_image))
		// {
		// 	unlink($medium_path.$campaign->campaign_image);
		// }
		// if(file_exists($original_path.$campaign->campaign_image))
		// {
		// 	unlink($original_path.$campaign->campaign_image);
		// }
		$campaign->delete();
		if($campaign->delete())
		{
			return 'success';
		}	
	}

	public function postUploadlogo(Request $request)
	{		
		$obj = new helpers();
		$folder_name=env('UPLOADS');
		$file_name=$_FILES[ 'file' ][ 'name' ];
		$temp_path = $_FILES[ 'file' ][ 'tmp_name' ];

		

		if (!file_exists($folder_name)) {			
			$create_folder= helpers::createDir($folder_name, 0777);
			$thumb_path= helpers::createDir($folder_name."/campaign/thumb", 0777);
			$medium_path= helpers::createDir($folder_name."/campaign/medium", 0777);
			$original_path= helpers::createDir($folder_name."/campaign/original", 0777);
		}
		else
		{			
			$thumb_path= env('UPLOADS')."/campaign/thumb"."/";
			$medium_path= env('UPLOADS')."/campaign/medium"."/";
			$original_path= env('UPLOADS')."/campaign/original"."/";
		}


		$extension = pathinfo($file_name, PATHINFO_EXTENSION);
		$new_file_name = time()."_".rand(111111111,999999999).'.'.$extension; // renameing image

		$file_ori = $_FILES[ 'file' ][ 'tmp_name' ];
		
		move_uploaded_file($file_ori, "$original_path$new_file_name");
		
		$obj->createThumbnail($original_path,$thumb_path,env('THUMB_SIZE'));
		$obj->createThumbnail($original_path,$medium_path,env('MEDIUM_SIZE'));		
		
		return $new_file_name;

	}

	
	public function postAddlogo(Request $request)
	{

		$campaign_name=$request->input('c_name');
		$start_date=$request->input('c_s_date');
		$end_date=$request->input('c_e_date');
		//$campaign_image=$request->input('campaign_image');
		//dd($request->all());
		//dd("A");
		// Get current logged in user ID
		$created_by=Auth::user()->id;

		if($campaign_name=="" || $start_date=="" || $end_date=="")
		{
			return 'error';
		}
		else
		{			
			$campaign = new Campaign();
			$campaign->campaign_name 	= $campaign_name;			
			$campaign->start_date 		= $start_date;	
			$campaign->end_date 		= $end_date;		
			$campaign->status 			= 1;			
			$campaign->created_by 		= $created_by;
			if($campaign->save())
			{
				return 'success';
			}
			else
			{
				return 'error';
			}			
		}
		
	}

	public function postEditcampaign(Request $request)
	{
		//dd($request[0]['campaign_name']);
		//dd($request->all());
		$campaign = Campaign::find($request[0]['id']);
		//dd($request[0]['campaign_name']);

		$campaign_name=$request[0]['campaign_name'];
		$start_date=$request[0]['start_date'];
		$end_date=$request[0]['end_date'];
		//$updated_at=$request[0]['updated_at'];
		//$campaign_image=$request->input('campaign_image');

		// Get current logged in user ID
		//$created_by=Auth::user()->id;
		if($request[0]['id']=="")
		{
			return 'invalid_id';
		}
		else if($campaign_name=="" || $start_date=="" || $end_date=="")
		{
			return 'error';
		}
		else
		{			
			
			$campaign->campaign_name 	= $campaign_name;			
			$campaign->start_date 		= $start_date;	
			$campaign->end_date 		= $end_date;			
			if($campaign->save())
			{
				return 'success';
			}
			else
			{
				return 'error';
			}			
		}
		
	}

	public function getCampaigndetails($campaign_id)
	{	
		//dd($campaign_id);
		$campaign=Campaign::where('status',1)
					  ->where('id',$campaign_id)						 
					  ->get();			
		return $campaign;	
	}

	public function getShowaddform()
	{
		return view('admin.reedemer.add_campaign');
	}


	public function postAddcampajax(Request $request)
	{
		// $inven = new Inventory();
		// $inven->inventory_name 		= $inventory_name;	
		// $inven->sell_price 		= $sell_price;
		// $inven->cost 		= $cost;	
		// $inven->inventory_image 		= $inventory_image;		
		// $inven->status 			= 1;			
		// $inven->created_by 		= $created_by;
		// $inven->save();
			
			
		// $inventory=Inventory::where('status',1)
		// 		  ->where('created_by',$created_by)	
		// 		  ->orderBy('id','DESC')					 
		// 		  ->get();
		// $select='<option value="">-- choose an option --</option>';
		// foreach($inventory as $inv)
		// {			
		// 	$select.='<option value="'.$inv['id'].'" ng-repeat="inventory in inventory_list" >'.$inv['inventory_name'].'</option>';
		// }        
		// return $select;


		
		$campaign_name=$request->input('c_name');
		$start_date=$request->input('c_s_date');
		$start_date_new = date("m/d/Y", strtotime($start_date));
		//dd($start_date_new);
		$end_date=$request->input('c_e_date');
		$end_date_new = date("m/d/Y", strtotime($end_date));
		
		// Get current logged in user ID
		$created_by=Auth::user()->id;

		if($campaign_name=="" || $start_date=="" || $end_date=="")
		{
			return 'error';
		}
		else
		{			
			$campaign = new Campaign();
			$campaign->campaign_name 	= $campaign_name;			
			$campaign->start_date 		= $start_date;	
			$campaign->end_date 		= $end_date;		
			$campaign->status 			= 1;			
			$campaign->created_by 		= $created_by;
			if($campaign->save())
			{
				$last_id=$campaign->id;
				//return 'success';
				$campaign=Campaign::where('status',1)
						  ->where('created_by',$created_by)	
						  ->orderBy('id','DESC')					 
						  ->get();
				$select='<option value="">--Select Campaign--</option>';
				foreach($campaign as $camp)
				{	
					if($last_id==$camp['id'])		
					{
						$sel="selected";
					}
					else
					{
						$sel="";	
					}
					$select.='<option value="'.$camp['id'].'" '.$sel.' >'.$camp['campaign_name'].'</option>';
				}    

				$all_value=array(
					'select'=>$select,
					'camp_min_date'=>$start_date_new,
					'camp_max_date'=>$end_date_new
				);
				return $all_value;
			}
			else
			{
				return 'error';
			}			
		}
		
	}

	public function postOfferbyid(Request $request){
		//dd($request[0]);
		$user = Auth::user();
		$offer=Offer::where('campaign_id',$request[0])
				->where('status','!=','4')
				->orderBy('id','DESC')
				->with('partnerSettings')
				->get();
		
		if($offer){
			foreach($offer as $key=>$ofr){
				$ofr->location = $user->location;
				$image_path = rtrim(base_path(),'admin'). $ofr->offer_image_path; 
				$ofr->is_file_exist = $ofr->offer_image_path && file_exists($image_path)?true:false;
				
				$ofr->validatedcount = $ofr->myofferDetails()->count();
				
				$ofr->redeepmtcount = $ofr->redemptions()->count();
				
				$offerProducts = OfferProduct::where('offer_id',$ofr->id)
				->get();

				$offerInventory = OfferInventory::where('offer_id',$ofr->id)
				->get();

				$ofr->productDetails = $offerProducts;
				$ofr->inventoryDetails = $offerInventory;
				$logo = Logo::where('action_id',3)->where('particular_id',$ofr->id)->first();

				$ofr->scanCount = 0;
				if($logo){
					$ofr->scanCount = $logo->num_scanned;
				}

			}
		}
				
		return $offer;
	}

}
