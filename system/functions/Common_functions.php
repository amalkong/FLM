<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
/**
 * Return the command sent by the user
 * Don't use $_REQUEST here because SetCookieArgs() uses $_GET
 */
 if ( ! function_exists('GetCommand')) {
	function GetCommand($type='action'){
		if( isset($_POST[$type]) ) return $_POST[$type];
		if( isset($_GET[$type]) ) return $_GET[$type];
		if( isset($_COOKIE[$type]) ) return $_COOKIE[$type];
		return false;
	}
}

if ( ! function_exists('is_php')) {
	function is_php($version = '5.0.0') {
		static $_is_php;
		$version = (string)$version;

		if ( ! isset($_is_php[$version])) {
			$_is_php[$version] = (version_compare(PHP_VERSION, $version) < 0) ? FALSE : TRUE;
		}

		return $_is_php[$version];
	}
}
// ------------------------------------------------------------------------
if ( ! function_exists('is_really_writable')){
	function is_really_writable($file){
		// If we're on a Unix server with safe_mode off we call is_writable
		if (DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE){
			return is_writable($file);
		}

		// For windows servers and safe_mode "on" installations we'll actually
		// write a file then read it.  Bah...
		if (is_dir($file)){
			$file = rtrim($file, '/').'/'.md5(mt_rand(1,100).mt_rand(1,100));

			if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE){
				return FALSE;
			}

			fclose($fp);
			@chmod($file, DIR_WRITE_MODE);
			@unlink($file);
			return TRUE;
		}elseif ( ! is_file($file) OR ($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE){
			return FALSE;
		}

		fclose($fp);
		return TRUE;
	}
}

// ------------------------------------------------------------------------
/**
* Class registry
*
* This function acts as a singleton.  If the requested class does not
* exist it is instantiated and set to a static variable.  If it has previously been instantiated the variable is returned.
*
* @access	public
* @param	string	the class name being requested
* @param	string	the directory where the class should be found
* @param	string	the class name prefix
* @return	object
*/
if ( ! function_exists('load_class')){
	function &load_class($class, $directory = 'library'){
		static $_classes = array();

		// Does the class exist?  If so, we're done...
		if (isset($_classes[$class])){
			return $_classes[$class];
		}

		$name = FALSE;

		// Look for the class first in the local application/libraries folder then in the native system/libraries folder
		foreach (array(APP_PATH, SYSTEM_PATH) as $path){
			if (file_exists($path.$directory.'/'.$class.'.php')){
				$name = $class;

				if (class_exists($name) === FALSE){
					require($path.$directory.'/'.$class.'.php');
				}
				break;
			}
		}

		// Is the request a class extension?  If so we load it too
		if (file_exists(APP_PATH.$directory.'/'.config_item('subclass_prefix').$class.'.php')){
			$name = config_item('subclass_prefix').$class;

			if (class_exists($name) === FALSE){
				require(APP_PATH.$directory.'/'.config_item('subclass_prefix').$class.'.php');
			}
		}

		// Did we find the class?
		// Note: We use exit() rather then show_error() in order to avoid a
		// self-referencing loop with the Excptions class
		if ($name === FALSE){
			exit('Unable to locate the specified class: '.$class.'.php');
		}

		// Keep track of what we just loaded
		is_loaded($class);
		$_classes[$class] = new $name();
		return $_classes[$class];
	}
}
// --------------------------------------------------------------------
/**
* Keeps track of which libraries have been loaded.  This function is
* called by the load_class() function above
*
* @access	public
* @return	array
*/
if ( ! function_exists('is_loaded')){
	function is_loaded($class = ''){
		static $_is_loaded = array();
		if ($class != ''){
			$_is_loaded[strtolower($class)] = $class;
		}
		return $_is_loaded;
	}
}
// ------------------------------------------------------------------------
if ( ! function_exists('loadScripts')){
    function loadScripts($type='css', $srcs,$defaultURL=true,$echo=false) {
		$script = "";
		$delimiter_exist = strpos((is_array($srcs) ? $srcs[0] : $srcs),',');
		if(!is_array($srcs) && $delimiter_exist) $srcs = explode(',',$srcs);
		if(is_array($srcs)){
		    foreach($srcs as $src){
		        if($type == 'c' || $type == 'css' || $type == 'style' || $type == 'stylesheet'){
				    if($defaultURL) $script .= '<link type="text/css" rel="stylesheet" href="'.CSS_URL.$src.'" />'."\n";
				    else $script .= "<link type='text/css' rel='stylesheet' href='$src' />\n";
		        } elseif($type == 'j' || $type == 'js' || $type == 'script' || $type == 'javascript'){
		            if($defaultURL) $script .= '<script type="text/javascript" charset="utf-8" src="'.JS_URL.$src.'"></script>'."\n";
		            else $script .= "<script type='text/javascript' charset='utf-8' src='$src'></script>\n";
		        }
		    }
		} else {
		    if($type == 'c' || $type == 'css' || $type == 'style' || $type == 'stylesheet'){
			    if($defaultURL) $script .= '<link type="text/css" rel="stylesheet" href="'.CSS_URL.$src.'" />'."\n";
		        else $script .= "<link type='text/css' rel='stylesheet' href='$srcs' />\n";
		    } elseif($type == 'j' || $type == 'js' || $type == 'script' || $type == 'javascript'){
			    if($defaultURL) $script .= '<script type="text/javascript" charset="utf-8" src="'.JS_URL.$src.'"></script>'."\n";
		        else $script .= "<script type='text/javascript' charset='utf-8' src='$srcs'></script>\n";
		    }
		}
		if(!$echo) return $script;
		elseif($echo) echo $script;
	}

}
// ------------------------------------------------------------------------
if ( ! function_exists('get_config')){
	function &get_config($replace = array()){
		static $_config;
		if (isset($_config)){
			return $_config[0];
		}
		// Is the config file in the environment folder?
		if (! file_exists($file_path = CONFIG_PATH.'settings.php')){
			$file_path = CONFIG_PATH.'settings.php';
		}
		// Fetch the config file
		if ( ! file_exists($file_path)){
			exit('The configuration file does not exist.');
		}

		require($file_path);

		// Does the $config array exist in the file?
		if ( ! isset($PB_CONFIG) OR ! is_array($PB_CONFIG)){
			exit('Your config file does not appear to be formatted correctly.');
		}
		// Are any values being dynamically replaced?
		if (count($replace) > 0){
			foreach ($replace as $key => $val){
				if (isset($config[$key])){
					$config[$key] = $val;
				}
			}
		}
		return $_config[0] =& $PB_CONFIG;
	}
}

