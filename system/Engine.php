<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    define('ENVIRONMENT', 'development');
/* 
 * ----------------------------------------------------- *
 *  Load the global functions                            *
 * ------------------------------------------------------*
 */
 	require(PATH.'application/config/constants.php');
 	require(PATH.'application/config/config.php');
    require(CONFIG_PATH.'settings.php');
	require(FUNC_PATH.'Encoder_functions.php');
	require(FUNC_PATH.'Common_functions.php');
	require(FUNC_PATH.'Search_functions.php');
	require(FUNC_PATH.'Image_functions.php');
	require(FUNC_PATH.'Auth_functions.php');
	include(CONFIG_PATH.'formats_time.php');
    
/*
 *----------------------------------------------------------*
 * ERROR REPORTING                                          *
 *----------------------------------------------------------*
 *
 * Different environments will require different levels of error reporting. 
 * By default development will show errors but testing and live will hide them.
 *
 * --------------------------------------------------------*
 *  Define a custom error handler so we can log PHP errors *
 * --------------------------------------------------------*
 */
    if (defined('ENVIRONMENT')) {
	    switch (ENVIRONMENT) {
		    case 'debug':
			    error_reporting(E_ALL);
                require( LIB_PATH.'php_error.php' );
                /*\php_error\reportErrors();*/
				 \php_error\reportErrors( array('display_line_numbers' => true) ); 
		    break;
			case 'development':
				$old_error_handler = set_error_handler("pbErrorHandler");
				//$old_error_handler = set_error_handler('_exception_handler');;
		    break;
		    case 'testing': error_reporting( E_WARNING | E_PARSE | E_NOTICE); break;
		    case 'production': error_reporting(0); break;
		    default: exit('The application environment is not set correctly.');
	    }
    }
	// if the user is running a PHP version less the 5.2.1, WE MUST DIE IN A FIRE!
    if(! is_php('5.2')){
        $message = "Your installed PHP version is less then 5.2.1. FLM requires at least PHP v5.2.1+ to run correctly. The latest version is highly reccomended. FLM will not run until this basic requirement is met, and has quit.";
		show_error($message,500,'PHP Vesion Error');
    }
	// if the user is running a PHP version less the 5.3, Kill magic quotes
	if ( ! is_php('5.3')) {
		@set_magic_quotes_runtime(0);
	}
    // Make sure the IP accessing the site hasn't been banned
    CheckIP();
/*
 * ------------------------------------------------------
 *  Set a liberal script execution time limit
 * ------------------------------------------------------
 */
	if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0) {
		@set_time_limit(120);
	}
    // Set memory limit
    ini_set('memory_limit','64M');
	
/*
 * ------------------------------------------------------
 *  Instantiate the config class
 * ------------------------------------------------------
 */
	$CFG =& load_class('Config', '');

	// Do we have any manually set config items in the index.php file?
	if (isset($assign_to_config)) {
		$CFG->_assign_to_config($assign_to_config);
	}
/*
 * ------------------------------------------------------
 *  Instantiate the classes used regularly
 * ------------------------------------------------------ 
 */ 
	$JSON =& load_class('Json', 'library');
	$UTIL =& load_class('Utility', 'library');
/*
 * ------------------------------------------------------
 *  Load required js scripts and list pages if any 
 * ------------------------------------------------------ 
 */ $js_scripts_array = array('jquery-1.7.2.js','jquery.backstretch.min.js','jquery.easing.1.3.js','ddsmoothmenu.js'/*,'jquery.fitvids.js','jquery.dcflickr.1.0.js','twitter.min.js'*/,'projectblu.js');
	// if the skin selected is not the default, unset [jquery.backstretch.min.js] since it is the only skin which currently uses it.
	//if($CFG->config['skin'] != 'default') unset($js_scripts_array[1]);
	$required_js_scripts = loadScripts('js', $js_scripts_array);
    //$required_css_scripts = loadScripts('css', array('default.css','background.css','test.css'));
    
	// page list for pages menu link
	//$pageList = GetDirContents(PAGE_PATH, 'files');
