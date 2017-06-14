<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
if (substr(PHP_OS, 0, 3) == 'WIN') {
    define('OS_WINDOWS', true);
    define('OS_UNIX',    false);
    define('PEAR_OS',    'Windows');
} else {
    define('OS_WINDOWS', false);
    define('OS_UNIX',    true);
    define('PEAR_OS',    'Unix'); // blatant assumption
}
    
    // First let's define our apps root directory
	define('BASE_PATH', str_replace("\\", "/",dirname(dirname( __dir__))).'/');
		define('APP_PATH', BASE_PATH.'application/');
            define( "ASSET_PATH", APP_PATH.'assets/' );
                define( "CSS_PATH", ASSET_PATH."css/" );
                define( "JS_PATH", ASSET_PATH."js/" );
                define( "IMG_PATH", ASSET_PATH."images/" );
                define( "ICON_PATH", IMG_PATH."icons/" );
		
		    define( "CONFIG_PATH", APP_PATH."config/" );
		
		    define( "DATA_PATH", APP_PATH."data/" );
		        define( "ARTICLES_PATH", DATA_PATH."news/" );
				define( "CACHE_PATH", DATA_PATH."cache/" );
		        define( "DATABASE_PATH", DATA_PATH."database/" );
		        
				define( "DATA_IMAGE_PATH", DATA_PATH."img/" );
				    define( "ARTICLES_IMAGE_PATH", DATA_IMAGE_PATH."news_pics/" );
			        define( "CATEGORY_IMAGE_PATH", DATA_IMAGE_PATH."category_pics/" );
		            define( "AVATAR_PATH", DATA_IMAGE_PATH."avatars/" );
		            define( "TEXTURES_PATH", DATA_IMAGE_PATH."textures/" );
		            define( "LOGO_PATH", DATA_IMAGE_PATH."logos/" );
		            define( "HEADER_PATH", DATA_IMAGE_PATH."headers/" );
		        
			
		        define( "LEAGUE_PATH", DATA_PATH."leagues/" );
				    define( "LEAGUE_FIXTURES_PATH", LEAGUE_PATH.LEAGUE."/fixtures/" );
				    define( "LEAGUE_RESULTS_PATH", LEAGUE_PATH.LEAGUE."/results/" );
				    define( "LEAGUE_IMAGE_PATH", LEAGUE_PATH.LEAGUE."/icons/" );
						
		        define( "LOG_PATH", DATA_PATH."logs/" );
				define( "PAGE_PATH", DATA_PATH."pages/" );
			
			    define('UPLOAD_PATH',DATA_PATH."uploads/");
	                define('AUDIO_UPLOAD_PATH',UPLOAD_PATH."audios/");
	                define('VIDEO_UPLOAD_PATH',UPLOAD_PATH."videos/");
	                define('IMAGE_UPLOAD_PATH',UPLOAD_PATH."images/");
					
	    	define( "GALLERY_PATH", "application/gallery/" );
		    define( "PLUGIN_PATH", APP_PATH."plugins/" );
		    define( "SKIN_PATH", APP_PATH."skins/" );
			define( "VIEW_PATH", APP_PATH."views/" );
			
		define('SYSTEM_PATH', BASE_PATH.'system/');
		    define( "LIB_PATH", SYSTEM_PATH.'library/');
            define( "FUNC_PATH", SYSTEM_PATH.'functions/');
