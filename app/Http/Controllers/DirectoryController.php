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
use File; 
use Session ;
use App\Model\Directory;
use App\Model\StoreImage;


class DirectoryController extends Controller {
	
	

	public function __construct( )
	{
		
	}
	
	// public function postShow()
	// {
	// 	$id=Auth::user()->id;
	// 	// Get current logged in user TYPE
	// 	$type=Auth::user()->type;
	// 	if($type==1)
	// 	{
	// 		$directory = Directory::where('directory_id',0)
	// 					 ->orderBy('id','DESC')
	// 					 ->get();
	// 	}
	// 	else
	// 	{
	// 		$directory = Directory::where('directory_id',0)
	// 					 ->where('created_by',$id)
	// 					 ->orderBy('id','DESC')
	// 					 ->get();
	// 	}
	// 	return $directory;
	// }

	// public function getAlldirectory()
	// {
	// 	$created_by=Auth::user()->id;
	// 	$directory = Directory::where('status',1)
	// 				 ->where('created_by',$created_by)
	// 				 ->where('directory_id',0)
	// 				 ->orderBy('id','DESC')
	// 				 ->get();
	// 	return $directory;
	// }

	public function getDirectorylist()
	{
		$created_by=Auth::user()->id;

		$directory = Directory::where('status',1)
					 ->where('created_by',$created_by)
					 ->orderBy('id','DESC')
					 ->get();

		return $directory;
	}

	public function getOnlydirectorylist()
	{
		$created_by=Auth::user()->id;

		$directory = Directory::where('status',1)
					 ->where('directory',1)
					 ->where('created_by',$created_by)
					 ->orderBy('id','DESC')
					 ->get();

		return $directory;
	}

	public function getAlllisting($id = 0)
	{
		$dir_arr=[];
		$created_by=Auth::user()->id;
		if($id != "0" || $id != 0){
			$directory = Directory::where('status',1)					 
					 ->where('directory_id',$id)
					 ->where('created_by',$created_by)
					 ->orderBy('id','DESC')
					 ->get();
		} else {
			$directory = Directory::where('status',1)
					 ->where('created_by',$created_by)
					 ->orderBy('id','DESC')
					 ->get();
		}
		

		if($id != "0" || $id != 0)
		{
			$di = Directory::where('id',$id)
					->get();
			$di['directory_id'] = $di[0]->directory_id;
		}
		else
		{
			$di['directory_id'] = null;
		}
		
		if($directory->count() >0)
		{
			
			foreach($directory as $dir)
			{
				$img_path = ltrim($dir['directory_base_path'],'./');
				
				$dimension = '0x0';
				if($img_path && file_exists(base_path($img_path))){
					try{
					$imgSize = getimagesize(base_path($img_path));
					$dimension = $imgSize[0] . 'x' . $imgSize[1];
					} catch(\Exception $e){
						$dimension = '0x0';
					}
				}
				
				
				$dir_arr[]=array(
					'id' => $dir['id'],
					'directory_id' => $dir['directory_id'],
					'original_name' => $dir['original_name'],
					'file_name' => $dir['file_name'],
					'directory_base_path' => $dir['directory_base_path'],
					'directory_url' => $dir['directory_url'],
					'directory' => $dir['directory'],
					'img_dimension' => $dimension,
					'status' => $dir['status'],
					'created_by' => $dir['created_by'],
					'created_at' => $dir['created_at'],
					'updated_at' => $dir['updated_at'],
					'previous_id' => $di['directory_id'],
					'is_dir'    => is_dir(base_path($img_path)),
				);
			}
		}
		
		return $dir_arr;
	}
	
	public function getRepos($id = 0){
	
		$dir_arr=[];
		$created_by=Auth::user()->id;
		if($id != "0" || $id != 0){
			$directory = Directory::where('status',1)					 
					 ->where('directory_id',$id)
					 ->where('created_by',$created_by)
					 ->where('directory',0)
					 ->orderBy('id','DESC')
					 ->get();
		} else {
			$directory = Directory::where('status',1)
					 ->where('created_by',$created_by)
					 ->where('directory_id',0)
					 ->where('directory',0)
					 ->orderBy('id','DESC')
					 ->get();
		}
		

		if($id != "0" || $id != 0)
		{
			$di = Directory::where('id',$id)
					->get();
			$di['directory_id'] = $di[0]->directory_id;
		}
		else
		{
			$di['directory_id'] = null;
		}
		
		if($directory->count() >0)
		{
			
			foreach($directory as $dir)
			{
				$img_path = ltrim($dir['directory_base_path'],'./');
				
				$dimension = '0x0';
				if($img_path && file_exists(base_path($img_path))){
					try{
					$imgSize = getimagesize(base_path($img_path));
					$dimension = $imgSize[0] . 'x' . $imgSize[1];
					} catch(\Exception $e){
						$dimension = '0x0';
					}
				}
				
				
				$dir_arr[]=array(
					'id' => $dir['id'],
					'directory_id' => $dir['directory_id'],
					'original_name' => $dir['original_name'],
					'file_name' => $dir['file_name'],
					'directory_base_path' => $dir['directory_base_path'],
					'directory_url' => $dir['directory_url'],
					'directory' => $dir['directory'],
					'img_dimension' => $dimension,
					'status' => $dir['status'],
					'created_by' => $dir['created_by'],
					'created_at' => $dir['created_at'],
					'updated_at' => $dir['updated_at'],
					'previous_id' => $di['directory_id'],
					'is_dir'    => is_dir(base_path($img_path)),
					'is_file_exist' => file_exists(base_path($img_path))
				);
			}
		}
		
		return $dir_arr;
	

	}

