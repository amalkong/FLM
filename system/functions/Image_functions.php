<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
/*------------------------------------------------------------------------------------------
     Micro Image Manipulation Pack

     ©PhpToys 2006
     http://www.phptoys.com

     Released under the terms and conditions of the
     GNU General Public License (http://gnu.org).

     $Revision: 1.0 $
     $Date: 2006/07/03 $
     $Author: PhpToys $
     
     USAGE:
          This package has 3 function to manipulta jpeg images with php code.
          - resizeImage : needs a filename and a width, height values.
          - dropShadow  : needs an image as image resource and not as filename, and you can define 
                          the shadow size.
          - createBorder: needs an image as image resource and not as filename, and the border 
                          width and height values.
--------------------------------------------------------------------------------------------*/
    function GetAlbumsPhotos($path,$src_url,$createThumb=false){
	    global $UTIL,$ifxs;
	    // display photos in folder
		$image_files = array();
        $src_folder = $UTIL->Check_For_Slash($path,true);
        $src_files  = GetDirContents($src_folder,'files');
        foreach($src_files as $file) {
			if($UTIL->isValidExt($file, $ifxs)) {
		        if ($createThumb) {
		            if (!is_dir($src_folder.'thumbs')) {
                        mkdir($src_folder.'thumbs');
                        chmod($src_folder.'thumbs', 0777);
                        //chown($src_folder.'/thumbs', 'apache'); 
                    }
		            $thumb = $src_folder.'thumbs/'.$file;
				
                    //if (!file_exists($thumb)) make_thumb($src_folder,$file,$thumb,$CFG->config['thumb_width']); 
		            if (!file_exists($thumb)) createThumb($file,$src_folder,$CFG->config['thumb_width']);
			    }
				$tmp = array();
				$tmp[0] = $file;
				$tmp[2] = $UTIL->removeFileExt($file);
				$tmp[5] = date('d-M-Y h:i:s A',filemtime($src_folder.$file));
				$tmp[7] = $UTIL->size('file',$src_folder.$file);
				array_push( $image_files, $tmp );
			}
		}
		return $image_files;
	}
    function show_gallery($galleryname){
	    global $UTIL,$CFG,$ifxs;
		$PB_output = NULL;
	    $f_sortby = ( isset($_GET['f_sortby']) ? GetCommand("f_sortby") : "title" );
	    $f_ascdsc = ( isset($_GET['f_ascdsc']) ? GetCommand("f_ascdsc") : "DESC" );
	    $GLOBALS['f_sortby'] = $f_sortby;
        $GLOBALS['f_ascdsc'] = $f_ascdsc;
		$url_prefix = SELF.'?mode='.MODE;
	        if(isset($_GET['section'])) $url_prefix .='&amp;section='.$_GET['section'];
	        if(isset($_GET['action'])) $url_prefix .='&amp;action='.$_GET['action'];
	        if(isset($_GET['album'])) $url_prefix .='&amp;album='.$_GET['album'];
	        if(isset($_GET['page'])) $url_prefix .='&amp;page='.$_GET['page'];
	        if(isset($_GET['newsArticle'])) $url_prefix .='&amp;newsArticle='.$_GET['newsArticle'];
	    $return_url = $url_prefix;
		
		$path = $UTIL->Check_For_Slash(GALLERY_PATH.$galleryname,true);
	    $src_url = $UTIL->Check_For_Slash(GALLERY_URL.$galleryname,true);
		if(isset($CFG->config['show_image_info']) && $CFG->config['show_image_info'] == 'YES') $thumb_pic_height = 'auto';
	    else $thumb_pic_height = '120px';
		
		$image_sortby_array = array("title"=>"Name", "size"=>"Size","date"=>"Date");
		foreach($image_sortby_array as $cat=>$v){
		    $sortby_html[] = '<div class="sort-link left-1">&nbsp;|&nbsp;<span class="i left-1">'.(($f_sortby == $cat) ? "<strong>$v</strong>" : "$v").'</span>'.
		    "<span class=\"left-1\"><a class=\"icon asc\" href=\"$url_prefix&amp;f_sortby=$cat&amp;f_ascdsc=ASC\" title=\"sort files by $cat ascending\"></a><a class=\"icon desc\" href=\"$url_prefix&amp;f_sortby=$cat&amp;f_ascdsc=DESC\" title=\"sort files by $cat descending\"></a></span></div>";
	    }
		
	    $image_files = GetAlbumsPhotos($path,$src_url,false);
		usort($image_files,"sort_file");
			
            if ( count($image_files) == 0 ) $PB_output .= '<div class="message user_status"><p>There are no image in this folder!</p></div>';
            else {
                $numPages = ceil( count($image_files) / $CFG->config['itemsPerPage'] );
                if(isset($_GET['p'])) {
	                $currentPage = $_GET['p'];
                    if($currentPage > $numPages) $currentPage = $numPages;
                } else $currentPage=1;
			    $start = ( $currentPage * $CFG->config['itemsPerPage'] ) - $CFG->config['itemsPerPage'];
                $total_images = count($image_files);
				
	            $sortby_htmo = implode(" ", $sortby_html);
	        
		        $PB_output .= '<div class="page-num head">PAGE &mdash; [ '.$currentPage.' ] &mdash;&raquo;</div>';
			    $PB_output .=  '<div class="hRule">&nbsp;</div>';
		        $PB_output .= '<div class="titlebar">'.
			        '<div class="left-1"><span class="title">'.$CFG->config['site_title_full'].'</span> - '.cleanPageTitles($galleryname).' Photo Album : '.$total_images.' Images - '.((MODE=='viewGallery') ? '<a href="'.$return_url.'">View All Directory</a>' : '<a href="javascript:history.back()">Back</a>' ).'</div>'.
                    '<div id="sortby" class="right-1 grid-45"><span class="left-1">Sort by: </span>'.$sortby_htmo.'</div>'.
                '</div>';
				
				$PB_output .= '<div class="clear"></div>';
			    $PB_output .=  '<div class="hRule">&nbsp;</div>';
                
                for( $i=$start; $i<$start + $CFG->config['itemsPerPage']; $i++ ) {
		            if( isset($image_files[$i][0]) && is_file( $path . $image_files[$i][0] ) ) {
			            $info = Image_info_basic($path .$image_files[$i][0]);
						$date = $image_files[$i][5];
						$title = strlen($image_files[$i][2]) > 18 ? substr($image_files[$i][2],0,16)."..." : $image_files[$i][2];
						$src_img = $src_url.$image_files[$i][0];
						$alt = $title . " -|- Last modified: " . $date;
		                $edit_url = SELF.'?mode=admin&amp;section=manage&amp;action=edit&amp;filetype=albumPic&amp;album='.$galleryname.'&amp;pic='.$image_files[$i][0];
		                $delete_url = SELF.'?mode=admin&amp;section=manage&amp;action=delete&amp;filetype=albumPic&amp;album='.$galleryname.'&amp;pic='.$image_files[$i][0];
			            
		                $PB_output .= ' <div class="image-wrapper shadow pad-5" style="height:'.$thumb_pic_height.';">';
					        $PB_output .= '<div class="title description">'.$title.'</div>';
		     	            if(isset($CFG->config['show_image_info']) && $CFG->config['show_image_info'] == 'NO') $PB_output .= '<div class="date description">Created on '.$date.'</div>';
			                $PB_output .= '<div class="thumb-wrapper shadow">'; 
	                            $PB_output .= '<div class="thumb">';
		                            $PB_output .= ' <a href="'.$src_img.'" class="albumpix" rel="albumpix" title="'.$title.'"><img src="'.$src_img.'" alt="'.$alt.'" width="'.$CFG->config['thumb_width'].'" title="'.$alt.'" /></a>';
				                $PB_output .= '</div>';
				            $PB_output .= '</div>'."\n";
						    $PB_output .= '<div class="clear">&nbsp;</div>';
					        $PB_output .= '<div class="description"><strong>'.$title.' Info</strong><br/><span>'.$info.'</span></div>';
				            if(isset($_SESSION['userRole']) && $_SESSION['userRole'] == 'superadmin')
						        $PB_output .= '<div class="link description"><a href="'.$edit_url.'" title="Edit : '.$image_files[$i][0].'">Rename</a>|<a href="'.$delete_url.'" title="Delete : '.$image_files[$i][0].'">Delete</a></div>';
				        $PB_output .= '</div>';
	                } else {
		                if( isset($image_files[$i][0]) ) $PB_output .= $image_files[$i][0];
             		}
                }
                $urlVars = $url_prefix."&amp;album=".urlencode($galleryname)."&amp;f_sortby=".$f_sortby."&amp;f_ascdsc=".$f_ascdsc;
				
				$PB_output .= '<div class="clear"></div>';
                $PB_output .= '<div align="center" class="paginate-wrapper">';
                   $PB_output .=  $UTIL->print_pagination($numPages,$urlVars,$currentPage);
                $PB_output .= '</div>';
            }
			return $PB_output;
	}
    function GetAlbums($path){
	    global $Gallery_DB_File,$UTIL,$CFG,$ifxs;
		$i = 0;
	     // display list of albums
	    $folders = GetDirContents($path,'dirs');
        //$folders = scandir($path, 0);
		$albums = array();
		$ignore = array('.','..','Thumbs.db','photothumb.db');
        if(is_array($folders) && count($folders) > 0){
        foreach($folders as $album) {
		    if(!in_array($album, $ignore) && is_dir($UTIL->Check_For_Slash($path.$album,true))) {
			    if(file_exists($Gallery_DB_File)) $galleryInfo = GetAlbumInfo($album);
				else $galleryInfo = NULL;
				if(is_array($galleryInfo) && $galleryInfo['album_name'] == $album){
				    $album_id = $galleryInfo['album_id'];
					$album_name = $galleryInfo['album_name'];
					$album_title = $galleryInfo['album_title'];
					$album_author = $galleryInfo['album_author'];
					$album_cat = $galleryInfo['album_category'];
					$album_explicit = $galleryInfo['explicit'];
					if($galleryInfo['album_description'] != NULL) $album_descr = $galleryInfo['album_description'];
			        else $album_descr = 'This Album description was not set.';
				} else {
					$album_id = $i;
					$album_name = $album;
					//$album_title = substr($album,0,20);
					$album_title = $album;
					$album_author = $CFG->config['admin_username'];
					$album_explicit = 'unknown';
					$album_cat = 'un-categorized';
					$album_descr = 'This Album description is unavailable.';
				}
				
				$rand_dirs = glob($UTIL->Check_For_Slash($path.$album,true).'*.*', GLOB_NOSORT);
				if(count($rand_dirs) == 0) $rand_pic  = IMG_URL.'unknown.png';
				else {
				    //$rand_pic  = $rand_dirs[array_rand(@$rand_dirs)];
				    if($UTIL->isValidExt(basename($rand_dirs[array_rand(@$rand_dirs)]),$ifxs)) $rand_pic = $rand_dirs[array_rand(@$rand_dirs)];
				}
				$tmp = array();
			    $tmp[0] = $album_name;
				$tmp[1] = $album_id;
				$tmp[2] = $album_title;
				$tmp[3] = $album_author; 
				$tmp[5] = date('d-m-Y H:i:s',filemtime($path.$album));
				$tmp[7] = ($CFG->config['display_dirsize'] == 'YES') ? $UTIL->size('file',$path.$album) : '???';
				$tmp[8] = $album_descr;
				$tmp[9] = $rand_pic;
				   
				array_push($albums,$tmp);
				$i++;
			}
	    }
		}
        return $albums;
	}
	 /*
	 * This function returns the information about the selected gallery album
	 *
	 * @param $return_array bool -- true/false, return categories in an array or in an ordered list
	 * @param $df_sortby -- default sorting method ["id","count","title"]
     * @param $df_ascdsc -- default ordering (ascending or descending) ["ASC","DESC"]
	 * @return mix -- array, string
	*/
   
    function GetAlbumInfo($galleryname){
	    global $Gallery_DB_File;
		$PB_output = NULL; //erase variable which contains data
		//$GLOBALS['f_sortby'] = $df_sortby;
        //$GLOBALS['f_ascdsc'] = $df_ascdsc;
		 
		$albums = array();
	    if(file_exists($Gallery_DB_File)){
		    $fp = @fopen($Gallery_DB_File, 'r');
		    //$array = explode("\n", fread($fp, filesize($Gallery_DB_File))); 
		    $array = file($Gallery_DB_File); 
			for($x=0;$x<sizeof($array);$x++) {	// start loop, each line of file
		        $temp = explode(":",$array[$x]); // explode the line and assign to temp
				$albums['album_id'] = $temp[0];
				$albums['album_name'] = $temp[1];
				$albums['album_title'] = $temp[2];
				$albums['album_author'] = $temp[3];
				$albums['album_description'] = $temp[4];
				$albums['album_category'] = $temp[5];
				$albums['explicit'] = $temp[6];
			}
			fclose($fp);
			return $albums;
	    }
		
		return('<div class="message user_status"><p>'._MISSING_GALLERY_DB_FILE.'</p></div>'."\n");
	}
	
	function createNewGallery($galleryname){
	    if(empty($galleryname)) return false;
		
	    if(is_dir(GALLERY_PATH)){
	        $new_gallery_path = GALLERY_PATH.$galleryname;
	        if(file_exists($new_gallery_path)) $message = '<div class="message error"><p>A gallery with this name&nbsp;&rarr;&nbsp;'.$galleryname.', already exists!</p></div>';
	        else {
			    if(mkdir($new_gallery_path,DIR_WRITE_MODE)) {
				    $message = '<div class="message"><p>The gallery&nbsp;&rarr;&nbsp;'.$galleryname.', has been created successfully.</p></div>';
	                
				} else $message = '<div class="message error"><p>unable to create the new gallery folder successfully</p></div>';
	        }
		} else $message = '<div class="message error"><p>error! the gallery path is not valid</p></div>';
	    
		return $message;
	}
    function resizeImage($originalImage,$toWidth,$toHeight){
        // Get the original geometry and calculate scales
        list($width, $height) = getimagesize($originalImage);
        $xscale=$width/$toWidth;
        $yscale=$height/$toHeight;
    
        // Recalculate new size with default ratio
        if ($yscale>$xscale){
            $new_width = round($width * (1/$yscale));
            $new_height = round($height * (1/$yscale));
        } else {
            $new_width = round($width * (1/$xscale));
            $new_height = round($height * (1/$xscale));
        }

        // Resize the original image
        $imageResized = imagecreatetruecolor($new_width, $new_height);
        $imageTmp     = imagecreatefromjpeg ($originalImage);
        imagecopyresampled($imageResized, $imageTmp, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        return $imageResized;
    }

    function createBorder($img,$x,$y){
        // Create image base 
        $image           = imagecreatetruecolor($x,$y);
        $backgroundColor = imagecolorallocate($image,255,255,255);
        $borderColor     = imagecolorallocate($image,50,50,50);
    
        imagefill($image,0,0,$backgroundColor);
        imagerectangle($image,0,0,$x-1,$y-1, $borderColor);

        $width  = imagesx($img);
        $height = imagesy($img);
    
        imagecopymerge($image,$img,($x-$width)/2,($y-$height)/2,0,0,$width,$height,100);

        return $image;
    }

    function dropShadow($img,$shadowSize=5){
        // Set the new image size  
        $width  = imagesx($img)+$shadowSize;
        $height = imagesy($img)+$shadowSize;
  
        $image = imagecreatetruecolor(imagesx($img)+$shadowSize, imagesy($img)+$shadowSize);

        for ($i = 0; $i < 10; $i++){
            $colors[$i] = imagecolorallocate($image,255-($i*25),255-($i*25),255-($i*25));
        }

        // Create a new image
        imagefilledrectangle($image, 0,0, $width, $height, $colors[0]);

        // Add the shadow effect
        for ($i = 0; $i < count($colors); $i++) {
            imagefilledrectangle($image, $shadowSize, $shadowSize, $width--, $height--, $colors[$i]);
        }

        // Merge with the original image
        imagecopymerge($image, $img, 0,0, 0,0, imagesx($img), imagesy($img), 100);

        return $image;
    }

	function process_image($dir, $filename,$width,$height) {
        // Set up the variables
        $dir = $dir . DIRECTORY_SEPARATOR;
        $i = strrpos($filename, '.');
        $image_name = substr($filename, 0, $i);
        $ext = substr($filename, $i);

        // Set up the read path
        $image_path = $dir . DIRECTORY_SEPARATOR . $filename;

        // Set up the write paths
        $image_path_400 = $dir . $image_name . '_400' . $ext;
        //$image_path_250 = $dir . $image_name . '_250' . $ext;
        $image_path_100 = $dir .'thumbs'. DIRECTORY_SEPARATOR . $image_name . '_100' . $ext;

        // Create an image that's a maximum of 400x300 pixels
        resize_image($image_path, $image_path_400, 400, 300);

        // Create an image that's a maximum of 250x250 pixels
        // resize_image($image_path, $image_path_250, 250, 250);

        // Create a thumbnail image that's a maximum of 100x100 pixels
        //resize_image($image_path, $image_path_100, 100, 100);
        resize_image($image_path, $image_path_100, $width, $height);
    }

/*******************************************
 * Resize image to 400x300 max
 ********************************************/
    function resize_image($old_image_path, $new_image_path,$max_width, $max_height) {
        // Get image type
        $image_info = getimagesize($old_image_path);
        $image_type = $image_info[2];

        // Set up the function names
        switch($image_type) {
            case IMAGETYPE_JPEG:
                $image_from_file = 'imagecreatefromjpeg';
                $image_to_file = 'imagejpeg';
            break;
            case IMAGETYPE_GIF:
                $image_from_file = 'imagecreatefromgif';
                $image_to_file = 'imagegif';
            break;
            case IMAGETYPE_PNG:
                $image_from_file = 'imagecreatefrompng';
                $image_to_file = 'imagepng';
            break;
            default:
                echo 'File must be a JPEG, GIF, or PNG image.';
            exit;
        }

        // Get the old image and its height and width
        $old_image = $image_from_file($old_image_path);
        $old_width = imagesx($old_image);
        $old_height = imagesy($old_image);

        // Calculate height and width ratios
        $width_ratio = $old_width / $max_width;
        $height_ratio = $old_height / $max_height;

        // If image is larger than specified ratio, create the new image
        if ($width_ratio > 1 || $height_ratio > 1) {
            // Calculate height and width for the new image
            $ratio = max($width_ratio, $height_ratio);
            $new_height = round($old_height / $ratio);
            //$new_height = $max_height;
            $new_width = round($old_width / $ratio);

            $new_image = imagecreatetruecolor($new_width, $new_height);// Create the new image

            // Set transparency according to image type
            if ($image_type == IMAGETYPE_GIF) {
                $alpha = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
                imagecolortransparent($new_image, $alpha);
            }
            if ($image_type == IMAGETYPE_PNG || $image_type == IMAGETYPE_GIF) {
                imagealphablending($new_image, false);
                imagesavealpha($new_image, true);
            }

            // Copy old image to new image - this resizes the image
            $new_x = 0;
            $new_y = 0;
            $old_x = 0;
            $old_y = 0;
            imagecopyresampled($new_image, $old_image,$new_x, $new_y, $old_x, $old_y,$new_width, $new_height, $old_width, $old_height);

            $image_to_file($new_image, $new_image_path);// Write the new image to a new file
            echo'<div class="message">Image re-sized successfully</div>';
            
            imagedestroy($new_image);// Free any memory associated with the new image
        } else {
            $image_to_file($old_image, $new_image_path);// Write the old image to a new file
        }
        
        imagedestroy($old_image);// Free any memory associated with the old image
    }

// create thumbnails from images
    function make_thumb($folder,$src,$dest,$thumb_width) {
        global $UTIL;
        $type = $UTIL->getSuffix($src);
		$folder = $UTIL->Check_For_Slash($folder,true);
		//if the $thumb_width contains characters ohther than numbers an error will occur, this line of code tries to minimize that
		$thumb_width = str_replace('px','',$thumb_width);
        // png or jpeg? either way get image
        if (is_file($folder.$src)) {
            if ($type==".png") $source_image = imagecreatefrompng($folder.$src);
            else if ($type==".gif") $source_image = imagecreatefromgif($folder.$src);
            else $source_image = imagecreatefromjpeg($folder.$src);
            
            if ( !$source_image ) {
                $UTIL->error("not a valid image file :(");
                return false;
            }
	        $width = imagesx($source_image);
	        $height = imagesy($source_image);
	
	        $thumb_height = floor($height*($thumb_width/$width));
	
	        $virtual_image = imagecreatetruecolor($thumb_width,$thumb_height);
	
	        imagecopyresampled($virtual_image,$source_image,0,0,0,0,$thumb_width,$thumb_height,$width,$height);
	        if ( $type == ".png" ) imagepng($virtual_image,$dest);
            else if ( $type == ".gif" ) imagegif($virtual_image,$dest,100);
            else imagejpeg($virtual_image,$dest,100);
            
        } else return false;
    }
	
	function createThumb($f,$path,$thumb_width) {
	    global $UTIL;
        $type = $UTIL->getSuffix($f);
        $path = $UTIL->Check_For_Slash($path,true);
		//if the $thumb_width contains characters ohther than numbers an error will occur, this line of code tries to minimize that
		$thumb_width = str_replace('px','',$thumb_width);
		$thumb_dir = 'thumbs/';
        // png or jpeg?, either way get image
        if (is_file($path.$f)) {
            if ($type==".png") $input = imagecreatefrompng($path.$f);
            else if ($type==".gif") $input = imagecreatefromgif($path.$f);
            else $input = imagecreatefromjpeg($path.$f);
          
            if ( !$input ) {
                echo("<div class='message error'>not a valid image file :( </div>");
                return false;
            } 

            // get size ( [0]=width, [1]=height )
            $tmp = getimagesize($path.$f);
            if ( !$tmp ) {
                echo("<div class='message error'>Could not get input image size</div>");
                return false;
            }
	
            // get width of new thumbnail by taking the smaller value from max and tmp width
            $w = ($tmp[0] > $thumb_width) ? $thumb_width : $tmp[0];
            // scale height according to width
            $h = ceil($tmp[1] * ($thumb_width / $tmp[0]));
            // create output image
			
            $output = imagecreatetruecolor($w,$h);
            if ( !$output ) {
                echo("<div class='message error'>could not create output image</div>");
                return false;
            }
            // copy big image over to thumbnail, and resize down
            imagecopyresized( $output,$input, 0,0, 0,0, $w,$h, $tmp[0],$tmp[1] );
            $newfile = $path.$thumb_dir.$f;
            // do the outputting!
            if ( $type == ".png" ) imagepng($output,$newfile);
            else if ( $type == ".gif" ) imagegif($output,$newfile);
            else imagejpeg($output,$newfile);
			
			$message = '<div class="message"><p>The thumb for&nbsp;&rarr;&nbsp;<span class="b">'.$f.'</span>, was created successfully.</p></div>';
		} else $message = '<div class="message error"><p><span class="b">'.$path.$f.'</span> is not a valid image!</p></div>';
    
	    return $message;
	    //echo $message;
	}
	
	function Image_info_basic($image_file){
	    global $UTIL;
	    if (!$image = imagecreatefromstring(file_get_contents($image_file))){
			exit;
		}
		$image_width = imagesx($image);
		$image_height = imagesy($image);
		$info = '';
		$info .= 'image name : '.basename($image_file)."<br/>";
	    $info .= 'Created : ';
		if(date('d-m-Y H:i:s',filemtime($image_file))){
			$info .= date('d-m-Y H:i:s',filemtime($image_file));
		}else{
			$info .= "n/a";
		}
		$info .= "<br/>";
	    $info .= 'size : '.$UTIL->GetFilesize($image_file)."<br/>";
	    $info .= 'dimension : '.$image_width.'x'.$image_height;
		return $info;
	}
	function Image_info($image_file,$echo=false){
	    global $CFG,$rotate_images,$UTIL;
        //$source_img = $image_file;
	    if (!$image = imagecreatefromstring(file_get_contents($image_file))) exit;
			
		$image_width = imagesx($image);
		$image_height = imagesy($image);
			
		$exif_info = "";
	    $exif_info .='<div class="image-info"><div class="basic-info">image name : '.basename($image_file).
	        '<br/>Created : ';
			if(date('d-m-Y H:i:s',filemtime($image_file))) 
				$exif_info .= date('d-m-Y H:i:s',filemtime($image_file));
			else $exif_info .= "n/a";
			    
	        $exif_info .='<br/>size : '.$UTIL->GetFilesize($image_file).
	        '<br/>dimension : '.$image_width.'x'.$image_height.
	        '<br/></div><div class="exif-info">Exif Info : ';
	        
		    if (function_exists("read_exif_data")){
			    if (isset($CFG->config['show_exif_info']) && $CFG->config['show_exif_info'] == 'YES' || $CFG->config['show_exif_info'] == 'yes'){
				    $exif_data = @exif_read_data( $image_file, "IFD0");
				    if ($exif_data !== FALSE){
					    $exif_info .= "EXIF Date : ";
					    if(isset($exif_data["DateTimeOriginal"])){
						    $exif_info .= $exif_data["DateTimeOriginal"];
					    } else $exif_info .= "n/a";
					    
						$exif_info .= "<br/>";
					    $exif_info .= "Camera : ";
                        if(isset($exif_data["Model"])){
						    $exif_info .= $exif_data["Model"];
					    } else $exif_info .= "n/a";
					    
						$exif_info .= "<br/>";
					    $exif_info .= "ISO : ";
					    if(isset($exif_data["ISOSpeedRatings"])){
						    $exif_info .= $exif_data["ISOSpeedRatings"];
					    } else $exif_info .= "n/a";
					    
					    $exif_info .= "<br/>";
					    $exif_info .= "Shutter Speed : ";
					    if(isset($exif_data["ExposureTime"])){
						    $exif_ExposureTime=create_function('','return '.$exif_data["ExposureTime"].';');
						    $exp_time = $exif_ExposureTime();
						    if ($exp_time > 0.25) $exif_info .= $exp_time;
						    else $exif_info .= $exif_data["ExposureTime"];
						    
						    $exif_info .= "s";
					    } else $exif_info .= "n/a";
					    
					    $exif_info .= "<br/>";
					    $exif_info .= "Aperture : ";
					    if(isset($exif_data["FNumber"])){
						    $exif_FNumber=create_function('','return number_format(round('.$exif_data["FNumber"].',1),1);');
						    $exif_info .= "f".$exif_FNumber();
					    } else $exif_info .= "n/a";
					    
					    $exif_info .= "<br/>";
					    $exif_info .=  "Focal Length : ";
					    if(isset($exif_data["FocalLength"])){
						    $exif_FocalLength=create_function('','return number_format(round('.$exif_data["FocalLength"].',1),1);');
						    $exif_info .= $exif_FocalLength();
					    } else $exif_info .= "n/a";
					    
					    $exif_info .= "mm<br/>";
				        $exif_info .= "Flash fired : ";
					    if(isset($exif_data["Flash"])){
						   $exif_info .= (($exif_data["Flash"] & 1) ? 'YES' : 'NO');
					    }else $exif_info .= "n/a";
					    
					    $exif_info .= "<br/>";
				    }else $exif_info .= "No EXIF information in image<br/>";
				    
			    }
                /* -------------------------------------- */
			    $source_img = $image_file;
	            if (!$image_file = imagecreatefromstring(file_get_contents($source_img))) exit;
			    
				if (isset($CFG->config['rotate_images']) and $CFG->config['rotate_images'] == 'YES' and isset($exif_data["Orientation"])){
				    switch ($exif_data["Orientation"]){
					case 2 :{
								$rotate = @imagecreatetruecolor($image_width, $image_height);
								imagecopyresampled($rotate, $image_file, 0, 0, $image_width-1, 0, $image_width, $image_height, -$image_width, $image_height);
								imagedestroy($image_file);
								$image_changed = TRUE;
								break;
							}
					case 3 :{
								$rotate = imagerotate($image_file, 180, 0);
								imagedestroy($image_file);
								$image_changed = TRUE;
								break;
							}
					case 4 :{
								$rotate = @imagecreatetruecolor($image_width, $image_height);
								imagecopyresampled($rotate, $image_file, 0, 0, 0, $image_height-1, $image_width, $image_height, $image_width, -$image_height);
								imagedestroy($image_file);
								$image_changed = TRUE;
								break;
							}
					case 5 :{
								$rotate = imagerotate($image_file, 270, 0);
								imagedestroy($image_file);
								$image_file = $rotate;
								$rotate = @imagecreatetruecolor($image_height, $image_width);
								imagecopyresampled($rotate, $image_file, 0, 0, 0, $image_width-1, $image_height, $image_width, $image_height, -$image_width);
								$image_changed = TRUE;
								break;
							}
					case 6 :{
								$rotate = imagerotate($image_file, 270, 0);
								imagedestroy($image_file);
								$image_changed = TRUE;
								break;
							}
					case 7 :{
								$rotate = imagerotate($image_file, 90, 0);
								imagedestroy($image_file);
								$image_file = $rotate;
								$rotate = @imagecreatetruecolor($image_height, $image_width);
								imagecopyresampled($rotate, $image_file, 0, 0, 0, $image_width-1, $image_height, $image_width, $image_height, -$image_width);
								$image_changed = TRUE;
								break;
							}
					case 8 :{
								$rotate = imagerotate($image_file, 90, 0);
								imagedestroy($image_file);
								$image_changed = TRUE;
								break;
							}
					default: $rotate = $image_file;
				    }
			        $image_file = $rotate;
		        }
	        }
		$exif_info .='</div></div>';
		
		if($echo) echo $exif_info;
		else return $exif_info;
	}
?>