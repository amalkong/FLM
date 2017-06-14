<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    if (($roleID < 5))  {
        echo'<div class="box"><div class="message error"><p>Access Denied!,You are not authorised to access this section, your user role does not permit you to use the file browser</p></div></div>';
	    include(BASE_PATH.'footer.php');
	    exit;
    }
    /* Get icon relating to the extension of the file */
    function GetIcon($file,$iconUrl,$size='32'){
		if (substr($file, strlen($file) - 3, 3) == "php") return('<img border="0" src="'.$iconUrl.'php.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 3, 3) == "txt") return('<img border="0" src="'.$iconUrl.'txt.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 3, 3) == "sql") return('<img border="0" src="'.$iconUrl.'sql.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 3, 3) == "htm" || substr($file, strlen($file) - 4, 4) == "html" ) return('<img border="0" src="'.$iconUrl.'htm.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 1, 1) == "h" || substr($file, strlen($file) - 1, 1) == "c" ) return('<img border="0" src="'.$iconUrl.'cpp.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 3, 3) == "css") return('<img border="0" src="'.$iconUrl.'css.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 2, 2) == "js") return('<img border="0" src="'.$iconUrl.'js.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 3, 3) == "ini") return('<img border="0" src="'.$iconUrl.'ini.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 2, 2) == "py" || substr($file, strlen($file) - 3, 3) == "pyt") return('<img border="0" src="'.$iconUrl.'py.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 3, 3) == "xls" || substr($file, strlen($file) - 4, 4) == "xlsx") return('<img border="0" src="'.$iconUrl.'xls.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 3, 3) == "ppt" || substr($file, strlen($file) - 4, 4) == "pptx") return('<img border="0" src="'.$iconUrl.'ppt.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 3, 3) == "fla" || substr($file, strlen($file) - 3, 3) == "flv") return('<img border="0" src="'.$iconUrl.'flvs.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 2, 2) == "pl" || substr($file, strlen($file) - 2, 2) == "pm") return('<img border="0" src="'.$iconUrl.'perl.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 3, 3) == "xml") return('<img border="0" src="'.$iconUrl.'xml.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 3, 3) == "pdf") return('<img border="0" src="'.$iconUrl.'pdf.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 3, 3) == "swf") return('<img border="0" src="'.$iconUrl.'swf.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 3, 3) == "zip" || substr($file, strlen($file) - 3, 3) == "rar" || substr($file, strlen($file) - 2, 2) == "gz") return('<img border="0" src="'.$iconUrl.'zip.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 2, 2) == "sh") return('<img border="0" src="'.$iconUrl.'sh.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 3, 3) == "tgz") return('<img border="0" src="'.$iconUrl.'tgz.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 3, 3) == "jar") return('<img border="0" src="'.$iconUrl.'jar.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 4, 4) == "java") return('<img border="0" src="'.$iconUrl.'java.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 3, 3) == "bmp" || substr($file, strlen($file) - 3, 3) == "gif" || substr($file, strlen($file) - 3, 3) == "png" || substr($file, strlen($file) - 3, 3) == "jpg" || substr($file, strlen($file) - 4, 4) == "jpeg") return('<img border="0" src="'.$iconUrl.'image.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 3, 3) == "mid" || substr($file, strlen($file) - 4, 4) == "midi") return('<img border="0" src="'.$iconUrl.'midi.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 3, 3) == "mp3" || substr($file, strlen($file) - 3, 3) == "aac" || substr($file, strlen($file) - 3, 3) == "ogg" || substr($file, strlen($file) - 3, 3) == "wav" || substr($file, strlen($file) - 3, 3) == "wma") return('<img border="0" src="'.$iconUrl.'audio.png" width="'.$size.'" height="'.$size.'" />');
        elseif(substr($file, strlen($file) - 3, 3) == "avi" || substr($file, strlen($file) - 3, 3) == "mkv" || substr($file, strlen($file) - 3, 3) == "mov" || substr($file, strlen($file) - 3, 3) == "mpg" || substr($file, strlen($file) - 4, 4) == "mpeg" || substr($file, strlen($file) - 4, 4) == "wbem") return('<img border="0" src="'.$iconUrl.'video.png" width="'.$size.'" height="'.$size.'" />');
        else return('<img border="0" src="'.IMG_URL.'other/unknown.png" />');
	}
	/* Get MIME-type for file */
	 function get_mimetype($filename) {
        global $MIMEtypes;
        reset($MIMEtypes);
        $extension = strtolower(substr(strrchr($filename, "."),1));

        if ($extension == "") return "Unknown/Unknown";

        while (list($mimetype, $file_extensions) = each($MIMEtypes)) foreach (explode(" ", $file_extensions) as $file_extension) if ($extension == $file_extension) return $mimetype;

        return "Unknown/Unknown";
    }
	/* Get current zoom level */
    function get_current_zoom_level($current_zoom_level, $zoom) {
        global $ZoomArray;
        reset($ZoomArray);

        while(list($number, $zoom_level) = each($ZoomArray))
            if ($zoom_level == $current_zoom_level)
            if (($number+$zoom) < 0) return $number;
            else if (($number+$zoom) >= count($ZoomArray)) return $number;
            else return $number+$zoom;
    }
        function showDirSelector($actpath='.',$echo=false){
		    $selector = NULL;
		    $selector .= '<div class="grid-4 wrapper">   
                <form action="" method="post"  class="push-center">
				    <input class="grid-3" name="filePath" type="text" size="50" value="'.$actpath.'" />
					<input class="update" type="submit" name="submitPath" title="List content" value="" />
                </form>
            </div>';
			
			if($echo) echo $selector;
			else return $selector;
        }
		function Check_For_Slash($path,$convertSlash=false) {
            if (substr($path, (strlen($path) - 1), 1) != "/") {
                $path = $path . "/";
            }
			if($convertSlash == true){
			    $path = str_replace('\\','/',$path);
			}
            return($path);
        }
	
		function showCreateDirForm($echo=false){
		    $output = NULL;
		    //$PB_output .='<span>Create A New Directory</span>';
		    $output .='<div class="grid-4 wrapper">   
	            <form action="" method="post" class="push-center">
                    <input class="grid-3 left-1" name="directory_name" type="text" size="50" value="Enter New Directory Name"  onfocus="if (this.value == \'Enter New Directory Name\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'Enter New Directory Name\';}" />
                    <input class="createdir" type="submit" name="cf_submitBtn" title="Create New Directory" value="" />
                </form>
	        </div>';
	        if(isset($_POST['cf_submitBtn'])){
	            $output .='<div class="left-2">';
				    if(empty($_POST['directory_name'])) $output .= '<div class="message error"><p>No directory name submitted.</p></div>';
				    else if(file_exists(Check_For_Slash(getActualPath(),true).$_POST['directory_name'])) $output .= '<div class="message error"><p>A directory with the name <strong>'.$_POST['directory_name'].'</strong>, already exists.</p></div>';
		            else {
					   if(mkdir(Check_For_Slash(getActualPath(),true).$_POST['directory_name'],DIR_WRITE_MODE)) $output .= '<div class="message"><p>The directory <strong>'.$_POST['directory_name'].'</strong>, has been created successfully</p></div>';
				       else $output .= '<div class="message error"><p><strong class="em">An error occured!, don&rsquo;t know what exactly what caused it.</strong></p></div>';
				    }
				$output .='</div>';
            }
			if($echo) echo $output;
			else return $output;
		}

	function getActualPath(){
        if (isset($_POST['submitPath'])) $dir = isset($_POST['filePath']) ? trim($_POST['filePath']) : getcwd();
        else if (isset($_GET['filePath'])) $dir = isset($_GET['filePath']) ? trim($_GET['filePath']) : getcwd();
        else $dir = getcwd();

        if (!file_exists($dir)) $dir = getcwd();
        return str_replace('\\','/',$dir); //return $dir;
    }
	/* Checks whether a file is viewable */
	function is_viewable_file($filename) {
        $ViewableFiles = "jpeg jpe jpg gif png bmp webp";
        $extension = strtolower(substr(strrchr($filename, "."),1));

        foreach(explode(" ", $ViewableFiles) as $type) if ($extension == $type) return TRUE;
        return FALSE;
    }
	/* Authenticate user using cookies */
    function authenticate_user() {
        global $username, $password;
        if (isset($_COOKIE['cookie_username']) && $_COOKIE['cookie_username'] == $username && isset($_COOKIE['cookie_password']) && $_COOKIE['cookie_password'] == md5($password)) return TRUE;
        else return FALSE;
    }
        /* checks whether the file is hidden. */
    function is_hidden_file($path) {
        global $hide_file_extension, $hide_file_string, $hide_directory_string;
        $extension = strtolower(substr(strrchr($path, "."),1));

        foreach ($hide_file_extension as $hidden_extension) if ($hidden_extension == $extension) return TRUE;

        foreach ($hide_file_string as $hidden_string) if (stristr(basename($path), $hidden_string)) return TRUE;

        foreach ($hide_directory_string as $hidden_string) if (stristr(dirname($path), $hidden_string)) return TRUE;

        return FALSE;
    }
    /* Checks whether the directory is hidden. */
    function is_hidden_directory($path)	{
        global $hide_directory_string;

        foreach ($hide_directory_string as $hidden_string) if (stristr($path, $hidden_string)) return TRUE;

        return FALSE;
    }
    function Browser_Image_info($image_file,$echo=false){
	    global $PB_CONFIG,$dateFormat,$UTIL;
		$PB_output = NULL;
		$exif_info = "";
	    if (!$image = imagecreatefromstring(file_get_contents($image_file))) exit;
			
	    $image_width = imagesx($image);
	    $image_height = imagesy($image);
	    $PB_output .='<div class="image-info"><div class="basic-info">image name : '.basename($image_file).
	        '<br/>Created : '.date($dateFormat,filemtime($image_file)).
	        '<br/>size : '.$UTIL->GetFilesize($image_file).
	        '<br/>dimension : '.$image_width.'x'.$image_height.
	        '<br/></div><div class="exif-info">Exif Info : ';
	        
		    if (function_exists("read_exif_data")){
			    if ($PB_CONFIG["SHOW_EXIF_INFO"] == 'YES'){
				    $exif_data = @exif_read_data( $image_file, "IFD0");
				    if ($exif_data !== FALSE){
					    $exif_info .= "EXIF Date : " . $exif_data["DateTimeOriginal"] ."<br/>";
					    $exif_info .= "Camera : " . $exif_data["Model"] ."<br>";
					    $exif_info .= "ISO : ";
					    if(isset($exif_data["ISOSpeedRatings"])) $exif_info .= $exif_data["ISOSpeedRatings"];
					    else $exif_info .= "n/a";
					    
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
					    }else $exif_info .= "n/a";
					    
					    $exif_info .= "mm<br/>";
				        $exif_info .= "Flash fired : ";
					    if(isset($exif_data["Flash"])){
						   $exif_info .= (($exif_data["Flash"] & 1) ? 'YES' : 'NO');
					    }else $exif_info .= "n/a";
					    
					    $exif_info .= "<br/>";
				    } else $exif_info .= "No EXIF informatin in image<br/>";
				    
				    $PB_output .= $exif_info;
			    } else $PB_output .= '<div class="message user_status"><p><em>Show EXIF Info is disabled</em>, enable this option to view an image EXIF information, if it is available</p></div>';
               /* -------------------------------------- */
			   $source_img = $image_file;
	            if (!$image_file = imagecreatefromstring(file_get_contents($source_img))) exit;
			    
				if ($PB_CONFIG["ROTATE_IMAGES"] == 'YES' and isset($exif_data["Orientation"])){
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
					$PB_output .= $image_file;
		        }
	        } else $PB_output .= '<div class="message error"><p>The function <pre><em>[read_exif_data()]</em></pre> doesn&rsqou;t exists.</p></div>';
		$PB_output .= '</div></div>';
		if($echo) echo $PB_output;
		else return $PB_output;
	}