/* --------------------------------------------------------- */	
    $http_request = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
    
    define('SELF', $_SERVER['PHP_SELF']); //define('SELF', $_SERVER['SCRIPT_NAME']);
	$this_file = basename(SELF);
    define('BASE_URI', str_replace('/'.$this_file, '', $_SERVER['SCRIPT_NAME']).'/');
	// PLEASE ensure that you set your site absolute url to avoid 'mishaps'. eg. define('BASE_URL', isset($http_request) ? $http_request.$_SERVER['HTTP_HOST'].'/pbd/pb_lab/projects/__BK_ARCHIVE__/flm/' : BASE_URI);
    define('BASE_URL', isset($http_request) ? $http_request.$_SERVER['HTTP_HOST'] : BASE_URI);
        define('APP_URL', BASE_URL.'application/');
            define('ASSET_URL', APP_URL.'assets/');
                define('JS_URL', ASSET_URL."js/");
                define('CSS_URL', ASSET_URL."css/");
                define('IMG_URL', ASSET_URL."images/");
			
            define('DATA_URL',APP_URL.'data/');
		        define( "DATA_IMAGE_URL", DATA_URL."img/" );
		            define( "ARTICLES_IMAGE_URL", DATA_IMAGE_URL."news_pics/" );
			        define( "AVATAR_URL", DATA_IMAGE_URL."avatars/" );
				    define( "CATEGORY_IMAGE_URL", DATA_IMAGE_URL."category_pics/" );
			        define( "HEADER_URL", DATA_IMAGE_URL."headers/" );
			        define( "LOGO_URL", DATA_IMAGE_URL."logos/" );
				    define( "TEXTURES_URL", DATA_IMAGE_URL."textures/" );
			
		        define( "LEAGUE_URL", DATA_URL."leagues/" );
			    define('PAGE_URL',DATA_URL.'pages/');
	    	
			    define('UPLOAD_URL',DATA_URL."uploads/");
	                define('AUDIO_UPLOAD_URL',UPLOAD_URL."audios/");
	                define('VIDEO_UPLOAD_URL',UPLOAD_URL."videos/");
	                define('IMAGE_UPLOAD_URL',UPLOAD_URL."images/");
				
		define( "GALLERY_URL", APP_URL."gallery/" );
		define('PLUGIN_URL',APP_URL.'plugins/');
		define('SKIN_URL',APP_URL.'skins/');
		define( "VIEW_URL", APP_URL."views/" );
	//---------------------------------------------------------------------
	define('DEFAULT_SKIN','default');
	//---------------------------------------------------------------------
	include(CONFIG_PATH.'doctypes.php');
	//---------------------------------------------------------------------
    define('BACKUP_LIMIT', '10');
    define('WYSIWYG', 'nicedit' );
	define('_DOCTYPE', $_doctypes['html5']);
    define('URL_FRIENDLY', false);
    define('STRICTFILENAMEPOLICY' ,"YES");
    define("OVERLAY_OPACITY", 90);
    define("FADE_FRAME_PER_SEC", 30);
    define("FADE_DURATION_MS", 3900);
    define("LOAD_FADE_GRACE", 500);
    define("FILE_IN_NEW_WINDOW", true);
    define('_MISSING_PAGE_FILE','Either the <b>URL</b> is incorrect , or <b>This page does not exists !</b>');
    define('_MISSING_PARENT_FILE','Unable to load your requested parent/controller file <b>[['.$_GET['mode'].'.php]]</b>. Page execution halted, please ensure the file specified has valid file extension or it actually exists or the path to this file is set correctly.');
    define('_MISSING_LEAGUE_DB_FILE','The <span class="b i">Leagues DB</span> file was <b>Not Found</b>, which could mean that it does not exists and no leagues are registered. Also ensure that the database directory is set to an existing directory in the file <em>[constants.php]</em></b> .... <a href="'.SELF.'?mode=admin&amp;section=systeminfo">Ensure that the <span class="b i">leagues DB</span> file exists</a>');
    define('_MISSING_CATEGORY_DB_FILE','The <span class="b i">Categories DB</span> file was <b>Not Found</b>, which could mean that it does not exists and no leagues are registered. Also ensure that the database directory is set to an existing directory in the file <em>[constants.php]</em></b> .... <a href="'.SELF.'?mode=admin&amp;section=systeminfo">Ensure that the <span class="b i">Categories DB</span> file exists</a>');
    define('_MISSING_GALLERY_DB_FILE','The <span class="b i">Galleries DB</span> file was <b>Not Found</b>, which could mean that it does not exists and no leagues are registered. Also ensure that the database directory is set to an existing directory in the file <em>[constants.php]</em></b> .... <a href="'.SELF.'?mode=admin&amp;section=systeminfo">Ensure that the <span class="b i">Galleries DB</span> file exists</a>');
    define('_MISSING_LEAGUE_TABLE_FILE','<b>No League Table File Found</b> ...</p><br/><p><b>The league table file selected was not found or it does not exists. One main reason could be, the season selected is not registered, ensure that the league selected, season directory exists or the <span class="i em">[LEAGUE_PATH]</span> constant is set to an existsing directory in the file <span class="i em">[constants.php]</span></b> or choose a different season ...');
    define('_MISSING_FIXTURE_FILE', 'The fixture selected was <span class="b">Not Found</span> or the fixtures directory is empty. Ensure that the fixtures directory for this league exists and is set in the <span class="em">[constants.php]</span> file to the correct path ...');
    define('_MISSING_RESULT_FILE', 'The match result selected was <span class="b">Not Found</span> or the results directory is empty. Ensure that the results directory for this league exists and is set in the <span class="em">[constants.php]</span> file to the correct path ...');
/*
|------------ --------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);
/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');
    
/* End of file constants.php */
/* Location: ./application/config/constants.php */