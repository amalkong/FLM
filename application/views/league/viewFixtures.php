<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    if(!isset($PB_output)) $PB_output = NULL;
	$humanRelativeDate =& load_class('HumanRelativeDate','library');
	$league_info = GetLeagueInfo(LEAGUE);
	$league_name = $league_info['league_title'];
	
	$i=0;
	$page_htmo = '';
	$todays_date=date("Y-m-d");
	
	//if(isset($_COOKIE['sortby'])) setcookie("sortby", $_GET['sortby'], time()+86400 );
	
	$f_sortby = isset($_GET['f_sortby']) ? $_GET['f_sortby'] : ($f_sortby = isset($_COOKIE['sortby']) ? $_COOKIE['sortby'] : "title" );
	$f_ascdsc = isset($_GET['f_ascdsc']) ? $_GET['f_ascdsc'] : "DESC";
	$GLOBALS['f_sortby'] = $f_sortby;
    $GLOBALS['f_ascdsc'] = $f_ascdsc;
	
	$path = LEAGUE_PATH.LEAGUE.'/'.SEASON.'/fixtures/';
	$fixtures_list = GetDirContents($path,'files');
	if(count($fixtures_list) > 0 && $UTIL->isValidExt($path.$fixtures_list[0],$tfxs)){
	    $first_file= end($fixtures_list);
		rsort($fixtures_list);
	} else $first_file = $todays_date;
	
	$selected_fixture = isset($_GET['fixture']) ? $_GET['fixture'] : $first_file;
	$ext = pathinfo($selected_fixture, PATHINFO_EXTENSION);
	$fixture = ($ext == '') ? $selected_fixture.'.txt' : $selected_fixture;
	$stripped_fixture = removeFileExt($fixture);
	
	$url_prefix = SELF.'?mode='.MODE.'&amp;section='.SECTION."&amp;league=".LEAGUE."&amp;season=".SEASON;
	$urlVars = $url_prefix."&amp;fixture=".$stripped_fixture."&amp;f_sortby=".$f_sortby;
	$edit_url = SELF.'?mode=admin&amp;section=updateresults&amp;match_type=leagues&amp;league='.LEAGUE.'&amp;season='.SEASON.'&amp;fixture='.$stripped_fixture;
	$delete_url = SELF.'?mode=admin&amp;section=deletefixture&amp;match_type=leagues&amp;league='.LEAGUE.'&amp;season='.SEASON.'&amp;fixture='.$stripped_fixture;
	
	$CFG->_assign_to_config(array('itemsPerPage'=>1));
    $match_fixtures = array();
	$fixture_sortby_array = array("title"=>"Title", "id"=>"Id", "time"=>"Time", "moddate"=>"Date");	
	
	$PB_output .= '<div class="content">'; 
        $PB_output .= '<div class="box">'; 
        $PB_output .= '<div class="header"><h2>'.$league_name.$separator.'Most Recent Fixture</h2></div>';
	    if(file_exists($path.$fixture)){
            $results = $JSON->decode(file_get_contents($path.$fixture));
		    if(is_array($results)) {
			    foreach($results as $league_table => $key ){
				    $matchdates[] = $results[$league_table]->matchdate;
				    $tmp = array();
				    $tmp[0] = $results[$league_table];
				    $tmp[1] = $results[$league_table]->match_id;
				    $tmp[2]['home_team'] = $results[$league_table]->home_team;
				    $tmp[2]['away_team'] = $results[$league_table]->away_team;
				    $tmp[4] = $results[$league_table]->matchdate;
				    $tmp[6] = $results[$league_table]->matchtime;
				    $tmp[8] = $results[$league_table]->matchyear;
				    $tmp[9] = $results[$league_table]->season;
					array_push($match_fixtures,$tmp);
			    }
		    } else {
			    $team_list = file($path);
			    foreach($team_list as $league_table){
				    $tmp = array();
				    $tmp[0] = $league_table;
					$league_table = $JSON->decode($league_table);
				    $tmp[1] = $league_table->id;
				    $tmp[2]['home_team'] = $league_table->home_team;
				    $tmp[2]['away_team'] = $league_table->away_team;
				    $tmp[4] = $league_table->matchdate;
				    $tmp[5] = $league_table->matchtime;
				    $tmp[8] = $league_table->matchyear;
				    $tmp[9] = $league_table->season;
					array_push($match_fixtures,$tmp);
			    } 
	        }
		    usort($match_fixtures,'sort_file');
			
	        if( count($match_fixtures) == 0 ) {
                $PB_output .= '<div class="message user_status"><p>There are currently no fixtures.</p></div>';
            } else {
			    $total_fixtures = count($match_fixtures);
		        $numPages = ceil( count($match_fixtures) / $CFG->config['itemsPerPage'] );
                if(isset($_GET['p'])) {
	                $currentPage = $_GET['p'];
                    if($currentPage > $numPages) $currentPage = $numPages;
                } else $currentPage=1;
        
                $start = ( $currentPage * $CFG->config['itemsPerPage'] ) - $CFG->config['itemsPerPage'];
				$PB_output .='<div class="link-bar">';
				    if($roleID >= 4) $PB_output .='<span class="grid-2"><a href="'.$edit_url.'">Update the result for this fixture &not;</a></span>';
				    $PB_output .='<div id="sortby-top" class="right-1 grid-3" valign="bottom"><span class="left-2">Sort by: </span><ul class="sort-link right-1">';
						foreach($fixture_sortby_array as $fixt=>$v){
		                    $PB_output .= '<li>
							    <div class="i left-1">&nbsp;|&nbsp;'.(($f_sortby == $fixt) ? '<strong>'.$v.'</strong>' : $v).'</div>
								<div class="right-1" style="width:6px;margin-right:5px;">
								    <a class="icon asc" href="'.$url_prefix.'&amp;f_sortby='.$fixt.'&amp;f_ascdsc=ASC" title="sort files by '.$fixt.' ascending"></a>
									<a class="icon desc" href="'.$url_prefix.'&amp;f_sortby='.$fixt.'&amp;f_ascdsc=DESC" title="sort files by '.$fixt.' descending"></a>
								</div>
							</li>';
	                    }
					$PB_output .='</ul></div><div class="clear">&nbsp;</div>';
				$PB_output .='</div>';
				
				$PB_output .='<div class="link-bar">';
		        $PB_output .='<form id="sortby-top" class="grid-9 right-1" action="" method="GET">';
		            $PB_output .='<input name="mode" type="hidden" value="'.MODE.'"><input name="section" type="hidden" value="'.SECTION.'">';
			        
					if(count($fixtures_list) > 0){
					    $PB_output .='<select name="fixture">';
				            foreach($fixtures_list as $f) {
							    if($UTIL->isValidExt($f,$tfxs)){
					                $sfixture = removeFileExt($f);
									if($fixture == $f || strpos($f,$fixture)) $selected_fixt = 'selected';
								    else $selected_fixt = '';
					                $PB_output .='<option value="'.$sfixture.'" '.$selected_fixt.' >'.$sfixture.'</option>';
					            }
		                    }
				        $PB_output .='</select>';
				    } else $PB_output .= '<div class="message user_status"><p><b>No Fixtures Found</b></p></div>'."\n";
		            
					if(file_exists($League_DB_File)){
						$PB_output .= '<select name="league">';
							$league_list = $JSON->decode(file_get_contents($League_DB_File));
							if(is_array($league_list)){ 
								foreach($league_list as $league_row) $PB_output .= '<option value="'.$league_list[$league_row]->league_name.'" '.(($selected_league==$league_list[$league_row]->league_name) ? "SELECTED" : '' ).'>'.(strlen($league_row->league_title) > 18 ? substr($league_row->league_title,0,16)."..." : $league_row->league_title).'</option>'."\n";
							} else {
								$league_list = file($League_DB_File);
                                foreach($league_list as $league_list_rows){
						            $league_row = $JSON->decode($league_list_rows);
									$leagueName = $league_row->league_name;
									$leagueTitle = $league_row->league_title;
						            $PB_output .= '<option value="'.$leagueName.'" '.(($selected_league == $leagueName) ? "SELECTED" : '' ).'>'.(strlen($leagueTitle) > 20 ? substr($leagueTitle,0,20)."..." : $leagueTitle).'</option>'."\n";
					            }
							}
                        $PB_output .= '</select>';
					} else $PB_output = '<div class="message user_status"><p>'._MISSING_LEAGUE_DB_FILE.'</p></div>'."\n";
                                    
					$PB_output .='<input type="submit" title="view another fixture" class="update" value="" />';
				$PB_output .='</form>';
				$PB_output .='<div class="clear">&nbsp;</div></div>';
				 $PB_output .= '<table class="table table-stats grid-full"><caption>Latest match fixtures for '.$league_name.'</caption>';
				    $PB_output .= '<thead><tr class="row-spacer"><th scope="col">Home Team <sub>vs</sub> Away Team</th><th scope="col">match preview</th></tr></thead>';
                    
					$PB_output .= '<tbody>';
					    for( $i=$start; $i<$start + $CFG->config['itemsPerPage']; $i++ ) {
		                    if( isset($match_fixtures[$i][0])) {
			                    if($match_fixtures[$i][9] == SEASON){
		                            $PB_output .= '<tr class="fixture">
                                        <td class="match-score hover-enabled">
                                           <span class="team-home">'.ucwords($match_fixtures[$i][2]['home_team']).'</span>
                                            <span class="score">V</span>
                                            <span class="team-away">'.ucwords($match_fixtures[$i][2]['away_team']).'</span>
                                            <span class="elapsed-time">on '.$match_fixtures[$i][4].'&nbsp;at&nbsp;'.$match_fixtures[$i][6].'</span>
                                        </td>
                                        <td class="match-link"><a href="'.SELF.'?mode=leagues&amp;section=matchpreview&amp;league='.LEAGUE.'&amp;season='.SEASON.'&amp;fixture='.$stripped_fixture.'&amp;match_id='.$match_fixtures[$i][1].'">Preview</a></td>
                                    </tr>';
							        $PB_output .= '<tr class="row-spacer"><td colspan="2">&nbsp;</td></tr>';
						        } else {
							        $PB_output .= '<tr class="fixture"><td><div class="message user_status"><p>The fixture selected was <b>Not Found or it does not exists.</b> Ensure that the league <b>'.LEAGUE.'</b>, league directory is set to an existing directory ...</p></div>';
	                                $PB_output .= '<div class="message unspecific"><p><a href="'.SELF.'?mode=home">Go to the homepage</a> or <a href="'.SELF.'?mode=leagues&amp;section=fixtures">Return to fixtures</a> or <a href="'.SELF.'?mode=admin&amp;section=addfixture">Create a new fixture</a></p></div></td></tr>';
							    }
						    }
						}
					$PB_output .= '</tbody>';
					$PB_output .= '<tfoot>';
						$PB_output .= '<tr class="last-row">';
						    $PB_output .= '<td class="grid-2">';
						        $date_arr = word_freq($matchdates);
	                            foreach($date_arr as $dt=>$key){
						            $PB_output .= '<span><span class="btn-highlight left-1">'.$key.'</span>';
							        if($todays_date >= $dt)$PB_output .= ' Match/s will be played ';
									else $PB_output .= ' Match/s played ';
									$PB_output .= $humanRelativeDate->getTextForSQLDate($dt).' on '.$dt.'</span>';
						        }
						    $PB_output .= '</td>';
								
						    $PB_output .='<td class="grid-2"><div class="paginate-wrapper grid-auto left-1"><span class="left-1"><strong>'.$total_fixtures.'</strong> Fixtures in total</span>&nbsp;|&nbsp;'.$UTIL->print_pagination($numPages,$urlVars,$currentPage).'</div></td>';
		                $PB_output .= '</tr>';
					$PB_output .= '</tfoot>';
	                
			    $PB_output .= '</table>';
			}
	    } else {
	        $PB_output .= '<div class="message user_status"><p>'._MISSING_FIXTURE_FILE.'</p></div>';
	        $PB_output .= '<div class="message unspecific"><p><a href="javascript:history.go(-1)">&larr;&nbsp;Back</a> |or| <a href="'.SELF.'?mode=leagues">Select a different league</a> |or| <a href="'.SELF.'?mode=home">Go to the homepage</a> |or| <a href="?mode=admin&amp;section=addfixture">Create a new fixture</a></p></div>';
	    }
        $PB_output .= '</div>';
    $PB_output .= '</div>';
	echo $PB_output;
?>