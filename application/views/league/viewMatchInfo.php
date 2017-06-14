<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
	if(!isset($PB_output)) $PB_output = NULL;
	$page_htmo = NULL;
	$todays_date=date("Y-m-d");
	$i = 0;
	
	if(SECTION == 'matchpreview'){
	    $path = LEAGUE_PATH.LEAGUE.'/'.SEASON.'/fixtures/';
	    $statFiles_list = GetDirContents($path,'files');
	    if(count($statFiles_list) > 1) rsort($statFiles_list);
	    $selected_statFile = (isset($_GET['fixture']) ? $_GET['fixture'] : ((count($statFiles_list) > 1) ? end($statFiles_list) : $statFiles_list[0] ));
		$info_title = 'Match Preview';
		$stat_type = 'matchpreview';
		$extra_url_bit = 'fixture=';
		$return_url = '<a href="'.SELF.'?mode=leagues&amp;section=fixtures&amp;league='.LEAGUE.'&amp;season='.SEASON.'&amp;fixture='.removeFileExt($selected_statFile).'">Return to previous fixtures</a>';
	} else if(SECTION == 'matchreport'){
	    $path = LEAGUE_PATH.LEAGUE.'/'.SEASON.'/results/';
	    $statFiles_list = GetDirContents($path,'files');
    	if(count($statFiles_list) > 1) rsort($statFiles_list);
	    $selected_statFile = (isset($_GET['result']) ? $_GET['result'] : ((count($statFiles_list) > 1) ? end($statFiles_list) : $statFiles_list[0] ));
		$info_title = 'Match Report';
		$stat_type = 'matchreport';
		$extra_url_bit = 'result=';
		$return_url = '<a href="'.SELF.'?mode=leagues&amp;section=results&amp;league='.LEAGUE.'&amp;season='.SEASON.'&amp;result='.removeFileExt($selected_statFile).'">Return to previous results</a>';
	}
	//$selected_result = (isset($_GET['result']) ? $_GET['result'] : ((count($statFiles_list) > 1) ? end($statFiles_list) : 0 ));
	$ext = pathinfo($selected_statFile, PATHINFO_EXTENSION);
	$statFile = ($ext == '') ? $selected_statFile.'.txt' : $selected_statFile;
	$stripped_statFile = removeFileExt($statFile);
	$f_sortby = (isset($_GET['f_sortby']) ? $_GET['f_sortby'] : (isset($_COOKIE['f_sortby']) ? $_COOKIE['f_sortby'] : "date" ));
	//setcookie("f_sortby", $_GET['f_sortby'], time()+86400 );
	// header("location: index.php");
    $fileId = isset($_GET['match_id']) ? $_GET['match_id'] : 1;
	$league_info = GetLeagueInfo(LEAGUE);
	$league_name = $league_info['league_title'];
	//------------------------------------------------------------------
	$PB_output .= '<div class="content"><div class="box">';
        $PB_output .= '<div class="header"><h2>'.$league_name.$separator.$info_title.'</h2></div>';
	    if(file_exists($path.$statFile)){
            $statFiles = $JSON->decode(file_get_contents($path.$statFile));
		    if(is_array($statFiles)) {
			    foreach($statFiles as $league_table => $key ){
				    $match_files[] = $statFiles[$league_table];
				    $match_ids[] = $statFiles[$league_table]->match_id;
				    $home_teams[] = $statFiles[$league_table]->home_team;
				    $away_teams[] = $statFiles[$league_table]->away_team;
				    $matchdates[] = $statFiles[$league_table]->matchdate;
				    $matchtimes[] = $statFiles[$league_table]->matchtime;
				    $matchyear[] = $statFiles[$league_table]->matchyear;
				    $seasons[] = $statFiles[$league_table]->season;
				    if(SECTION == 'matchpreview') $matchnote[] = $statFiles[$league_table]->matchpreview;
				    else if(SECTION == 'matchreport') {
					    $home_scores[] = $statFiles[$league_table]->home_score;
					    $away_scores[] = $statFiles[$league_table]->away_score;
					    $matchnote[] = $statFiles[$league_table]->matchreport;
					}
					$prevNext_link_titles[] = $statFiles[$league_table]->home_team.'_v_'.$statFiles[$league_table]->away_team;
				}
		    } else {
			    $team_list = file($path);
			    foreach($team_list as $league_table){
			        $league_table = json_decode($league_table);
				    $match_files[] = $league_table;
				    $match_ids[] = $league_table->id;
				    $home_teams[] = $league_table->home_team;
				    $away_teams[] = $league_table->away_team;
				    $matchdates[] = $league_table->matchdate;
				    $matchtimes[] = $league_table->matchtime;
				    $matchyear[] = $league_table->matchyear;
				    $seasons[] = $league_table->season;
				    if(SECTION == 'matchpreview') $matchnote[] = $league_table->matchpreview;
				    else if(SECTION == 'matchreport') $matchnote[] = $league_table->matchreport;
			        $prevNext_link_titles[] = $league_table->home_team.'_v_'.$league_table->away_team;
				}
	        }
		    asort($match_files);
		    asort($match_ids);
	        asort($matchtimes);
	        asort($matchdates);
	 
	        switch($f_sortby){
    		    case "name": $statFile_arr_final = $match_files; break;
    		    case "id": $statFile_arr_final = $match_ids; break;
		        case "time": $statFile_arr_final = $matchtimes; break;
		        case "date": $statFile_arr_final = $matchdates; break;
	        }
			
	        $total_statFiles = count($statFile_arr_final);
			
	        if( $total_statFiles > 0 ) {
				$PB_output .= '<div class="news-clips grid-">';
				    foreach($statFile_arr_final as $fixt=>$v){
		                if($match_ids[$fixt] == $fileId){
							$PB_output .= '<div class="news-title">';
							    $PB_output .= '<span class="home-team">'.ucwords($home_teams[$fixt]).'</span>&nbsp;<span class="versus"><sup class="b i">V</sup><sub class="b">S</sub></span>&nbsp;<span class="away-team">'.ucwords($away_teams[$fixt]).'</span>';
								$PB_output .= $separator.$info_title.'&nbsp;'.$fileId;
							$PB_output .= '</div>';
							$PB_output .= '<div class="news-content">';
							    if(SECTION == 'matchreport') $PB_output .= '<div class="news-score">'.ucwords($home_teams[$fixt]).'<span class="match-scores">'.$home_scores[$fixt].'-'.$away_scores[$fixt].'</span>'.ucwords($away_teams[$fixt]).'</div>';
								$PB_output .= $matchnote[$fixt];
							$PB_output .= '</div>';
							$PB_output .= '<div class="post-nav grid-4">';
							    $piFile = ($match_ids[$fixt] - 1);
								$niFile = ($match_ids[$fixt] + 1);
							    if ($fileId > 1) $PB_output .= '<span class="three-nav nav-prev"><a href="'.SELF.'?mode=leagues&amp;section='.$stat_type.'&amp;league='.LEAGUE.'&amp;season='.SEASON.'&amp;'.$extra_url_bit.$stripped_statFile.'&amp;match_id='.$piFile.'" title="show the previous '.$stat_type.' : '.$prevNext_link_titles[$fixt-1].'">&larr;&nbsp;'.$prevNext_link_titles[$fixt-1].'</a></span>';
								$PB_output .= '<span class="three-nav nav-center"> | '.$return_url.' | </span>';
								if ($fileId < $total_statFiles) $PB_output .= '<span class="three-nav nav-next"><a href="'.SELF.'?mode=leagues&amp;section='.$stat_type.'&amp;league='.LEAGUE.'&amp;season='.SEASON.'&amp;'.$extra_url_bit.$stripped_statFile.'&amp;match_id='.$niFile.'" title="show the next '.$stat_type.' : '.$prevNext_link_titles[$fixt+1].'">'.$prevNext_link_titles[$fixt+1].'&nbsp;&rarr;</a></span>';
							$PB_output .= '</div>';
							$PB_output .= '<div class="clear"></div>';
						}
					}
				$PB_output .= '</div>';
			} else $PB_output .= '<div class="message user_status"><p>There are currently no results or fixtures.</p></div>';
	    } else {
	        if(SECTION == 'matchpreview') $PB_output .= '<div class="message user_status"><p>'._MISSING_FIXTURE_FILE.'</p></div>';
	        else if(SECTION == 'matchreport') $PB_output .= '<div class="message user_status"><p>'._MISSING_RESULT_FILE.'</p></div>';
	        $PB_output .= '<div class="message unspecific"><p><a href="'.SELF.'?mode=home">Go to the homepage</a> |or| <a href="'.SELF.'?mode=leagues&amp;section=fixtures">Return to fixtures</a></p>';
			    if($roleID >= 4) $PB_output .= ' |or| <a href="'.SELF.'?mode=admin&amp;section=addfixture&amp;league='.LEAGUE.'&amp;season='.SEASON.'">Create a new fixture</a>';
		    $PB_output .= '</p></div>';
	    }
    $PB_output .= '</div></div>';
	echo $PB_output;
?>