	public function postStore(Request $request)
	{
		
		$created_by=Auth::user()->id;
		$upload_dir = env('UPLOADS');

		//uploads folder url
		$base_dir=$upload_dir."/".$created_by;
		
		
		//Check if dir not exists create
		if(!is_dir($base_dir))
		{
			//create base folder
			helpers::createDir($base_dir);
		}

		//Filemanager Url	
		$dest_dir="../../filemanager/userfiles/".$created_by;
		//Check if copy dir not exists create
		if(!file_exists($dest_dir))
		{
			//create base folder
			//helpers::createDir($dest_dir, 0777);
			helpers::createDir($dest_dir);
		}

		//Directory name given by user
		$directory_name=$request->get('dir_name');
		//$directory_url=url()."/".$directory_name;
		$directory_path=$base_dir."/".$directory_name;
		$copy_directory_path=$dest_dir."/".$directory_name;

		//Check if dir not exists create
		if(!file_exists($directory_path) && !file_exists($copy_directory_path))
		{
			//create base folder
			//helpers::createDir($directory_path, 0777);
			helpers::createDir($directory_path);
			//helpers::createDir($copy_directory_path, 0777);
			helpers::createDir($copy_directory_path);
			
			$directory_id = 0;
			$status=1;	
			

			$directory = new Directory();
			$directory->directory_id 		= $directory_id;			
			$directory->original_name 		= $directory_name;	
			$directory->file_name 			= $directory_name;				
			$directory->directory_base_path = $base_dir."/".$directory_name;	
			$directory->directory_url 		= $directory_path;
			$directory->copy_url 			= $copy_directory_path;
			$directory->directory 			= 1;
			$directory->status 				= $status;
			$directory->created_by 			= $created_by;

			if($directory->save())
			{
				return 'success';
			}
			else
			{
				return 'error';
			}
		} else {
			return 'folder_exists';
		}
		
	}

	//public function getDelete($id = Null)
	//{			
	//	$directory = Directory::find($id);	
	//
	//	//dd($id);
	//
	//	if($directory->directory==0)
	//	{
	//		//dd($directory->copy_url);
	//		//$get_explode=explode("/",$directory->copy_url);
	//		//dd($get_explode[6]);
	//		//dd($directory->directory_url);
	//		//dd(count($get_explode));
	//		$file_name=$directory->file_name;
	//		$file_path=$directory->directory_base_path."/".$directory->file_name;
	//		$copy_file_path="../../filemanager/userfiles/".$directory->created_by."/".$directory->file_name;
	//
	//		if(file_exists($file_path))
	//		{
	//			@unlink($file_path);
	//		}
	//		if(file_exists($copy_file_path))
	//		{
	//			@unlink($copy_file_path);
	//			//echo "BBB";
	//		}
	//	}
	//	else
	//	{
	//		
	//		$file_name=$directory->file_name;
	//		$file_path=$directory->directory_base_path;
	//		//dd($file_path);
	//		//$copy_file_path="../../filemanager/userfiles/".$directory->created_by."/".$directory->file_name;
	//
	//		//dd($file_path);
	//		$this->removeDirectory($file_path);
	//		//$this->removeDirectory($file_path);
	//	}
	//	//dd("c");
	//	//dd($directory->directory_base_path);	
	//	//dd($file_path->directory);	
	//	//dd($directory->directory_base_path);
	//	//dd($directory->directory);
	//	// $thubm_path=env('UPLOADS')."/inventory/thumb/";
	//	// $medium_path=env('UPLOADS')."/inventory/medium/";
	//	// $original_path=env('UPLOADS')."/inventory/original/";
	//
	//	// if(file_exists($thubm_path.$inventory->campaign_image))
	//	// {
	//	// 	@unlink($thubm_path.$inventory->campaign_image);
	//	// } 
	//	// if(file_exists($medium_path.$inventory->campaign_image))
	//	// {
	//	// 	@unlink($medium_path.$inventory->campaign_image);
	//	// }
	//	//======================
	//	 // if($directory->directory==1)
	//	 // {
	//		// $file=$directory->directory_base_path."/";
	//		// File::deleteDirectory($file);
	//	 // }
	//	 // else
	//	 // {
	//	 // 	$file=$directory->directory_base_path."/".$directory->file_name;
	//	 // 	if(file_exists($file))
	//		// {
	//		// 	unlink($file);
	//		// }
	//	 // }
	//	 //============================
	//	 
	//	 //dd($file);
	//	
	//
	//	// if($directory->directory==1)
	//	// {
	//	// 	if (is_dir($directory->directory_base_path)) 
	//	// 	{
	//	// 		rmdir($directory->directory_base_path);
	//	// 	}
	//	// }
	//	//unlink();
	//	//$directory->delete();
	//	if($directory->delete())
	//	{
	//		return 'success';
	//	}		
	//	else{
	//		return 'error';
	//	}		
	//}
	public function removeDirectory($path){
		
		$files = glob($path . '/*');
		//dd($files);
		foreach ($files as $file) {
			is_dir($file) ? $this->removeDirectory($file) : unlink($file);
		}
		if(is_dir($path)){
			rmdir($path);
			return 1;
		}else{
			return 0;
		}
	}
	
