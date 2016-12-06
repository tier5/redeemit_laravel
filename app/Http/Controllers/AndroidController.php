<?php namespace App\Http\Controllers;
use Auth;
//use App\Model\Logo;
use App\Model\Wptoken;
use App\Model\Demotest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Illuminate\Http\Response; 
use Redirect;
use Input;
use Session;
use App\Helper\vuforiaclient;
use App\Model\User;
use App\Model\Logo;
use App\Model\Category;

class AndroidController extends Controller {

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
	
	

}
