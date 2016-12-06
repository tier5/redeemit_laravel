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
use App\Model\Productgroup;
use App\Model\Offer;


class GroupController extends Controller {
	
	

	public function __construct( )
	{
		
	}	

	public function postGrouplist()
	{		
		$created_by=Auth::user()->id;
		$created_by_arr=array($created_by,1);
		$productgroup = Productgroup::where('status',1)
						->whereIn('created_by',$created_by_arr)						
						->orderBy('order','ASC')
						->orderBy('id','DESC')
						->get();
		
		
		return $productgroup;
	}

	public function getShowaddform()
	{
		return view('admin.group.add_group');
	}

	// Add group name if its not exists
	public function postAddgroupajax(Request $request)
	{
		$group_name=$request->get("group_name");		
		$created_by=Auth::user()->id;
		$check_pg=Productgroup::Where('group_name', 'like', '%' . $group_name . '%')
				  ->where('created_by',$created_by)
				  ->get();
		if($check_pg->count() <=0)
		{
			$product_group = new Productgroup();
			$product_group->group_name 	= $group_name;	
			$product_group->status 		= '1';
			$product_group->created_by 	= $created_by;	
		
			if($product_group->save())
			{
				$group_id=$product_group->id;
				$product_group=Productgroup::where('status',1)
						  ->where('created_by',$created_by)	
						  ->orderBy('id','DESC')					 
						  ->get();
				$select='<option value="1">Default Group</option>';
				foreach($product_group as $group)
				{	
					if($group_id==$group['id'])		
					{
						$sel="selected";
					}
					else
					{
						$sel="";	
					}
					$select.='<option value="'.$group['id'].'" '.$sel.' >'.$group['group_name'].'</option>';
				} 
				$ret_arr=array(
							'group_id'=>$group_id,
							'select'=>$select,
							'message'=>'success'
						 );
				return $ret_arr;
			}
			else
			{
				$ret_arr=array(						
							'message'=>'error',
							'message_details'=>'Upable to insert record. Please try again.'
						 );
				return $ret_arr;
			}
		}
		else
		{
			$ret_arr=array(						
							'message'=>'error',
							'message_details'=>'Group name already exists.'
						 );
			return $ret_arr;
		}
	}
}