	public function getDelete($id = Null){
		
		$directory = Directory::find($id);	
		if(!$directory->directory) return 'Not a dir';
		
		$upldBasePath = base_path('uploads/' . $directory['created_by'] . '/' . $directory['original_name']);
		$this->removeDirectory($upldBasePath);
		$filMngrBasePath = rtrim(base_path(),'admin/') . '/filemanager/userfiles/' . $directory['created_by'] . '/' . $directory['original_name'];
		$this->removeDirectory($filMngrBasePath);
		
		// delete all child rec
		$childRepoDlt = Directory::where('directory_id',$id)->delete();
		$directory->delete();
		return 'success'; exit;
		
	}
	
	
	public function getDeleterepo($id){
		
		if(!$id) return 0; 
	
		$repo = Directory::find($id);
		
		
		$dirName  = '';
		
		if($repo['directory_id']){
			$dirRepo = Directory::find($repo['directory_id']);
			$dirName = $dirRepo['original_name'] .'/';
		}
		
		$upldBasePath = base_path('uploads/' . $repo['created_by'] . '/' . $dirName);
		$filMngrBasePath = rtrim(base_path(),'admin/') . '/filemanager/userfiles/' . $repo['created_by'] . '/' . $dirName;
			
		//delete from upload dir
		$upldDealImg = $upldBasePath.'large_image/'.$repo['original_name'];
		
		
		if($repo['original_name'] && file_exists($upldDealImg)){
		
			unlink($upldDealImg);
			
		}
		
		$upldDealThmImg = $upldBasePath.'small_image/'.$repo['original_name'];
		if($repo['original_name'] && file_exists($upldDealThmImg)){
			unlink($upldDealThmImg);
			
		}

		$upldDealThmImg = $upldBasePath.'medium_image/'.$repo['original_name'];
		if($repo['original_name'] && file_exists($upldDealThmImg)){
			unlink($upldDealThmImg);
			
		}


		
	 
		
		//filemanager from upload dir
		$fileMngrDealImg = $filMngrBasePath.'large_image/'.$repo['original_name'];
		if($repo['original_name'] && file_exists($fileMngrDealImg)){
			unlink($fileMngrDealImg);
			
		}

		$fileMngrDealThmImg = $filMngrBasePath.'medium_image/'.$repo['original_name'];
		if($repo['original_name'] && file_exists($fileMngrDealThmImg)){
			unlink($fileMngrDealThmImg);
			
		}
		
		$fileMngrDealThmImg = $filMngrBasePath.'small_image/'.$repo['original_name'];
		if($repo['original_name'] && file_exists($fileMngrDealThmImg)){
			unlink($fileMngrDealThmImg);
			
		}
		
		
		
		$repo->delete();
		echo 'success';
		exit;
		
	}
	
	public function postRenamedir(Request $request){
		if(!$request->id || !$request->new_name) return 0;
		$oldname = '';
		$dir = Directory::find($request->id);
		if($dir){
			$oldname = $dir->original_name;
			$dir->file_name = $dir->original_name = $request->new_name;
			$dir->directory_url = '../uploads/' . $dir['created_by'] . '/' . $request->new_name;
			$dir->directory_base_path = '../uploads/' . $dir['created_by'] . '/' . $request->new_name;
			$dir->copy_url = '../../filemanager/userfiles/'. $dir['created_by'] . '/' . $request->new_name;
			$dir->save();
			
			//update image path for each child rec
			$childRepo = Directory::where('directory_id',$request->id)->get();
			foreach($childRepo as $repo){
				$repo->directory_base_path = '../uploads/' . $dir['created_by'] . '/' . $dir->original_name .'/small_image/'. $repo->original_name;
				$repo->copy_url = '../../filemanager/userfiles/' . $dir['created_by'] . '/' . $dir->original_name .'/small_image/'. $repo->original_name;
				$repo->save();
			}
			
			$upldBasePath = base_path('uploads/' . $dir['created_by'] . '/' );
			$filMngrBasePath = rtrim(base_path(),'admin/') . '/filemanager/userfiles/' . $dir['created_by'] . '/' ;
			
			rename($upldBasePath.$oldname,$upldBasePath.$dir->original_name);
			rename($filMngrBasePath.$oldname,$filMngrBasePath.$dir->original_name);
			
			chmod($upldBasePath.$dir->original_name,0777);
			chmod($filMngrBasePath.$dir->original_name,0777);
			
		}
		
		echo $oldname;
		exit;
	}
	
