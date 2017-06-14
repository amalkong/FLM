<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    if (($roleID < 4))  {
       echo'<div class="box"><div class="message error"><p>Access Denied!,You are not authorised to access this section, your user role does not permit you to update leagues.</p></div></div>';
	   include(BASE_PATH.'footer.php');
	   exit;
    }
    if(!isset($PB_output)) $PB_output = NULL;
	$path = $UTIL->Check_For_Slash(LEAGUE_PATH.LEAGUE,true);
	$season_path = $UTIL->Check_For_Slash($path.SEASON,true);
	$profiles_path = $season_path.'profiles/';
	$league_file = SEASON.'_league_table.txt';
	$league_file_path = $path.$league_file;
    $i = 1;
	$league_tables = array();
    $data = array();
	$league_info = GetLeagueInfo(LEAGUE);
	$league_name = $league_info['league_title'];
	$PB_output .= '<div class="content">';
	    $PB_output .= '<div class="box">';
        if (isset($_POST['submit']) AND isset($_GET['mode']) AND $_GET['mode']=="admin" AND isset($_GET['c']) AND $_GET['c']=="ok") {
            if (isset($_POST['team_name'])) {
                $PB_output .= '<h3>Creating league File</h3>';
	            $team_name = $_POST['team_name'];
		        $played = $_POST['played'];
		        $id = $_POST['team_id'];
		        $wins = $_POST['wins'];
		        $loss = $_POST['loss'];
		        $draws = $_POST['draws'];
				
		        $for = $_POST['for'];
		        $against = $_POST['against'];
		        $difference = $_POST['difference'];
		        $points = $_POST['points'];
		        $profile = isset($_POST['profile']) ? $_POST['profile'] : 'This team profile currently un-available';
	        	
		        $fileName = $path.$league_file;
            
				$number_of_teams = count($team_name);
			    if (strlen($fileName)>0){
			        $final_data = "";
			        $file = @fopen($fileName,"w");
			        // write the txt file
		            if ($file != false){
				        for($i=0;$i<$number_of_teams;$i++){
					        if(strlen($team_name[$i]) > 0) {
							    $data[$i] = array('id' => $id[$i],'name'=>$team_name[$i],'pl'=>$played[$i],'win'=>$wins[$i],'lose'=>$loss[$i],'draw'=>$draws[$i],'gf'=>$for[$i],'ga'=>$against[$i],'gd'=>str_replace(array('-','+'),' ',$difference[$i]),'pts'=>$points[$i]);
				                $data2 = array('team_name'=>$team_name[$i],'team_profile'=> (($profile[$i] != NULL) ? $profile[$i] : 'This team profile currently un-available')); 
							    $NewProfile_FileName = $profiles_path.$team_name[$i].'.txt';
							    $team_profile = @fopen($NewProfile_FileName,"w");
							    if ($team_profile != false){
							        $final_data2 = $JSON->encode($data2);
		                            fwrite($team_profile,$JSON->clearWhitespaces($final_data2)."\n");
			                        fclose($team_profile);
								    $PB_output .= '<div class="message"><p><strong><font color="green">Team Profile for '.$team_name[$i].' edited successfully.</font></strong></p></div>';
							    } else $PB_output .= '<div class="message error"><p><strong>ERROR:</strong><font color="red">Team Profile for '.$team_name[$i].' wasn&rsquot edited successfully.</font></p></div>';
							}
						}
					    $final_data .= $JSON->encode($data);
		                fwrite($file,$JSON->clearWhitespaces($final_data)."\n");
			            fclose($file);
					    // If upload is successful.
		                $PB_output .= '<div class="message"><p><font color="green"><strong>League Table</strong> file for '.$league_name.' was edited successfully.</font></p></div>';
		                $PB_output .= '<div class="message unspecific"><p><a href="'.SELF.'?mode=home">Go to the homepage</a> - <a href="?mode=admin&amp;section=addleague">Create a new league</a></p></div>';
		            }
			    }
				// end creation txt file
	            
	        } else $PB_output .= '<div class="message error"><p><strong>ERROR:</strong> League Name wasn&rsquo;t set ,<a href="javascript:history.back()">back</a></p></div>'; // If empty league name.
        } else {
		    if(file_exists($league_file_path)){
                $results = $JSON->decode(file_get_contents($league_file_path));
			    if(is_array($results)) {
			        foreach($results as $league_table => $key ){
				        $files = array();
				        $files[0] = $league_file;
				        $files[1] = $results[$league_table]->id;
				        $files[2] = $results[$league_table]->name;
					
				        $total_played = $results[$league_table]->win + $results[$league_table]->lose + $results[$league_table]->draw;
                        $files[3] = $total_played;
				        $files[4] = $results[$league_table]->win;
                        $files[5] = $results[$league_table]->lose;
                        $files[6] = $results[$league_table]->draw;
                        $files[7] = $results[$league_table]->gf;
                        $files[8] = $results[$league_table]->ga;
					
                        $goaldifference = calculateGoalDifference($files[7],$files[8]);
					    $files[9] = $goaldifference;
					    //$files[9] = $results[$league_table]->gd;
					
				        $total_points = calculatePoints($files[4],$files[4],$files[6]);
				        //$files[10] = $$results[league_table]->pts;
                        $files[10] = $total_points;
			            array_push( $league_tables,$files);
	                }
			    } else {
			        $team_list = file($league_file_path);
			        foreach($team_list as $league_table){
				        $files = array();
				        $files[0] = $league_table;
			            $league_table = json_decode($league_table);
				        $files[1] = $league_table->id;
			    	    $files[2] = $league_table->name;
				        $total_played = $league_table->win + $league_table->lose + $league_table->draw; 
                        //$files[3] = $league_table->pl;
                        $files[3] = $total_played;
				        $files[4] = $league_table->win;
                        $files[5] = $league_table->lose;
                        $files[6] = $league_table->draw;
                        $files[7] = $league_table->gf;
                        $files[8] = $league_table->ga;
					    $goaldifference = calculateGoalDifference($files[7],$files[8]);
					    $files[9] = $goaldifference;
                        //$files[9] = $league_table->gd;
				        $total_points = calculatePoints($files[4],$files[4],$files[6]);
				        //$files[10] = $league_table->pts;
                        $files[10] = $total_points;
			            array_push( $league_tables,$files);
	                }
			    }
			    
                $PB_output .= '<form action="'.SELF.'?mode=admin&amp;section='.SECTION.(isset($_GET['action']) ? '&amp;action='.$_GET['action'].'&amp;filetype=league' : '').'&amp;league='.LEAGUE.'&amp;season='.SEASON.'&amp;c=ok" method="POST" enctype="multipart/form-data" name="uploadform" id="uploadform">';
		            $PB_output .= '<fieldset><legend><b>Main information (required):</b></legend>';
			    	    $PB_output .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.$max_upload_size.'">';
				        $PB_output .= '<table class="table add-league grid-full" cellspacing="2">';
				            //$PB_output .= '<tr><td>&nbsp;</td><td>Team Icon</td><td>Team Name</td></tr>';
					    	$PB_output .= '<thead><tr class="table-header">';
				                $PB_output .= '<th scope="col">Pos</th><th scope="col">&nbsp;</th>
			                    <th title="sort by team names">Team</a></th>
					            <th scope="col" title="matches played">Pl</th>
					            <th scope="col" title="matches won">W</th>
					            <th scope="col" title="matches lost">L</th>
					            <th scope="col" title="matches drawn">D</th>
					            <th scope="col" title="goals scored for">GF</th>
					            <th scope="col" title="goals scored against">GA</th>
					            <th scope="col" title="goals difference">GD</th>
					            <th scope="col" title="total points">PTS</th>';
					        $PB_output .= '</tr></thead>';
					        $PB_output .= '<tbody>';
							    foreach($league_tables as $table){
								    $profile_filename = getTeamProfile($table[2]);
						            $teamnum = $i + 1;
									$logo_tmp_name = str_replace(array(' ','&nbsp;','-'),'_',$table[2]);
						            $logo_png = LEAGUE.'/'.SEASON.'/icons/'.$logo_tmp_name.'.png';
						            $logo_gif = LEAGUE.'/'.SEASON.'/icons/'.$logo_tmp_name.'.gif';
						            $logo_jpg = LEAGUE.'/'.SEASON.'/icons/'.$logo_tmp_name.'.jpg';
							
							        if(file_exists(LEAGUE_PATH.$logo_png)) $team_logo = LEAGUE_URL.$logo_png;
							        elseif(file_exists(LEAGUE_PATH.$logo_gif)) $team_logo = LEAGUE_URL.$logo_gif;
							        elseif(file_exists(LEAGUE_PATH.$logo_jpg)) $team_logo = LEAGUE_URL.$logo_jpg;
							        else $team_logo = IMG_URL.'football_classic1.png';
					         
	                                $PB_output .= '<tr>';
					                    $PB_output .= '<td class="position" align="center"><input type="text" name="team_id[]" size="1" value="'.$table[1].'" /></td>
							            <td class="logo"><img style="width:25px;" src="'.$team_logo.'" alt="'.$table[2].' logo" /></td>
				        	            <td title="'.$table[2].'" class="team-name"><input type="text" name="team_name[]" size="10" value="'.$table[2].'" /></td>
					                    <td class="played"><input type="text" name="played[]" size="1" value="'.$table[3].'" /></td>
				        	            <td class="wins"><input type="text" name="wins[]" size="1" value="'.$table[4].'" /></td>
					                    <td class="loss"><input type="text" name="loss[]" size="1" value="'.$table[5].'" /></td>
					                    <td class="draws"><input type="text" name="draws[]" size="1" value="'.$table[6].'" /></td>
				        	            <td class="for"><input type="text" name="for[]" size="1" value="'.$table[7].'" /></td>
					                    <td class="against"><input type="text" name="against[]" size="1" value="'.$table[8].'" /></td>
                                        <td class="difference"><input type="text" name="difference[]" size="1" value="'.$table[9].'" /></td>
                                        <td class="points"><input type="text" name="points[]" size="1" value="'.$table[10].'" /></td>';
				                    $PB_output .= '</tr>';
									$PB_output .= '<tr class="row-spacer"><td colspan="11"><p class="grid-full" style="margin-top:10px;"><a onClick="return kadabra(\'teamprofile_'.$i.'\');" href="#">Edit '.$table[2].' Profile ?</a></p>';
			                            $PB_output .= '<div id="teamprofile_'.$i.'" style="display:none" align="center">';
						    	        $PB_output .= 'Profile<br><textarea rows="4" cols="90" name="profile[]">'.$profile_filename[1].'</textarea>';
							            $PB_output .= '</div>';
					                $PB_output .= '</td></tr>';
				                    $i++;
						        }
                            $PB_output .= '</tbody>';
                        $PB_output .= '</table>';
				
			            $PB_output .= '<p class="admin_hints">Fields marked with * are required.</p>';
			        $PB_output .= '</fieldset>';
			
			        $PB_output .= '<br />';
                    $PB_output .= '<center><input type="submit" name="submit" value="Update League Table" onClick="showNotify(\'Updating...league...table...\');" class="save" /><a class="cancel" href="'.SELF.'?mode=admin" title="cancel and return to the admin index">Cancel</a></center><br />';
	            $PB_output .= '</form>';
	        } else {
	            $PB_output .= '<div class="message user_status"><p>'._MISSING_LEAGUE_TABLE_FILE.'</p><br/><strong>Debug Info:</strong><br/> Path'.$separator.$path.'<br/> File'.$separator.$league_file.',</div>';
                $PB_output .= '<div class="message unspecific"><p><a href="'.SELF.'?mode=admin&amp;section=addleague">Create a new league</a></p>';
		    }
        }
	    $PB_output .= '</div>';
	$PB_output .= '</div>';
	if(ACTION == 'edit') return $PB_output;
	else echo $PB_output;
?>