/* ----------------------------- END FUNCTIONS -------------------------------------------- */
    //error_reporting(0);
	//error_reporting( E_WARNING | E_PARSE );
	$PB_CONFIG['theme'] = '';
	$PB_CONFIG['index_page'] = 'filemanager.php';
	$PB_CONFIG['display_dirsize'] = 'NO';
	$PB_CONFIG["SHOW_EXIF_INFO"] = 'NO';
	$PB_CONFIG["ROTATE_IMAGES"] = 'NO';
	$PB_CONFIG["max_width"] = '800px';
	
    /* --------------------------------------------------------- */
	# Access configuration
    # Each variable can be set to either TRUE or FALSE.
    $AllowCreateFile    = TRUE;
    $AllowCreateFolder  = TRUE;
    $AllowDownload      = TRUE;
    $AllowRename        = TRUE;
    $AllowUpload        = TRUE;
    $AllowDelete        = TRUE;
    $AllowView          = TRUE;
    $AllowEdit          = TRUE;
	$PBD_auth           = TRUE;
	
    $username       = "amalkong";
    $password       = "Unknownthugz9";
    $separator2 = '&nbsp;|&nbsp;';
	# Hidden files and directories
    $sfxs = array(".bat",".csv",".css",".cpp",".doc",".docx",".htm",".html",".java",".js",".inc",".ini",".php",".pl",".ppt",".py",".pyt",".sql",".txt",".vb",".xml","","");
    $hide_file_extension    = array("exec","dll2");
    $hide_file_string       = array(".htaccess", ".COM", ".ini",".db", ".sys", ".SYS", ".BAT");
    $hide_directory_string  = array("secret dir","Program Files","SERVER","xampp");
	$MIMEtypes = array(
     "application/andrew-inset"=> "ez","application/mac-binhex40"=> "hqx","application/mac-compactpro"=> "cpt",
     "application/msword"=> "doc","application/octet-stream"=> "bin dms lha lzh exe class so dll","application/oda"=> "oda","application/pdf"=> "pdf","application/postscript"=> "ai eps ps","application/smil"=> "smi smil",
     "application/vnd.ms-excel"=> "xls","application/vnd.ms-powerpoint"=> "ppt","application/vnd.wap.wbxml"=> "wbxml","application/vnd.wap.wmlc"=> "wmlc","application/vnd.wap.wmlscriptc" => "wmlsc",
     "application/x-bcpio"=> "bcpio","application/x-cdlink"=> "vcd","application/x-chess-pgn"=> "pgn","application/x-cpio"=> "cpio","application/x-csh"=> "csh",
     "application/x-director"=> "dcr dir dxr","application/x-dvi"=> "dvi","application/x-futuresplash"=> "spl","application/x-gtar"=> "gtar","application/x-hdf"=> "hdf",
     "application/x-javascript"=> "js","application/x-koan"=> "skp skd skt skm","application/x-latex"=> "latex","application/x-netcdf"=> "nc cdf",
     "application/x-sh"=> "sh","application/x-shar"=> "shar","application/x-shockwave-flash"=> "swf","application/x-stuffit"=> "sit",
     "application/x-sv4cpio"=> "sv4cpio","application/x-sv4crc"=> "sv4crc",
     "application/x-tar"=> "tar","application/x-tcl"=> "tcl","application/x-tex"=> "tex","application/x-texinfo"=> "texinfo texi",
	 "application/x-troff"=> "t tr roff","application/x-troff-man"=> "man","application/x-troff-me"=> "me","application/x-troff-ms"=> "ms",
	 "application/x-ustar"=> "ustar","application/x-wais-source"=> "src","application/zip"=> "zip",
     "audio/basic"=> "au snd","audio/midi"=> "mid midi kar","audio/mpeg"=> "mpga mp2 mp3","audio/x-aiff"=> "aif aiff aifc",
     "audio/x-mpegurl"=> "m3u","audio/x-pn-realaudio"=> "ram rm","audio/x-pn-realaudio-plugin"=> "rpm",
     "audio/x-realaudio"=> "ra","audio/x-wav"=> "wav",
     "chemical/x-pdb"=> "pdb","chemical/x-xyz"=> "xyz",
     "image/bmp"=> "bmp","image/gif"=> "gif","image/ief"=> "ief",
     "image/jpeg"=> "jpeg jpg jpe","image/png"=> "png","image/tiff"=> "tiff tif","image/vnd.wap.wbmp"=> "wbmp","image/x-cmu-raster"=> "ras",
     "image/x-portable-anymap"=> "pnm","image/x-portable-bitmap"=> "pbm","image/x-portable-graymap"=> "pgm","image/x-portable-pixmap"=> "ppm",
	 "image/x-rgb"=> "rgb","image/x-xbitmap"=> "xbm","image/x-xpixmap"=> "xpm","image/x-xwindowdump"=> "xwd",
     "model/iges"=> "igs iges","model/mesh"=> "msh mesh silo","model/vrml"=> "wrl vrml",
     "text/css"=> "css","text/html"=> "html htm","text/plain"=> "asc txt","text/richtext"=> "rtx","text/rtf"=> "rtf","text/sgml"=> "sgml sgm","text/tab-separated-values"=> "tsv",
     "text/vnd.wap.wml"=> "wml","text/vnd.wap.wmlscript"=> "wmls","text/x-setext"=> "etx","text/xml"=> "xml xsl",
     "video/mpeg"=> "mpeg mpg mpe","video/quicktime"=> "qt mov","video/vnd.mpegurl"=> "mxu","video/x-msvideo"=> "avi","video/x-sgi-movie"=> "movie",
     "x-conference/x-cooltalk"=> "ice",
    );

    # Zoom levels for PHPFM's image viewer.
    $ZoomArray = array(
        5, 7, 10, 15, 20, 30, 50, 70,
        100,       # Base zoom level (do not change)
        150, 200, 300, 500, 700, 1000
    );