	public function postChangeaddress(Request $request){
		$newDirId = $request->dirid;
		$repoId = $request->repoid;
		
		$repo = Directory::find($repoId);
		$oldDirName = '';
		
		if($repo->directory_id == $newDirId){
			return 'Same directory';
			exit;
		}
		
		if($repo->directory_id){
			$oldDir = Directory::find($repo->directory_id);
			$oldDirName = $oldDir->original_name .'/';
		}
		$newdirName = '';
		if($newDirId){
			$newdir = Directory::find($newDirId);
			$newdirName = $newdir->original_name .'/';
		}
		
		
		
		//move file of uploads dir
		$oldupldBasePath = base_path('uploads/' . $repo['created_by'] .'/' .$oldDirName );
		$newupldBasePath = base_path('uploads/' . $repo['created_by'] .'/' .$newdirName );
		
		$oldFileMngrBasePath = rtrim(base_path(),'admin/') . '/filemanager/userfiles/'. $repo['created_by'] .'/' .$oldDirName ;
		$newFileMngrBasePath = rtrim(base_path(),'admin/') . '/filemanager/userfiles/' . $repo['created_by'] .'/' .$newdirName ;
		
		$repo->directory_base_path = '../uploads/' .$repo['created_by']. '/' .$newdirName. 'small_image/' . $repo->original_name;
		$repo->directory_id = $newDirId;
		$repo->copy_url = '../../filemanager/userfiles/' .$repo['created_by']. '/' .$newdirName. 'small_image/' . $repo->original_name;
		$repo->save();
		
		if(file_exists($oldupldBasePath.'small_image/' . $repo->original_name)){
			
			if(!is_dir($newupldBasePath.'small_image')){
				helpers::createDir($newupldBasePath.'small_image');
			}
			copy( $oldupldBasePath.'small_image/' . $repo->original_name, $newupldBasePath.'small_image/' . $repo->original_name);
			chmod($newupldBasePath.'small_image/' . $repo->original_name,0777);
			unlink($oldupldBasePath.'small_image/' . $repo->original_name);
		}

		if(file_exists($oldupldBasePath.'medium_image/' . $repo->original_name)){
			if(!is_dir($newupldBasePath.'medium_image')){
				
				helpers::createDir($newupldBasePath.'medium_image');
			}
			copy($oldupldBasePath.'medium_image/' . $repo->original_name, $newupldBasePath.'medium_image/' . $repo->original_name);
			chmod($newupldBasePath.'medium_image/' . $repo->original_name,0777);
			unlink($oldupldBasePath.'medium_image/' . $repo->original_name);
		}
		
		if(file_exists($oldupldBasePath.'large_image/' . $repo->original_name)){
			if(!is_dir($newupldBasePath.'large_image')){
				
				helpers::createDir($newupldBasePath.'large_image');
			}
			copy($oldupldBasePath.'large_image/' . $repo->original_name, $newupldBasePath.'large_image/' . $repo->original_name);
			chmod($newupldBasePath.'large_image/' . $repo->original_name,0777);
			unlink($oldupldBasePath.'large_image/' . $repo->original_name);
		}
		
		
		
		//move file for filemanager dir
		if(file_exists($oldFileMngrBasePath.'small_image/' . $repo->original_name)){
			if(!is_dir($newFileMngrBasePath.'small_image')){
				helpers::createDir($newFileMngrBasePath.'small_image');
			}
			copy($oldFileMngrBasePath.'small_image/' . $repo->original_name, $newFileMngrBasePath.'small_image/' . $repo->original_name);
			chmod($newFileMngrBasePath.'small_image/' . $repo->original_name,0777);
			unlink($oldFileMngrBasePath.'small_image/' . $repo->original_name);
		}
		if(file_exists($oldFileMngrBasePath.'large_image/' . $repo->original_name)){
			if(!is_dir($newFileMngrBasePath.'large_image')){
				helpers::createDir($newFileMngrBasePath.'large_image');
			}
			copy($oldFileMngrBasePath.'large_image/' . $repo->original_name, $newFileMngrBasePath.'large_image/' . $repo->original_name);
			chmod($newFileMngrBasePath.'large_image/' . $repo->original_name,0777);
			unlink($oldFileMngrBasePath.'large_image/' . $repo->original_name);
		}
		
		
		return 'success'; exit;
	}

