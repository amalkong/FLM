<?php
    if (($roleID < 4))  {
       echo'<div class="message error"><p>Access Denied!,You are not authorised to access this section, your user role does not permit you to edit fixtures.</p></div>';
	   include(BASE_PATH.'footer.php');
	   exit;
    }
	$PB_output = NULL;
	$url = SELF.'?mode=admin&amp;section='.SECTION;
	$database_content_list = GetDirContents(DATABASE_PATH,'files');
	$total_db_files = count($database_content_list);
	
    # convert max upload size set in config.php in megabytes
	$max_upload_form_size_MB = $max_upload_size/1048576;
	$max_upload_form_size_MB = round($max_upload_form_size_MB, 2);
	//$showmin = min($max_upload_form_size_MB, ini_get('upload_max_filesize')+0, ini_get('post_max_size')+0); // min function
	// Note: if I add +0 it eliminates the "M" (e.g. 8M,9M) and this solves some issues with the "min" function
	
    #show server information
	$PB_output .= '<div class="content box">';
	    $PB_output .= '<div class="box2">';
		    $PB_output .= '<h3>Your server configuration</h3>';
		    $PB_output .= '<ul>';
	            $PB_output .= '<li>Operating System:<span class="i b">'.((php_uname('s')!= NULL) ? php_uname('s') : 'n/a' ).'</span></li>';
	            $PB_output .= '<li>PHP Version: <span class="i b">'.phpversion().'</span></li>';
			    $PB_output .= '<li>display_errors = <span class="i b">' . ini_get('display_errors').'</span></li>';
                //if value not null
			    $PB_output .= '<li>register_globals = <span class="i b">' .((ini_get('register_globals')!= NULL) ? ini_get('register_globals') : 'n/a' ).'</span></li>';
	            $PB_output .= '<li>upload_max_filesize (php.ini) = <span class="i b">' . ini_get('upload_max_filesize') . '</span></li>';
		        $PB_output .= '<li>post_max_size (php.ini) = <span class="i b">' . ini_get('post_max_size') . '</span></li>';
                //if value not null
	            $PB_output .= '<li>memory_limit (php.ini) = <span class="i b">' .((ini_get('memory_limit')!= NULL) ? ini_get('memory_limit') : 'n/a' ). '</span></li>';
	            $PB_output .= '<li>Max upload size set in the script (config.php): <span class="i b">'.$max_upload_form_size_MB.'MB</span></li>';
		    $PB_output .= '</ul>';
		
            $PB_output .= '<h3>Database [DB] Files</h3>';
	        $PB_output .= '<div class="panel"><p>A total of '.$total_db_files.' DB_Files were found'.(($CFG->config['display_dirsize'] == 'YES' ) ? ', totalling '.$UTIL->size('dir',DATABASE_PATH).' in size' : '' ).'</p></div>';
	    $PB_output .= '</div>';
	    foreach($database_content_list as $key => $DB_File){
		    if($UTIL->isValidExt(DATABASE_PATH.$DB_File,$tfxs)){
	            $fp = @fopen(DATABASE_PATH.$DB_File, 'r');
	            $array = explode("\n", fread($fp, filesize(DATABASE_PATH.$DB_File))); 
	            $count = count($array);
				
		        if(is_really_writable(DATABASE_PATH.$DB_File)) $state = "<font color=green>Writable</font>";
		        else $state = "<font color=red>Not Writable, <a href=\"http://www.perlservices.net/en/faq/cute_ftp.shtml\" target=\"_blank\">CHMOD</a> \"$DB_File\"!</font>";
		
		        $dbsize = $UTIL->size('file',DATABASE_PATH.$DB_File);
		        $mod_date = date('d-M-Y h:i:s A',filemtime(DATABASE_PATH.$DB_File));
		
		        if($key %2) $PB_output .='<div class="panel right-1" style="width:46%;margin-right:auto;">';
		        else $PB_output .='<div class="panel left-1" style="width:46%;margin-right:5px;">';
		            $PB_output .='<ul class="UL-list">';
			            $PB_output .='<li> DB File Name = <span class="btn-highlight"><b>'.$DB_File.'</b></span></li>';
			            $PB_output .='<li> DB File State = <span class="btn-highlight"><b>'.$state.'</b></span></li>';
		         	    $PB_output .='<li> DB File Size = <span class="btn-highlight"><b>'.$dbsize.'</b></span></li>';
		        	    $PB_output .='<li> DB File Mod-date = <span class="btn-highlight"><b>'.$mod_date.'</b></span></li>';
		                $PB_output .='<li> DB Rows/Lines Count = <span class="btn-highlight"><b>'.$count.'</b></span></li>';
		            $PB_output .='</ul>';
	     	    $PB_output .='</div>';
	        }
	    }
	$PB_output .='</div>';
	
	echo $PB_output;
?>