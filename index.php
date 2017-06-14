<?php
	define('PBD_FLM', TRUE);
	define('PATH', str_replace('\\','/',__dir__).'/');

    //if(!file_exists('application/config/setting.php') && file_exists('install.php')) header("Location: install.php");
	
	if(file_exists('install.php')){
        echo "<html><head><title>FLM: Fatal Error</title></head><body><center><div style='background:#bbb;margin:10px 20px;padding:10px 50px;text-align:center;'><h2 style='color:#F00;background:#fff;margin-bottom:10px;'>WARNING!!!</h2><h3 style='background:#797979;text-decoration:underline;'>The <a href='install.php' target='_blank'>Installer</a> file exist!</h3></div><div style='background:#ddd;margin:10px 20px;padding:50px;text-align:justify;'><p>This is a BIG security risk and as such FLM will not continue loading until this file is deleted from your server. If you have just uploaded FLM use the above links to either: <a href='install.php' target='_blank'>Setup FLM for the first time</a>. Once complete, please delete the <em>install.php</em> and reload this page.</p></div></center></body></html>";
        exit();
    }
    
	if(file_exists(PATH.'system/Engine.php')) require(PATH.'system/Engine.php');
	else require('./system/Engine.php');
    
	//--------------------------
	if ( ! file_exists(VIEW_PATH.MODE.'.php')) show_error(_MISSING_PARENT_FILE,404,'The Requested File Wasn&rsquo;t Found');
	include('header.php');
	switch ( MODE ) {
	    case 'home': include(VIEW_PATH.'home.php'); break;
        case 'admin': include(VIEW_PATH.'admin.php'); break;
		case 'leagues': include(VIEW_PATH.'leagues.php'); break;
		case 'viewGallery': include(VIEW_PATH.'viewGallery.php'); break;
		
		case 'viewArticle': include(VIEW_PATH.'viewArticle.php'); break;
		case 'viewFixtures': include(VIEW_PATH.'viewFixtures.php'); break;
		
	    case 'filter': include(VIEW_PATH.'filter.php'); break;
		case 'profiles': include(VIEW_PATH.'profiles.php'); break;
	    case 'sitemap': include(VIEW_PATH.'sitemap.php'); break;
	}

	if(MODE != 'admin') include(VIEW_PATH.'inc/sidebar.php');
	include('footer.php');
?>