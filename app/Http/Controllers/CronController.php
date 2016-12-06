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


class CronController extends Controller {	
	

	public function getUpdaterating($id = Null)
	{		
		//dd("A");
		$logo=Logo::where('tracking_rating','<',0)->get();
		$logo_details=json_decode($logo);
		//dd($logo->toArray());
		$client = new vuforiaclient();
		foreach($logo_details as $logo)
		{
			$target_res_details=$client->getTarget($logo->target_id); 
			$response_arr=json_decode($target_res_details);
			//echo $logo->target_id."<br>";
			//dd($response_arr->target_record->tracking_rating);

			if(isset($response_arr->target_record)) {			
				$target_id=$response_arr->target_record->target_id;
				$tracking_rating=$response_arr->target_record->tracking_rating;
				$affectedRows = Logo::where('target_id', $target_id)->update(['tracking_rating' => $tracking_rating, 'status' => 1]);
			}

		}
		//return $logo;

		//getVuforiarate
	}

	
}
