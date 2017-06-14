<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
	switch ( SECTION ) {
        case 'section_index': include(VIEW_PATH.'league/league_index.php'); break;
		case 'fixtures': include(VIEW_PATH.'league/viewFixtures.php'); break;
		case 'leagueTable':
		    echo '<div class="content box">';
		        GetLeagueTables(LEAGUE,true);
			echo '</div>';
		break;
		case 'results': include(VIEW_PATH.'league/viewResults.php'); break;
		case 'viewTeams': include(VIEW_PATH.'league/viewTeams.php'); break;
		case 'matchreport':
		case 'matchpreview':
		    include(VIEW_PATH.'league/viewMatchInfo.php');
		break;
	}
?>