/**
* Returns the specified config item
*
* @access	public
* @return	mixed
*/
if ( ! function_exists('config_item')){
	function config_item($item){
		static $_config_item = array();
		if ( ! isset($_config_item[$item])){
			$config =& get_config();
			if ( ! isset($config[$item])){
				return FALSE;
			}
			$_config_item[$item] = $config[$item];
		}
		return $_config_item[$item];
	}
}
// ------------------------------------------------------------------------
// error handler functions
if ( ! function_exists('pbErrorHandler')){
    function pbErrorHandler($errno, $errstr, $errfile, $errline){
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting
            //return;
        }
        switch ($errno) {
            case E_USER_ERROR:
            case E_ERROR:
    	        echo "<center><div class='php-error fatal'>";
                echo "<b>Fatal ERROR type</b> [$errno] $errstr<br />\n";
                echo "  Fatal error on line $errline in file $errfile";
                echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
                echo "Aborting...<br />\n";
		        echo "</div></center>";
		        //error_log("Aborting...<br />\n", 3, "templates/logs/pbd_error.log");
                exit(1);
            break;
            case E_USER_WARNING:
                echo "<center><div class='php-error warning'>";
                echo "<b>Warning Error type</b> - <span class='error-str'>$errstr</span>, on line <span class='error-line'>$errline</span> in file <span class='error-file'>$errfile</span><br />\n";
                echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
		        echo "</div></center>";
		        //error_log("<b>Strict</b> - $errstr, on line $errline in file $errfile<br />\n", 3, "templates/logs/pbd_error.log");
            break;
            case E_USER_NOTICE:
                echo "<center><div class='php-error notice'>";
                echo "<b>Notice Error type</b> - <span class='error-str'>$errstr</span>, on line <span class='error-line'>$errline</span> in file <span class='error-file'>$errfile</span><br />\n";
                echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
		        echo "</div></center>";
		        //error_log("<b>Strict</b> - $errstr, on line $errline in file $errfile<br />\n", 3, "templates/logs/pbd_error.log");
                break;
	        case E_STRICT:
	            echo "<center><div class='php-error strict'>";
                echo "<b>Strict Error type</b> - <span class='error-str'>$errstr</span>, on line <span class='error-line'>$errline</span> in file <span class='error-file'>$errfile</span><br />\n";
                echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
		        echo "</div></center>";
		        //error_log("<b>Strict</b> - $errstr, on line $errline in file $errfile<br />\n", 3, "templates/logs/pbd_error.log");
            break;
            default:
		        echo "<center><div class='php-error unknown'>";
                echo "<b>Unknown error type:</b> - <span class='error-str'>$errstr</span>, on line <span class='error-line'>$errline</span> in file <span class='error-file'>$errfile</span><br />\n";
                echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
		        echo "</div></center>";
		        //error_log("<b>Unknown Error</b> - $errstr, on line $errline in file $errfile<br />\n", 3, "templates/logs/pbd_error.log");
            break;
        }
         /* Don't execute PHP internal error handler */
        return true;
    }
}
// ------------------------------------------------------------------------
if ( ! function_exists('show_error')){
	function show_error($message, $status_code = 500, $heading = 'An Error Was Encountered'){
		$_error =& load_class('Exceptions', '');
		echo $_error->show_error($heading, $message, 'error_general', $status_code);
		exit;
	}
}
// ------------------------------------------------------------------------
if ( ! function_exists('show_404')){
	function show_404($page = '', $log_error = TRUE){
		$_error =& load_class('Exceptions', '');
		echo $page;
		$_error->show_404($page, $log_error);
		exit;
	}
}
// ------------------------------------------------------------------------
if ( ! function_exists('log_message')){
	function log_message($level = 'error', $message, $php_error = FALSE){
		static $_log;

		if (config_item('log_threshold') == 0){
			return;
		}
		$_log =& load_class('Log','');
		$_log->write_log($level, $message, $php_error);
	}
}
// ------------------------------------------------------------------------
if ( ! function_exists('set_status_header')){
	function set_status_header($code = 200, $text = ''){
		$stati = array(
			200	=> 'OK', 201 => 'Created', 202	=> 'Accepted', 203	=> 'Non-Authoritative Information',
			204	=> 'No Content', 205	=> 'Reset Content', 206	=> 'Partial Content',

			300	=> 'Multiple Choices', 301	=> 'Moved Permanently', 302	=> 'Found',
			304	=> 'Not Modified', 305	=> 'Use Proxy', 307	=> 'Temporary Redirect',

			400	=> 'Bad Request', 401 => 'Unauthorized', 403 => 'Forbidden', 404 => 'Not Found',
			405	=> 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required',
			408	=> 'Request Timeout', 409 => 'Conflict', 410 => 'Gone', 411	=> 'Length Required',
			412	=> 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Long',
			415	=> 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable', 417	=> 'Expectation Failed',

			500	=> 'Internal Server Error', 501	=> 'Not Implemented', 502	=> 'Bad Gateway',
			503	=> 'Service Unavailable', 504	=> 'Gateway Timeout', 505	=> 'HTTP Version Not Supported'
		);

		if ($code == '' OR ! is_numeric($code)){
			show_error('Status codes must be numeric', 500);
		}

		if (isset($stati[$code]) AND $text == ''){
			$text = $stati[$code];
		}

		if ($text == ''){
			show_error('No status text available.  Please check your status code number or supply your own message text.', 500);
		}

		$server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;

		if (substr(php_sapi_name(), 0, 3) == 'cgi'){
			header("Status: {$code} {$text}", TRUE);
		}elseif ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0'){
			header($server_protocol." {$code} {$text}", TRUE, $code);
		}else{
			header("HTTP/1.1 {$code} {$text}", TRUE, $code);
		}
	}
}
// --------------------------------------------------------------------
/**
* Exception Handler
*
* The main reason we use this is to permit PHP errors to be logged in our own log files since the user may
* not have access to server logs. Since this function effectively intercepts PHP errors, however, we also need
* to display errors based on the current error_reporting level. We do that with the use of a PHP error template.
*
* @access	private
* @return	void
*/
if ( ! function_exists('_exception_handler')){
	function _exception_handler($severity, $message, $filepath, $line){
		 // We don't bother with "strict" notices since they tend to fill up
		 // the log file with excess information that isn't normally very helpful.
		 // For example, if you are running PHP 5 and you use version 4 style
		 // class functions (without prefixes like "public", "private", etc.)
		 // you'll get notices telling you that these have been deprecated.
		if ($severity == E_STRICT){
			return;
		}

		$_error =& load_class('Exceptions', '');

		// Should we display the error? We'll get the current error_reporting
		// level and add its bits with the severity bits to find out.
		if (($severity & error_reporting()) == $severity){
			$_error->show_php_error($severity, $message, $filepath, $line);
		}

		// Should we log the error?  No?  We're done...
		if (config_item('log_threshold') == 0){
			return;
		}
		$_error->log_exception($severity, $message, $filepath, $line);
	}
}

	function getFileNumber($path){
		$files = GetDirContents($path, 'files');
		//Are there any file?
		if ($files == false) $file_count = 1;
		else $file_count = count($files) + 1;

		return $file_count;
	}

// -------------------------<  League Table Calculations >-------------------------------
    /**
    * This function calculates a team's total points.
	*
	* @param integer $wn -- the total amount of games won
	* @param integer $ls -- the total amount of games lost 
	* @param integer $dr -- the total amount of games drawn
	* @return integer - The goal difference
    */
	function calculatePoints($wn,$ls,$dr){
	   $win_points = 3;
	   $draw_point = 1;
	   $loss = 0;
	   $total_win_points = ($win_points * $wn);
	   $total_draw_points = ($draw_point * $dr);
	   $total_points = $total_win_points + $total_draw_points;
	   
	   return $total_points;
	}
	/**
    * This function calculates a team's goal difference.
	*
	* @param integer $gf -- the total amount of goals scored for
	* @param integer $ga -- the total amount of goals scored against
	* @return integer -- The goal difference
    */
	function calculateGoalDifference($gf,$ga){
	    $gd = ($gf - $ga);
	    if($gd > 0) $diff = "+$gd";
	    //elseif($gd < 0) $diff = "-$gd";
        else $diff = "$gd";
        
	    return $diff;
	}
	
//-------------------------< Filters and Sorters >-------------------------------
	function filter_stopwords($words, $stopwords) {
	    foreach ($words as $pos => $word) {
	        if (!in_array(strtolower($word), $stopwords, TRUE)) {
	            $filtered_words[$pos] = $word;
	        }
	    }	 
	    return $filtered_words;
 
    }
	
    function word_freq($words) {	 
	    $frequency_list = array();
	    foreach ($words as $pos => $word) {	 
            $word = strtolower($word);
	        if (array_key_exists($word, $frequency_list)) {
	            ++$frequency_list[$word];
	        } else {
                $frequency_list[$word] = 1;
	        }
	    }
        return $frequency_list;
    }
	
	/**
    * This function filters news articles by date,author or category.
    *
    * @param string $filterVar -- The $_GET variable that identifies what kind of filter to perform.
    * @param string $filter_by -- The name to filter articles by.
    * @param integer $filter_count -- The max number of articles to show, Defaults is 3.
    * @param $filter_sort_by -- default sorting method ["date","category","author"]
    * @param $filter_ascdsc -- default sort ordering (ascending or descending) ["ASC","DESC"]
	* @return array -- Returns the filtered articles
	*/
	function filterArticles( $filterVar,$filter_by='author',$filter_count=3,$filter_sort_by='title',$filter_ascdsc='ASC' ){
		$i = 0;
		// get all news articles
		$posts = GetArticles($filter_count,$filter_sort_by,$filter_ascdsc);
		// filter the news articles according to the parameters specified.
		foreach ($posts as $key => $post) {
		    if($filter_by == 'date' &&  $post[5] !== $filterVar ) unset( $posts[ $key ] );
			else if($filter_by == 'author' && strtolower( $post[3] ) !== strtolower( $filterVar ) ) unset( $posts[ $key ] );
			else if($filter_by == 'category' && strtolower( $post[7] ) !== strtolower( $filterVar ) ) unset( $posts[ $key ] );
			else if($filter_by == 'tag' && !preg_match('/\b'.$filterVar.'\b/', $post[8]) ) unset( $posts[ $key ] );
			$i++;
		}
		// return the filtered articles
		return $posts;
	}
    // league table sorter
	function sort_table($a,$b) {
        if ( $GLOBALS['t_sortby'] == "filename" ) $n = 0;
        elseif ( $GLOBALS['t_sortby'] == "id" ) $n = 1;
        elseif ( $GLOBALS['t_sortby'] == "name" ) $n = 2;
        elseif ( $GLOBALS['t_sortby'] == "pl" ) $n = 3;
        elseif ( $GLOBALS['t_sortby'] == "win" ) $n = 4;
        elseif ( $GLOBALS['t_sortby'] == "lose" ) $n = 5;
        elseif ( $GLOBALS['t_sortby'] == "draw" ) $n = 6;
        elseif ( $GLOBALS['t_sortby'] == "gf" ) $n = 7;
        elseif ( $GLOBALS['t_sortby'] == "ga" ) $n = 8;
        elseif ( $GLOBALS['t_sortby'] == "gd" ) $n = 9;
        elseif ( $GLOBALS['t_sortby'] == "pts" ) $n = 10;
        
        $m = ( $GLOBALS['t_ascdsc'] == "ASC" ) ? 1 : -1;
        if ( $a[$n] == $b[$n] ) return 1;
        return ($a[$n] > $b[$n]) ? $m : -1*$m;
    }
	// file sorter
    function sort_file($a,$b) {
        if ( $GLOBALS['f_sortby'] == "filename" ) $n = 0;
        elseif ( $GLOBALS['f_sortby'] == "id" ) $n = 1;
        elseif ( $GLOBALS['f_sortby'] == "title" ) $n = 2;
        elseif ( $GLOBALS['f_sortby'] == "author" ) $n = 3;
		elseif ( $GLOBALS['f_sortby'] == "createdate" ) $n = 4;
		elseif ( $GLOBALS['f_sortby'] == "moddate" ) $n = 5;
		elseif ( $GLOBALS['f_sortby'] == "time" ) $n = 6;
		elseif ( $GLOBALS['f_sortby'] == "size" ) $n = 7;
		elseif ( $GLOBALS['f_sortby'] == "count" ) $n = 8;
		elseif ( $GLOBALS['f_sortby'] == "type" ) $n = 9;
        $m = ( $GLOBALS['f_ascdsc'] == "ASC" ) ? 1 : -1;
        if ( $a[$n] == $b[$n] ) return 1;
        return ($a[$n] > $b[$n]) ? $m : -1*$m;
    }
	// article sorter
    function sort_article($a,$b) {
		if ( $GLOBALS['sortby'] == "filename" ) $n = 0;
        elseif ( $GLOBALS['sortby'] == "id" ) $n = 1;
        elseif ( $GLOBALS['sortby'] == "title" ) $n = 2;
        elseif ( $GLOBALS['sortby'] == "author" ) $n = 3;
        elseif ( $GLOBALS['sortby'] == "email" ) $n = 4;
        elseif ( $GLOBALS['sortby'] == "date" ) $n = 5;
        elseif ( $GLOBALS['sortby'] == "moddate" ) $n = 6;
        elseif ( $GLOBALS['sortby'] == "category" ) $n = 7;
		elseif ( $GLOBALS['sortby'] == "keywords" ) $n = 8;
	    elseif ( $GLOBALS['sortby'] == "news_image" ) $n = 9;
		elseif ( $GLOBALS['sortby'] == "cat_image" ) $n = 10;
		elseif ( $GLOBALS['sortby'] == "summary" ) $n = 11;
		elseif ( $GLOBALS['sortby'] == "details" ) $n = 12;
		elseif ( $GLOBALS['sortby'] == "publish" ) $n = 13;
         //else  $n = 4;
        $m = ( $GLOBALS['ascdsc'] == "ASC" ) ? 1 : -1;
        if ( $a[$n] == $b[$n] ) return 1;
        return ($a[$n] > $b[$n]) ? $m : -1*$m;
    }
