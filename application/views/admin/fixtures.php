<?php
    if (($roleID < 4))  {
       echo'<div class="message error"><p>Access Denied!,You are not authorised to access this section, your user role does not permit you to add or edit league fixtures.</p></div>';
	   exit;
    }
    $PB_output = NULL;
    $i = 1;
	$ccount=0;
	$PB_CONFIG['max_number_fixtures'] = 10;

    $timestamp_date=date("Y-m-d");
    $year = substr("$timestamp_date", 0, 4);
    $month = substr("$timestamp_date", 5,-3);
    $day = substr("$timestamp_date", 8);
    $nextyear = $year + 1;
	$kickoffmins = array('00','05','10','15','20','30','40','45','50','55');
	//$matchTypes = array('friendly'=>'Friendly','world_cup'=>'World Cup','euro'=>'UEFA European Championship','concacaf'=>'Concacaf Championship','copa'=>'Copa America','afc'=>'Africa Cup Of Nations');
	(isset($_GET['match_type'])) ? $match_type = $_GET['match_type'] : $match_type = 'international';
	if($match_type == 'international'){
	    $competitionIds = array();
	    $competitionTitles = array();
	    $competitionTypes = array();
	    if(file_exists($Competition_DB_File)){
	    $results = file($Competition_DB_File);
		foreach($results as $competition_list){
		    $competition_list = $JSON->decode($competition_list);
			$competitionIds[] = $competition_list->competition_id;
			$competitionTypes[] = $competition_list->competition_name;
			$competitionTitles[] = $competition_list->competition_title;
			$ccount++;
		}
	    }
	}else{
        $selected_table = LEAGUE;
        $ext = pathinfo($selected_table, PATHINFO_EXTENSION);
	    //$league = ($ext == '') ? $selected_table.'.txt' : $selected_table;
	    $league = $selected_table.'/league_table.txt';
        $path = LEAGUE_PATH.$league;
	    if(file_exists($path)){
        $results = $JSON->decode(file_get_contents($path));
		if(is_array($results)) {
			foreach($results as $league_table => $key ){
				$team_ids[] = $results[$league_table]->id;
				$team_names[] = $results[$league_table]->name;
			}
		} else {
			$team_list = file($path);
			foreach($team_list as $league_table){
			    $league_table = $JSON->decode($league_table);
				$team_ids[] = $league_table->id;
				$team_names[] = $league_table->name;
			}
	    }
		$number_of_teams = count($team_ids);
	    }
	}
	$PB_output .= '<div class="content box">';
    if (!isset($_GET['number_of_fixtures'])){
	    $PB_output .= '<div class="header"><h2 align="center">Select the number of fixture to added</h2></div>';
	    $PB_output .= '<div class="panel grid-45 left-1">You are about to add new fixtures, so the first thing to do is select the number of fixture to add. The maximum number of fixtures are <b>'.$PB_CONFIG['max_number_fixtures'].'</b>. Also set the match type to store the fixture under.</div>';
	    $PB_output .= '<div class="right-1 grid-2"><form action="" method="GET">';
		    $PB_output .= '<input name="mode" type="hidden" value="'.MODE.'"><input name="section" type="hidden" value="'.SECTION.'">';
		    $PB_output .= '<fieldset>';
			    $PB_output .= '<label for="number_of_fixtures">fixtures count*</label><input type="text" name="number_of_fixtures" class="grid-10" size="3" value="'.$PB_CONFIG['max_number_fixtures'].'" required />&nbsp;&nbsp;';
	            $PB_output .= '<label for="competition">Match Type*</label><select name="competition">';
				    for($i=0;$i<(count($results));$i++){
					    $PB_output .= '<option value="'.$competitionTypes[$i].'">'.$competitionTitles[$i].'</option>';
					}
				$PB_output .= '</select>';
	            $PB_output .= '<center><input type="submit" value="Submit" /></center>';
	        $PB_output .= '</fieldset>';
		$PB_output .= '</form></div>';
	} else if (isset($_GET['number_of_fixtures']) && $_GET['number_of_fixtures'] <= $PB_CONFIG['max_number_fixtures']){
	    $number_of_fixtures = $_GET['number_of_fixtures'];
		
	    if (isset($_POST['submit']) AND isset($_GET['number_of_fixtures']) AND isset($_GET['competition']) AND isset($_GET['mode']) AND $_GET['mode']=="admin" AND isset($_GET['section']) AND $_GET['section']=="addinternational" AND isset($_GET['c']) AND $_GET['c']=="ok") {
            if (isset($_POST['home_team']) && isset($_POST['away_team'])) {
		        $match_id = $_POST['match_id'];
		        $away_team = $_POST['away_team'];
		        $home_team = $_POST['home_team'];
		        $matchmonth = $_POST['matchmonth'];
		        $matchyear = $_POST['matchyear'];
		        $matchday = $_POST['matchday'];
		        $kickoffmin = $_POST['kickoffmin'];
		        $kickoffhour = $_POST['kickoffhour'];
		        $venue = $_POST['venue'];
                
                $matchnotes = (isset($_POST['matchday_notes']) && strlen($_POST['matchday_notes']) > 5) ? $_POST['matchday_notes'] : 'No match note was entered';
				$match_date = $timestamp_date;
				
				$matchnotes_filename = $match_date.'_matchnote.txt';
				$fixtures_filename = $match_date.'_fixture.txt';
			
		        $NewMatchnote_FileName = COMPETITION_PATH.COMPETITION.'/matchnotes/'.$matchnotes_filename ;
		        $NewFixture_FileName = COMPETITION_PATH.COMPETITION.'/fixtures/'.$fixtures_filename ;
                $id = 1;
			    if (strlen($NewFixture_FileName)>0){
			        $file = @fopen($NewFixture_FileName,"w");
			        // write the txt file
		            if ($file != false){
				        for($i=0;$i<$number_of_fixtures;$i++){
						    if(strlen($home_team[$i]) > 0){
							    $matchdate[$i] = $matchyear.'-'.$matchmonth[$i].'-'.$matchday[$i];
                                if($kickoffhour[$i] == 'TBA') $matchtime[$i] = 'TBA';
					            else $matchtime[$i] = $kickoffhour[$i].':'.$kickoffmin[$i];
								$data[$i] = array('match_id' => $id,'home_team'=>$home_team[$i],'away_team'=>$away_team[$i],'venue'=>$venue[$i],'matchdate'=>$matchdate[$i],'matchtime'=>$matchtime[$i],'matchyear'=>$matchyear);
				            }
		                    $id++;
				        }
					    $final_data = $JSON->encode($data);
		                fwrite($file,$JSON->clearWhitespaces($final_data)."\n");
			            fclose($file);
					    // If upload is successful.
		                $PB_output .= '<div class="message"><p><b><font color="green">competition Fixture File saved successfully.</font></b></p></div>';
		                
					    $file2 = @fopen($NewMatchnote_FileName,"w");
			            // write the matchnotes txt file
		                if ($file2 != false){
					        $data2 = array(
			                    'note_date' => $match_date,
			                    'note' => sanitize($matchnotes,false)
						    );
						    $final_data2 = $JSON->encode($data2);
		                    fwrite($file2,$JSON->clearWhitespaces($final_data2)."\n");
							$PB_output .= '<div class="message"><p><b><font color="green">Match Notes File saved successfully.</font></b></p></div>';
					    }
					    $PB_output .= '<div class="message"><p><a href="'.BASE_URI.'index.php?mode=home">Go to the homepage</a> - <a href="?mode=admin&amp;section=addfixture&amp;match_type=international">Create another fixture</a></p></div>';
		                $PB_output .= '<center><input type="button" onclick="open_fixture_window();" style="color:lime" value="Open Fixtures Window"></center>';
					} else $PB_output .= '<p class="message error"><b>Wrong file name!</b></p>'; // If upload is un-successful.
			        
				} else $PB_output .= '<p class="message error"><b>Empty file name!</b></p>'; // If a file name was not submitted.
				// end creation txt file 
	        } else {
		        $PB_output .= '<div class="message user_status"><p>The home teams and away teams were not selected </p></div>';
		    }
	    } else {
		    $PB_output .= '<div class="header"><h2 align="center">Add '.cleanPageTitles($_GET['competition']).' Fixture, <span class="i em"><b>Enter Match Fixture Details</b></span></h2></div>';
            $PB_output .= '<form method="POST" action="'.$_SERVER['PHP_SELF'].'?mode=admin&amp;section=addinternational&amp;number_of_fixtures='.$number_of_fixtures.'&amp;competition='.COMPETITION.'&amp;c=ok">';
                $PB_output .= '<fieldset><legend><b>Main information (required):</b></legend>';
                    /*$PB_output .= '<ul>';
                        countrylist('list');
                    $PB_output .= '</ul>';*/
                    $PB_output .= '<table class="table fixtures-table grid-full">';
                        $PB_output .= '<thead><tr class="table-header"><th>Match #</th><th>Home Team</th><th>&nbsp;</th><th>Away Team</th><th colspan="2">Match Date</th><th colspan="2">Match Time</th></tr></thead>';
                        $PB_output .= '<tbody>';
					        //<span class="admin_hints">Kick Off Time</span>
				            for($i=0;($i<$number_of_fixtures);$i++){
		                        $teamnum = $i + 1;
	                            $PB_output .= "<tr class=\"fixture-row\">";
						            $PB_output .= "<td align=\"center\">Match : $teamnum <input type=\"hidden\" value=\"$teamnum\" name=\"match_id[]\" /></td>";
							        
									//home team name column
							        $PB_output .= "<td><select name=\"home_team[]\" >";
							            $PB_output .= '<option value="">Home Team</option>';
                                        $PB_output .= countrylist('dropdown','Jamaica');
							        $PB_output .= "</select></td>";
									
							        // versus column
							        $PB_output .= "<td align=\"center\"><sup class=\"b i\">V</sup><sub class=\"b\">S</sub></td>";
									
							        //away team name column
							        $PB_output .= "<td><select name=\"away_team[]\" >";
							            $PB_output .= '<option value="">Away Team</option>';
                                        $PB_output .= countrylist('dropdown','Mexico');
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
					        	$PB_output .= '<tr class="fixture-row"><td colspan="8" align="center"><label for="venue">Venue</label><input type="text" name="venue[]" class="grid-2" /></td></tr>';
					        	$PB_output .= '<tr class="row-spacer"><td colspan="8">&nbsp;</td></tr>';
	                        }
	               
					        $PB_output .= '<tr class="row-spacer">';
							   //competition year column
						        $PB_output .= "<td colspan=\"8\" align=\"center\"><span class=\"admin_hints\">Year</span><br><select name=\"matchyear\">"; 
                                    $PB_output .= "<option value=\"$year\">$year</option>"; 
                                    $PB_output .= "<option value=\"$nextyear\">$nextyear</option>";
                                $PB_output .= "</select></td>"; 
                            $PB_output .= '</tr>';
							
					        $PB_output .= '<tr><td colspan="8" align="center"><p class="grid-full" style="margin-top:10px;"><input type="checkbox" value="" onClick="return kadabra(\'main\');">add match day preview information</p>';
			                    $PB_output .= '<div id="main" style="display:none">';
						    	   $PB_output .= 'Matchday Notes<br><textarea rows="4" cols="90" name="matchday_notes"></textarea>';
							    $PB_output .= '</div>';
					        $PB_output .= '</td></tr>';
				        $PB_output .= '</tbody>';
                    $PB_output .= '</table>';
                $PB_output .= '</fieldset>';
				$PB_output .= '<center><input type="Submit" name="submit" value="Save Fixture" onClick="showNotify(\'Creating....new....fixture....\');" class="save" /><a class="cancel" href="?mode=admin" title="cancel and return to the admin index">Cancel</a></center><br />';
            $PB_output .= '</form>';
	    }
	} else if (isset($_GET['max_number_fixtures']) && $_GET['max_number_fixtures'] >= $PB_CONFIG['max_number_fixtures']){
	    $PB_output .= '<div class="message user_status">A max number of <b>'.$PB_CONFIG['max_number_fixtures'].'</b> fixtures can be added, but <b>'.$_GET['max_number_fixtures'].'</b> fixtures were set. <a href="?mode=admin&amp;section=upload">Return</a></div>';
	}
	$PB_output .= '</div>';
	echo $PB_output;
?>
	    <script type="text/javascript">
			function open_fixture_window(){
			    var baseUrl = '<?php echo BASE_URL?>';
			    var competition = '<?php echo COMPETITION?>';
			    var fixture = '<?php echo $fixtures_filename?>';
		        window.open(baseUrl + "index.php?mode=competitions&section=fixtures&competition="+ competition +"&fixture="+ fixture, "_blank", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, copyhistory=no, width=610, height=460");
	        }
	    </script>