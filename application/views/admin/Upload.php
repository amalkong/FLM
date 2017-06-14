<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    if (($roleID < 3))  {
       echo'<div class="message error"><p>Access Denied!,You are not authorised to access this section, your user role does not permit you to upload files.</p></div>';
	   include(BASE_PATH.'footer.php');
	   exit;
    }
    if(!isset($PB_output)) $PB_output = NULL;
	$max_upload_size_MB = NULL; // declare variable for duplicated filenames
	$uploaded = false;
    $fileNAMES = array();
    $data = array();
	$upload_type = (isset($_POST['upload_type']) ? $_POST['upload_type'] : isset($_GET['upload_type']) ? $_GET['upload_type'] : 'avatar');
	
	// Determine max upload file size through php script reading the server parameters (and the form parameter specified in config.php. We find the minimum value: it should be the max file size allowed...
	// convert max upload size set in config.php in megabytes
	if($upload_type == 'video' ) {
	    $max_upload_size_MB = $max_upload_video_size/1048576;
		$allowed_file_types = $vfxs;
		if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0) {
		    @set_time_limit(300); // 5 minutes
	    }
	} else { 
	    if($upload_type == 'audio' ) $allowed_file_types = $afxs;
	    else  $allowed_file_types = $ifxs;
	}
	
	$max_upload_size_MB = round($max_upload_size_MB, 2);
	$showmin = min($max_upload_size_MB, ini_get('upload_max_filesize')+0, ini_get('post_max_size')+0); // min function
	// Note: if I add +0 it eliminates the "M" (e.g. 8M, 9M) and this solves some issues with the "min" function
	
	$PB_output .= '<div class="content">';
	$PB_output .= '<div class="box">';
     if (!isset($_GET['number_of_uploads'])){
	    $PB_output .= '<div class="header"><h2>Select the number of image to upload, and upload type</h2></div>';
	    $PB_output .= '<div class="panel grid-2 left-1">This is the upload section where you can upload new avatars,background textures, logos and headers. Images can also be uploaded to existing galleries. So the first thing to do is select the amount of image to upload. The maximum number of files that can be uploaded in one go are <strong>'.$PB_CONFIG['max_number_uploads'].'</strong>, set in <span class="em i">[settings_file.php]</span><span class="small i">( you can&rsquo;t go above the options set. <a href="'.SELF.'?mode=admin&amp;section=setup&amp;action=file_setup">you can change this setting</a>)</span>. Then select the upload type and proceed to the uploads select panel.</div>';
	    $PB_output .= '<div class="right-1 grid-40"><form action="" method="GET" class="">';
		    $PB_output .= '<input name="mode" type="hidden" value="'.MODE.'"><input name="section" type="hidden" value="'.SECTION.'">';
		    $PB_output .= '<fieldset>';
			    $PB_output .= '<label for="number_of_uploads"># of uploads*</label><select name="number_of_uploads" class="grid-30">';
	                for($x=0;$x<$CFG->config['max_number_uploads'];$x++) $PB_output .= '<option value="'.($x+1).'">'.($x+1).'</option>';
				$PB_output .= '</select>';
				
				$PB_output .= '<label for="upload_type">Upload Type*</label>
				<select name="upload_type">
				    <option value="avatar">Avatar</option>
				    <option value="logo">Logo</option>
				    <option value="header">Header</option>
				    <option value="texture">Texture</option>
				    <option value="image">Image</option>
				    <option value="audio">Audio</option>
				    <option value="video">Video</option>
				    <option value="gallery">Gallery</option>
				</select>';
	            $PB_output .= '<center><input type="submit" value="" class="continue" title="click and continue to uploads panel" /></center>';
	        $PB_output .= '</fieldset>';
		$PB_output .= '</form></div>';
	} else if (isset($_GET['number_of_uploads']) && $_GET['number_of_uploads'] <= $PB_CONFIG['max_number_uploads']){
	    $number_of_uploads = $_GET['number_of_uploads'];
		
        if (isset($_POST['submit']) AND isset($_GET['number_of_uploads']) AND isset($_GET['mode']) AND $_GET['mode']=="admin" AND isset($_GET['section']) AND $_GET['section']=="upload" AND isset($_GET['c']) AND $_GET['c']=="ok") {
            if(isset($_FILES['userfile']) AND $_FILES['userfile']!=NULL){
				$PB_output .= '<div class="header"><h2>Now uploading new '.$upload_type.' file'.((count($_FILES['userfile']['name']) > 1) ?  's': '').'</h2></div>';
				$upload_title = isset($_POST['uploadTitle']) ? $_POST['uploadTitle'] : NULL;
				$upload_directory = isset($_POST['uploadDirectory']) ? $_POST['uploadDirectory'] : NULL;
				$upload_files = $_FILES['userfile']['name'];
				
				if($upload_type == 'avatar') $upload_folder = AVATAR_PATH;
				elseif($upload_type == 'logo') $upload_folder = LOGO_PATH;
				elseif($upload_type == 'header') $upload_folder = HEADER_PATH;
				elseif($upload_type == 'texture') $upload_folder = TEXTURES_PATH;
				elseif($upload_type == 'image') $upload_folder = IMAGE_UPLOAD_PATH;
				elseif($upload_type == 'audio') $upload_folder = AUDIO_UPLOAD_PATH;
				elseif($upload_type == 'video') $upload_folder = VIDEO_UPLOAD_PATH;
				elseif($upload_type == 'gallery') $upload_folder = $UTIL->Check_For_Slash(GALLERY_PATH.$upload_directory,true);
				
		        // Create the necessary folders which will contain the new upload files if it does'nt exists
				if(!is_dir($upload_folder)) mkdir($upload_folder,DIR_WRITE_MODE);
				
	            for($i=0;$i<$number_of_uploads;$i++){
		            if(strlen($upload_files[$i]) > 0){
					    //$uFile = explode(".", $_FILES['userfile']['name'][$i]);
		                $uFile = explode(".", $upload_files[$i]); // divide filename from extension
			            $n = strrpos($upload_files[$i],".");
                        $uFile[1] = substr($upload_files[$i],$n,strlen($upload_files[$i])-$n);
                        $uFile[1] = str_replace('.','',$uFile[1]);
					
                        if($UTIL->isValidExt('.'.$uFile[1],$ifxs)) {
					        $imagedata = getimagesize($_FILES['userfile']['tmp_name'][$i]);
                            $width = $imagedata[0];
                            $height = $imagedata[1];
						}
			            //if($rename_upload_file) $fileNAMES[$i] = sanitize_filename($uFile[0]);
			            if($rename_upload_file && $upload_title[$i] !=NULL) $fileNAMES[$i] = sanitize_filename($upload_title[$i]);
			            else $fileNAMES[$i] = sanitize_filename($uFile[0]);
						
		       	        //if(!in_array(strtolower($uFile[1]), $allowed_file_types)){
		       	        if($upload_type == 'avatar' || $upload_type == 'texture' || $upload_type == 'header' || $upload_type == 'logo' || $upload_type == 'image' && !$UTIL->isValidExt('.'.$uFile[1],$ifxs)) $PB_output .= '<div class="message error"><p>FAILED: This File, '. $fileNAMES[$i] .'.'. $uFile[1] .' is not a valid image, image type can be any one of these extension, '.implode($ifxs,'|').'.</p></div>'."\n";
		       	        else if($upload_type == 'audio' && !$UTIL->isValidExt('.'.$uFile[1],$afxs)) $PB_output .= '<div class="message error"><p>FAILED: This File '. $fileNAMES[$i] .'.'. $uFile[1] .', is not a valid audio, audio type can be any one of these extension, '.implode($afxs,'|').'.</p></div>'."\n";
		       	        else if($upload_type == 'video' && !$UTIL->isValidExt('.'.$uFile[1],$vfxs)) $PB_output .= '<div class="message error"><p>FAILED: This File '. $fileNAMES[$i] .'.'. $uFile[1] .', is not a valid video, video type can be any one of these extension, '.implode($vfxs,'|').'.</p></div>'."\n";
		    	        
						elseif($UTIL->isValidExt('.'.$uFile[1],$ifxs) && $_FILES['userfile']['size'][$i] > $max_upload_size) $PB_output .= '<div class="message error"><p>FAILED: Image File '. $_FILES['userfile']['name'][$i] .' size too large.</p></div>'."\n";
						elseif($UTIL->isValidExt('.'.$uFile[1],$afxs) && $_FILES['userfile']['size'][$i] > $max_upload_size) $PB_output .= '<div class="message error"><p>FAILED: Audio File '. $_FILES['userfile']['name'][$i] .' size too large.</p></div>'."\n";
						elseif($UTIL->isValidExt('.'.$uFile[1],$vfxs) && $_FILES['userfile']['size'][$i] > $max_upload_video_size) $PB_output .= '<div class="message error"><p>FAILED: Video File '. $_FILES['userfile']['name'][$i] .' size too large.</p></div>'."\n";
			            
						else if ($upload_type == 'avatar' && ($width >= 150) && ($height >= 150)) $PB_output .= '<div class="message error"><p>FAILED: The Avatar '.$separator.$fileNAMES[$i] .'.'. $uFile[1] .', dimensions too big. allowed dimensions 150x150, dimension of avatar submitted '.$imagedata[0].'x'.$imagedata[1].'</p></div>'."\n";
						elseif(file_exists($upload_folder.$fileNAMES[$i] .".". $uFile[1])) $PB_output .= '<div class="message error"><p>FAILED: The '.$upload_type.$separator. $fileNAMES[$i] .'.'. $uFile[1] .' already exists.</p></div>'."\n";
			            else{
			    	        if(move_uploaded_file($_FILES['userfile']['tmp_name'][$i], $upload_folder.$fileNAMES[$i] .".". $uFile[1])){
			                    $uploaded = true;
								$PB_output .= '<div class="message"><p>UPLOADED: The '.$upload_type.$separator. $fileNAMES[$i] .'.'. $uFile[1] ." has been uploaded successfully.</p></div>\n";
						    } else $PB_output .= '<div class="message error"><p>FAILED: Upload failed for the file'.$separator.$_FILES['userfile']['name'][$i] .', ERROR: Undetermined.</p></div>'."\n";
			            }
		            }
	            }
				$PB_output .= '<div class="message unspecific"><p><a href="'.SELF.'?mode=admin">Go to the admin index</a> or ';
				   if($uploaded) $PB_output .= '<a href="'.SELF.'?mode=admin&amp;section=upload">Upload more files</a>';
				   else $PB_output .= '<a href="javascript:history.back()">Back</a>';
				$PB_output .= '</p></div>';
	        }
        } else {
			$PB_output .= '<div class="header"><h2>Upload new '.$upload_type.' file'.(($number_of_uploads > 1) ?  's': '').'</h2></div>';
            $PB_output .= '<form action="'.SELF.'?mode=admin&amp;section=upload&amp;number_of_uploads='.$number_of_uploads.'&amp;upload_type='.$upload_type.'&amp;c=ok" method="POST" enctype="multipart/form-data" name="uploadform" id="uploadform">';
		        $PB_output .= '<fieldset><legend><strong>Main information (required):</strong></legend>';
			    	$PB_output .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.(($upload_type == 'video') ? $max_upload_video_size : $max_upload_size).'">';
			        $PB_output .= "<p>Allowed file types: ".implode($allowed_file_types,' | '). "</p>\n";
				    if ($showmin!=NULL and $showmin!="0") $PB_output .= '<p><span class ="admin_hints">Your server configuration allows you to upload '.$upload_type .'files up to '.$showmin.'MB</span></p>';
				    if($upload_type == 'gallery'){
					    $PB_output .='<label>Gallery Albums : </label><br />';
					    $album_list=GetDirContents(GALLERY_PATH,'dirs');
	                    if(is_array($album_list) && count($album_list) > 0){
						    $PB_output .='<select name="uploadDirectory">';
						    foreach( $album_list as $album) {
		                        if($album != "." && $album != "..") $PB_output .= '<option value="'.$album.'">'.$album."&nbsp;</option><br/>";
						        unset($album);
	                        }
							$PB_output .='</select>';
					    } else $PB_output .= '<div class="message user_status"><p>[0] gallery album found! So files can&rsquo;t be uploaded to an album.</p></div>';
					}
				    $PB_output .= '<table class="table add grid-full">';
				        $PB_output .= '<tr><td>'.ucfirst($upload_type).' Count</td><td>'.ucfirst($upload_type).'</td><td>Rename '.ucfirst($upload_type).'</td></tr>';
                        for($i=0;$i<$number_of_uploads;$i++){
						    $num = $i + 1;
	                        $PB_output .= "<tr class=\"new-row\"><td>&nbsp;$upload_type : $num </td><td><input type=\"file\" name=\"userfile[]\" /></td><td><input type=\"text\" name=\"uploadTitle[]\" /></td></tr>";
	                        $PB_output .= '<tr class="row-spacer"><td colspan="3">&nbsp;</td></tr>';
						}
                    $PB_output .= '</table>';
				
			        $PB_output .= '<p class="admin_hints">Fields marked with * are required.</p>';
			    $PB_output .= '</fieldset>';
			    $PB_output .= '<center><input type="submit" name="submit" value="Upload" onClick="showNotify(\'Uploading...'.$upload_type.'...please wait...\');" class="save" /><a class="cancel" href="'.SELF.'?mode=admin" title="cancel and return to the admin index">Cancel</a></center><br />';
	        $PB_output .= '</form>';
	    }
	} else if (isset($_GET['number_of_uploads']) && $_GET['number_of_uploads'] >= $PB_CONFIG['max_number_uploads']){
	    $PB_output .= '<div class="header"><h2>Too Much Uploads!</h2></div>';
		$PB_output .= '<div class="message user_status"><span class="b">Too Much Uploads!</span> A max number of <span class="i em">'.$PB_CONFIG['max_number_uploads'].'</span> '.$upload_type.'s can be uploaded, <span class="i em">'.$_GET['number_of_uploads'].'</span> '.$upload_type.'s were set. <a href="'.SELF.'?mode=admin&amp;section=upload">Return</a></div>';
	}
	$PB_output .= '</div>';
	$PB_output .= '</div>';
	echo $PB_output;
?>