	public function postUpload(Request $request)
	{			

		//// dd($request->all())	;
		//dd($request->input('home_thumb_img'));
		//dd($request->input('deal_details_img'));
		//dd($request->input('deal_details_thumb_img'));
		$created_by=Auth::user()->id;
		$upload_dir = env('UPLOADS');
		$copy_base_path=env('REPOSITORY_IMAGE_COPY').$created_by;
		$upload_path=$upload_dir."/".$created_by;
		
		if(!file_exists($upload_path))
		{
			//create base folder
			
			helpers::createDir($upload_path, 0777);
		}	
		if(!file_exists($copy_base_path))
		{
			//create base folder
			helpers::createDir($copy_base_path, 0777);
		}	
		//dd("check");	
		
		if($request->dir_id==0)
		{
			$dir_id=$request->dir_id;
			$base_dir=$upload_dir."/".$created_by;
			//Copy same into filemanager			
			$copy_dir=$copy_base_path;
		}
		else
		{
			$dir_id=$request->dir_id;
			$directory=Directory::find($dir_id);
			$base_dir=$directory->directory_base_path;
			$dir_extract=explode("../uploads/",$base_dir);
			$extra_dir=$dir_extract[1];			
			$copy_dir=env('REPOSITORY_IMAGE_COPY').$extra_dir;
		}	

		$folder_1="small_image";
		$folder_2="medium_image";
		$folder_3="large_image";

		// Folder path to admin
		$folder_path_1=$base_dir."/".$folder_1;
		$folder_path_2=$base_dir."/".$folder_2;
		$folder_path_3=$base_dir."/".$folder_3;

		// Folder path to filemanager
		$filemanager_folder_path_1=$copy_dir."/".$folder_1;
		$filemanager_folder_path_2=$copy_dir."/".$folder_2;
		$filemanager_folder_path_3=$copy_dir."/".$folder_3;

		//dd($copy_dir);
		// Making folder to admin
		$this->make_folder($folder_path_1);
		$this->make_folder($folder_path_2);
		$this->make_folder($folder_path_3);

		// Makeing folder to filemanager
		$this->make_folder($filemanager_folder_path_1);
		$this->make_folder($filemanager_folder_path_2);
		$this->make_folder($filemanager_folder_path_3);
		
		//dd($base_dir);
		$image_type="png";
		//dd("V");
		//dd($image_type);
		//Get desire image name by user
		$upload_image_name=$request->image_name.".jpg";		

		$small_image_path=$folder_path_1."/".$upload_image_name;
		$small_image_path_copy=$filemanager_folder_path_1."/".$upload_image_name;

		$medium_image_thumb_path=$folder_path_2."/".$upload_image_name;
		$medium_image_thumb_path_copy=$filemanager_folder_path_2."/".$upload_image_name;
		
		$large_image_thumb_path=$folder_path_3."/".$upload_image_name;
		$large_image_thumb_path_copy=$filemanager_folder_path_3."/".$upload_image_name;
		
		//$request->input('home_thumb_img')

		//$upload_img_url=$base_dir."/".$upload_image_name;
		//$copy_img_url=$copy_dir."/".$upload_image_name;

		//dd($request->all());
		//dd($request->input('home_thumb_img'));
		//dd($request->input('deal_details_img'));
		//dd($request->input('deal_details_thumb_img'));
		//$base64_to_jpeg=$this->base64_to_jpeg($request->image_data,$upload_img_url);
		//$upload_to_filemanager=$this->base64_to_jpeg($request->image_data,$copy_img_url);

		$obj = new helpers();
		//Actually uploadingimage
		$small_src=$request->input('small_image');
		$medium_src=$request->input('medium_image');
		$large_src=$request->input('large_image');
		//dd($thumb_page);
		//$original_path=$upload_img_url;
		//$medium_path=$original_path;
		//dd($original_path);
		////$medium=$obj->create_jpg_from_png($src, $original_path);
		//$medium=$obj->create_thumb($src, $original_path, '1000');
		//$small=$obj->create_thumb($thumb_page, $original_path, '1000');		
		//$original=$obj->base64_to_jpeg($thumb_page, $original_path);
		//$src=$original_path;
		//dd("a");
		//$small=$obj->create_thumb($src, $thumb_path, $thumb_size);	
		//dd($src);
		//dd("a");
		//if($image_type=="png")
		//{
			$small_page_img=$obj->convertImage($small_src, $small_image_path, $image_type);
			$small_page_img_copy=$obj->convertImage($small_src, $small_image_path_copy, $image_type);

			$medium_page_img_thumb=$obj->convertImage($medium_src, $medium_image_thumb_path, $image_type);
			$medium_page_img_copy_thumb=$obj->convertImage($medium_src, $medium_image_thumb_path_copy, $image_type);

			$large_page_img_thumb=$obj->convertImage($large_src, $large_image_thumb_path, $image_type);
			$large_page_img_copy_thumb=$obj->convertImage($large_src, $large_image_thumb_path_copy, $image_type);

			//$medium_path_new=$medium_path;
			//$medium=$obj->create_thumb($medium_path_new, $original_path, '1000');
		//}
		// else if($image_type=="gif")
		// {
		// 	$medium=$obj->convertImage($src, $medium_path, $image_type);
		// 	//$medium_path_new=$medium_path;
		// 	//$medium=$obj->create_thumb($medium_path_new, $medium_path, $medium_size);
		// }
		// else
		// {
		// 	$medium=$obj->create_thumb($src, $medium_path, $medium_size);
		// }
		//dd("ccheck");
		$directory_save = new Directory();
		$directory_save->directory_id 		= $dir_id;			
		$directory_save->original_name 		= $upload_image_name;	
		$directory_save->file_name 			= $upload_image_name;				
		$directory_save->directory_base_path = $small_image_path;	
		$directory_save->copy_url= $small_page_img_copy;
		$directory_save->directory_url 		= '';
		$directory_save->directory = 0;
		$directory_save->status = 1;
		$directory_save->created_by = $created_by;

		

		if($directory_save->save())
		{
			$upload_arr=array('status'=>'success','image_name'=>$small_image_path,'image_id'=>$directory_save->id);
		 	return $upload_arr;
		}
		else{
			$upload_arr=array('status'=>'error');
		 	return $upload_arr;
		}

		//echo $_FILES['image_file']['name']."---".$request->input('dir_id');
		//exit;
		//dd($request->all());
		//return $request;
	}