// ------------------------------< File manipulation >---------------------------------	
    /**
	 * Remove extension from a file. eg.(file.php = file) or (news.txt = news)
	 *
	 * @param string $filename -- The string/name of the file to be split
	 * @return string -- The splitted string
	*/
    function removeFileExt($filename) {
        $newFileName = @explode('.',$filename);
        return $newFileName[0];
    }

	/**
	 * Clean a string for use as a page title (url)
	 * Removes potentially problematic characters
	 *
	 * @param string -- $title The string to be cleansed
	 * @param string -- $spaces The string spaces will be replaced with -- default '_'
	 * @return string -- The cleansed string
	 */
	function CleanTitle($title,$echo=false,$spaces = '_'){
		if( empty($title) ){
			return $title;
		}

		// Remove control characters
		$title = preg_replace( '#[[:cntrl:]]#u', '', $title ) ; // 	[\x00-\x1F\x7F]
		$title = str_replace(array('"',"'",'?','*',':'),array(''),$title); // # needed for entities
		$title = str_replace(array('<','>','|','\\'),array(' ',' ',' ','/'),$title);
		$title = preg_replace('#\.+([\\\\/])#','$1',$title);
		$title = trim($title,'/');

		$title = trim($title);
		if( $spaces ){
			//$title = preg_replace( '#[[:space:]]#', $spaces, $title );
			$title = str_replace(' ',$spaces,$title);
		}
        
		if($echo) echo $title;
		else return $title;
	}
    /*
	 * This function strips a string of some specific characters [-,__,___] , and replace them with a space.
	 *
	 * @param string $title -- the string to be stipped
	 * @return string
	*/
    function cleanPageTitles($title,$echo=false){
        $clean_title = str_replace(array('-','_','__'),' ', $title);
		if(strlen($clean_title) <=3 ) $clean_title = strtoupper($clean_title);
		else $clean_title = ucwords($clean_title);
        if($echo) echo $clean_title;
		else return $clean_title;
    }
	/*
	 * This function return all the categories
	 *
	 * @param $return_array bool -- true/false, return categories in an array or in an ordered list
	 * @param $df_sortby -- default sorting method ["id","count","title"]
     * @param $df_ascdsc -- default ordering (ascending or descending) ["ASC","DESC"]
	 * @return mix -- array, string
	*/
    function GetCategories($return_array=true,$df_sortby="id",$df_ascdsc="ASC"){
	    global $JSON,$Category_DB_File;
		$PB_output = NULL; //erase variable which contains data
		$GLOBALS['f_sortby'] = $df_sortby;
        $GLOBALS['f_ascdsc'] = $df_ascdsc;
		
		$categories = array();
	    if(file_exists($Category_DB_File)){
		    $fp = @fopen($Category_DB_File, 'r');
		    $array = explode("\n", fread($fp, filesize($Category_DB_File))); 
			for($x=0;$x<sizeof($array);$x++) {	// start loop, each line of file
		        $temp = explode(":",$array[$x]); // explode the line and assign to temp
				$tmp=array();
				$tmp[1] = $temp[0];
				$tmp[2] = $temp[1];
				$tmp[8] = $temp[2];
		        array_push($categories,$tmp);
			}
			if($return_array) {
			    return $categories;
			} else {
				usort($categories,'sort_file');
			    $total_categories = count($categories);
			    if($total_categories > 0){
				    $PB_output .= '<ol>';
			        for( $i=0; $i<$total_categories; $i++ ) {
						if( isset($categories[$i][1]) ) {
							$cat_id = $categories[$i][1];
							$cat_name = $categories[$i][2];
					        $cat_image = $categories[$i][8];
				            $PB_output .= '<li><a href="?mode=filter&amp;filterby=category&amp;category='.$cat_name.'&amp;categoryID='.$cat_id.'">'.$cat_name.'</a></li>';
			            }else {
							if( isset($categories[$i][1]) ) $PB_output .= $categories[$i][1];
						}
			        }
			        $PB_output .= '</ol>';
			    }
			}
			 
	    } else $PB_output .= '<div class="message user_status"><p>'._MISSING_CATEGORY_DB_FILE.'</p></div>'."\n";
		
		return $PB_output;
	}
	
    function GetArticles($max_num_news=3,$df_sortby = "title",$df_ascdsc = "DESC"){
	    global $tfxs,$UTIL,$JSON;
		$sortby = isset($_COOKIE['sortby']) ? $_COOKIE['sortby'] : $df_sortby ;
	    if(isset($_GET['sortby'])){
	        $sortby = isset($_GET['sortby']) ? $_GET['sortby'] : $df_sortby;
		    //setcookie("sortby", $_GET['sortby'], time()+86400 );
	    }
	    $ascdsc = isset($_GET['ascdsc']) ? $_GET['ascdsc'] : $df_ascdsc;
		$GLOBALS['sortby'] = $sortby;
        $GLOBALS['ascdsc'] = $ascdsc;
		
		$path = ARTICLES_PATH;
		$max_count = 0; //set recents to zero
		$news_files = array();
		
		if(is_dir($path)){
		    $handle = opendir($path);
		    while($file = readdir($handle)) {
			    if($UTIL->isValidExt($file,$tfxs)){
				    if ($max_count < $max_num_news ) {
				        $tmp = array();
					    $tmp[0] = $file;
			            $file = $JSON->decode(file_get_contents($path.$file));
			        
			            $tmp[1] = $file->id;
                        $tmp[2] = $file->title;
				        $tmp[3] = $file->author;
                        $tmp[4] = $file->email;
                        $tmp[5] = $file->date;
                        $tmp[6] = $file->date_modified;
				        $tmp[7] = isset($file->category) ? $file->category : 'un-categorized';
					    $tmp[8] = isset($file->keywords) ? $file->keywords : '';
					    $tmp[9] = isset($file->news_image) ? $file->news_image : '';
				        $tmp[10] = isset($file->cat_image) ? $file->cat_image : '';
                        $tmp[11] = $file->summary;
				        $tmp[12] = isset($file->details) ? $file->details : '';
				        $tmp[13] = isset($file->publish) ? $file->publish : 'YES';
					
                        array_push($news_files,$tmp);
					    $max_count++;
					}
			    }
		    }
		    closedir($handle);
		    usort($news_files,'sort_article');
			
			return $news_files;
		} else {
	       return( '<div class="message user_status"><p><b>News Directory Not Found or it does not exists. Ensure that the news article directory is set to an existing directory in the file <em>[constants.php]</em></b> ...</p></div>'."\n");
        }
	}
	
    /**
    * This function gets the most recent news articles.
    *
    * @param integer $max_recent -- The max number of articles to show, Defaults is 3.
    * @param $df_sortby -- default sorting method ["date","category","title"]
    * @param $df_ascdsc -- default ordering (ascending or descending) ["ASC","DESC"]
	*/
	function GetRecentEntryList($max_recent=3,$df_sortby = "title",$df_ascdsc = "DESC",$echo=false){
	    global $separator;
		$PB_output = NULL; //erase variable which contains data
		$ascdsc = isset($_GET['ascdsc']) ? $_GET['ascdsc'] : $df_ascdsc;
		$sortby = isset($_GET['sortby']) ? $_GET['sortby'] : $df_sortby;
		$GLOBALS['sortby'] = $sortby;
        $GLOBALS['ascdsc'] = $ascdsc;
		
		$recent_files = GetArticles($max_recent,$sortby,$ascdsc);
		
		$PB_output .= '<div class="widget-title">Recent Articles</div>';
	    if(count($recent_files) > 0){
			usort($recent_files,'sort_article');
				
		    $PB_output .= '<ul class="post-list">';
			    foreach($recent_files as $recent_news_row){
                    $recent_news_filename = removeFileExt($recent_news_row[0]);
			        $recent_news_title = $recent_news_row[2];
			        $recent_news_author = $recent_news_row[3];
                    $recent_news_date = $recent_news_row[5];
			        $recent_news_image = $recent_news_row[9];
			        $recent_cat_image = $recent_news_row[10];
                       
			        $ryear = substr($recent_news_date, 0, 4);
                    $rmonth = substr($recent_news_date, 5,-3);
                    $rday = substr($recent_news_date, 8);
				
			        $view_url = SELF.'?mode=viewArticle&amp;newsArticle='.$recent_news_filename;
				    $img_alt = substr($recent_news_title,0,20);
				    if(is_file(ARTICLES_IMAGE_PATH.$recent_news_image)) $news_thumb = ARTICLES_IMAGE_URL.$recent_news_image;
				    else $news_thumb = CATEGORY_IMAGE_URL.$recent_cat_image;
				
				    $PB_output .= '<li>';
			  		    $PB_output .= '<div class="frame"><a href="'.$view_url.'"><img src="'.$news_thumb.'" alt="'.$img_alt.'" width="67px" height="67px" /></a></div>';
					    $PB_output .= '<div class="meta">
					        <div class="heading"><a href="'.$view_url.'">'.$recent_news_title.'</a></div>
					        <div class="postdate"><em> Posted by '.ucfirst($recent_news_author).' on '.$rday.'-'.$rmonth.'-'.$ryear.'</em></div>
				        </div>';
				    $PB_output .= '</li>';
	            }
		    $PB_output .= '</ul>'."\n";
	    } else {
	        $PB_output .= '<div class="message user_status"><p><b>No News Article File Found</b> ...</p></div>'."\n";
        }
		
        if($echo) echo $PB_output;
		else return $PB_output;
	}
	/**
    * This function gets the most recent news articles.
    *
    * @param integer -- $max_num_news The max number of articles to show, Defaults is 3.
    * @param $df_sortby -- default sorting method ["date","category","title"]
    * @param $df_ascdsc -- default ordering (ascending or descending) ["ASC","DESC"]
	*/
	function getNewsArticles($max_num_news=3,$df_sortby = "title",$df_ascdsc = "DESC",$echo=false){
	    global $tfxs,$UTIL,$separator,$News_DB_File,$JSON;
		$roleID = getRoleId();
		$recent_count = 0; //set recents to zero
		$PB_output = NULL; //erase variable which contains data
		$sortby = isset($_GET['sortby']) ? $_GET['sortby'] : $df_sortby;
	    $ascdsc = isset($_GET['ascdsc']) ? $_GET['ascdsc'] : $df_ascdsc;
		$GLOBALS['sortby'] = $sortby;
        $GLOBALS['ascdsc'] = $ascdsc;
		$create_url = SELF.'?mode=admin&amp;section=addnews';
		
		$news_files = array();
		$news_files = GetArticles($max_num_news,$sortby,$ascdsc);
		
	    if(count($news_files) > 0){
			usort($news_files,'sort_article');
			$total_news_articles = count($news_files);
				
			for($x=0;$x<$total_news_articles;$x++){
				if($news_files[$x][13] == 'YES') $publish_y[] = $news_files[$x][13]; 
				if($news_files[$x][13] == 'NO') $publish_n[] = $news_files[$x][13]; 
			}
			
			$total_published = count($publish_y);
			$total_unpublished = count($publish_n);
			
			$PB_output .= '<div class="header"><h2>Most Recent Posts ';
			    if($roleID >= 3) $PB_output .= '<span>'.$total_news_articles.' News Article Found, '.$total_published.' published and '.$total_unpublished.' unpublished</span>';
			$PB_output .= '</h2></div>';
			
			foreach($news_files as $news_row){
			    //if $recent_count are not more than $max_num_news
		        if ($recent_count < $max_num_news ) {
                    $news_file = removeFileExt($news_row[0]);
                    $news_id = $news_row[1];
			        $news_title = $news_row[2];
			        $news_author = $news_row[3];
			        $news_author_email = $news_row[4];
                    $news_date = $news_row[5];
                    $news_moddate = $news_row[6];
                    $news_category = $news_row[7];
                    $news_keywords = $news_row[8];
				    $news_image = $news_row[9];
			        $cat_image = $news_row[10];
                    $news_summary = $news_row[11];
                    $news_details = $news_row[12];
			        $publish = $news_row[13];
         
			  		$PB_output .= showArticleBox($news_file,$news_id,$news_title,$news_author,$news_author_email,$news_date,$news_moddate,$news_category,$news_keywords,$news_image,$cat_image,$news_summary,$news_details,$publish);
					
				    $recent_count++; //increment recents
		        }
	        }
	    } else {
		    $PB_output .= '<div class="message user_status"><p><b>No News Article File Found</b> ...</p>';
		    if($roleID >= 3) $PB_output .= '<p><a href="'.$create_url.'">Create a news article</a></p></div>'."\n";
		}
            
        if($echo) echo $PB_output;
		else return $PB_output;
	}
	
	function showArticleBox($file,$id,$title,$author,$email,$date,$moddate,$category,$keywords,$image,$cat_image,$summary,$details,$publish){
	    global $ifxs,$UTIL,$roleID,$separator;
		$img_alt = substr($title,0,10);
		$nyear = substr($date, 0, 4);
        $nmonth = substr($date, 5,-3);
        $nday = substr($date, 8);
		$full_date = "$nyear-$nmonth-$nday";
		
		$view_url = SELF.'?mode=viewArticle&amp;newsArticle='.$file;
		$edit_url = SELF.'?mode=admin&amp;section=manage&amp;action=edit&amp;filetype=article&amp;newsArticle='.$file;
		$delete_url = SELF.'?mode=admin&amp;section=manage&amp;action=delete&amp;filetype=article&amp;newsArticle='.$file;
		
		$PB_output = NULL;
	    if($publish == 'YES'){
		    $PB_output .= '<div class="grid-2 box post">'."\n";
			    if(file_exists(ARTICLES_IMAGE_PATH.$image) && $UTIL->isValidExt(ARTICLES_IMAGE_PATH.$image,$ifxs)){
			        $PB_output .= '<div class="frame"><a href="'.$view_url.'"><img src="'.ARTICLES_IMAGE_URL.$image.'" align="center" alt="'.$img_alt.' image" /></a></div>'."\n";
				    //$PB_output .= '<br/>';
                }
			    $PB_output .= '<h3 class="title" align="center"><a href="'.$view_url.'"><b>'.$title.'</b></a></h3>'."\n";
			    $PB_output .= '<div class="cat_image grid-1 left-1"><img src="'.CATEGORY_IMAGE_URL.$cat_image.'" alt="'.$img_alt.'"/></div>'."\n";
            
			    $PB_output .= '<div class="grid-7 right-1 content">';
				    $PB_output .= '<span align="justify">'.$summary.'</span>';
				    if(isset($details) && $details > $summary) $PB_output .= '&nbsp;&nbsp;<span><a href="'.$view_url.'" title="click to read more about ['.$title.']">Read More....</a></span>';
				$PB_output .= '</div>'."\n";
				$PB_output .= '<br class="clear" />'."\n";
				$PB_output .= '<div class="details">';
				    //$PB_output .= '<span class="icon-image"><a href="#"><font color="#0099cc" size="2"> Posted by '.(ucfirst($author)).' on '.$full_date.'</font></a></span>';
                    $PB_output .= '<span class="icon-image"><font color="#0099cc" size="2"> Posted by <a href="'.$_SERVER['PHP_SELF'].'?mode=filter&amp;filterby=author&amp;author='.$author.'">'.ucfirst($author).'</a> on <a href="'.$_SERVER['PHP_SELF'].'?mode=filter&amp;filterby=date&amp;date='.$full_date.'">'.$full_date.'</a> in <a href="?mode=filter&amp;filterby=category&amp;category='.$category.'">'.ucfirst($category).'</a></font></span>';
					if($roleID >= 3){
                        $PB_output .= '<span class="left-1 icon-link">';
                            $PB_output .= '<span class="links"><a href="'.$delete_url.'" title="Delete News Post">delete</a></span><span class="links">&nbsp;-&nbsp;</span><span class="links"><a href="'.$edit_url.'" title="Edit News Post">edit</a></span>';
                        $PB_output .= '</span>';
                    }
					// add facebook url
				    $PB_output .= '<span class="likes"><a href="#" class="likeThis">44</a></span>';
				    $PB_output .= '<span class="comments"><a href="?mode=comments&amp;newsArticle='.$file.'">3</a></span>';
			    $PB_output .= '</div>'."\n";
			
            $PB_output .= '</div>';
		} else if($roleID >= 3) $PB_output .= '<div class="grid-2 box post"><div class="message user_status"><p>The News Article <span class="em i">'.$separator.$file.'</span> was <span class="b">not published</span> ...</p></div><div class="message unspecific"><p><a href="'.$edit_url.'">Publish this news article</a></p></div></div>'."\n";
	    
		return $PB_output;
	}
	/*
	 * This function return all the selected league, table data 
	 * @return array
	*/
	function GetTable($selected_league='epl',$echo=false){
		global $JSON,$League_DB_File;
		
	    $PB_output = NULL;
		$df_sortby = "pts"; // default sorting method ["pts","gd","ga","gf","name","win","lose","draw","pl"]
        $df_ascdsc = "DESC";// default ordering (ascending or descending) ["ASC","DESC"]
        $t_sortby = ( isset($_GET["t_sortby"]) ) ? $_GET["t_sortby"] : $df_sortby;
        $t_ascdsc = ( isset($_GET["t_ascdsc"]) ) ? $_GET["t_ascdsc"] : $df_ascdsc;
        $season = ( isset($_GET["season"]) ) ? $_GET["season"] : SEASON;
		$GLOBALS['t_sortby'] = $t_sortby;
        $GLOBALS['t_ascdsc'] = $t_ascdsc;
	    $url = $_SERVER["PHP_SELF"]."?mode=".MODE."&amp;section=".SECTION."&amp;league=$selected_league";
		$create_url = $_SERVER["PHP_SELF"]."?mode=".MODE."&amp;section=addleague";
	    
		$ext = pathinfo($selected_league, PATHINFO_EXTENSION);
	    $league = $selected_league.'/'.SEASON.'_league_table.txt';
		$path = LEAGUE_PATH.$league;
		$i = 1;
		$league_tables = array();
		//$allLeagueSeasons = GetDirContents(LEAGUE_PATH.$selected_league.'/','dirs');
		if(file_exists($path)){
            $results = $JSON->decode(file_get_contents($path));
			if(is_array($results)) {
			    foreach($results as $league_table => $key ){
				    $files = array();
				    $files[0] = $league;
				    $files[1] = $results[$league_table]->id;
				    $files[2] = $results[$league_table]->name;
					
				    $total_played = $results[$league_table]->win + $results[$league_table]->lose + $results[$league_table]->draw;
                    $files[3] = $total_played;
				    $files[4] = $results[$league_table]->win;
                    $files[5] = $results[$league_table]->lose;
                    $files[6] = $results[$league_table]->draw;
                    $files[7] = $results[$league_table]->gf;
                    $files[8] = $results[$league_table]->ga;
					
                    $goaldifference = calculateGoalDifference($files[7],$files[8]);
					$files[9] = $goaldifference;
					//$files[9] = $results[$league_table]->gd;
					
				    $total_points = calculatePoints($files[4],$files[4],$files[6]);
				    //$files[10] = $$results[league_table]->pts;
                    $files[10] = $total_points;
			        array_push( $league_tables,$files);
	            }
			} else {
			    $team_list = file($path);
			    foreach($team_list as $league_table){
				    $files = array();
				    $files[0] = $league_table;
			        $league_table = json_decode($league_table);
				    $files[1] = $league_table->id;
			    	$files[2] = $league_table->name;
				    $total_played = $league_table->win + $league_table->lose + $league_table->draw; 
                    //$files[3] = $league_table->pl;
                    $files[3] = $total_played;
				    $files[4] = $league_table->win;
                    $files[5] = $league_table->lose;
                    $files[6] = $league_table->draw;
                    $files[7] = $league_table->gf;
                    $files[8] = $league_table->ga;
					$goaldifference = calculateGoalDifference($files[7],$files[8]);
					$files[9] = $goaldifference;
                    //$files[9] = $league_table->gd;
				    $total_points = calculatePoints($files[4],$files[4],$files[6]);
				    //$files[10] = $league_table->pts;
                    $files[10] = $total_points;
			        array_push( $league_tables,$files);
	            }
			}
			return $league_tables;
	    }
		return false;
    }
	/*
	 * This function return the team profile of a selected team in an array
	 * @param string $file -- the name of the team 
	 * @return array [team name,team profile]
	*/
	function getTeamProfile($file){
	    global $JSON;
	    $ext = pathinfo($file, PATHINFO_EXTENSION);
	    $filename = ($ext == '') ? $file.'.txt' : $file;
		$profile_path = LEAGUE_PATH.LEAGUE.'/'.SEASON.'/profiles/'.$filename;
		$profiles = array();
	    if(file_exists($profile_path)){
		    $team_profiles = $JSON->decode(file_get_contents($profile_path));
		    if(is_array($team_profiles)) {
			    foreach($team_profiles as $league_list => $key ){
					if($file == $team_profiles[$league_list]->team_name) {
					    $profiles[0] = $team_profiles[$league_list]->team_name;
					    $profiles[1] = $team_profiles[$league_list]->team_profile;
					}
			    }
		    } else {
                $team_profiles = file($profile_path);
                foreach($team_profiles as $profile_list_rows){
				    $profile_row = $JSON->decode($profile_list_rows);
				    if($file==$profile_row->team_name) {
					    $profiles[0] = $profile_row->team_name;
				    	$profiles[1] = $profile_row->team_profile;
					}
			    }
			}
			return $profiles;
	    }
		
		return array(0=>$file,1=>'The profile for '.$file.' was not found!');
	}
	
	/*
	 * This function return all the leagues in an array
	*/
	function GetLeagues(){
	    global $League_DB_File,$JSON;
		
		$league_files = array();
		
	    if(file_exists($League_DB_File)){
		    $results = $JSON->decode(file_get_contents($League_DB_File));
		    if(is_array($results)) {
			    foreach($results as $league_list => $key ){
			        $tmp=array();
				    $tmp[0] = $results[$league_list];
				    $tmp[1] = $results[$league_list]->league_id;
				    $tmp[2] = $results[$league_list]->league_title;
				    $tmp[3] = $results[$league_list]->league_name;
				    $tmp[9] = $results[$league_list]->league_image;
					array_push($league_files,$tmp);
			    }
		    } else {
			    $results = file($League_DB_File);
		        foreach($results as $league_list){
					$tmp = array();
					$tmp[0] = $league_list;
		            $league_list = $JSON->decode($league_list);
			          
			        $tmp[1] = $league_list->league_id;
					$tmp[2] = $league_list->league_title;
			        $tmp[3] = $league_list->league_name;
			        $tmp[9] = $league_list->league_image;
			        array_push($league_files,$tmp);
		        }
		    }
			
			return $league_files;
	    } else return $league_files; //return false;
	}
	/*
	 * This function return the info of a selected league in an array
	 * @param string -- the name of the league 
	*/
	function GetLeagueInfo($file){
	    global $League_DB_File,$JSON;
		
		$PB_output = array();
	    if(file_exists($League_DB_File)){
		    $league_rows = $JSON->decode(file_get_contents($League_DB_File));
		    if(is_array($league_rows)) {
			    foreach($league_rows as $league_list => $key ){
					if($file == $league_rows[$league_list]->league_name) {
					    $PB_output['league_id'] = $league_rows[$league_list]->league_id;
					    $PB_output['league_name'] = $league_rows[$league_list]->league_name;
					    $PB_output['league_title'] = $league_rows[$league_list]->league_title;
					    $PB_output['league_logo'] = $league_rows[$league_list]->league_image;
					}
			    }
		    } else {
                $league_rows = file($League_DB_File);
                foreach($league_rows as $league_list_rows){
				    $league_row = $JSON->decode($league_list_rows);
				    if($file==$league_row->league_name) {
					    $PB_output['league_id'] = "$league_row->league_id";
					    $PB_output['league_name'] = "$league_row->league_name";
					    $PB_output['league_title'] = "$league_row->league_title";
					    $PB_output['league_logo'] = "$league_row->league_image";
					}
			    }
			}
	    } else $PB_output = '<div class="message user_status"><p>'._MISSING_LEAGUE_DB_FILE.'</p></div>'."\n";
		
		return $PB_output;
	}
	
    function GetLeagueTables($selected_league='epl',$echo=false){
		global $JSON,$League_DB_File,$allLeagueSeasons,$separator;
		
	    $PB_output = NULL;
		$df_sortby = "pts"; // default sorting method ["pts","gd","ga","gf","name","win","lose","draw","pl"]
        $df_ascdsc = "DESC";// default ordering (ascending or descending) ["ASC","DESC"]
        $t_sortby = ( isset($_GET["t_sortby"]) ) ? $_GET["t_sortby"] : $df_sortby;
        $t_ascdsc = ( isset($_GET["t_ascdsc"]) ) ? $_GET["t_ascdsc"] : $df_ascdsc;
        //$allLeagueSeasons = GetDirContents(LEAGUE_PATH.$selected_league,'dirs');
		$season = (( isset($_GET["season"]) ) ? $_GET["season"] : ((count($allLeagueSeasons) > 1 ) ? end($allLeagueSeasons) : $allLeagueSeasons[0]));
        $GLOBALS['t_sortby'] = $t_sortby;
        $GLOBALS['t_ascdsc'] = $t_ascdsc;
	    $url = SELF."?mode=".MODE."&amp;section=".SECTION."&amp;league=$selected_league";
		$create_url = SELF."?mode=".MODE."&amp;section=addleague";
	    
		$ext = pathinfo($selected_league, PATHINFO_EXTENSION);
	    $league = $selected_league.'/'.SEASON.'_league_table.txt';
		$path = LEAGUE_PATH.$league;
		$fixtures_path = LEAGUE_PATH.$selected_league.'/'.SEASON.'/fixtures/';
	    $fixtures_list = GetDirContents($fixtures_path,'files');
		$results_path = LEAGUE_PATH.$selected_league.'/'.SEASON.'/results/';
	    $results_list = GetDirContents($results_path,'files');
		$i = 1; 
		if(file_exists($path)){
		    $league_tables = array();
		    $league_info = GetLeagueInfo($selected_league);
            $league_tables = GetTable($selected_league);
			
            usort($league_tables,"sort_table");
			
			$PB_output .= '<div class="league-table grid-4">';//$PB_output .= '<div class="league_tables corner_5_all">';
			    $PB_output .= '<div class="header"><span class="league-logo"><img src="'.LOGO_URL.$league_info['league_logo'].'" alt="" /></span><h2 align="center">'.$league_info['league_title'].' : '.SEASON.'</h2><span class="title panel grid-4"><a href="#" onclick="return kadabra(\'sort_table\');" title="toggle table select menu">click to show table sorting panel</a></span></div>';

				$PB_output .= '<table class="full-table">';
			        $PB_output .= '<thead><tr class="table-header">';
				        $PB_output .= '<th scope="col">Pos</th><th scope="col">&nbsp;</th>
			            <th><a href="'.$url.'&amp;t_sortby=name&amp;t_ascdsc=ASC" title="sort by team names ascending">Team</a></th>
					    <th scope="col"><a href="'.$url.'&amp;t_sortby=pl&amp;t_ascdsc=ASC" title="sort by matches played ascending">Pl</a></th>
					    <th scope="col"><a href="'.$url.'&amp;t_sortby=win&amp;t_ascdsc=ASC" title="sort by matches won ascending">W</a></th>
					    <th scope="col"><a href="'.$url.'&amp;t_sortby=lose&amp;t_ascdsc=ASC" title="sort by matches lost ascending">L</a></th>
					    <th scope="col"><a href="'.$url.'&amp;t_sortby=draw&amp;t_ascdsc=ASC" title="sort by matches drawn ascending">D</a></th>
					    <th scope="col"><a href="'.$url.'&amp;t_sortby=gf&amp;t_ascdsc=ASC" title="sort by goals scored for ascending">GF</a></th>
					    <th scope="col"><a href="'.$url.'&amp;t_sortby=ga&amp;t_ascdsc=ASC" title="sort by goals scored against ascending">GA</a></th>
					    <th scope="col"><a href="'.$url.'&amp;t_sortby=gd&amp;t_ascdsc=ASC" title="sort by goals difference ascending">GD</a></th>
					    <th scope="col"><a href="'.$url.'&amp;t_sortby=pts&amp;t_ascdsc=ASC" title="sort by total points ascending">PTS</a></th>';
					$PB_output .= '</tr></thead>';
				    
					$PB_output .= '<tbody>';
				        $PB_output .= '<tr class="sort-row"><td colspan="11" align="center">
                            <center><div id="sort_table" class="grid-4" style="display:none;"><form name="f" action="" method="GET"><input name="mode" type="hidden" value="'.MODE.'"><input name="section" type="hidden" value="'.SECTION.'">';
							$PB_output .= '<table border="0" cellpadding="0">';
							    
								$PB_output .= '<tr><td align="center">Select table<br />';
								        if(file_exists($League_DB_File)){
									        $PB_output .= '<select name="league">';
								                $league_list = $JSON->decode(file_get_contents($League_DB_File));
								                if(is_array($league_list)){ 
										            foreach($league_list as $league_row) $PB_output .= '<option value="'.$league_list[$league_row]->league_name.'" '.(($selected_league==$league_list[$league_row]->league_name) ? "SELECTED" : '' ).'>'.$league_row->league_name.'</option>'."\n";
										        } else {
										            $league_list = file($League_DB_File);
                                                    foreach($league_list as $league_list_rows){
						                                $league_row = $JSON->decode($league_list_rows);
						                                $PB_output .= '<option value="'.$league_row->league_name.'" '.(($selected_league==$league_row->league_name) ? "SELECTED" : '' ).'>'.$league_row->league_name.'</option>'."\n";
					                                }
										        }
                                            $PB_output .= '</select>';
									    } else $PB_output = '<div class="message user_status"><p>'._MISSING_LEAGUE_DB_FILE.'</p></div>'."\n";
                                    $PB_output .= '</td>';
								
							        $PB_output .= '<td colspan="" align="center">sort by<br/>';
							        $PB_output .= '<select name="t_sortby">
                                        <option value="-1">-- sort by --</option> 
                                        <option value="pts" '.( ($t_sortby=="pts") ? "SELECTED" : '' ).">points</option>";
                                        $PB_output .= '<option value="gd" '.( ($t_sortby=="gd") ? "SELECTED" : '' ).'>goal difference</option>';
                                        $PB_output .= '<option value="gf" '.( ($t_sortby=="gf") ? "SELECTED" : '').'>goals for</option>';
                                        $PB_output .= '<option value="ga" '.( ($t_sortby=="ga") ? "SELECTED" : '' ).'>goals against</option>';
                                        $PB_output .= '<option value="draw" '.( ($t_sortby=="draw") ?"SELECTED" : '' ).'>draw</option>';
                                        $PB_output .= '<option value="lose" '.( ($t_sortby=="lose") ? "SELECTED" : '' ).'>lose</option>';
                                        $PB_output .= '<option value="win" '.( ($t_sortby=="win")? "SELECTED" : '' ).'>win</option>';
                                        $PB_output .= '<option value="pl" '.( ($t_sortby=="pl") ? "SELECTED" : '' ).'>matches played</option>';
                                        $PB_output .= ' <option value="name" '.( ($t_sortby=="name") ? "SELECTED" : '' ).'>name</option>';
							        $PB_output .= '</select>';
							        $PB_output .= '</td>';
							    
						    	    $PB_output .= '<td align="center">ascending<input name="t_ascdsc" value="ASC" type="radio" '.( ($t_ascdsc=="ASC") ? " CHECKED" : '' ).'></td>';
                                    $PB_output .= '<td align="center">descending<input name="t_ascdsc" value="DESC" type="radio" '.( ($t_ascdsc=="DESC") ? " CHECKED" : '' ).'></td>';
								    
									$PB_output .= '<td align="center">season';
					                if(count($allLeagueSeasons) > 0){
				                        $PB_output .='<select name="season">';
                                            foreach($allLeagueSeasons as $season_rows) $PB_output .= '<option value="'.$season_rows.'" '.((SEASON == $season_rows) ? 'SELECTED' : '' ).'>'.$season_rows.'</option>';
				                        $PB_output .='</select>';
				                    } else $PB_output .= '<div class="message user_status"><p><b>This league<span class="em i">'.$separator.LEAGUE.'</span>, has no <span class="b">Season Folder</span></b></p></div>'."\n";
					                $PB_output .='</td>';
								$PB_output .= '</tr>';
								
                                $PB_output .= '<tr><td colspan="6" align="center"><input type="submit" class="update" value="" title="sort table"></td></tr>';
                            
							$PB_output .= '</table>';
                            $PB_output .= '</form></div></center>';
                        $PB_output .= '</td></tr>';
				       
			            foreach($league_tables as $table){
						    $logo_tmp_name = str_replace(array(' ','&nbsp;','-'),'_',$table[2]);
						    $logo_png = LEAGUE.'/'.SEASON.'/icons/'.$logo_tmp_name.'.png';
						    $logo_gif = LEAGUE.'/'.SEASON.'/icons/'.$logo_tmp_name.'.gif';
						    $logo_jpg = LEAGUE.'/'.SEASON.'/icons/'.$logo_tmp_name.'.jpg';
							
							if(file_exists(LEAGUE_PATH.$logo_png)) $team_logo = LEAGUE_URL.$logo_png;
							elseif(file_exists(LEAGUE_PATH.$logo_gif)) $team_logo = LEAGUE_URL.$logo_gif;
							elseif(file_exists(LEAGUE_PATH.$logo_jpg)) $team_logo = LEAGUE_URL.$logo_jpg;
							else $team_logo = IMG_URL.'football_classic1.png';
					         
							if($GLOBALS['t_sortby'] == 'pts'){
							    if($i>0 && $i<5) $row_class = 'champions-league-row';
							    else if($i>4 && $i<8) $row_class = 'europa-league-row';
							    else if($i>17) $row_class= 'relegation-row';
							    else $row_class = 'regular-row';
							} else $row_class = 'regular-row';
							   
			                $PB_output .= '<tr class="'.$row_class.'">';
					            $PB_output .= '<td class="position" align="center">'.$i.'</td>
							    <td class="logo"><img style="width:35px;" src="'.$team_logo.'" alt="'.$table[2].' logo" /></td>
				        	    <td title="'.$table[2].'" class="team-name"><a href="'.SELF.'?mode=profiles&amp;section=teamprofile&amp;league='.$selected_league.'&amp;team='.$table[2].'">'.ucwords($table[2]).'</a></td>
					            <td class="played">'.$table[3].'</td>
				        	    <td class="wins">'.$table[4].'</td>
					            <td class="loss">'.$table[5].'</td>
					            <td class="draws">'.$table[6].'</td>
				        	    <td class="for">'.$table[7].'</td>
					            <td class="against">'.$table[8].'</td>
                                <td class="difference">'.$table[9].'</td>
                                <td class="points">'.$table[10].'</td>';
				            $PB_output .= '</tr>';
				            $i++;
			            }
			
			        $PB_output .= '</tbody>';
					$PB_output .= '<tfoot><tr class="table-header">';
				        $PB_output .= '<th scope="col">Pos</th><th scope="col">&nbsp;</th>
			            <th><a href="'.$url.'&amp;t_sortby=name&amp;t_ascdsc=DESC" title="sort by team names descending">Team</a></th>
					    <th scope="col"><a href="'.$url.'&amp;t_sortby=pl&amp;t_ascdsc=DESC" title="sort by matches played descending">Pl</a></th>
					    <th scope="col"><a href="'.$url.'&amp;t_sortby=win&amp;t_ascdsc=DESC" title="sort by matches won descending">W</a></th>
					    <th scope="col"><a href="'.$url.'&amp;t_sortby=lose&amp;t_ascdsc=DESC" title="sort by matches lost descending">L</a></th>
					    <th scope="col"><a href="'.$url.'&amp;t_sortby=draw&amp;t_ascdsc=DESC" title="sort by matches drawn descending">D</a></th>
					    <th scope="col"><a href="'.$url.'&amp;t_sortby=gf&amp;t_ascdsc=DESC" title="sort by goals scored for descending">GF</a></th>
					    <th scope="col"><a href="'.$url.'&amp;t_sortby=ga&amp;t_ascdsc=DESC" title="sort by goals scored against descending">GA</a></th>
					    <th scope="col"><a href="'.$url.'&amp;t_sortby=gd&amp;t_ascdsc=DESC" title="sort by goals difference descending">GD</a></th>
					    <th scope="col"><a href="'.$url.'&amp;t_sortby=pts&amp;t_ascdsc=DESC" title="sort by total points descending">PTS</a></th>';
					$PB_output .= '</tr></tfoot>';
					
			    $PB_output .= '</table>';
				
			    $PB_output .= '<div class="wrapper block">';
			        if(count($fixtures_list > 0)) $PB_output .= '<a href="'.SELF.'?mode='.MODE.'&amp;section=fixtures&amp;league='.$selected_league.'&amp;season='.SEASON.'">Latest Fixtures</a> | ';
				    if(count($results_list > 0)) $PB_output .= '<a href="'.SELF.'?mode='.MODE.'&amp;section=results&amp;league='.$selected_league.'&amp;season='.SEASON.'">Latest Results</a>';
		        $PB_output .= '</div>';
			$PB_output .= '</div>';
	    } else {
	        $PB_output .= '<div class="message user_status">'.
			    '<p>'._MISSING_LEAGUE_TABLE_FILE.'</p>'.
				'<p><a href="javascript:history.back();">&larr;&nbsp;change season</a> |or| <a href="'.SELF.'?mode=home">return to home page</a> |or| <a href="'.$create_url.'">Create a new league</a></p>'.
			'</div>'."\n";
		}
		
		if($echo) echo $PB_output;
		else return $PB_output;
    }
	
	function GetDirContents($directory, $fileType) {
		$default_ignore_file = array( ".","..","Thumbs.db",".ini",".sys",".SYS",".BAT",".COM");
		$slash = ( strstr($directory,"\\") ) ? "\\" : "/";
	    if (!is_dir($directory)) return false;
		
	    $path = opendir($directory);
	    while (false !== ($file = readdir($path))) {
		    if (!in_array($file,$default_ignore_file)) {
		        if (is_file($directory.$slash.$file)) $files[] = $file;
			    elseif (is_dir($directory.$slash.$file)) $dirs[] = $file;
		    }
	    }
	    closedir($path);
     
	    if ($fileType == 'files' && isset($files)) return $files;
	    elseif ($fileType == 'dirs' && isset($dirs)) return $dirs;
	    else return false;
    }
	
    function GetIP(){
	    global $CFG;
        $ip = $_SERVER['REMOTE_ADDR'];
        if (!preg_match('/^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$/',$ip)){
            if ($CFG->config['allow_IPv6'] && preg_match('/^[0-9A-Fa-f\:]+$/',$ip)){
                return $ip;
            }
            die('ERROR: Invalid IP address, access blocked!');
        }
        return $ip;
    }

	function CheckIP(){
	    global $PB_CONFIG;
        $ip = GetIP();
        $myBanned = file_get_contents(BASE_PATH.'banned_ip.txt');
        if (strpos($myBanned,$ip) !== false){
            die('ERROR: We don\'t like spammers. You have been permanently banned from this website!');
        }
        return true;
    }

    function BanIP($ip,$Die=false){
        $fp=fopen(BASE_PATH.'banned_ip.txt','a');
        fputs($fp,$ip.'%');
        fclose($fp);
        if ($Die){
            die('ERROR: We don\'t like spammers. You have been permanently banned from this website!');
        }
        return true;
    }
