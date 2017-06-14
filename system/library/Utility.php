<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
/**
 * @ignore
 */
define('FILE_WIN32', defined('OS_WINDOWS') ? OS_WINDOWS : !strncasecmp(PHP_OS, 'win', 3));

class Utility{

    /**
     * Returns a path without leading / or C:\. If this is not
     * present the path is returned as is.
     *
     * @static
     * @access  public
     * @param   string  $path The path to be processed
     * @return  string  The processed path or the path as is
     */
    function skipRoot($path){
        if (Utility::isAbsolute($path)) {
            if (FILE_WIN32) {
                return substr($path, $path{3} == '\\' ? 4 : 3);
            }
            return ltrim($path, '/');
        }
        return $path;
    }

    /**
     * Returns boolean based on whether given path is absolute or not.
     *
     * @static
     * @access  public
     * @param   string  $path Given path
     * @return  boolean True if the path is absolute, false if it is not
     */
    static function isAbsolute($path){
        if (preg_match('/(?:\/|\\\)\.\.(?=\/|$)/', $path)) {
            return false;
        }
        if (FILE_WIN32) {
            return (($path{0} == '/') ||  preg_match('/^[a-zA-Z]:(\\\|\/)/', $path));
        }
        return ($path{0} == '/') || ($path{0} == '~');
    }

	/**
 * PHP 5 standard microtime start capture.
 *
 * @access private
 * @since 0.71
 * @global float $timestart Seconds from when function is called.
 * @return bool Always returns true.
 */
    function timer_start() {
	    global $timestart;
	    $timestart = microtime( true );
	    return true;
    }

/**
 * Return and/or display the time from the page start to when function is called.
 *
 * You can get the results and print them by doing:
 * <code>
 * $nTimePageTookToExecute = timer_stop();
 * echo $nTimePageTookToExecute;
 * </code>
 *
 * Or instead, you can do:
 * <code>
 * timer_stop(1);
 * </code>
 * which will do what the above does. If you need the result, you can assign it to a variable, but
 * in most cases, you only need to echo it.
 *
 * @since 0.71
 * @global float $timestart Seconds from when timer_start() is called
 * @global float $timeend Seconds from when function is called
 *
 * @param int $display Use '0' or null to not echo anything and 1 to echo the total time
 * @param int $precision The amount of digits from the right of the decimal to display. Default is 3.
 * @return float The "second.microsecond" finished time calculation
 */
    function timer_stop( $display = 0, $precision = 3 ) { // if called like timer_stop(1), will echo $timetotal
	    global $timestart, $timeend;
		$time = time();
	    $time = date(' H:i A', $time );
		
	    $timeend = microtime( true );
	    $timetotal = $timeend - $timestart;
	    $r = ( function_exists( 'number_format_i18n' ) ) ? number_format_i18n( $timetotal, $precision ) : number_format( $timetotal, $precision );
	    if ( $display ) echo 'This page was originally rendered at [ '.$time.' ] in '.$r.' seconds';
	    else return $r;
    }
	
/* ----------------------- ++++++++++++++++++ ---------------------- */
    function Check_For_Slash($path,$convertSlash=false) {
            if (substr($path, (strlen($path) - 1), 1) != "/") {
                $path = $path . "/";
            }
			if($convertSlash == true){
			    $path = str_replace('\\','/',$path);
			}
            return($path);
    }
	
    function getSuffix($f) {
        $n = strrpos($f,".");
        return substr($f,$n,strlen($f)-$n);
    }
	
	function isValidExt($f,$a) {
        $t = $this->getSuffix($f);
        return ( in_array($t,$a) ) ? true : false;
    }
    
    function getFileExt($f,$strip_dot=false) {
        $n = strrpos($f,".");
        $result = substr($f,$n,strlen($f)-$n);
		if($strip_dot) return str_replace('.','',$result);
		else return $result;
    }
	
    function removeFileExt($filename) {
        $newFileName = @explode('.',$filename);
        return $newFileName[0];
    }
    
