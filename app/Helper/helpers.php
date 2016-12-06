<?php
namespace App\Helper;
use Session;use DB;

ini_set('memory_limit', '-1');


	
class helpers  {   

    //upload bae64 decode image
    function base64_to_jpeg($base64_string, $output_file) {
        $ifp = fopen($output_file, "wb");
        $data = explode(',', $base64_string);
        fwrite($ifp, base64_decode($data[1])); 
        fclose($ifp); 
		chmod($output_file,0777);
        return $output_file; 
    }
	
	function base64toiamge($base64_string, $output_file) {
        $ifp = fopen($output_file, "wb");
        $data = explode(',', $base64_string);
        fwrite($ifp, base64_decode($data[1])); 
        fclose($ifp); 
		chmod($output_file,0777);
        return $output_file; 
    }

    function create_thumb($src, $dest, $desired_width = false, $desired_height = false)
    {
      /* If no dimenstion for thumbnail given, return false */    
      if (!$desired_height && !$desired_width)
          return false;

      $fparts = pathinfo($src);
      $ext = strtolower($fparts['extension']);

      /* if its not an image return false */
      if (!in_array($ext, array(
              'gif',
              'jpg',
              'png',
              'jpeg'
          )))
          return false;

      /* read the source image */
      if ($ext == 'gif')
          $resource = imagecreatefromgif($src);
      else if ($ext == 'png')
          $resource = imagecreatefrompng($src);
      else if ($ext == 'jpg' || $ext == 'jpeg')
          $resource = imagecreatefromjpeg($src);

      $width = imagesx($resource);
      $height = imagesy($resource);

      /* find the “desired height” or “desired width” of this thumbnail, relative
       * to each other, if one of them is not given */
      if (!$desired_height)
          $desired_height = floor($height * ($desired_width / $width));

      if (!$desired_width)
          $desired_width = floor($width * ($desired_height / $height));

      /* create a new, “virtual” image */
      $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

      switch ($ext)
      {
      case "png":
          // integer representation of the color black (rgb: 0,0,0)
          $background = imagecolorallocate($virtual_image, 255, 255, 255);
          
          // removing the black from the placeholder
          imagecolortransparent($virtual_image, $background);

          // turning off alpha blending (to ensure alpha channel information 
          // is preserved, rather than removed (blending with the rest of the 
          // image in the form of black))
          imagealphablending($virtual_image, false);

          // turning on alpha channel information saving (to ensure the full range 
          // of transparency is preserved)
          imagesavealpha($virtual_image, true);
          //imagealphablending($virtual_image, true);

          break;
      case "gif":
          // integer representation of the color black (rgb: 0,0,0)
          $background = imagecolorallocate($virtual_image, 255, 255, 255);
          
          // removing the black from the placeholder
          imagecolortransparent($virtual_image, $background);

          break;
      }

      /* copy source image at a resized size */
      imagecopyresampled($virtual_image, $resource, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);

      /* create the physical thumbnail image to its destination */
      /* Use correct function based on the desired image type from $dest thumbnail
       * source */
      $fparts = pathinfo($dest);
      $ext = strtolower($fparts['extension']);
      /* if dest is not an image type, default to jpg */
      if (!in_array($ext, array(
              'gif',
              'jpg',
              'png',
              'jpeg'
          )))
          $ext = 'jpg';
      $dest = $fparts['dirname'] . '/' . $fparts['filename'] . '.' . $ext;

      if ($ext == 'gif')
          imagegif($virtual_image, $dest);
      else if ($ext == 'png')
          imagepng($virtual_image, $dest, 1);
      else if ($ext == 'jpg' || $ext == 'jpeg')
          imagejpeg($virtual_image, $dest, 100);

      return array(
          'width' => $width,
          'height' => $height,
          'new_width' => $desired_width,
          'new_height' => $desired_height,
          'dest' => $dest
      );
    } 

    function watermarkImage($stamp,$rootImage,$finalImage)
    {
      //set the source image (foreground) - the watermark image
      $sourceImage = $stamp;
      //set the destination image (background)
      $destImage = $rootImage;
      //get the size of the source image, needed for imagecopy()
      list($srcWidth, $srcHeight) = getimagesize($sourceImage);
      if (exif_imagetype($sourceImage) == IMAGETYPE_GIF) {      
        //create a new image from the source image
        $src = imagecreatefromgif($sourceImage);      
      }
      if (exif_imagetype($sourceImage) == IMAGETYPE_JPEG) {     
        //create a new image from the source image
        $src = imagecreatefromjpeg($sourceImage);     
      }
      if (exif_imagetype($sourceImage) == IMAGETYPE_PNG) {
        //create a new image from the source image
        $src = imagecreatefrompng($sourceImage);
      }
      //create a new image from the destination image
      $dest = imagecreatefromjpeg($destImage);
      
      //set the x and y positions of the source image on top of the destination image
      $src_xPosition = 10; //10 pixels from the left
      $src_yPosition = 10; //10 pixels from the top
      //set the x and y positions of the source image to be copied to the destination image
      $src_cropXposition = 0; //do not crop at the side
      $src_cropYposition = 0; //do not crop on the top        

      if ((exif_imagetype($sourceImage) == IMAGETYPE_GIF) || (exif_imagetype($sourceImage) == IMAGETYPE_PNG)) 
      {       
        //merge the source and destination images
        imagecopy($dest,$src,$src_xPosition,$src_yPosition,$src_cropXposition,$src_cropYposition,$srcWidth,$srcHeight);
      }
      if (exif_imagetype($sourceImage) == IMAGETYPE_JPEG) {   
        //merge the source and destination images
        imagecopymerge($dest,$src,$src_xPosition,$src_yPosition,$src_cropXposition,$src_cropYposition,$srcWidth,$srcHeight,50);
      }
      //output the merged images to a file
      /*
       * '100' is an optional parameter,
       * it represents the quality of the image to be created,
       * if not set, the default is about '75'
       */
      // echo $finalImage;    
      // exit;
      imagejpeg($dest,$finalImage,100);
      //destroy the source image
      imagedestroy($src);
      //destroy the destination image
      imagedestroy($dest);    
    }


