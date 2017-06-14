<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
	$humanRelativeDate =& load_class('HumanRelativeDate','library');
	
	$PB_output = NULL;
	$i='';
	$itemsPerPage = 5;
	$league_tables = array();
	
	$league_info = GetLeagueInfo(LEAGUE);
	$league_name = $league_info['league_title'];
	$path = LEAGUE_PATH.LEAGUE.'/'.SEASON.'/results/';
	$results_list = GetDirContents($path,'files');
	$selected_result = isset($_GET['result']) ? $_GET['result'] : ((count($results_list) > 1) ? end($results_list) : '' );
	$ext = pathinfo($selected_result, PATHINFO_EXTENSION);
	$result = ($ext == '') ? $selected_result.'.txt' : $selected_result;
	 
	$edit_url = '?mode=admin&amp;section=updateresults&amp;match_type=leagues&amp;league='.LEAGUE.'&amp;result='.removeFileExt($result);
	$delete_url = '?mode=admin&amp;section=deleteresults&amp;match_type=leagues&amp;league='.LEAGUE.'&amp;result='.removeFileExt($result);
	$page_htmo = '';
	$f_sortby = isset($_COOKIE['f_sortby']) ? $_COOKIE['f_sortby'] : "date" ;
	if(isset($_GET['f_sortby'])){
		$f_sortby = isset($_GET['f_sortby']) ? $_GET['f_sortby'] : '';
		//setcookie("f_sortby", $_GET['f_sortby'], time()+86400 );
		// header("location: index.php");
	}
	//echo $path.$result;
	//$PB_output .= '<table class="results-table " border="0" width="65%" height="auto" cellpadding="0" cellspacing="0">';
    $PB_output .= '<div class="content">'; 
        $PB_output .= '<div class="box">'; 
        $PB_output .= '<div class="header"><h2>Current results'.$separator.$league_name.'</h2></div>';
	        if(file_exists($path.$result)){
                $results = $JSON->decode(file_get_contents($path.$result));
		        if(is_array($results)) {
			        foreach($results as $league_table => $key ){
			        	$match_files[] = $results[$league_table];
			        	$match_ids[] = $results[$league_table]->match_id;
				
				        $home_teams[] = $results[$league_table]->home_team;
			        	$home_scores[] = $results[$league_table]->home_score;
				
			         	$away_teams[] = $results[$league_table]->away_team;
			        	$away_scores[] = $results[$league_table]->away_score;
			 	
			        	$matchdates[] = $results[$league_table]->matchdate;
			        	$matchtimes[] = $results[$league_table]->matchtime;
			          	$matchyear[] = $results[$league_table]->matchyear;
			          	$seasons[] = $results[$league_table]->season;
			        }
		        } else {
			        $team_list = file($path);
			        foreach($team_list as $league_table){
			            $league_table = $JSON->decode($league_table);
			          	$match_files[] = $league_table;
			           	$match_ids[] = $league_table->id;
			        	$home_teams[] = $league_table->home_team;
		          		$home_scores[] = $league_table->home_score;
				
		           		$away_teams[] = $league_table->away_team;
		         		$away_scores[] = $league_table->away_score;
				
		           		$matchdates[] = $league_table->matchdate;
		         		$matchtimes[] = $league_table->matchtime;
		         		$matchyear[] = $league_table->matchyear;
		         		$seasons[] = $league_table->season;
		         	}
	            }
		        asort($match_files);
		        asort($match_ids);
	            asort($matchtimes);
	            asort($matchdates);
	 
	            switch($f_sortby){
    		        case "name": $result_arr_final = $match_files; break;
    		        case "id": $result_arr_final = $match_ids; break;
		            case "time": $result_arr_final = $matchtimes; break;
		            case "date": $result_arr_final = $matchdates; break;
	            }
	     
	            if( count($match_ids) == 0 ) {
                    $PB_output .= '<div class="message user_status"><p>There are currently no results.</p></div>';
                } else {
		            $numPages = ceil( count($match_ids) / $itemsPerPage );
                    if(isset($_GET['p'])) {
	                    $currentPage = $_GET['p'];
                        if($currentPage > $numPages) $currentPage = $numPages;
                    } else $currentPage=1;
        
                    //$start = ( $currentPage * $itemsPerPage ) - $itemsPerPage;
				    $start = isset($_GET['start']) ? $_GET['start'] : 0 ;
				    $total_results = count($result_arr_final);
				
		            for($p=0; $p*$itemsPerPage <  $total_results ; $p++){
		                $class = ($p*$itemsPerPage) == $start ? "active" : "" ;
		                $page_htmo .= '<a href="index.php?mode=leagues&section=results&amp;league='.LEAGUE;
		                    if(isset($f_sortby)) $page_htmo .= '&amp;f_sortby='.$f_sortby;
		                    if(isset($_GET['result'])) $page_htmo .= '&amp;result='.removeFileExt($_GET['result']);
		                $page_htmo .= '&amp;start='.($p*$itemsPerPage).'" class="'.$class.' pages">'.($p+1).'</a>';
	                }

	                $arr_sortby = array("name"=>"Name", "id"=>"Id", "time"=>"Time", "date"=>"Date");	
	                foreach($arr_sortby as $fixt=>$v){
		                if($f_sortby == $fixt) $sortby_html[] = "<strong>$v</strong>";
		                else $sortby_html[] = "<a href=\"index.php?mode=leagues&section=results&amp;league=".LEAGUE."&amp;f_sortby=$fixt&amp;start=$start\">$v</a>";
	                }	
	
	                $sortby_htmo = implode(" | ", $sortby_html);
		         
				    $PB_output .='<div class="link-bar">
				        <span class="grid-2"><a href="'.$edit_url.'">Update this results</a></span>
					    <div id="sortby-top" class="right-" align="right">Sort by: '.$sortby_htmo.'</div>
					    <div class="clear">&nbsp;</div>
				    </div>';
				 
				    $PB_output .='<div class="link-bar">';
		                $PB_output .='<form id="sortby-top" class="grid-9" action="index.php?mode=leagues&amp;section=results" method="GET">';
		                    $PB_output .='<input name="mode" type="hidden" value="'.MODE.'"><input name="section" type="hidden" value="'.SECTION.'">';
			        
					        if(file_exists($League_DB_File)){
				                $PB_output .='<select name="league">';
                                    $league_names = file($League_DB_File);
                                    foreach($league_names as $league_list_rows){
				                        $league_row = $JSON->decode($league_list_rows);
				                        $PB_output .= '<option value="'.$league_row->league_name.'">'.$league_row->league_title.'</option>';
			                        }
				                $PB_output .='</select>';
				            } else $PB_output .= '<div class="message user_status"><p><b>No Leagues Database File Found</b></p></div>'."\n";
		            
					        if(count($results_list) > 0){
					            $PB_output .='<select name="result">';
				                foreach($results_list as $f) {
							        if($UTIL->isValidExt($f,$tfxs)){
					                    $striped_result = removeFileExt($f);
									    if($result == $f || strpos($f,$result)) $selected_res = 'selected';
								        else $selected_res = '';
					                    $PB_output .='<option value="'.$striped_result.'" '.$selected_res.' >'.$striped_result.'</option>';
					                }
		                        }
				                $PB_output .='</select>';
				            } else $PB_output .= '<div class="message user_status"><p><b>No results Found</b></p></div>'."\n";
		            
					        $PB_output .='<input type="submit" title="view another fixture" class="update" value="" />';
				        $PB_output .='</form>';
				        $PB_output .='<div class="clear">&nbsp;</div>';
				    $PB_output .='</div>';
                    $PB_output .= '<table class="table table-stats grid-full"><caption>Live Scores matches</caption>';
				        $PB_output .= '<thead><tr class="row-spacer"><th scope="col">Home Team <sub>vs</sub> Away Team</th><th scope="col">preview/result</th></tr></thead>';
					    $PB_output .= '<tbody>';
	                        foreach($result_arr_final as $fixt=>$v){
						        $i++;
		                        if($i < $start+1) continue;
		                        if($i > $start + $itemsPerPage) break;
		                    
						        if ($home_scores[$fixt] > $away_scores[$fixt]) $note = 'Home team '.ucwords($home_teams[$fixt]).' won';
	                            else if ($home_scores[$fixt] < $away_scores[$fixt]) $note = ucwords($away_teams[$fixt]).' won away';
	                            else if ($home_scores[$fixt] == $away_scores[$fixt]) $note = 'Match drawn '.$home_scores[$fixt].':'.$away_scores[$fixt];
	                        
		                        //$img_name = strlen($img_arr[$fixt]) > 18 ? substr($img_arr[$fixt],0,16)."..." :$img_arr[$fixt];
			                    $PB_output .= '<tr class="report">
                                    <td class="match-score hover-enabled live-scores-show">
                                        <span class="team-home">'.$home_teams[$fixt].'</span>
                                        <span class="score">'.$home_scores[$fixt].'-'.$away_scores[$fixt].'</span>
                                        <span class="team-away">'.$away_teams[$fixt].'</span>
                                        <span class="elapsed-time">'.$note.' on '.$matchdates[$fixt].'&nbsp;,match time&nbsp;&rarr;&nbsp;'.$matchtimes[$fixt].'</span>
                                    </td>
                                    <td class="match-link"><a href="?mode=leagues&amp;section=matchreport&amp;league='.LEAGUE.'&amp;result='.removeFileExt($result).'&amp;match_id='.$match_ids[$fixt].'">Report</a></td>
                                </tr>';
							    $PB_output .= '<tr class="row-spacer"><td colspan="2">&nbsp;</td></tr>';
						    }
					    $PB_output .= '</tbody>';
						$PB_output .= '<tfoot>';
						    $PB_output .= '<tr class="row-spacer">';
						        $PB_output .= '<td><span>This weekend : <strong>'.$total_results.'</strong> results</span>';
						            $date_arr = word_freq($matchdates);
	                                foreach($date_arr as $dt=>$key) $PB_output .= '<span><span class="btn-highlight left-1">'.$key.'</span> Results match/s played '.$humanRelativeDate->getTextForSQLDate($dt).' the '.$dt.'</span>';
						        $PB_output .= '</td>';
						        $PB_output .='<td><div class="paginate-wrapper"> | Page: '.$page_htmo.'</div></td>';
		                    $PB_output .= '</tr>';
						$PB_output .= '</tfoot>';
			        $PB_output .= '</table>';
			    }
	        } else {
	            $PB_output .= '<div class="message user_status"><p>'._MISSING_RESULT_FILE.'</p></div>';
	            $PB_output .= '<div class="message unspecific"><p><a href="'.SELF.'?mode=home">Go to the homepage</a> |or| <a href="'.SELF.'?mode=leagues">Return to league index</a>';
		            if($roleID >= 4) $PB_output .= ' |or| <a href="'.SELF.'?mode=admin&amp;section=addfixture&amp;league='.LEAGUE.'&amp;season='.SEASON.'">Create a new fixture</a>';
		        $PB_output .= '</p></div>';
	        }
        $PB_output .= '</div>';
    $PB_output .= '</div>';
	echo $PB_output;
?>