// --------------------------------------------------------------------
if ( ! function_exists('Truncate')){
    function Truncate($content,$limit = 60){
	    $replace_newlines = ( implode( ' ', array_slice( explode( ' ', strip_tags( $content ) ), 0, $limit ) ) . '...' );
		return '<p>' . str_replace( '\n' , '</p><p>', $replace_newlines) . '</p>';
	}
}
    function PostTruncate($string, $limit, $postID, $break=".", $pad=" <span class='view_full_post'>more...</span>") { 
	    $pad=" <span title='" . $postID ."' class='view_full_post'>more...</span>";
	    if(strlen($string) <= $limit) return $string;
	    if(false !== ($breakpoint = strpos($string, $break, $limit))) { 
		    if($breakpoint < strlen($string) - 1) { 
			    $string = substr($string, 0, $breakpoint) . $pad; 
		    }
	    }
	    return $string; 
    }
	
    /**
    * This function creates a word/tag cloud.
    *
    * @param bool $is_tagCloud -- Select whether to show tags in a word cloud or not -- default true.
    * @param integer $div_size -- The width of cloud or cloud container -- default 280.
    * @param mix (array/string) $data -- The array of words/tags (optional).
    * @param integer $minFontSize -- Minimum font size -- default 13
    * @param integer $maxFontSize -- Maximum font size -- default 34
    * @param integer $limit -- Maximum number of tags to display -- default 10
	*/
 
	function GetTags($tags = '',$is_tagCloud=true, $div_size = 280,$fmax = 34,$fmin =13,$limit = 10){
	    global $default_stopwords,$PAGE;
		$PB_output = NULL;
	    $stopwords = explode(",", $default_stopwords);
		$limit_count = 0;
		if($tags != NULL && !is_array($tags)) $tags_tmp_Array = $tags;
		$tags_tmp_Array .= ',';//
		if(isset($PAGE->file_metadata)) $tags_tmp_Array .= $PAGE->file_metadata['keywords'];
		//$tags_tmp_Array = $tags.','.$PAGE->file_metadata['keywords']; 
		$tagsArray = explode(',',$tags_tmp_Array);
		$PB_output .= "<div class=\"tagcloud\" style=\"width: {$div_size}px\">";
		if(isset($tagsArray) && count($tagsArray) > 0) {
		
			$filtered_tags = filter_stopwords($tagsArray, $stopwords);
			$tag_frequency = word_freq($filtered_tags);
			$tmin = min($tag_frequency); /* Frequency lower-bound */
            $tmax = max($tag_frequency); /* Frequency upper-bound */
		    
			//--------------------------------------------
			foreach ($tag_frequency as $tag => $frequency) {
			    if ($limit_count < $limit ) {
					if(!$is_tagCloud) {
				        //$PB_output .= '<a href="?mode=filter&amp;filterby=tags&amp;tag='.$tag.'" title="'.$tag.' returned a count of '.$frequency.'"><span class="btn-highlight left-1">'.$frequency.'</span> '.$tag.'</a>';
			            $PB_output .= '<span class="tag"><a title="'.$tag.' returned a count of '.$frequency.'" href="?mode=filter&amp;filterby=tags&amp;tag='.$tag.'">&nbsp;'.$tag.'&nbsp;</a></span>'."\n";
			        } else {
                        if ($frequency > $tmin) {
                            $font_size = floor(  ( $fmax * ($frequency - $tmin) ) / ( $tmax - $tmin )  );
                            /* Define a color index based on the frequency of the word */
                            $r = floor( 125 * ($frequency / $tmax) );
					        $g = 50;
					        $b = floor( 225 * ($frequency / $tmax) );
                            $color = '#' . sprintf('%02s', dechex($r)) . sprintf('%02s', dechex($g)) . sprintf('%02s', dechex($b));
                        } else {
                            $font_size = $fmin;
                            $color= '#b9b9b9';
                        }
                        if ($font_size >= $fmin) {
                           $PB_output .= "<span class=\"tag\"><a style=\"font-size: {$font_size}px; color: $color;\" href=\"?mode=filter&amp;filterby=tags&amp;tag=$tag\" title=\"$tag returned a count of $frequency\">$tag</a></span>";
                        }
				    }
					$limit_count++;
                }
			}
		} else $PB_output .= '<div class="message user_status"><b>No Tags Found</b></div>';
		$PB_output .= "</div>";
		return $PB_output;
	}
	
	/**
     *
     * @param   string   $size_dir - size directory(small,large)
     * @param   integer  $max_num_ads - The maximun amount of ads to display
     * @param   string  $width - width of the ad
     * @param   string  $height - height of the ad
     * @param   string  $title - title of the ad box
     */
	function adsRotator($size_dir="small",$max_num_ads = 4,$width="120px",$height="120px"){
		global $JSON,$Ads_DB_File;
		$bannerArray = array();
		$ads_folder_url = DATA_URL.'ads/';
		if(file_exists($Ads_DB_File)){
		    //$ads_list = file($Ads_DB_File);
			$fp = @fopen($Ads_DB_File, 'r');
		    $ads_list = explode("\n", fread($fp, filesize($Ads_DB_File))); 
		    for($x=0;$x<sizeof($ads_list);$x++) {	// start loop, each line of file
		        $temp = explode(":",$ads_list[$x]); // explode the line and assign to temp
			    $ad_files = array();
			    $ad_files['ad_id'] = $temp[0];
			    $ad_files['ad_pic'] = $temp[1];
			    $ad_files['ad_pic_alt'] = $temp[2];
				$ad_files['ad_link'] = $temp[3];
			    $ad_files['ad_text'] = $temp[4];
			    $ad_files['ad_size'] = $temp[5];
			    $ad_files['show_ad'] = $temp[6];
				array_push( $bannerArray,$ad_files);
	        }
		    
			$total_ads_count = count($bannerArray);
			$recent_count =0;
		    if ($total_ads_count >= 0) {
			    shuffle($bannerArray);
				for($i=0;$i<$total_ads_count;$i++){
					if ( $recent_count < $max_num_ads && $bannerArray[$i]['ad_size'] == $size_dir ) {
					    //srand ((double) microtime () * 1000000);
					    //$randomAd = mt_rand (($i), count ($bannerArray)-1);
						//$randomAd = intval(rand($i,count($bannerArray)-1));
					    $ad_id = $bannerArray[$i]['ad_id'];
					    $ad_pic = $bannerArray[$i]['ad_pic'];
					    $ad_pic_alt = $bannerArray[$i]['ad_pic_alt'];
					    $ad_text = $bannerArray[$i]['ad_text'];
					    $ads_url = $bannerArray[$i]['ad_link'];
					    $ad_size = $bannerArray[$i]['ad_size'];
					    $show_ad = $bannerArray[$i]['show_ad'];
					    if($show_ad == 'YES' && $ad_size == $size_dir ) echo '<a href="http://'.$ads_url.'" target="_blank"><img src="'.$ads_folder_url.$size_dir.'/'.$ad_pic . '" width="'.$width.'" height="'.$height.'" alt="'.$ad_pic_alt.'" title="'.$ad_text.'" /></a>';
				        
						$recent_count++;
					}
				}
		    } else echo '<div class="message error"><b>Empty directory,No ad found</b></div>';
		} else echo '<div class="message error">Unable to open <b>adBanners.txt</b>, either the file does not exists or ads directory was not found</div>';
    }
	
	function countrylist($action = 'dropdown', $selectedID = null,$echo = false) {
	    $PB_output = NULL;
        $country_list = array(
        "Afghanistan", "Albania", "Algeria", "Andorra","Angola", "Antigua and Barbuda",
		"Argentina","Armenia", "Australia", "Austria", "Azerbaijan",
        "Bahamas", "Bahrain", "Bangladesh", "Barbados","Belarus", "Belgium", "Belize", "Benin",
        "Bhutan", "Bolivia", "Bosnia and Herzegovina","Botswana", "Brazil", "Brunei", "Bulgaria","Burkina Faso", "Burundi", 
		"Cambodia", "Cameroon","Canada", "Cape Verde", "Central African Republic",
        "Chad", "Chile", "China", "Colombi", "Comoros","Congo (Brazzaville)", "Congo", "Costa Rica",
        "Cote d'Ivoire", "Croatia", "Cuba", "Cyprus","Czech Republic", 
		"Denmark", "Djibouti", "Dominica","Dominican Republic", 
		"East Timor (Timor Timur)","Ecuador", "Egypt", "El Salvador", "Equatorial Guinea","Eritrea", "Estonia", "Ethiopia",
		"Fiji", "Finland","France", 
		"Gabon", "Gambia", "Georgia", "Germany","Ghana", "Greece", "Grenada", "Guatemala", "Guinea","Guinea-Bissau", "Guyana",
		"Haiti", "Honduras","Hungary", 
		"Iceland", "India", "Indonesia", "Iran","Iraq", "Ireland", "Israel", "Italy",
		"Jamaica","Japan", "Jordan",
		"Kazakhstan", "Kenya", "Kiribati","Korea, North", "Korea, South", "Kuwait", "Kyrgyzstan",
        "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya","Liechtenstein", "Lithuania", "Luxembourg", 
		"Macedonia","Madagascar", "Malawi", "Malaysia", "Maldives", "Mali","Malta", "Marshall Islands", "Mauritania", "Mauritius",
        "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia","Morocco", "Mozambique", "Myanmar",
		"Namibia", "Nauru","Nepa", "Netherlands", "New Zealand", "Nicaragua","Niger", "Nigeria", "Norway", 
		"Oman", 
		"Pakistan","Palau", "Panama", "Papua New Guinea", "Paraguay","Peru", "Philippines", "Poland", "Portugal", 
		"Qatar",
        "Romania", "Russia", "Rwanda", 
		"Saint Kitts and Nevis","Saint Lucia", "Saint Vincent", "Samoa", "San Marino",
        "Sao Tome and Principe", "Saudi Arabia", "Senegal","Serbia and Montenegro", "Seychelles", "Sierra Leone",
        "Singapore", "Slovakia", "Slovenia", "Solomon Islands","Somalia", "South Africa", "Spain", "Sri Lanka", "Sudan",
        "Suriname", "Swaziland", "Sweden", "Switzerland","Syria",
		"Taiwan", "Tajikistan", "Tanzania","Thailand", "Togo", "Tonga", "Trinidad and Tobago",
        "Tunisia", "Turkey", "Turkmenistan", "Tuvalu",
        "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom","United States", "Uruguay", "Uzbekistan", 
		"Vanuatu","Vatican City", "Venezuela", "Vietnam", "Yemen",
        "Zambia", "Zimbabwe"
        );

        if ($action == 'dropdown') {
            foreach ($country_list as $country) {
                $PB_output .= "<option value=\"".$country."\"";
                if (!empty($selectedID)) {
                    if ($selectedID == $country) $PB_output .= " SELECTED";
                }
                $PB_output .= ">".$country."</option>\n";
            }
        } elseif ($action == 'list') {
            foreach ($country_list as $country) {
			    if (!empty($selectedID)) {
                    if ($selectedID == $country) $PB_output .= " SELECTED";
                } else $selectedID = 'normal';
                $PB_output .= "<li id=\"$selectedID\">".$country."</li>\n";
            }
        }
		
		if($echo) echo $PB_output;
		else return $PB_output;
    }
	/**
 * Shows a menu for inserting images in TinyMCE.
 *
 * @param $dir -- image directory
 * @package admin
 */
    function show_image_insert_box($dir,$echo=false) {
        $PB_output = NULL;
	    $images = GetDirContents($dir, 'files');
		
	    if ($images) {
		    natcasesort($images);
	        if($dir == ARTICLES_IMAGE_PATH) $dir_url = ARTICLES_IMAGE_URL;
	        else if($dir == GALLERY_PATH) $dir_url = GALLERY_URL;
	        else if($dir == IMAGE_UPLOAD_PATH) $dir_url = IMAGE_UPLOAD_URL;
	
		    $PB_output .= '<div class="menudiv">';
			    $PB_output .= '<span><img src="'.IMG_URL.'other/view_image.png" alt="" /></span>';
			    $PB_output .= '<span>';
				    $PB_output .= '<select id="insert_images">';
					    foreach ($images as $image) $PB_output .= '<option>'.$image.'</option>';
				    $PB_output .= '</select>';
				    $PB_output .= '<br />';
				    $PB_output .= '<a href="#" onclick="insert_image_link(\''.$dir_url.'\');return false;">insert image</a>';
			    $PB_output .= '</span>';
		    $PB_output .= '</div>';
	    } else $PB_output .= '<div class="message user_status"><p>[0] image found in this directory </p></div>';
        
		if($echo) echo $PB_output;
		else return $PB_output;
	}

