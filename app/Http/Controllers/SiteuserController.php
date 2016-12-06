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



class SiteuserController extends Controller {


public function __construct( )
	{
//		if($this->middleware('auth'))
//		{
//			Auth::logout();
//    		return redirect('auth/login');	
//    	}
		
	}

	public function getDashboard()
	{
		return view('siteuser.dashboard.index');
	}

}