/*
 * ------------------------------------------------------
 *  Check if user is logged in
 * ------------------------------------------------------
 */
	checkUser();
	/* ------------------------------------------------------------------ */
	// Detects the insertion of code in the $_GET array
    foreach ($_GET as $check_url) {
	    //if ((eregi("<[^>]*script*\"?[^>]*>", $check_url)) || (eregi("<[^>]*object*\"?[^>]*>", $check_url)) || (eregi("<[^>]*iframe*\"?[^>]*>", $check_url)) || (eregi("<[^>]*applet*\"?[^>]*>", $check_url)) || (eregi("<[^>]*meta*\"?[^>]*>", $check_url)) || (eregi("<[^>]*style*\"?[^>]*>", $check_url)) || (eregi("<[^>]*form*\"?[^>]*>", $check_url)) || (eregi("\([^>]*\"?[^)]*\)", $check_url)) || (eregi("\"", $check_url)))
	    if ((preg_match("/<[^>]*script*\"?[^>]*>/", $check_url)) || (preg_match("/<[^>]*object*\"?[^>]*>/", $check_url)) || (preg_match("/<[^>]*iframe*\"?[^>]*>/", $check_url)) || (preg_match("/<[^>]*applet*\"?[^>]*>/", $check_url)) || (preg_match("/<[^>]*meta*\"?[^>]*>/", $check_url)) || (preg_match("/<[^>]*style*\"?[^>]*>/", $check_url)) || (preg_match("/<[^>]*form*\"?[^>]*>/", $check_url)) || (preg_match("{\([^>]*\"?[^)]*\)}", $check_url)) || (preg_match("{\"}", $check_url)))
		    die ('Hijacking attempt, dying....');
    }
    unset($check_url);
	/** initialisation */
    $mode = isset($_GET['mode']) ? $_GET['mode'] : 'home';
	$section = isset( $_GET['section'] ) ? $_GET['section'] : 'section_index';
	$action = isset($_GET['action']) ? $_GET['action'] : 'action_index';
	
    $current_date = date("Y-m-d");
	$year = substr("$current_date", 0, 4);
    $month = substr("$current_date", 5,-3);
    $day = substr("$current_date", 8);
	$prevyear = ($year - 1);
    $nextyear = ($year + 1);
	if($month >= 5 && $day <= 32) $thisseason = $prevyear.'-'.$year;
	else $thisseason = $year.'-'.$nextyear;
    //$thisseason = $prevyear.'-'.$year;
	//echo $thisseason;
	
	define('MODE' , $mode);
	define('SECTION' , $section);
	define('ACTION' , $action);
	
	if(file_exists(PAGE_PATH) && is_dir(PAGE_PATH)) $allPages = GetDirContents(PAGE_PATH,'files');
	else $allPages = NULL;
	$selected_page = ((isset($_GET['page'])) ? $_GET['page'] : ((count($allPages) > 1) ? end($allPages) : 'about'));
    define('PAGE' , $selected_page);
   
	if(file_exists(LEAGUE_PATH) && is_dir(LEAGUE_PATH))$allLeagues = GetDirContents(LEAGUE_PATH,'dirs');
	else $allLeagues = NULL;
	$selected_league = isset($_GET['league']) ? $_GET['league'] : ((count($allLeagues) > 1) ? end($allLeagues) : '');// enter your default league here
	define('LEAGUE' , $selected_league);
	
	if(file_exists(LEAGUE_PATH.LEAGUE) && is_dir(LEAGUE_PATH.LEAGUE))$allLeagueSeasons = GetDirContents(LEAGUE_PATH.LEAGUE,'dirs');
	else $allLeagueSeasons = NULL;
	$selected_season = isset($_GET['season']) ? $_GET['season'] : ((count($allLeagueSeasons) > 1) ? end($allLeagueSeasons) : $thisseason );
	define('SEASON' , $selected_season);
	
	if(isset($PAGE->file_metadata)){
	    $meta_title = $PAGE->file_metadata['title'];
	    $meta_author = $PAGE->file_metadata['author'];
	    $meta_keywords = $PAGE->file_metadata['keywords'];
	    $meta_description = $PAGE->file_metadata['description'];
	} elseif(isset($news_title)){
	    $meta_title = $news_title;
	    $meta_author = $news_author;
	    $meta_keywords = $news_keywords;
	    $meta_description = $news_summary;
	}else{
	    $meta_title = (isset($title) ? $title : $PB_CONFIG['site_title_full']);
	    $meta_author = (isset($author) ? $author : $PB_CONFIG['author']);
	    $meta_keywords = (isset($keywords) ? $keywords : $PB_CONFIG['keywords']);
	    $meta_description = (isset($summary) ? $summary : $PB_CONFIG['description']);
	}
    $BK_CSS_FILE = CSS_URL.'default.css';
	if(isset($_GET['newSkin'])) define('SKIN_FILE' , SKIN_URL.$_GET['newSkin'].'/style.css');
	else if(($CFG->config['skin'] == 'default' || $CFG->config['skin'] == '') && file_exists(SKIN_PATH.DEFAULT_SKIN.'/style.css')) define('SKIN_FILE', SKIN_URL.DEFAULT_SKIN.'/style.css');
    else if(file_exists(SKIN_PATH.$CFG->config['skin'].'/style.css')) define('SKIN_FILE' , SKIN_URL.$CFG->config['skin'].'/style.css');
    else define('SKIN_FILE',$BK_CSS_FILE);
 
    if ((isset($_SESSION['validUser'])) && ($_SESSION['validUser'] == true)) {
	   $logged_in = 1;
	   $is_validUser = getUser($_SESSION['userName']);
	   $roleID = getRoleId();
    } else $logged_in = $roleID = 0;
	
	//--------------------------// assigning some variables to config array
	$CFG->_assign_to_config(array(
		'base_url'=>'./', // hard code your absolute url here
		'redirect'=>false, // if true, redirect users to specified page (eg. when a user logs out they will be redirected to login page)
	    'gui_thumb_width'=>'150', // reccomended width
	    'gui_thumb_height'=>'150', // reccomended height
		'allow_IPv6' => 0,/* Allow IPv6 format? 1 = YES, 0 = NO */
		'albumsPerPage' => 3,
		'thumbsPerPage' => 6,
		'js_gallery_plugin' => 'colorbox', //the javascript plugin used to display album pics
	    'thumb_width'=>'150px',
		'show_image_info'=>'YES',
		'image_max_width'=>"660px",
		'show_exif_info'=>'YES',
		'rotate_images'=>'YES',
	));
    
/*	
//Load all the modules.
$module_list = array('tinymce','cj');
//Sort the modules.
natcasesort($module_list);

//Then include necessary module files for each module.
foreach ($module_list as $module) {
	if (file_exists(PLUGIN_PATH.$module.'/'.$module.'.php')) {
		require_once (PLUGIN_PATH.$module.'/'.$module.'.php');

		//If we are on the index.php, include the needed functions.
		if (strpos($_SERVER['SCRIPT_FILENAME'], 'index.php') !== false && file_exists(PLUGIN_PATH.$module.'/'.$module.'.site.php'))
			require_once (PLUGIN_PATH.$module.'/'.$module.'.site.php');
	}
}
unset($module);
*/
/* End of file Engine.php */
/* Location: ./system/Engine.php */