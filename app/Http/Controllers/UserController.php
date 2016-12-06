<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Illuminate\Http\Response; 
use App\Model\User;
use Hash;
use Validator;
use App\Model\Logo;
use App\Model\Category;
use App\Model\Action;
use App\Helper\vuforiaclient;
use App\Helper\helpers;
use Auth; 
use Session ;


class UserController extends Controller {

	
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

	
	/**
	 * add user to the system.
	 *
	 * @return Response
	*/
	
	

	public function getAdd($id = Null)
	{
		
		return view('user.add');
	}

	// Register user as reedemer
	public function postStore(Request $request)
	{	
	
		$rules = array(
				'company_name'     => 'required',  
				'email'            => 'required|email|unique:users',   
				'password'         => 'required|min:6'

		);	
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {				
			$messages = $validator->messages();
			// redirect our user back to the form with the errors from the validator			
			return redirect()->back()
							 ->withInput($request->only('company_name'))
							 ->withErrors($validator);
		} else {
			// create the data for our user
			$user = new User();
			$user->company_name 		= $request->input('company_name');			
			$user->type 		= 2;			
			$user->approve 		= 0;
			$user->email 		= $request->input('email');
			$user->password = bcrypt($request->input('password'));
			$user->save();
				
			
			$request->session()->flash('alert-success', 'User has been created successfully');			
			return redirect('user/add');		
			exit;	
		}
	}

	

	public function getStatusupdate($id)
	{
		dd($id);
		//$user = new User();
		//$user->status=1;
		//$user->save();
		//return $id;
	}

	public function getDash()
	{
		dd("a");
		//$user = new User();
		//$user->status=1;
		//$user->save();
		//return $id;
	}

	public function getDashboard()
	{
		return view('user.dashboard.index');
	}

	public function getLogo()
	{	
		$id=Auth::user()->id;
		dd($id);	
		$logo_details = Logo::where('reedemer_id',41)
						->orderBy('id','DESC')
						->get();
		//dd("d");
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
						'uploaded_by'=>41,
						'created_at'=>$logo_details['created_at'],
						'updated_at'=>$logo_details['updated_at'],
					  );
		}
		$logo_json=json_encode($logo_arr);
		
		return $logo_json;	
	}

	public function getShow()
	{
		$id=Auth::User()->id;
		//dd($id);
		$user=User::where('id',$id)->get();
		if($user[0]['cat_id'] != '')
		{
			//$id=$request[0];
			$category = Category::where('parent_id',$user[0]['cat_id'])
						->where('visibility',1)
						->get();
			$catdetails = Category::where('id',$user[0]['cat_id'])
						->where('visibility',1)
						->get();
		}
		else
		{
			$category = 'subcat_not_exists';
		}
		$user[0]['subcat_list'] = $category;
		$user[0]['cat_name'] = $catdetails[0]['cat_name'];

		return $user;
	}

	public function getChangeaction($logo_id,$action_id)
	{

		$user_id=Auth::User()->id;
		$user=User::find($user_id);
		$user_level=$user->membership_level;
		$logo=Logo::where('reedemer_id',$user_id)->first();
		$action_id=$logo->action_id;
		$action_list=Action::where('status',1)
					->where('min_level','<=',$user_level)
					->orderBy('action_name','ASC')
					->get();

		$array=array(
			'success'=>true,
			'action_list'=>$action_list,
			'action_id'=>$action_id,
			'logo_id'=>$logo_id
		);
		return $array;
	}

	public function postUpdateaction(Request $request)
	{
		// dd($request->all());
		$action_id=$request->input('action_id');
		$logo_id=$request->input('logo_id');
		if($action_id==2)
		{
			$particular_id=$request->input('offer_id'); //It is basically a campoaign id
		}
		else if($action_id==3)
		{
			$particular_id=$request->input('offer_id');
		}
		else 
		{
			$particular_id='';
		}
		
		$logo=Logo::find($logo_id);
		$logo->action_id= $action_id;			
		$logo->particular_id= $particular_id;	
		if($logo->save())
		{
			$action=Action::find($action_id);
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
	
	
    public function getUserinfo(){
		$userData = array();
		if($user=Auth::User()){
		//you can add index for which data you need
		$userData['location'] = $user->location;
		$userData['lat'] = $user->lat;
		$userData['lng'] = $user->lng;
		$userData['address'] = $user->address;
		return $userData;
		}
	}

	public function postUpdatedata(Request $request){
		// dd($request->all());
		$zipcode=$request->zipcode;
		$location_arr=$this->get_lat_lng($zipcode);
		
		$lat=$location_arr['lat'];
		$lng=$location_arr['lng'];
		$user=User::find($request->id);
		if($user){
			$user->email 		 = $request->email;
			$user->cat_id        = $request->cat_id;
			$user->subcat_id     = $request->subcat_id;
			$user->company_name  = $request->company_name;
			$user->first_name    = $request->first_name;
			$user->last_name     = $request->last_name;
			$user->address       = $request->address;
			$user->city			 = $request->city;
			$user->state		 = $request->state;
			$user->zipcode       = $zipcode;
			$user->lat           = $lat;
			$user->lng			 = $lng;
			$user->mobile		 = $request->mobile;
			$user->web_address	 = $request->web_address;
			$user->brand_details = $request->brand_details;

			if($request->changepssData){
				if(\Hash::check($request->changepssData['old_pass'],$user->password)){
					$user->password = bcrypt($request->changepssData['new_pass']);
				} else {
					return 'error';
				}
			}

			if($user->save()){
				return 'success';
			} else {
				return 'error';
			}
		}


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
}
