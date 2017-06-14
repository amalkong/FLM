<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    $PAGE =& load_class('Page','library');
    $PB_output = NULL;
	echo'<div class="content">';
	    echo'<div class="box">';
	        switch ( SECTION ) {
	            case 'section_index': getNewsArticles(6,'date','DESC',true); break;
		        case 'viewArticle': include(VIEW_PATH.'viewArticle.php'); break;
		        case 'viewLeagueTable': include(VIEW_PATH.'viewLeagueTable.php'); break;
		        case 'pages': include(VIEW_PATH.'pages.php'); break;
	    }
        echo'<div class="clear">&nbsp;</div>';
        echo'</div>';
    echo'</div>';
?>