<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Illuminate\Http\Response; 
use App\Model\User;
use App\Model\Video;
use Hash;
use Validator;
use App\Model\Logo;
use App\Helper\vuforiaclient;
use App\Helper\helpers;
use Auth; 
use Session ;
use App\Model\Campaign;
use App\Model\Inventory;


class VideoController extends Controller {	
	
	
	public function __construct( )
	{
//		if($this->middleware('auth'))
//		//if(!Auth::user()->id)
//		{
//			Auth::logout();
//    		return redirect('auth/login');	
//    	}
		
	}

	public function postList()
	{	
		// Get current logged in user ID
		$uploaded_by=Auth::user()->id;

		// Get current logged in user TYPE
		$type=Auth::user()->type;		
			
		$video=Video::where('uploaded_by',$uploaded_by)		
					  ->orderBy('id','DESC')				 
					  ->get();	
		
		return $video;	
	}

	public function postStore(Request $request)
	{		
		$video_url=$request->get('video_url');
		$uploaded_by=Auth::user()->id;
		$status=$request->get('status');
		$provider=$request->get('provider');
		$video_name=$request->get('video_name');

		$videoType=$this->videoType($video_url);
		if($videoType=="unknown")
		{
			return 'invalid_video';
			//die();
		}		
		if($provider==1)
		{
			if($videoType!="youtube")
			{
				return 'invalid_url';
			}
			parse_str( parse_url( $video_url, PHP_URL_QUERY ), $my_array_of_vars );
			$video_id=$my_array_of_vars['v']; 
			$video_thumb='http://img.youtube.com/vi/'.$video_id.'/mqdefault.jpg';
		}
		if($provider==2)
		{	
			if($videoType!="vimeo")
			{
				return 'invalid_url';
			}		
			$video_id=(int) substr(parse_url($video_url, PHP_URL_PATH), 1);
			//echo $video_id;
			//exit;
			
			$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$video_id.php"));
			$video_thumb=$hash[0]['thumbnail_medium']; 
			// exit;
		}

		$chk_video=Video::where('uploaded_by',$uploaded_by)->count();
		$default_video=0;
		if($chk_video==0)
		{
			$default_video=1;
		}
		
		 $video = new Video();
		 $video->video_url	= $video_url;			
		 $video->provider	= $provider;	
		 $video->video_id 	= $video_id;	
		 $video->video_name 	= $video_name;
		 $video->video_thumb		= $video_thumb;	
		 $video->uploaded_by 	= $uploaded_by;	
		 $video->default_video 	= $default_video;	
		 $video->status 	= 1;	
		
		 if($video->save())
		 {
		 	return 'success';
		 }
		 else
		 {
		 	return 'error';	
		 }
	}

	function videoType($url) {
		if (strpos($url, 'youtube') > 0) 
		{
			return 'youtube';
		} 
		elseif (strpos($url, 'vimeo') > 0) 
		{
			return 'vimeo';
		} 
		else 
		{
			return 'unknown';
		}
	}

	public function getMainvideo($id)
	{		
		$uploaded_by=Auth::user()->id;

		Video::where('uploaded_by', $uploaded_by)->update(array('default_video' => 0));

		$video = Video::find($id);
		$video->default_video 	= 1;
		if($video->save())
		{		
			return 'success';
		}
		else
		{
				return 'success'; //teamtreehouse	
		}
			
	}

	public function getDelete($id)
	{		
		$video = Video::find($id);
		
		$video->delete();
		if($video->delete())
		{
			return 'success';
		}	
	}


	
}