    function convertImage($originalImage, $outputImage,$image_type, $quality=100)
    {

	
        // jpg, png, gif or bmp?

         // $exploded = explode('.',$originalImage);
         // $ext = $exploded[count($exploded) - 1]; 
         $ext = $image_type; 
         //dd($ext);
         if (preg_match('/jpg|jpeg/i',$ext))
         {
            dd("A");
			
            $this->create_thumb_from_jpeg($originalImage,$outputImage,$quality);
         }
         else if (preg_match('/png/i',$ext))
         {
           // dd("B");
            $this->create_jpg_from_png($originalImage,$outputImage);
         }
         else if (preg_match('/gif/i',$ext))
         {
            dd("C");
             $imageTmp=imagecreatefromgif($originalImage);
         }
         else if (preg_match('/bmp/i',$ext))
         {
            dd("D");
             $imageTmp=imagecreatefrombmp($originalImage);
         }
         else
         {
            dd("E");
             return 0;
         }

        // // quality is a value from 0 (worst) to 100 (best)
        // imagejpeg($imageTmp, $outputImage, $quality);
        // imagedestroy($imageTmp);

        // return 1;

        // $input = imagecreatefrompng($originalImage);
        // list($width, $height) = getimagesize($originalImage);
        // $output = imagecreatetruecolor($width, $height);
        // $white = imagecolorallocate($output,  255, 255, 255);
        // imagefilledrectangle($output, 0, 0, $width, $height, $white);
        // imagecopy($output, $input, 0, 0, 0, 0, $width, $height);
        // imagejpeg($output, $output_file);
    }

    function create_jpg_from_png($originalImage,$outputImage)
    {
      //dd("zzz");
	  
      $input = imagecreatefrompng($originalImage);
      list($width, $height) = getimagesize($originalImage);
      $output = imagecreatetruecolor($width, $height);
      $white = imagecolorallocate($output,  255, 255, 255);
      imagefilledrectangle($output, 0, 0, $width, $height, $white);
      imagecopy($output, $input, 0, 0, 0, 0, $width, $height);
      imagejpeg($output, $outputImage);
	  chmod($outputImage,0777);
      return 1;
    }

    function create_thumb_from_png($fileName,$newFilename,$newwidth)
    {
      list($width, $height) = getimagesize($fileName);

      $newheight=(($height/$width)*$newwidth);
      $thumb = imagecreatetruecolor($newwidth, $newheight);
      imagealphablending($thumb, false);
      imagesavealpha($thumb, true);  

      $source = imagecreatefrompng($fileName);
      imagealphablending($source, true);

      imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

      imagepng($thumb,$newFilename);
	  chmod($newFilename,0777);
      return 1;
    }

    function create_thumb_from_jpeg($fileName,$newFilename,$quality)
    {
      dd($fileName);
      imagejpeg($fileName, $newFilename, $quality);
	  chmod($newFilename,0777);
      imagedestroy($imageTmp);
    }


    function createThumbs( $pathToImages, $pathToThumbs, $thumbWidth )
    {
      dd($pathToImages);
      // open the directory
      $dir = opendir( $pathToImages );

      // loop through it, looking for any/all JPG files:
      while (false !== ($fname = readdir( $dir ))) {
        // parse path for the extension
        $info = pathinfo($pathToImages . $fname);
        // continue only if this is a JPEG image
        if ( strtolower($info['extension']) == 'jpg' )
        {
          //echo "Creating thumbnail for {$fname} <br />";

          // load image and get image size
          $img = imagecreatefromjpeg( "{$pathToImages}{$fname}" );
          $width = imagesx( $img );
          $height = imagesy( $img );

          // calculate thumbnail size
          $new_width = $thumbWidth;
          $new_height = floor( $height * ( $thumbWidth / $width ) );

          // create a new temporary image
          $tmp_img = imagecreatetruecolor( $new_width, $new_height );

          // copy and resize old image into new image
          imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

          // save thumbnail into a file
          imagejpeg( $tmp_img, "{$pathToThumbs}{$fname}" );
        }
      }
      // close the directory
      closedir( $dir );
      return 1;
    }

	
	public static function chmod_r($path,$permission=0777) {
		$dir = new \DirectoryIterator($path);
		foreach ($dir as $item) {
			chmod($item->getPathname(), $permission);
			if ($item->isDir() && !$item->isDot()) {
				self::chmod_r($item->getPathname());
			}
		}
	}
	
	public static function createDir($base_dir,$permission=0777){
		$old = umask(0);
		$md = mkdir($base_dir, $permission);
		umask($old);
		return $md;
	}

}

?>