/* ----------------------------- END CONFIG -------------------------------------------- */
	$i = 0;
	$dirList = array();
    $fileList = array();
	$files = array();
	//$path = isset($_GET['filePath']) ? $_GET['filePath'] : '';
	$path = getActualPath();
	$path = $UTIL->Check_For_Slash($path,true);
	$back = getActualPath();
    $dn=dirname($path);
	$back_folder_name = basename($back);
	$current_dir=basename($path).'/';
	$_dir=basename(dirname($path)).'/';
    $filename = isset($_GET['fileName']) ? $_GET['fileName'] : '';
	$mimetype = get_mimetype($path.$filename);
	
	$files_icon_url = IMG_URL.'files/';
	$other_icon_url = IMG_URL.'other/';
	$button_icon_url = IMG_URL.'btn/';
	$return_url = $_SERVER['PHP_SELF'].'?mode='.MODE.'&amp;section=manage&amp;action=browser&amp;filePath='.$back;
	//$edit_url = '?action=edit&amp;filePath='.$path.'&amp;fileName='.$filename;
	$dateFormat = 'd-m-Y H:i:s';
	$pic_info_path = $path.$filename;
    $dirs = explode('/', $path);
	$path_prefix = $dirs[0] . '/'.$dirs[1] . '/';
	for($x=2;$x<sizeof($dirs);$x++){
	   $app_path[] = $dirs[$x];
	}
    $app_path_1 = implode($app_path,'/');
	
	if(file_exists($path_prefix.$app_path_1.$filename)) $view_pic = $app_path_1.$filename;
	else if(file_exists($path_prefix.$_dir.$current_dir.$filename)) $view_pic = $_dir.$current_dir.$filename;
	else if(file_exists($path_prefix.$current_dir.$filename)) $view_pic = $current_dir.$filename;
	else if(file_exists($path_prefix.$_dir.$filename)) $view_pic = $_dir.$filename;
	else $view_pic = './'.$filename;

    if(!isset($PB_output)) $PB_output = NULL;
    $BA = ((isset($_POST['ba'])) ? $_POST['ba'] : (isset($_GET['ba'])) ? $_GET['ba'] : '');
    define('BACTION',$BA);
	//$PB_output .= '<div class="content">';
	if(BACTION == ''){
	        $PB_output .= '<div class="box">';
                $PB_output .= '<p>This a self-contained hidden page that should be used wisely. &lsquo;Then why include it ?&rsquo;, you may ask. 
                    Well let&rsquo;s say, sometimes when you delete a league or even an article, some files related to the deleted item, may be left back.
				</p>
	            <p>Although rare, it may happen, this page allows you to browse the entire site folder(permitted you configure the page to do so), or browse selected/browsable folders to delete or even edit any file found.
	                The danger lies in the fact that you may accidentally delete some vital system files if you don&rsquo;t know what you are doing, so be careful what file you modify.
				</p>';
	        $PB_output .= '</div>';
	        $PB_output .= '<div class="spacer">&nbsp;</div>';
	        $PB_output .= '<div class="box2">';
            $PB_output .= '<a name="TOP" id="TOP"></a>';
            if ($handle = @opendir($path)) {
			    while (false !== ($file = readdir($handle))) {
                    if ($file != '.' && $file != '..') {
                        $file = $UTIL->Check_For_Slash($path,true).$file;
						if(is_file($file) && !is_hidden_file($file)) $fileList[] = $file;
                        elseif (is_dir($file) && !is_hidden_directory($file)) $dirList[] = $file;
                    }
                }
				closedir($handle);
				
				$PB_output .= '<center><table id="main_table" class="table browser-table">';
                    $PB_output .='<thead>';
					$PB_output .= '<tr class=" table-header"><th colspan="4">'.strtoupper('File Manager').'</th><th colspan="4">Current Directory &raquo; <span class="i b u">'.$current_dir.'</span></th></tr>';
				    $PB_output .= '<tr>';
					    $PB_output .= '<td><a href="'.$_SERVER['PHP_SELF'].'?mode='.MODE.'&amp;section='.SECTION.'&amp;action=browser&amp;filePath='.$dn.'" title="Up one level to the directory ['.basename($dn).']"><img src="'.$other_icon_url.'folder_parent.png" alt="Dir" /></a></td>';
				        $PB_output .= '<td colspan="4" align="center">'.showDirSelector($path) .'</td>';
				        $PB_output .= '<td colspan="3" align="center">'.showCreateDirForm().'</td>';
				    $PB_output .='</tr>';
                    $PB_output .= '<tr class="table-header"><td width="40px">Delete File</td><td width="40px">Edit File</td><td width="40px">File Type</td><td>File Name</td><td>File Size</td><td>Perm.</td><td>Modified Date</td><td>Download</td></tr>';
					$PB_output .='</thead>';
					$PB_output .='<tbody>';
                    if (count($dirList) == 0 AND count($fileList) == 0) $PB_output .= '<tr><td colspan="7" align="center" width="100%" ><b>No Files or Directory found</b></td></tr>';
				    else
					
                if (sizeof($dirList) > 0){
                    sort($dirList);  
                    foreach ($dirList as $dir) {
                        $size = $UTIL->size('dir',$dir);
                        $perm = substr(sprintf('%o', fileperms($dir)), -4);
						//$permissions = decoct(fileperms($dir)%01000);
                        if (function_exists('mime_content_type')) $type = mime_content_type($dir);
                        else $type = filetype($dir);       
                        $date = date ($dateFormat, filemtime($dir));
                        $name = basename($dir);
					    
						$delete_url = $_SERVER['PHP_SELF'].'?mode='.MODE.'&amp;section='.SECTION.'&amp;action=browser&amp;ba=delete&amp;filetype=dir&amp;filePath='.$path.'&amp;dirName=' . urlencode($name);
						$rename_url = $_SERVER['PHP_SELF'].'?mode='.MODE.'&amp;section='.SECTION.'&amp;action=browser&amp;ba=rename&amp;filePath='.$path.'&amp;dirName=' . basename($name);
						$download_url = BASE_URL.'download.php?filePath='.$path.'&amp;dirName=' . basename($name);
						
						if ($i++%2) $PB_output .= '<tr class="u">';
                        else $PB_output .= '<tr class="tr2 u">';
                            //$PB_output .= '<td width="40px">'.$type.'</td>';
					        $PB_output .= '<td width="40px"><a href="'.$delete_url.'"><img src="'.$other_icon_url.'edit_delete.png" title="Delete : '.$name.'"  alt="Delete : '.$name.'" /></a></td>';
						    $PB_output .= '<td width="40px"><a href="'.$rename_url.'" target="self"><img src="'.$other_icon_url.'edit_rename.png" title="Rename : '.$name.'"  alt="Rename : '.$name.'" /></a></td>';
						    $PB_output .= '<td width="40px"><img src="'.$other_icon_url.'folder_open.png" alt="Dir"/></td>';
						    $PB_output .= '<td class="fname"><a href="'.$_SERVER['PHP_SELF'].'?mode='.MODE.'&amp;section='.SECTION.'&amp;action=browser&amp;filePath='.$dir.'"><font class="dir-style" title="Browse the directory : '.$name.'">'.$name.'</font></a></td>';
						    $PB_output .= '<td>'.$size.'</td>';
						    $PB_output .= '<td>'.$perm.'</td>';	
						    $PB_output .= '<td>'.$date.'</td>';	
					        //$PB_output .= '<td><a href="'.$download_url.'" target="self"><img src="'.$other_icon_url.'download.gif" title="Download : '.$name.'"  alt="Download : '.$name.'" /></a></td>';
					        $PB_output .= '<td></td>';
					    $PB_output .= '</tr>';
					}
                }
                if (sizeof($fileList) > 0){
                    sort($fileList);  
                    foreach ($fileList as $file) {
						$exClass = $UTIL->getSuffix($file);
						if (preg_match('/'.$exClass.'/',$file)) {
                            if($UTIL->isValidExt($exClass,$sfxs)){
							    $exClass = str_replace('.','',$exClass);
								$class = $exClass;
                            }else if($UTIL->isValidExt($exClass,$ifxs)) $class = 'image';
                            else $class = 'unknown';
				        }
                        $size = $UTIL->size('file',$file);
                        $perm = substr(sprintf('%o', fileperms($file)), -4);
						
                        if (function_exists('mime_content_type')) $type = mime_content_type($file);
                        else $type = filetype($file);
						
                        $date = date ($dateFormat, filemtime($file));
                        $name = basename($file);
						if($UTIL->isValidExt($file,$sfxs)) $fileType = 'script';
						elseif($UTIL->isValidExt($file,$tfxs)) $fileType = 'plaintext';
						elseif($UTIL->isValidExt($file,$ifxs)) $fileType = 'image';
						else $fileType = 'unknown';
						$delete_url = $_SERVER['PHP_SELF'].'?mode='.MODE.'&amp;section='.SECTION.'&amp;action=browser&amp;ba=delete&amp;filetype='.$fileType.'&amp;filePath='.$path.'&amp;fileName=' . urlencode($file);
			            $edit_url = $_SERVER['PHP_SELF'].'?mode='.MODE.'&amp;section='.SECTION.'&amp;action=browser&amp;ba=edit&amp;filetype='.$fileType.'&amp;&amp;filePath='.$path.'&amp;fileName=' . basename($file);
			            $rename_url = $_SERVER['PHP_SELF'].'?mode='.MODE.'&amp;section='.SECTION.'&amp;action=browser&amp;ba=rename&amp;filetype='.$fileType.'&amp;filePath='.$path.'&amp;fileName=' . basename($file);
			            $view_url = $_SERVER['PHP_SELF'].'?mode='.MODE.'&amp;section='.SECTION.'&amp;action=browser&amp;ba=view&amp;filetype='.$fileType.'&amp;filePath='.$path.'&amp;fileName=' . basename($file);
			            $download_url = BASE_URL.'download.php?filetype='.$fileType.'&amp;filePath='.$path.'&amp;fileName=' . basename($file);
						
						if ($i++%2) $PB_output .= '<tr class="u">';
                        else $PB_output .= '<tr class="tr2 u">';
						    //$PB_output .= '<td class="c" width="40px">'.$type.'</td>';
						    if($UTIL->isValidExt($file,$sfxs) OR $UTIL->isValidExt($file,$ifxs)){
						        $PB_output .= '<td width="40px"><a href="'.$delete_url.'"><img src="'.$other_icon_url.'edit_delete.png" title="Delete : '.$name.'"  alt="Delete : '.$name.'" /></a></td>';
							    if($UTIL->isValidExt($file,$sfxs)) $PB_output .= '<td width="40px"><a href="'.$edit_url.'" target="self"><img src="'.$other_icon_url.'edit.png" title="Edit : '.$name.'"  alt="Edit : '.$name.'" /></a></td><td width="40px">'. GetIcon($file,$files_icon_url).'</td><td class="fname"><a href="'.$view_url.'" target="_self"><font class="file-style '.$class.'" title="View : '.$name.'">'.$name.'</font></a></td><td>'.$size.'</td><td>'.$perm.'</td><td>'.$date.'</td>';
							    elseif($UTIL->isValidExt($file,$ifxs)) $PB_output .= '<td width="40px"><a href="'.$rename_url.'" target="self"><img src="'.$other_icon_url.'edit_rename.png" title="Rename : '.$name.'"  alt="Rename : '.$name.'" /></a></td><td width="40px">'. GetIcon($file,$files_icon_url).'</td><td class="fname"><a href="'.$view_url.'" target="_self"><font class="file-style '.$class.'" title="View : '.$name.'">'.$name.'</font></a></td><td>'.$size.'</td><td>'.$perm.'</td><td>'.$date.'</td>';
								$PB_output .='<td><a href="'.$download_url.'" target="self"><img src="'.$other_icon_url.'download.gif" title="Download : '.$name.'"  alt="Download : '.$name.'" /></a></td>';
							} else {
						        $PB_output .= '<td width="40px"><img src="'.$other_icon_url.'lock_closed.gif" title="Can&rsqou;t Delete : '.$name.'"  alt="Delete : '.$name.'" /></td><td width="40px"><img src="'.$other_icon_url.'no_access.gif" title="Can&rsqou;t Edit : '.$name.'"  alt="Edit : '.$name.'" /></td><td width="40px">'. GetIcon($file,$files_icon_url).'</td><td class="fname"><font class="file-style '.$class.'" title="View : '.$name.'">'.$name.'</font></td><td>'.$size.'</td><td>'.$perm.'</td><td>'.$date.'</td><td><img src="'.$other_icon_url.'no_access.gif" title="Can&rsqou;t download : '.$name.'"  alt="Download : '.$name.'" /></td>';
						   }
						$PB_output .= "</tr>";
					}
					
                }
				$PB_output .= "</tbody>";
				$PB_output .=  '<tfoot><tr class="table-footer"><td><img src="'.$other_icon_url.'arrow_up.png" alt="Return to top" /></td><td align="center" colspan="7"><a href="#TOP">RETURN TO TOP</a></td></tr></tfoot>';	
                $PB_output .=  "</table></center>\n";
            } else $PB_output .= '<div class="message user_status">Could not open directory</div>';
	        $PB_output .= '</div>';
	} elseif(BACTION == 'view'){
		if ($AllowView && isset($_GET['fileName'])) {
		    $show = file($path.$filename);
	        $size = $UTIL->size('file',$path.$filename);
	        //$modified = date('d-m-Y H:i:s',filemtime($path.$filename));
	        $modified = date($dateFormat,filemtime($path.$filename));
			$line_count = count($show);
            if ( $UTIL->isValidExt($path.$filename,$tfxs) ){
			    $PB_output .=  '<div class="view-panel">
			        <span class="column view-panel-filename">You are now viewing the text file &raquo;&nbsp;'.$filename.'</span>'.$separator2.'
			        <span class="column view-panel-filesize">File Size &raquo;&nbsp;'.$size.'</span>'.$separator2.'
			        <span class="column view-panel-linecount">Lines &raquo;&nbsp;'.$line_count.'</span>'.$separator2.'
			        <span class="column view-panel-date">Last Modified &raquo;&nbsp;'.$modified.'</span>
			    </div>';
		        $PB_output .= '<div class="panel">';
		            $PB_output .= '<ul class="UL-list push-3">';
		            foreach ($show as $line_num => $line) {
		                if ($i++%2) $PB_output .=  "<li class='even'>";
                        else $PB_output .=  "<li class='odd'>";
                        $PB_output .=  "<pre><span class='line-number'>#{$line_num}</span> : <span class='line-text'>" . htmlspecialchars($line) . "</span></li></pre>\n";
		                // $PB_output .=  '<div class="line-number">#'.$line_num.' :-</div> ' . htmlspecialchars($line) . '<br />';
				    }
		            $PB_output .= '</ul>';
		        $PB_output .= '</div>';
	        } elseif ( $UTIL->isValidExt($path.$filename,$sfxs) ){
			    $PB_output .= '<div class="view-panel">
			        <span class="column view-panel-filename">You are now viewing the script file &raquo;&nbsp;'.$filename.'</span>'.$separator2.'
			        <span class="column view-panel-filesize">File Size &raquo;&nbsp;'.$size.'</span>'.$separator2.'
			        <span class="column view-panel-linecount">Lines &raquo;&nbsp;'.$line_count.'</span>'.$separator2.'
			        <span class="column view-panel-date">Last Modified &raquo;&nbsp;'.$modified.'</span>
			        <span class="column right-1 push-3"><a href="javascript:history.back()" class="button return" title="click to return to previous page">Return</a></span>
			    </div>';
		        $PB_output .= '<div class="panel">';
		            $PB_output .= '<ul class="UL-list push-3">';
		            foreach ($show as $line_num => $line) {
		                if ($i++%2) $PB_output .=  "<li class='even'>";
                        else $PB_output .=  "<li class='odd'>";
                        $PB_output .=  "<code><span class='line-number'>#{$line_num}</span> : <span class='line-text'>" . htmlspecialchars($line) . "</span></li></code>\n";
		                // $PB_output .=  '<div class="line-number">#'.$line_num.' :-</div> ' . htmlspecialchars($line) . '<br />';
			    	}
		            $PB_output .= '</ul>';
		        $PB_output .= '</div>';
	        } elseif ( $UTIL->isValidExt($filename,$ifxs) ){
		        if (!$image_file = imagecreatefromstring(file_get_contents($path.$filename))) exit;
	            
	            $original_img_width = imagesx($image_file);
	            $original_img_height = imagesy($image_file);
			
                $PB_output .= '<table class="grid-4">';
                    $PB_output .= '<tr>';
					    $PB_output .= '<td class="iheadline" align="left" height="21"><a href="'.$return_url.'" class="button return">Back</a></td>';
                        $PB_output .= '<td class="iheadline" height=21>&nbsp;Viewing &raquo; '.$filename.'</td>';
                        $PB_output .= '<td class="iheadline" align="right" height=21>File MymeType : '.$mimetype.'</td>';
                    $PB_output .= '</tr>';
                    $PB_output .= '<tr>';
                        $PB_output .= '<td align="center" valign="top" colspan="3"><center><br/>';
                    
                        if (is_file($path.$filename) && is_viewable_file($filename)) {
                            $image_info = GetImageSize($path.$filename);
							//imagesx($image_file)
                            $size = isset($_GET['size']) ? $_GET['size'] : 100;
                            $zoom_in_level = get_current_zoom_level($size, 1);
                            $zoom_out_level = get_current_zoom_level($size, -1);
							$zoom_in = $ZoomArray[$zoom_in_level];
                            $zoom_out = $ZoomArray[$zoom_out_level];
                            $image_width = $image_info[0] * $size / 100;
                            $image_height = $image_info[1] * $size / 100;
							
                            if ($open_path = opendir($path)) {
                                while ($file = readdir($open_path)) if (is_file($path.$file) && is_viewable_file($file)) $files[] = $file;
                                closedir($open_path);
                                sort($files);
								
                                if (count($files)>1) {
                                    for($i=0;$files[$i]!=$filename;$i++);
                                    if ($i==0) $prev = $files[$i+count($files)-1];
                                    else $prev = $files[$i-1];
                                    if ($i==(count($files)-1)) $next = $files[$i-count($files)+1];
                                    else $next = $files[$i+1];
                                } else {
                                    $prev = $filename;
                                    $next = $filename;
                                }
                            }
						    
                            $PB_output .= '<table class="table menu grid-4">';
                                $PB_output .= '<tr class="">';
								    $PB_output .= '<td align="left" class="table-view-link"><a href="?mode='.MODE.'&amp;section='.SECTION.'&amp;action=browser&amp;ba=view&amp;filePath='.$path.'&amp;fileName='.$prev.'&amp;size='.$size.'" class="icon previous left-1" title="Previous Image" >&nbsp;</a></td>';
                                    $PB_output .= '<td align="center" class="table-view-link"><a href="?mode='.MODE.'&amp;section='.SECTION.'&amp;action=browser&amp;ba=view&amp;filePath='.$path.'&amp;fileName='.$filename.'&amp;size='.$zoom_in.'" class="icon zoom_in" title="Zoom Image In">&nbsp;</a></td>';
                                    $PB_output .= '<td align="center" class="table-view-link"><a href="?mode='.MODE.'&amp;section='.SECTION.'&amp;action=browser&amp;ba=view&amp;filePath='.htmlentities(rawurlencode($path)).'&amp;fileName='.htmlentities(rawurlencode($filename)).'&amp;size=100" class="icon zoom_original" title="Zoom To Original Image Size">&nbsp;</a></td>';
									$PB_output .= '<td align="center" class="table-view-link"><a href="?mode='.MODE.'&amp;section='.SECTION.'&amp;action=browser&amp;ba=view&amp;filePath='.$path.'&amp;fileName='.$filename.'&amp;size='.$zoom_out.'" class="icon zoom_out" title="Zoom Image Out" >&nbsp;</a></td>';
                                    $PB_output .= '<td align="right" class="table-view-link"><a href="?mode='.MODE.'&amp;section='.SECTION.'&amp;action=browser&amp;ba=view&amp;filePath='.$path.'&amp;fileName='.$next.'&amp;size='.$size.'" class="icon next" title="Next Image">&nbsp;</a></td>';
                                $PB_output .= "</tr>";
                            $PB_output .= '</table><br />';
                            
							$PB_output .= '<center><div class="image-frame ">';
							    $PB_output .=' <div class="image-info">'.Browser_Image_info($pic_info_path).'</div>';
							    //$PB_output .='<img class="photo" src="'.$view_pic.'" alt="'.$filename.'"  width="'.$image_width.'" height="'.$image_height.'" />';
                                $PB_output .='<img class="grid-" style="width:'.$PB_CONFIG['max_width'].';margin:auto !important;" src="'.VIEW_URL.'inc/getimage.php?filePath='.$path.'&amp;fileName='.$filename.'&amp;mimetype='.$mimetype.'" width="'.$image_width.'" height="'.$image_height.'"  title="'.$filename.'" alt="'.$filename.'" />';
                                $PB_output .='<div class="zoom-btn" title="Zoom photo In/Out">&nbsp;</div><a href="'.$return_url.'"><div class="return-btn" title="Return to gallery" ></div></a>';
						    $PB_output .= '</div></center>';
						} else {
                            $PB_output .= '<div class="message error"><font color="#CC0000">View Fail : </font>';
							    if(!is_file($filename)) $PB_output .= 'the file'.$separator.$filename.'</em> is not an image file.';
							    elseif(!is_viewable_file($filename)) $PB_output .= 'the file'.$separator.$filename.'</em> is not a viewable image file.';
							$PB_output .= '</div>';
                        }
                        $PB_output .= '<br/>'.$view_pic.'<br/>';
                    
                        $PB_output .= '</center></td>';
                    $PB_output .= '</tr>';
                $PB_output .= '</table>';
			
	            $PB_output .= "<script type=\"text/javascript\">
                    $(document).ready(function(){
	                    var move = -1,\n";
                        if($original_img_width <= '150') $PB_output .= 'zoom = 0.5;';
		            	else $PB_output .= 'zoom = 1.5;';
			
	                    $PB_output .= "$('.image-frame').hover(function() {
                            $(this).find('div.image-info').stop(false,true).fadeIn(800);
                            $(this).find('div.zoom-btn').stop(false,true).fadeIn(800);
                            $(this).find('div.return-btn').stop(false,true).fadeIn(800);
                        },
                        function() {
                            $(this).find('div.image-info').stop(false,true).fadeOut(1000);
                            $(this).find('div.zoom-btn').stop(false,true).fadeOut(1000);
                            $(this).find('div.return-btn').stop(false,true).fadeOut(1000);
                        });
                        $('.zoom-btn').toggle(function() {
                            width = $('.image-frame').width() * zoom;
                            height = $('.image-frame').height() * zoom;
							$('.box').css({'overflow-x':'scroll'});
                            $('.image-frame img').stop(false,true).animate({'width':width, 'height':height, 'top':move, 'left':move,'transition':'width 1.45s, height 1.45s ease-in-out'}, {duration:600});
			            },
				        function() {
						    $('.box').css({'overflow':'hidden'});
                            $('.image-frame img').stop(false,true).animate({'width':$('.image-frame').width(), 'height':$('.image-frame').height(), 'top':'10px', 'left':'10px','transition':'width 1.45s, height 1.45s ease-in-out'}, {duration:600});    
                            $('.image-frame img').stop(false,true).animate({'width':'$original_img_width', 'height':'$original_img_height', 'top':'35px', 'left':'35px','transition':'width 1.45s, height 1.45s ease-in-out'}, {duration:600});
                        });
			        });
                </script>";
	        } else {
	            $PB_output .= 'The file &mdash;&gt;&gt;&gt;&nbsp;'.$filename.', is <b>not a valid image</b>';
		    }
        } else {
		    if(!$AllowView) $PB_output .= '<font color="#CC0000">ACCESS DENIED: You are not allowed to view any file</font><br/>';
            if(!$_GET['fileName']) $PB_output .= '<font color="#CC0000">ACCESS DENIED: Filename was not set</font>';
	    }
	} //elseif(BACTION == 'download') include(BASE_PATH.'download.php');
	//$PB_output .= '</div>';
	return $PB_output;
?>