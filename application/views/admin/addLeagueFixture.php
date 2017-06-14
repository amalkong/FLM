<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    if (($roleID < 4))  {
       echo'<div class="box"><div class="message error"><p>Access Denied!,You are not authorised to access this section, your user role does not permit you to add league fixtures.</p></div></div>';
	   include(BASE_PATH.'footer.php');
	   exit;
    }
	if (!file_exists($League_DB_File))  {
       echo'<div class="box"><div class="message error"><p>'._MISSING_LEAGUE_DB_FILE.'</p></div></div>';
	   include(BASE_PATH.'footer.php');
	   exit;
    }
	if(!isset($PB_output)) $PB_output = NULL;
	$league_info = GetLeagueInfo(LEAGUE);
	$league_name = $league_info['league_title'];
	$path = $UTIL->Check_For_Slash(LEAGUE_PATH.LEAGUE,true);
	$season_path = $UTIL->Check_For_Slash($path.SEASON,true);
	$league_file = SEASON.'_league_table.txt';
	$league_file_path = $path.$league_file;
	
    $i = 1;
	 
    $timestamp_date=date("Y-m-d");

    $year = substr("$timestamp_date", 0, 4);
    $nextyear = $year + 1;
    $yearafternext = $nextyear + 1;
    $prevyear = $year -1;
    $lastseason = ($year -1).'-'.$prevyear;
    $thisseason = $prevyear.'-'.$year;
    $nextseason = $year.'-'.$nextyear;
    $seasonafternext = $nextyear.'-'.$yearafternext;
	$kickoffmins = array('00','05','10','15','20','30','40','45','50','55');
	if(!isset($allLeagueSeasons))$allLeagueSeasons = GetDirContents(LEAGUE_PATH.LEAGUE,'dirs');
	
	if(file_exists($league_file_path)){
        $results = $JSON->decode(file_get_contents($league_file_path));
		if(is_array($results)) {
			foreach($results as $league_table => $key ){
				$team_ids[] = $results[$league_table]->id;
				$team_names[] = $results[$league_table]->name;
			}
		} else {
			$team_list = file($league_file_path);
			foreach($team_list as $league_table){
			    $league_table = $JSON->decode($league_table);
				$team_ids[] = $league_table->id;
				$team_names[] = $league_table->name;
			}
	    }
		$number_of_teams = count($team_ids);
	}
   
	$PB_output .= '<div class="content box">';
	    $PB_output .= '<div class="header">';
            if (isset($_POST['submit'])) $PB_output .= '<h2>Creating '.$league_name.' fixture...</h2>';
	        else $PB_output .= '<h2 align="center">Add '.$league_name.' Fixture , <span class="i em"><b>Enter Match Fixture Details</b></span></h2>';
	    $PB_output .= '</div>';
	    if (isset($_POST['submit']) AND isset($_GET['mode']) AND $_GET['mode']=="admin" AND isset($_GET['section']) AND $_GET['section']=="addfixture" AND isset($_GET['c']) AND $_GET['c']=="ok") {
            if (isset($_POST['home_team']) && isset($_POST['away_team'])) {
		        $match_id = $_POST['match_id'];
		        $away_team = $_POST['away_team'];
		        $home_team = $_POST['home_team'];
		        $matchmonth = $_POST['matchmonth'];
		        $matchyear = $_POST['matchyear'];
		        $season = $_POST['season'];
		        $matchday = $_POST['matchday'];
		        $kickoffmin = $_POST['kickoffmin'];
		        $kickoffhour = $_POST['kickoffhour'];
                $matchday_preview_notes = isset($_POST['matchday_preview_notes']) ? $_POST['matchday_preview_notes'] : 'No match report available for this fixture';
				
                $match_date = $timestamp_date;
				$fixtures_filename = $match_date.'_fixture.txt';
		        $NewFixture_FileName = $season_path.'fixtures/'.$fixtures_filename ;
                $id = 1;
			    if (strlen($NewFixture_FileName)>0){
			        $file = @fopen($NewFixture_FileName,"w");
			        // write the txt file
		            if ($file != false){
				        for($i=0;$i<$number_of_teams/2;$i++){
						    if(strlen($home_team[$i]) > 0){
							    $matchdate[$i] = $matchyear.'-'.$matchmonth[$i].'-'.$matchday[$i];
                                if($kickoffhour[$i] == 'TBA')$matchtime[$i] = 'TBA';
					            else $matchtime[$i] = $kickoffhour[$i].':'.$kickoffmin[$i];
								if(strlen($matchday_preview_notes[$i]) > 5) $matchday_preview_notes[$i] = $matchday_preview_notes[$i];
								else $matchday_preview_notes[$i] = 'No match preview available for this fixture';
					            
								$data[$i] = array('match_id' => $id,'home_team'=>$home_team[$i],'away_team'=>$away_team[$i],'matchdate'=>$matchdate[$i],'matchtime'=>$matchtime[$i],'matchyear'=>$matchyear,'matchpreview'=>$matchday_preview_notes[$i],'season'=>$season);
				            }
		                    $id++;
				        }
					    $final_data = $JSON->encode($data);
		                fwrite($file,$JSON->clearWhitespaces($final_data)."\n");
			            fclose($file);
					    // If upload is successful.
		                $PB_output .= '<div class="message"><p><b><font color="green">League Fixture File saved successfully.</font></b></p></div>';
					    $PB_output .= '<div class="message unspecific"><p><a href="'.BASE_URI.'index.php?mode=home">Go to the homepage</a> - <a href="?mode=admin&amp;section=addfixture&amp;match_type='.$match_type.'">Create another fixture</a></p></div>';
		                $PB_output .= '<center><input type="button" onclick="open_fixture_window();" style="color:lime" value="Open Fixtures Window"></center>';
					} else $PB_output .= '<p class="message error"><b>Wrong file name!</b></p>'; // If upload is un-successful.
			        
				} else $PB_output .= '<p class="message error"><b>Empty file name!</b></p>'; // If a file name was not submitted.
				// end creation txt file 
	        } else {
		        $PB_output .= '<div class="message user_status"><p>The home teams and away teams were not selected </p></div>';
		    }
	    } else {
		    
			$PB_output .='<div class="link-bar">';
		        $PB_output .='<form id="sortby-top" class="grid-9" action="" method="GET">';
		            $PB_output .='<input name="mode" type="hidden" value="'.MODE.'"><input name="section" type="hidden" value="'.SECTION.'"><input name="match_type" type="hidden" value="'.$match_type.'">';
			        
					if(file_exists($League_DB_File)){
				        $PB_output .='<select name="league">';
                            $league_names = file($League_DB_File);
                            foreach($league_names as $league_list_rows){
				                $league_row = $JSON->decode($league_list_rows);
								if(LEAGUE == $league_row->league_name) $selected_league = ' SELECTED';
								else $selected_league = '';
				                $PB_output .= '<option value="'.$league_row->league_name.'" '.$selected_league.'>'.$league_row->league_title.'</option>';
			                }
				        $PB_output .='</select>';
				    } else {
		                $PB_output .= '<div class="message user_status"><p><b>No Leagues Database File Found</b></p></div>'."\n";
		            }
					$PB_output .='<input type="submit" title="view another fixture" class="update" value="" />';
				$PB_output .='</form>';
				$PB_output .='<div class="clear">&nbsp;</div>';
			$PB_output .='</div>';
			
            $PB_output .= '<form method="POST" action="'.$_SERVER['PHP_SELF'].'?mode=admin&amp;section=addfixture&amp;match_type='.$match_type.'&amp;c=ok">';
                $PB_output .= '<fieldset><legend><b>Main information (required):</b></legend>';
				
                    $PB_output .= '<table class="table fixtures-table grid-full">';
                        $PB_output .= '<thead><tr class="table-header"><th>Match #</th><th>Home Team</th><th>&nbsp;</th><th>Away Team</th><th colspan="2">Match Date</th><th colspan="2">Match Time</th></tr></thead>';
                        $PB_output .= '<tbody>';
					        //<span class="admin_hints">Kick Off Time</span>
				            for($i=0;($i<$number_of_teams/2);$i++){
		                        $teamnum = $i + 1;
	                            $PB_output .= "<tr class=\"fixture-row\">";
						            $PB_output .= "<td align=\"center\">Match : $teamnum <input type=\"hidden\" value=\"$teamnum\" name=\"match_id[]\" /></td>";
							        
									//home team name column
							        $PB_output .= "<td><select name=\"home_team[]\" >";
							        $PB_output .= '<option value="">Home Team</option>';
								    foreach($team_names as $home_team){
							            $PB_output .= "<option value=\"$home_team\">$home_team</option>";
							        }
							        $PB_output .= "</select></td>";
									
							        // versus column
							        $PB_output .= "<td align=\"center\"><sup class=\"b i\">V</sup><sub class=\"b\">S</sub></td>";
									
							        //away team name column
							        $PB_output .= "<td><select name=\"away_team[]\" >";
							        $PB_output .= '<option value="">Away Team</option>';
							        foreach($team_names as $away_team){
							            $PB_output .= "<option value=\"$away_team\">$away_team</option>";
							        }
							        $PB_output .= "</select></td>";
									
							        //match day column
                                    $PB_output .= "<td><select name=\"matchday[]\">"; 
                                    for($md=1;($md<32);$md++){
								        $matchday = $md;
	               	                    if($matchday == date('j')) $selected_date ='SELECTED';
									    else $selected_date = '';
									
		            	         	    $PB_output .= "<option value=\"$matchday\" $selected_date>$matchday</option>";
		             	            }
                                    $PB_output .= "</select></td>";
									
                                    //match month column
                                    $PB_output .= "<td><select name=\"matchmonth[]\">"; 
                                    for($mnt=1;($mnt<13);$mnt++){
		                                $month = $mnt;
									    if($month == date('n')) $selected_month ='SELECTED';
								     	else $selected_month = '';
			            	            $PB_output .= "<option value=\"$month\" $selected_month>$month</option>";
		             	            }
                                    $PB_output .= "</select></td>";
									
                                    //match kickoff hour column
                                    $PB_output .= "<td><select name=\"kickoffhour[]\">"; 
                                    $PB_output .= "<option value=\"TBA\">TBA</option> "; 
                                    for($hr=1;($hr<25);$hr++){
		                                $hour = $hr;
			                	        $PB_output .= "<option value=\"$hour\">$hour</option>";
			                        }
                                    $PB_output .= "</select></td>";
									
									//match kickoff minute column
                                    $PB_output .=  "<td><select name=\"kickoffmin[]\">";
                                    foreach($kickoffmins as $minutes){
							            $PB_output .= "<option value=\"$minutes\">$minutes</option>";
							        }
						            $PB_output .= "</select></td>";
					
						        $PB_output .= "</tr>";
								$PB_output .= '<tr><td colspan="8"><p class="grid-full" style="margin-top:10px;"><input type="checkbox" value="" onClick="return kadabra(\'preview_'.$i.'\');">add match preview info</p>';
			                        $PB_output .= '<div id="preview_'.$i.'" style="display:none" align="center">';
						    	        $PB_output .= 'Match Preview<br><textarea rows="4" cols="90" name="matchday_preview_notes[]"></textarea>';
							        $PB_output .= '</div>';
					            $PB_output .= '</td></tr>';
					        	$PB_output .= '<tr class="row-spacer"><td colspan="8">&nbsp;</td></tr>';
	                        }
	               
					        $PB_output .= '<tr class="row-spacer">';
							    //league season column
                                $PB_output .= '<td align="center" colspan="4"><span class="admin_hints">Season</span><br>';
					                if(count($allLeagueSeasons) > 0){
				                        $PB_output .='<select name="season">';
                                        foreach($allLeagueSeasons as $season_rows){
							                if($season_rows != 'fixtures' && $season_rows != 'results' && $season_rows != 'icons'){
								                if(SEASON == $season_rows) $selected_season = ' SELECTED';
								                else $selected_season = '';
				                                $PB_output .= '<option value="'.$season_rows.'" '.$selected_season.'>'.$season_rows.'</option>';
			                                }
							            }
				                        $PB_output .='</select>';
				                    } else $PB_output .= '<div class="message user_status"><p><span class="b">No Leagues Database File Found</span></p></div>'."\n";
                                $PB_output .=  '</td>'; 
						        //league year column
						        $PB_output .= "<td colspan=\"4\" align=\"center\"><span class=\"admin_hints\">Year</span><br><select name=\"matchyear\">"; 
                                    $PB_output .= "<option value=\"$year\">$year</option>"; 
                                    $PB_output .= "<option value=\"$nextyear\">$nextyear</option>";
                                $PB_output .= "</select></td>"; 
                            $PB_output .= '</tr>';
				        $PB_output .= '</tbody>';
                    $PB_output .= '</table>';
                $PB_output .= '</fieldset>';
				$PB_output .= '<center><input type="Submit" name="submit" value="Save Fixture" onClick="showNotify(\'Creating....new....fixture....\');" class="save" /><a class="cancel" href="?mode=admin" title="cancel and return to the admin index">Cancel</a></center><br />';
            $PB_output .= '</form>';
	    }
	$PB_output .= '</div>';
	echo $PB_output;
?> <script type="text/javascript">
			function open_fixture_window(){
			    var baseUrl = '<?php echo BASE_URL?>';
			    var competition = '<?php echo LEAGUE?>';
			    var fixture = '<?php echo $fixtures_filename?>';
		        window.open(baseUrl + "index.php?mode=leagues&section=fixtures&league="+ competition +"&fixture="+ fixture, "_blank", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=610, height=460");
	        }
	    </script>