	function create_slug($string){  
        $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);  
        return $slug;  
    } 

	function getMediaType($file) {
		$split = explode('.', $file); 
		$ext = $split[count($split) - 1];
		if ( preg_match('/mp3/', $ext) ) return 'mp3';
		else if ( preg_match('/ogg/i', $ext) ) return 'ogg';
		else if ( preg_match('/wma/i', $ext) ) return 'wma';
		else if ( preg_match('/avi/i', $ext) ) return 'avi';
		else if ( preg_match('/flv/i', $ext) ) return 'flv';
		else if ( preg_match('/mp4/i', $ext) ) return 'mp4';
		else if ( preg_match('/ogv/i', $ext) ) return 'ogv';
		else if ( preg_match('/swf/i', $ext) ) return 'swf';
		else if ( preg_match('/webm/i', $ext) ) return 'webm';
		else return '<font color="#CC0000"><b>Unknown Media File</b></font>';
	}
/* -------------------------// display pagination  --------------------------------- */
    function print_pagination($numPages,$urlVars,$currentPage,$echo=false) {
        $PB_output = NULL;
		if ($numPages > 1) {
	        $PB_output .= 'Page '. $currentPage .' of '. $numPages;
	        $PB_output .= '&nbsp;&nbsp;|&nbsp;';
   
            if ($currentPage > 1) {
	            $prevPage = $currentPage - 1;
	            $PB_output .= '<a class="paginate-arrow" href="'. $urlVars .'&amp;p='. $prevPage.'">&larr;&laquo;</a> ';
	        }
	   
	        for( $e=0; $e < $numPages; $e++ ) {
                $p = $e + 1;
	            if ($p == $currentPage) $class = 'active';
	            else $class = 'paginate';
	            
		        //$PB_output .= '<span class="btn-highlight"><a class="'. $class .' pages" href="?'. $urlVars .'p='. $p .'">'. $p .'</a></span>';
		        $PB_output .= '<a class="'. $class .' pages" href="'. $urlVars .'&amp;p='. $p .'">'. $p .'</a>';
	        }
	   
	        if ($currentPage != $numPages) {
               $nextPage = $currentPage + 1;
		        $PB_output .= ' <a class="paginate-arrow" href="'. $urlVars .'&amp;p='. $nextPage.'">&raquo;&rarr;</a>';
	        }
			if($echo) echo $PB_output;
			else return $PB_output;
        }
    }

    function GetFilesize($files){
        $kilobyte = 1024;
        $megabyte = 1048576;
        $gigabyte = 1073741824;
        $terabyte = 1099511627776;
	
		if (filesize($files) >= $terabyte) return (round(filesize($files) / 1099511627776 * 100) / 100).'&nbsp;TB';
		else if (filesize($files) >= $gigabyte) return (round(filesize($files) / 1073741824 * 100) / 100).'&nbsp;GB';
		else if(filesize($files) >= $megabyte) return (round(filesize($files) / 1048576 * 100) / 100).'&nbsp;MB';
		else if (filesize($files) >= $kilobyte) return (round(filesize($files) / 1024 * 100) / 100).'&nbsp;KB';
		else return filesize($files)."&nbsp;Byte";
    }
	// DISPLAY A FILESIZES HUMAN-READABLE
	function display_size($file_size) {
		if($file_size >= 1073741824) $file_size = (round($file_size / 1073741824 * 100) / 100).' GB';
		elseif($file_size >= 1048576) $file_size = (round($file_size / 1048576 * 100) / 100).' MB';
		elseif($file_size >= 1024) $file_size = (round($file_size / 1024 * 100) / 100).' KB';
		elseif($file_size != '') $file_size = $file_size.' Byte';
		    
		return $file_size;
    }
	// EVALUATING THE DIRECTORY- AND FILE-SIZES
    function size($fileType, $file) {
		global $PB_CONFIG;
		if($fileType == 'dir') {
			if($PB_CONFIG['display_dirsize'] == 'NO') {
			     return false;
		    }
		    // exec- and read-permission needed for evaluating the dirsize
			if(is_readable($file)) return $this->display_size($this->GetDirsize($file));
			else return '???';
			
		} elseif($fileType == 'file') return $this->GetFilesize($file);	//return $this->display_size($this->GetFilesize($file));
		else return false;
	}
	// EVALUATE A DIRSIZE (RECURSIVE)
	function GetDirsize($directory) {
		$total = 0;

		if($directory[strlen($directory) - 1] == '/') $directory = substr($directory, 0, -1);
		if($directory == BASE_PATH.'..') return 0;
		// adding @ for dirsize-bug
		$to_dig = @dir($directory);

	    if(is_object($to_dig)) {
		    while($entry = $to_dig->read()) {
				if($entry == '..' || $entry == '.') continue;
			    	
				if(!is_dir($directory.'/'.$entry)) $total += filesize($directory.'/'.$entry);
				else $total += $this->GetDirsize($directory.'/'.$entry);
			}
		} else return false;
		    
		return $total;
	}

	function show_file_box($file,$path) {
	    global $ifxs,$tfxs,$mfxs,$mode;
	    //Find the margin.
	    preg_match_all('|\/|', $file, $margin);
	    if (!empty($margin[0])) $margin = count($margin[0]) * 20 + 10;
	    else $margin = 0;
	    $title = basename($file);
		if(is_dir($path.$file)){
		    $view_url = '?mode='.$mode.'&amp;action=explore&amp;album='.$title;
			$view_title = 'Click to Explore the directory '.$title;
		    $edit_url = '?mode='.$mode.'&amp;action=rename&amp;album='.$title;
		    $delete_url = '?mode='.$mode.'&amp;action=delete&amp;album='.$title;
			$class = '';
		} else if($this->isValidExt($file,$ifxs)){
		   //$view_url = '?mode='.$mode.'&amp;setup_action=view&amp;pic='.$title;
		   $view_url = $path.$file;
		   $view_title = 'Click to View the image '.$title;
		    $edit_url = '?mode='.$mode.'&amp;action=rename&amp;pic='.$title;
		    $delete_url = '?mode='.$mode.'&amp;action=delete&amp;pic='.$title;
			$class = 'albumpix';
		}
	    ?>
		<div class="menudiv" <?php if ($margin != 0) echo 'style="margin-left: '.$margin.'px;"'; ?>>
			<span>
			<?php
				if(is_dir($path.$file)) echo' <img src="assets/img/icons/folder_2.gif" alt="Dir" />';
				else if($this->isValidExt($file,$ifxs)) echo'<img src="assets/img/icons/image.gif" alt="Image" />';
		    ?>
			</span>
			<span class="title-page"><?php echo $title; ?></span>
			<span class="right-1">
			    <span><a href="<?php echo $view_url; ?>" class="<?php echo $class; ?>" rel="albumpix" title="<?php echo $title; ?>"><img src="<?php echo IMG_URL.'view.png';?>" title="<?php echo $view_title; ?>" alt="<?php echo $title; ?>" /></a></span>
			    <span><a href="<?php echo $edit_url; ?>"><img src="<?php echo IMG_URL.'edit.png';?>" title="<?php echo 'Edit/Rename '.$title; ?>" alt="Edit/Rename" /></a></span>
			    <span><a href="<?php echo $delete_url; ?>"><img src="<?php echo IMG_URL.'delete.png';?>" title="<?php echo 'Delete '.$title; ?>" alt="Delete Page" /></a></span>
			</span>
		</div>
	    <?php
    }
	
	/* --------------------------- DESTROY/DELETE A DIRECTORY ---------------------------- */
	/** 
     * @dir - Directory to destroy 
     * @virtual[optional]- whether a virtual directory 
    **/  
        function destroyDir($dir, $virtual = false) {  
            $ds = DIRECTORY_SEPARATOR;  
            $dir = $virtual ? realpath($dir) : $dir;  
            $dir = substr($dir, -1) == $ds ? substr($dir, 0, -1) : $dir;  
            if (is_dir($dir) && $handle = opendir($dir)) {  
                while ($file = readdir($handle)) {  
                    if ($file == '.' || $file == '..') continue;  
                    elseif (is_dir($dir.$ds.$file)) $this->destroyDir($dir.$ds.$file);  
                    else unlink($dir.$ds.$file);  
                }  
                closedir($handle);  
                rmdir($dir);
				//echo '<font color="#009900">The Directory <span class="b">'.basename($dir) .'</span>, Deleted successfully</font>';  
                return true;  
            } else {  
			    //echo '<font color="#CC0000">Error: The Folder <span class="b">'.basename($dir) .'</span> , was <b>Not</b> Deleted or does not exist.</font>';
                return false;  
            }  
        }
		
        function copy_directory( $source, $destination ) {
	        if ( is_dir( $source ) ) {
		        @mkdir( $destination );
		            $directory = dir( $source );
		        while ( FALSE !== ( $readdirectory = $directory->read() ) ) {
			        if ( $readdirectory == '.' || $readdirectory == '..' ) {
				        continue;
			        }
			        $PathDir = $source . '/' . $readdirectory; 
			        if ( is_dir( $PathDir ) ) {
				        copy_directory( $PathDir, $destination . '/' . $readdirectory );
				        continue;
			        }
			        copy( $PathDir, $destination . '/' . $readdirectory );
		        }
		        $directory->close();
	        }else {
		        copy( $source, $destination );
	        }
        }
        //copy_directory('a','b');
	function renameFile($from_fileName,$to_fileName){
		global $ifxs,$tfxs;
		$PB_output = NULL;
		if(is_dir($from_fileName)){
		    $file = 'Directory';
		} else if(is_file($from_fileName)){
		    if ( $this->isValidExt($from_fileName,$ifxs) ) $file = 'Image';
		    if ( $this->isValidExt($from_fileName,$tfxs) ) $file = 'Text File';
		} else $file = 'file';
		
		if(file_exists($from_fileName)){
		    if(file_exists($to_fileName)){
			    $PB_output .='<div class="message error"><font color="#CC0000">Error : The '.$file.' Already Exists</font></div>';
		    } else {
			    if(rename($from_fileName,$to_fileName)) $PB_output .='<div class="message"><font color="#009900">'.$file.' renamed successfully, from '.basename($from_fileName).' to '.basename($to_fileName).'</font></div>';
			    else $PB_output .='<div class="message error"><font color="#ff0000">ERROR: Something went wrong when trying to rename '.$file.'.</font></div>';
		    
		    }
		} else $PB_output .= '<div class="message error">The '.$file.' <strong>'.basename($from_fileName).'</strong> does not exist, so it can&rsquo;t be renamed!.</div>';
		
		return $PB_output;
	}
	function scanWriter($dir,$fileName,$valid_cache_file,$message = false,$file_ext='txt'){
	    $fileList = scandir($dir);
	    $fileName = $dir.$fileName.'_CACHE.'.$file_ext;
		if (strlen($fileName)>0){
		    $openfile = @fopen($fileName,"w");
	        foreach($fileList as $file){
		        if($file != '.' && $file != '..' && $this->isValidExt($file,$valid_cache_file) && $file != basename($fileName)){
			        fwrite($openfile,$file."|");
	            }
	        }
			fclose($openfile);
			
			$Files = trim(file_get_contents($fileName));
			$fileCount = explode("|", $Files);
		    $fileCount = count($fileCount);
			if($message == true){
			    echo"Scan completed for the directory <span class=\"b\">".$dir."</span> and <span class=\"b\">".($fileCount - 1)."</span> files found and written to the file <span class=\"b\">".basename($fileName)."</span>\n";
	        }
			return 1;
	   }
		return -1;
	}
    
	function make_scripts() {
        global $PB_CONFIG;
        $scripts = array();
        if ($PB_CONFIG['use_js_editor'] == 1) {
            $scripts[] = 'jquery-1.7.1.min.js';
            $scripts[] = 'filethingie.js';
        }
        //$result = ft_invoke_hook('add_js_file');
        $scripts = array_merge($scripts, $result);
        foreach ($scripts as $c) {
            echo "<script type='text/javascript' charset='utf-8' src='js/{$c}'></script>\r\n";
        }
    }
}  
?>