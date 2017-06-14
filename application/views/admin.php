<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
//session_start();
//if (($_SESSION['perm'] < "3"))  {
//echo "<font color='#$col_text'>"; die ('You are not authorised to access this section');
//}
//require('authorize.php');
	
	switch ( SECTION ) {
        case 'section_index': include(VIEW_PATH.'admin/admin_index.php'); break;
		case 'addnews':
		case 'updatenews':
		    include(VIEW_PATH.'admin/editNews.php');
		break;
		
		case 'addfixture':
		    (isset($_GET['match_type'])) ? $match_type = $_GET['match_type'] : $match_type = 'leagues';
		    if($match_type == 'leagues') include(VIEW_PATH.'admin/addleagueFixture.php');
			else if($match_type == 'international') include(VIEW_PATH.'admin/addInternationalFixture.php');
		break;
		case 'addleague': include(VIEW_PATH.'admin/addLeague.php'); break;
		case 'addseason': include(VIEW_PATH.'admin/addSeason.php'); break;
		case 'addcategory': include(VIEW_PATH.'admin/addCategory.php'); break;
		
		case 'updateadmin': include(VIEW_PATH.'admin/updateAdmin.php'); break;
		
		case 'addbanner':
		case 'updatebanner':
		    include(VIEW_PATH.'admin/editAdBanners.php');
		break;
		
		case 'addgallery':
		case 'updategallery':
		    include(VIEW_PATH.'admin/editGallery.php');
		break;
		
		case 'addpage':
		case 'updatepage':
		    include(VIEW_PATH.'admin/editPage.php');
		break;
		
		case 'updateleague': include(VIEW_PATH.'admin/updateLeague.php'); break;
		
		case 'updatefixtures':
		case 'updateresults':
		    include(VIEW_PATH.'admin/updateResult.php');
		break;
		
		
		case 'manage': include(VIEW_PATH.'admin/FileManager.php'); break;
		case 'setup': include(VIEW_PATH.'admin/Setup.php'); break;
		case 'systeminfo': include(VIEW_PATH.'admin/SystemInfo.php'); break;
		case 'upload': include(VIEW_PATH.'admin/Upload.php'); break;
	}
	//echo $PB_output
?>