	public function getUpdatestatus($id)
	{
		$directory = Directory::find($id);
		if($directory->status ==0)
		{
			$new_status=1;
		}
		else
		{
			$new_status=0;
		}
		$directory->status = $new_status;			
		if($directory->save())
		{
			return 'success';
		}
	}

	public function getUploadrepoform()
	{
		$getAlldirectory=$this->getAlldirectory();
		$directory_list=$getAlldirectory;
		//dd($directory_list);
		return view('admin.directory.upload_file')->with('directory_list',$directory_list);
		//return view('admin.directory.upload_file','directory_list');
	}

	//Lis all directory
	public function getAlldirectory()
	{
		$created_by=Auth::user()->id;
		$directory = Directory::where('status',1)
					 ->where('created_by',$created_by)
					 ->where('directory',1)
					 ->orderBy('id','DESC')
					 ->get();
		return $directory;
	}
	
	public function postUploadofferp(Request $request)
	{	
		$created_by=Auth::user()->id;
		$upload_dir = env('UPLOADS');

		$base_dir=$upload_dir."/".$created_by;		
		$dest_dir="../../filemanager/userfiles/".$created_by;

		//check if base folder exists
		if(!file_exists($base_dir))
		{
			//create base folder
			helpers::createDir($base_dir, 0777);
		}
		if(!file_exists($dest_dir))
		{
			//create base folder
			helpers::createDir($dest_dir, 0777);
		}
		
		$dir_id_table=$request->input('directory_id');
		$directory =Directory::find($dir_id_table);		
		$upload_path=$directory->directory_base_path;
		$image_name=$request->input('file');		
		$created_by=Auth::user()->id;
		
		$obj = new helpers();
		$folder_name=env('UPLOADS');
		$file_name=$_FILES[ 'file' ][ 'name' ];
		$temp_path = $_FILES[ 'file' ][ 'tmp_name' ];
		//$request->input('dir_id');
		
		
		$original_path= $upload_path."/";		
		$extension = pathinfo($file_name, PATHINFO_EXTENSION);
		if($image_name)
		{
			$new_file_name = $image_name;
		}
		else
		{
			$new_file_name = time()."_".rand(111111111,999999999).'.'.$extension; // renameing image
		}		
		$directory_url=url()."/".$original_path.$new_file_name;
		$check_url=$original_path.$new_file_name;
		
		if (File::exists($check_url))		
		{
			return 'already_exists';
			echo "has";
		}
		
		
		$file_ori = $_FILES[ 'file' ][ 'tmp_name' ];	

		$file_name_arr=explode("uploads/",$upload_path);		
		$up_folder=$directory->file_name;
		$up_folder_path="../../filemanager/userfiles/".$created_by."/".$up_folder;		
		$copy_file_url=$up_folder_path."/".$new_file_name;		
		$up_path=$upload_path."/".$new_file_name;		
		
		//check if base folder exists
		if(!file_exists($up_folder_path))
		{
			//create base folder
			helpers::createDir($up_folder_path, 0777);
		}
		//dd($up_folder_path);
		if(!file_exists($upload_path))
		{
			//create base folder
			helpers::createDir($upload_path, 0777);
		}
		
		copy($file_ori, $copy_file_url);			
		move_uploaded_file($file_ori, $up_path);		
		
		
		
		//dd()
		$directory_save = new Directory();
		$directory_save->directory_id 		= $dir_id_table;			
		$directory_save->original_name 		= $_FILES[ 'file' ][ 'name' ];	
		$directory_save->file_name 		= $new_file_name;				
		$directory_save->directory_base_path 		= $up_path;	
		$directory_save->directory_url 		= $directory_url;
		$directory_save->directory = 0;
		$directory_save->status = 1;
		$directory_save->created_by = $created_by;

		if($directory_save->save())
		{
			return 'success';
		}

		//echo $_FILES['image_file']['name']."---".$request->input('dir_id');
		//exit;
		//dd($request->all());
		//return $request;
	}

