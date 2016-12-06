<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Illuminate\Http\Response; 
use App\Model\User;
use App\Model\Partnersetting;
use App\Model\Price;
use Hash;
use Validator;
use App\Model\Logo;
use App\Helper\vuforiaclient;
use App\Helper\helpers;
use Auth; 
use Session ;
use App\Model\Campaign;
use App\Model\Inventory;


class PartnersettingController extends Controller {
	
	
	//public function __construct( )
	//{
		//$this->middleware('auth');
		//Auth::logout();
    	//return redirect('auth/login');	
		
	//}


	public function postList()
	{	
		//dd("a")	;
		//dd($request[0]);
		// Get current logged in user ID
		$created_by=Auth::user()->id;
		//dd($created_by);
		// Get current logged in user TYPE
		//$type=Auth::user()->type;
		//$setting=Partnersetting::range;
		$setting = Partnersetting::where('created_by',$created_by)->get();
		//$setting = Partnersetting::find(1)->range;
		//dd($setting->toArray());
		return $setting;	
	}

	public function postUpdate(Request $request)
	{	
		//dd($request->all());
		// Get current logged in user ID
		$created_by=Auth::user()->id;
		
		// if($request[0]['update_id']!=$created_by)
		// {
		// 	return 'id_not_match';	
		// }
		if($request[0]['price_range_id']=="")
		{
			return 'error';
		}
		$partnersetting = Partnersetting::find($request[0]['update_id']);
		$partnersetting->price_range_id 	= $request[0]['price_range_id'];				
		if($partnersetting->save())
		{
			return 'success';
		}
		else
		{
			return 'error';
		}
	}

	public function getAllrange($id='')
	{	
		$price=Price::where('status',1)->get();
		//dd($price->toArray());
		return $price;
	}

	
}
