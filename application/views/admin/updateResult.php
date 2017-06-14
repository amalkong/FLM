<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    if (($roleID < 4))  {
       echo'<div class="box"><div class="message error"><p>Access Denied!,You are not authorised to access this section, your user role does not permit you to update fixture results.</p></div></div>';
	   include(BASE_PATH.'footer.php');
	   exit;
    }
	if (!file_exists($League_DB_File))  {
       echo'<div class="box"><div class="message error"><p>'._MISSING_LEAGUE_DB_FILE.'</p></div></div>';
	   include(BASE_PATH.'footer.php');
	   exit;
    }
    $humanRelativeDate =& load_class('HumanRelativeDate','library');
	
    $PB_output = NULL;
	$i = $statFile = $selected_statFiles = $stat_type = $path = '';
    $id = 1;
	$itemsPerPage = 10;
	$page_htmo = '';
	
	$f_sortby = isset($_COOKIE['f_sortby']) ? $_COOKIE['f_sortby'] : "date" ;
	if(isset($_GET['f_sortby'])){
		$f_sortby = isset($_GET['f_sortby']) ? $_GET['f_sortby'] : '';
		//setcookie("f_sortby", $_GET['f_sortby'], time()+86400 );
	    // header("location: index.php");
	}
	
	$fileId = isset($_GET['fileId']) ? $_GET['fileId'] : $id;
	    if(isset($_GET['fixture'])) {
		    $path = LEAGUE_PATH.LEAGUE.'/'.SEASON.'/fixtures/';
	        $statFiles_list = GetDirContents($path,'files'); 
	        $first_file= end($statFiles_list);
	
	        $selected_statFiles = isset($_GET['fixture']) ? $_GET['fixture'] : $first_file;
	        $ext = pathinfo($selected_statFiles, PATHINFO_EXTENSION);
	        $statFile = ($ext == '') ? $selected_statFiles.'.txt' : $selected_statFiles;
			$stat_type = 'fixture';
		}else if(isset($_GET['result'])) {
		    $path = LEAGUE_PATH.LEAGUE.'/'.SEASON.'/results/';
	        $statFiles_list = GetDirContents($path,'files'); 
	        $first_file= end($statFiles_list); 
	
	        $selected_statFiles = isset($_GET['result']) ? $_GET['result'] : $first_file;
	        $ext = pathinfo($selected_statFiles, PATHINFO_EXTENSION);
	        $statFile = ($ext == '') ? $selected_statFiles.'.txt' : $selected_statFiles;
			$stat_type = 'result';
		}
		$result_tmp_name = removeFileExt($statFile);
		$match_date = explode('_',$result_tmp_name);
	    $extra_url_bit= 'league='.LEAGUE;
		$league_info = GetLeagueInfo(LEAGUE);
	    $match_type_name = $league_info['league_title'];
	
	$result_filename = $match_date[0].'_result.txt';
	$NewResult_FileName = LEAGUE_PATH.LEAGUE.'/results/'.$result_filename ;
	
	$form_url = $_SERVER['PHP_SELF'].'?mode=admin&amp;section=updateresults&amp;'.$extra_url_bit.'&amp;'.$stat_type.'='.$selected_statFiles.'&amp;c=ok';
	
	if(file_exists($path.$statFile)){
        $results = $JSON->decode(file_get_contents($path.$statFile));
		if(is_array($results)) {
			foreach($results as $match_type_row => $key ){
				$match_files[] = $results[$match_type_row];
				$match_ids[] = $results[$match_type_row]->match_id;
				$home_teams[] = $results[$match_type_row]->home_team;
				$away_teams[] = $results[$match_type_row]->away_team;
				$matchdates[] = $results[$match_type_row]->matchdate;
				$matchtimes[] = $results[$match_type_row]->matchtime;
				$matchyear[] = $results[$match_type_row]->matchyear;
				$seasons[] = $results[$match_type_row]->season;
				if(isset($_GET['result'])){
				    $matchreport[] = $results[$match_type_row]->matchreport;
				    $home_scores[] = $results[$match_type_row]->home_score;
				    $away_scores[] = $results[$match_type_row]->away_score;
				} else {
				    $matchreport[] = NULL;
				    $home_scores[] = NULL;
				    $away_scores[] = NULL;
				}
			}
		} else {
			$team_list = file($path.$statFile);
			foreach($team_list as $match_type_row){
			    $match_type_row = json_decode($match_type_row);
				$match_files[] = $match_type_row;
				$match_ids[] = $match_type_row->id;
				$home_teams[] = $match_type_row->home_team;
				$away_teams[] = $match_type_row->away_team;
				$matchdates[] = $match_type_row->matchdate;
				$matchtimes[] = $match_type_row->matchtime;
				$matchyear[] = $match_type_row->matchyear;
				$seasons[] = $match_type_row->season;
				if(isset($_GET['result'])){
				    $matchreport[] = $match_type_row->matchreport;
				    $home_scores[] = $match_type_row->home_score;
				    $away_scores[] = $match_type_row->away_score;
				} else {
				    $matchreport[] = NULL;
				    $home_scores[] = NULL;
				    $away_scores[] = NULL;
				}
			}
	    }
		 
		asort($match_files);
		asort($match_ids);
	    asort($matchtimes);
	    asort($matchdates);
	    asort($seasons);
	
		
	}
	$number_of_teams = count($match_ids);
	$PB_output .= '<div class="content"><div class="box">';
        if (isset($_POST['submit']) AND isset($_GET['mode']) AND $_GET['mode']=="admin" AND isset($_GET['section']) AND $_GET['section']=="updateresults" AND isset($_GET['c']) AND $_GET['c']=="ok") {
		    if (isset($_POST['home_team']) && isset($_POST['away_team'])) {
		        $match_id = $_POST['match_id'];
			
                $home_team = $_POST['home_team'];
			    $home_score = $_POST['home_score'];
            
                $away_team = $_POST['away_team'];
			    $away_score = $_POST['away_score'];
			 
			    $matchdate = $_POST['matchdate'];
			    $matchtime = $_POST['matchtime'];
			    $season = $_POST['season'];
				
			    $matchday_report = isset($_POST['matchday_report']) ? $_POST['matchday_report'] : 'No match report available for this fixture';
				
			    if (strlen($NewResult_FileName)>0){
			        $file = @fopen($NewResult_FileName,"w");
			        // write the txt file
		            if ($file != false){
				        for($i=0;$i<$number_of_teams;$i++){
						    if(strlen($home_team[$i]) > 0){
							    if(strlen($matchday_report[$i]) > 5) $matchday_report[$i] = $matchday_report[$i];
								else $matchday_report[$i] = 'No match report available for this fixture';
					            $data[$i] = array('match_id' => $match_id[$i],'home_team'=>$home_team[$i],'home_score'=>$home_score[$i],'away_team'=>$away_team[$i],'away_score'=>$away_score[$i],'matchdate'=>$matchdate[$i],'matchtime'=>$matchtime[$i],'matchyear'=>$matchyear[$i],'matchreport'=>$matchday_report[$i],'season'=>$season);
				            }
							//$MatchReport_Filename = $home_teams[$i].'_vs_'.$away_teams[$i].'_report.txt';
		                    $id++;
				        }
					    $final_data = $JSON->encode($data);
		                fwrite($file,$JSON->clearWhitespaces($final_data)."\n");
			            fclose($file);
					    // If upload is successful.
		                $PB_output .= '<div class="message"><p><b><font color="green">League Fixture Results File saved successfully.</font></b></p></div>';
					    $PB_output .= '<div class="message unspecific"><p><a href="'.BASE_URI.'index.php?mode=admin">Go to the admin index</a> or <a href="?mode=leagues&amp;section=results&amp;'.$extra_url_bit.'&amp;result='.basename($NewResult_FileName).'">View The Updated Results</a> or <a href="?mode=admin&amp;section=addfixture">Add another fixture</a></p></div>';
		            } else $PB_output .= '<p class="message error"><b>Wrong file name!</b></p>'; // If upload is un-successful.
				} else $PB_output .= '<p class="message error"><b>Empty file name!</b></p>'; // If a file name was not submitted.
				// end creation txt file 
		    } else {
	            $PB_output .= '<div class="message user_status"><p><b>No home team or away team was submitted.</b> ...</p></div>';
	        }
        } else { 
    	    if(file_exists($path.$statFile)){
	            switch($f_sortby){
    	    	    case "name": $statFile_arr_final = $match_files; break;
    	    	    case "id": $statFile_arr_final = $match_ids; break;
		            case "time": $statFile_arr_final = $matchtimes; break;
		            case "date": $statFile_arr_final = $matchdates; break;
		            case "season": $statFile_arr_final = $seasons; break;
	            }
	        
		        if( count($match_ids) == 0 ) {
                    $PB_output .= '<div class="message user_status"><p>There are currently no fixtures.</p></div>';
                } else {
				    $start = isset($_GET['start']) ? $_GET['start'] : 0 ;
			    	$total_statFiles = count($statFile_arr_final);
				
		            for($p=0; $p*$itemsPerPage <  $total_statFiles ; $p++){
		                $class = ($p*$itemsPerPage) == $start ? "active" : "" ;
		                if(isset($f_sortby)) $page_htmo .= '<a href="index.php?mode=leagues&section=fixtures&amp;f_sortby='.$f_sortby.'&amp;start='.($p*$itemsPerPage).'" class="'.$class.' pages">'.($p+1).'</a>';
		                else $page_htmo .= '<a href="index.php?mode=leagues&section=fixtures&amp;start='.($p*$itemsPerPage).'" class="'.$class.' pages">'.($p+1).'</a>';
	                }

	                $arr_sortby = array("name"=>"Name", "id"=>"Id", "time"=>"Time", "date"=>"Date","season"=>"Season");	
	                foreach($arr_sortby as $fixt=>$v){
		                if($f_sortby == $fixt) $sortby_html[] = "<strong>$v</strong>";
		                else $sortby_html[] = "<a href=\"index.php?mode=leagues&section=fixtures&amp;f_sortby=$fixt&amp;start=$start\">$v</a>";
	                }	
	
	                $sortby_htmo = implode(" | ", $sortby_html);
				
			    	$PB_output .= '<div class="header"><h2>Updating Fixtures'.$separator.$match_type_name.'</h2></div>';
				
			    	$PB_output .='<div class="link-bar"><span class="grid-2"></span><div id="sortby-top" class="right-" align="right">Sort by: '.$sortby_htmo.'</div><div class="clear">&nbsp;</div></div>';
				
			    	$PB_output .= '<form method="POST" action="'.$form_url.'">';
                        $PB_output .= '<table class="table table-stats grid-full"><caption>Update Match Scores</caption>';
				            $PB_output .= '<thead><tr class="row-spacer"><th scope="col">Match ID</th><th scope="col">Home Team</th><th>Scores</th><th scope="col">Away Team</th><th>Match Date</th><th>Match Time</th><th>Season</th></tr></thead>';
                    
					        $PB_output .= '<tbody>';
	                            foreach($statFile_arr_final as $fixt=>$v){
						            $i++;
		                            if($i < $start+1) continue;
		                            if($i > $start + $itemsPerPage) break;
			
			                        $PB_output .= '<tr class="results">
                                        <td class="match-id" align="center"><input type="text" name="match_id[]" size="1" value="'.$match_ids[$fixt].'" readonly /></td>
                                        <td class="team-home" align="center"><input type="text" name="home_team[]" size="15" value="'.$home_teams[$fixt].'" readonly /></td>
                                        <td class="score" align="center"><input type="text" class="grid-10" name="home_score[]" value="'.$home_scores[$fixt].'" required /> - <input type="text" class="grid-10" name="away_score[]" value="'.$away_scores[$fixt].'" required /></td>
                                        <td class="team-away" align="center"><input type="text" name="away_team[]" size="15" value="'.$away_teams[$fixt].'" readonly /></td>
							            <td><input type="text" name="matchdate[]" size="7" value="'.$matchdates[$fixt].'" readonly /></td>
							        	<td><input type="text" name="matchtime[]" size="3" value="'.$matchtimes[$fixt].'" readonly /></td>
							        	<td><input type="text" name="season[]" size="10" value="'.$seasons[$fixt].'" readonly /></td>
							        </tr>';
									$PB_output .= '<tr><td colspan="7"><p class="grid-full" style="margin-top:10px;"><input type="checkbox" value="" onClick="return kadabra(\'report_'.$i.'\');">add match report</p>';
			                            $PB_output .= '<div id="report_'.$i.'" style="display:none" align="center">';
						    	        $PB_output .= 'Match Report<br><textarea rows="4" cols="90" name="matchday_report[]">'.$matchreport[$fixt].'</textarea>';
							            $PB_output .= '</div>';
					                $PB_output .= '</td></tr>';
						        	$PB_output .= '<tr class="row-spacer"><td colspan="7">&nbsp;</td></tr>';
					        	}
						        $PB_output .= '<tr class="row-spacer"><td colspan="7" align="center"><input type="Submit" name="submit" value="Update Fixture" onClick="showNotify(\'Updating....fixture....\');" class="save" /><a class="cancel" href="?mode=admin" title="cancel and return to the admin index">Cancel</a></td></tr>';
	                        $PB_output .= '</tbody>';
						    $PB_output .= '<tfoot>';
						        $PB_output .= '<tr class="row-spacer">';
						            $PB_output .= '<td colspan="4"><span>This weekend : <strong>'.$total_statFiles.'</strong> Fixtures</span>';
						            $date_arr = word_freq($matchdates);
	                                foreach($date_arr as $dt=>$key){
						                $PB_output .= '<span><span class="btn-highlight left-1">'.$key.'</span> Match/s will be played '.$humanRelativeDate->getTextForSQLDate($dt).' on '.$dt.'</span>';
						            }
						            $PB_output .= '</td>';
						            $PB_output .='<td colspan="3"><div id="pagenav"> | Page: '.$page_htmo.'</div></td>';
		                        $PB_output .= '</tr>';
						    $PB_output .= '</tfoot>';
			            $PB_output .= '</table>';
			        $PB_output .= '</form>';
			    }
	    
	        } else {
	            $PB_output .= '<div class="message user_status"><p>'._MISSING_FIXTURE_FILE.'</p></div>';
	        }
        }
	$PB_output .= '</div></div>';
	echo $PB_output;
?>