	public function postUploadoffer(Request $request)
	{

		$created_by=Auth::user()->id;
		$upload_dir = env('UPLOADS');

		$base_dir=$upload_dir."/".$created_by;
		$directory_name=$request->get('dir_name');
		$dest_dir="../../filemanager/userfiles/".$created_by;
		$directory_url=url()."/".$directory_name;
		if($request->get('new_dir_id'))
		{
			
			$directory_id=$request->get('new_dir_id');
			$directory = Directory::where('status',1)->where('id',$directory_id)->orderBy('id','DESC')->get();
			$base_dir = $directory[0]['directory_base_path'];
			$dest_dir = $directory[0]['directory_url'];
			$directory_url = $directory[0]['directory_url']."/".$directory_name;
		}
		else
		{
			$directory_id=0;
		}
		//check if base folder exists
		if(!file_exists($base_dir))
		{
			//create base folder
			helpers::createDir($base_dir, 0777);
		}
		if(!file_exists($dest_dir))
		{
			//create base folder
			helpers::createDir($dest_dir, 0777);
		}

		// check if folder exists
		if(!file_exists($base_dir."/".$directory_name))
		{	
			//create folder		
			helpers::createDir($base_dir."/".$directory_name, 0777);
		}
		else
		{
			return 'folder_exists';
		}

		if(!file_exists($dest_dir."/".$directory_name))
		{	
			//create folder		
			helpers::createDir($dest_dir."/".$directory_name, 0777);
		}

		$status=1;	
		

		$directory = new Directory();
		$directory->directory_id 		= $directory_id;			
		$directory->original_name 		= $directory_name;	
		$directory->file_name 		= $directory_name;				
		$directory->directory_base_path 		= $base_dir."/".$directory_name;	
		$directory->directory_url 		= $directory_url;
		$directory->directory = 1;
		$directory->status = $status;
		$directory->created_by = $created_by;


		//$directory = new Directory();
		//$directory->directory_name 			= $directory_name;			
		//$directory->directory_base_path 	= $base_dir."/".$directory_name;	
		//$directory->status 					= $status;	
		//$directory->created_by 				= $created_by;		
		if($directory->save())
		{
			return 'success';
		}
		else
		{
			return 'error';
		}
	}


	function base64_to_jpeg($base64_string, $output_file) {
	    $ifp = fopen($output_file, "wb"); 

	    $data = explode(',', $base64_string);

	    fwrite($ifp, base64_decode($data[1])); 
	    fclose($ifp); 

	    return $output_file; 
	}

	function make_folder($recent_folder_path)
	{		
		if(!file_exists($recent_folder_path))
		{
			//create base folder
			//helpers::createDir($recent_folder_path, 0777);
			$oldmask = umask(0);
			helpers::createDir($recent_folder_path, 0777);
			umask($oldmask);
		}
	}

	public function getRepository($directory_id='')
	{
		//dd($directory_id);
	//	echo $directory_id."VV";
		$created_by=Auth::user()->id;
		if($directory_id>0)
		{
			$directory=Directory::where('created_by',$created_by)
					->where('directory_id',$directory_id)
					->get();
		}
		else
		{
			$directory=Directory::where('created_by',$created_by)
					->where('directory_id','0')	
					->get();
		}
					
		$site_path=env('SITE_PATH');
		// return view('partner.list',[
		// 				'logo_details' =>$logo_details,
		// 				'url' =>$url
		// 		   ]);
		//dd($directory->toArray());
		//return view('');
		return view('admin.promotion.list',[
						'directory_list' =>$directory,
						'site_path' =>$site_path,
						'directory_id' =>$directory_id
				   ]);
	}


	public function getRepositoryimage($image_id='')
	{
		//dd("a");
		$directory=Directory::find($image_id);
		//dd($image_id);
		return $directory;
	}

	

	public function getDirtree(){
		$created_by=Auth::user()->id;
		$dirs = Directory::where('created_by',$created_by)->where('directory_id',0)->orderBy('directory','ASC')->get();
		
		$tree = array();
		
		foreach($dirs as $key=>$dir){
			
			$dir->files = Directory::where('directory_id',$dir->id)->get();
			
		}
		
		return $dirs;
		//dd($dirs->toArray());
	}

	public function getAllstoreimages(){
		$created_by=Auth::user()->id;
		$images = StoreImage::where('user_id',$created_by)->get();
		if($images){
			return $images;
		} else {
			return 'error';
		}
	}