/**
 * Shows a menu for inserting gallery inclusion code in TinyMCE.
 *
 * @since 4.7
 * @package admin
 */
    function show_gallery_insert_box($echo = false) {
        $PB_output = NULL;
	    //Load all galleries.
        $gallery_list = GetDirContents(GALLERY_PATH,'dirs');
	    if(is_dir(GALLERY_PATH) && count($gallery_list) > 0 ){
	        $PB_output .= '<div class="menudiv">';
		        $PB_output .= '<span><img src="'.IMG_URL.'other/albums.png" alt="" /></span>';
		        $PB_output .= '<span>';
			        $PB_output .= '<select id="insert_gallery">';
				        foreach ($gallery_list as $gallery) $PB_output .= '<option value="'.$gallery.'">'.$gallery.'</option>';
			        $PB_output .= '</select>';
			        $PB_output .= '<br />';
			        $PB_output .= '<a href="#" onclick="insert_gallery();return false;">insert gallery</a>';
		        $PB_output .= '</span>';
	        $PB_output .= '</div>';
        } else $PB_output .= '<div class="message user_status"><p><strong>[0]</strong> gallery found</p></div>';
		
		if($echo) echo $PB_output;
		else return $PB_output;
    }

/**
 * Shows a menu for inserting links to pages in TinyMCE.
 *
 * @since 4.6
 * @package admin
 */
function show_link_insert_box($echo = false) {
    $PB_output = NULL;
	$pages = GetDirContents(PAGE_PATH,'files');
	if ($pages) {
		$PB_output .= '<div class="menudiv">';
			$PB_output .= '<span><img src="'.IMG_URL.'other/page.png" alt="" /></span>';
			$PB_output .= '<span>';
				$PB_output .= '<select id="insert_pages">';
					foreach ($pages as $page) {
						//require PAGE_PATH.$page;
						$page = removeFileExt($page);
						$PB_output .= '<option value="'.$page.'">'.$page.'</option>';
					}
				$PB_output .= '</select>';
				$PB_output .= '<br />';
				$PB_output .= '<a href="#" onclick="insert_page_link(); return false;">insert link</a>';
			$PB_output .= '</span>';
		$PB_output .= '</div>';
	} else $PB_output .= '<div class="message user_status"><p><strong>[0]</strong> page found</p></div>';
	
	if($echo) echo $PB_output;
    else return $PB_output;
}

/* End of file Common.php */
/* Location: ./_system/Common.php */