	public function postUploadstoreimage(Request $request){
		// dd($request->all());
		$created_by=Auth::user()->id;
		$upload_dir = env('UPLOADS');
		$copy_base_path=env('REPOSITORY_IMAGE_COPY').$created_by;
		$upload_path=$upload_dir."/".$created_by;
		
		if(!file_exists($upload_path))
		{
			//create base folder
			
			helpers::createDir($upload_path, 0777);
		}	
		if(!file_exists($copy_base_path))
		{
			//create base folder
			helpers::createDir($copy_base_path, 0777);
		}	
		//dd("check");	
		
		if($request->dir_id==1)
		{
			$dir_id=$request->dir_id;
			$base_dir=$upload_path;
			//Copy same into filemanager			
			$copy_dir=$copy_base_path;
		}
		else
		{
			$upload_arr=array('status'=>'error');
		 	return $upload_arr;
		}	

		$folder="Store";

		// Folder path to admin
		$folder_path=$base_dir."/".$folder;

		// Folder path to filemanager
		$filemanager_folder_path=$copy_dir."/".$folder;

		//dd($copy_dir);
		
		//dd($base_dir);
		$image_type="png";

		//Get desire image name by user
		$store_front_image_name=time()."_".rand(111111111,999999999)."-store-front-image.jpg";
		$brand_image_name=time()."_".rand(111111111,999999999)."brand-image.jpg";	

		$store_front_uploads_image_path=$folder_path."/".$store_front_image_name;
		$store_front_filemanager_image_path_copy=$filemanager_folder_path."/".$store_front_image_name;

		$brand_uploads_image_path=$folder_path."/".$brand_image_name;
		$brand_filemanager_image_path_copy=$filemanager_folder_path."/".$brand_image_name;

		
		$front_image_src=$request->input('front_image');
		$brand_image_src=$request->input('brand_image'); 	
		
		$obj = new helpers();

		$old_data = StoreImage::where('user_id', $created_by)->first();
		//Actually uploadingimage
		if($front_image_src && $brand_image_src){
			
			$front_image_img=$obj->convertImage($front_image_src, $store_front_uploads_image_path, $image_type);
			$front_image_img_copy=$obj->convertImage($front_image_src, $store_front_filemanager_image_path_copy, $image_type);

			$brand_image_img=$obj->convertImage($brand_image_src, $brand_uploads_image_path, $image_type);
			$brand_image_img_copy=$obj->convertImage($brand_image_src, $brand_filemanager_image_path_copy, $image_type);
		
			if($old_data){
				$old_data->brand_image 				= $brand_image_name;
				$old_data->brand_image_path 		= str_replace('../../filemanager', 'filemanager', $brand_filemanager_image_path_copy);
				$old_data->store_front_image 		= $store_front_image_name;
				$old_data->store_front_image_path 	= str_replace('../../filemanager', 'filemanager', $store_front_filemanager_image_path_copy);

				if($old_data->save())
				{
					$upload_arr=array('status'=>'success');
				 	return $upload_arr;
				}
				else{
					$upload_arr=array('status'=>'error');
				 	return $upload_arr;
				}
			} else {
				$save_new_data 	= new StoreImage();
				$save_new_data->user_id 				= $created_by;
				$save_new_data->brand_image 			= $brand_image_name;
				$save_new_data->brand_image_path 		= str_replace('../../filemanager', 'filemanager', $brand_filemanager_image_path_copy);
				$save_new_data->store_front_image 		= $store_front_image_name;
				$save_new_data->store_front_image_path 	= str_replace('../../filemanager', 'filemanager', $store_front_filemanager_image_path_copy);

				if($save_new_data->save())
				{
					$upload_arr=array('status'=>'success');
				 	return $upload_arr;
				}
				else{
					$upload_arr=array('status'=>'error');
				 	return $upload_arr;
				}
			}

		} else if($front_image_src && !$brand_image_src){
		
			$front_image_img=$obj->convertImage($front_image_src, $store_front_uploads_image_path, $image_type);
			$front_image_img_copy=$obj->convertImage($front_image_src, $store_front_filemanager_image_path_copy, $image_type);
		
			if($old_data){
				$old_data->store_front_image 		= $store_front_image_name;
				$old_data->store_front_image_path 	= str_replace('../../filemanager', 'filemanager', $store_front_filemanager_image_path_copy);

				if($old_data->save())
				{
					$upload_arr=array('status'=>'success');
				 	return $upload_arr;
				}
				else{
					$upload_arr=array('status'=>'error');
				 	return $upload_arr;
				}
			} else {
				$save_new_data 	= new StoreImage();
				$save_new_data->user_id 				= $created_by;
				$save_new_data->brand_image 			= "";
				$save_new_data->brand_image_path 		= "";
				$save_new_data->store_front_image 		= $store_front_image_name;
				$save_new_data->store_front_image_path 	= str_replace('../../filemanager', 'filemanager', $store_front_filemanager_image_path_copy);

				if($save_new_data->save())
				{
					$upload_arr=array('status'=>'success');
				 	return $upload_arr;
				}
				else{
					$upload_arr=array('status'=>'error');
				 	return $upload_arr;
				}
			}
		} else if(!$front_image_src && $brand_image_src){
		
			$brand_image_img=$obj->convertImage($brand_image_src, $brand_uploads_image_path, $image_type);
			$brand_image_img_copy=$obj->convertImage($brand_image_src, $brand_filemanager_image_path_copy, $image_type);
		
			if($old_data){
				$old_data->brand_image 				= $brand_image_name;
				$old_data->brand_image_path 		= str_replace('../../filemanager', 'filemanager', $brand_filemanager_image_path_copy);

				if($old_data->save())
				{
					$upload_arr=array('status'=>'success');
				 	return $upload_arr;
				}
				else{
					$upload_arr=array('status'=>'error');
				 	return $upload_arr;
				}
			
			} else {
				$save_new_data 	= new StoreImage();
				$save_new_data->user_id 				= $created_by;
				$save_new_data->brand_image 			= $brand_image_name;
				$save_new_data->brand_image_path 		= str_replace('../../filemanager', 'filemanager', $brand_filemanager_image_path_copy);
				$save_new_data->store_front_image 		= "";
				$save_new_data->store_front_image_path 	= "";

				if($save_new_data->save())
				{
					$upload_arr=array('status'=>'success');
				 	return $upload_arr;
				}
				else{
					$upload_arr=array('status'=>'error');
				 	return $upload_arr;
				}
			}
		} else {
			$upload_arr=array('status'=>'error');
		 	return $upload_arr;
		}

	}
}
