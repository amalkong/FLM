<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    if (($roleID < 4))  {
       echo'<div class="box"><div class="message error"><p>Access Denied!,You are not authorised to access this section, your user role does not permit you to add or edit league seasons.</p></div></div>';
	   include(BASE_PATH.'footer.php');
	   exit;
    }
    $PB_output = NULL;
	
	$PB_output .= '<div class="content">';
	$PB_output .= '<div class="box">';
	
	if(file_exists(LEAGUE_PATH.LEAGUE)){
	$filesuffix = NULL; // declare variable for duplicated filenames
	$image_new_name = NULL; // declare variable for image name
	
	$fileNAMES = array();
    $data = array();
	$id = 1;
	// Determine max upload file size through php script reading the server parameters (and the form parameter specified in config.php. We find the minimum value: it should be the max file size allowed...
	// convert max upload size set in config.php in megabytes
	$max_upload_size_MB = $max_upload_size/1048576;
	$max_upload_size_MB = round($max_upload_size_MB, 2);
	$showmin = min($max_upload_size_MB, ini_get('upload_max_filesize')+0, ini_get('post_max_size')+0); // min function
	// Note: if I add +0 it eliminates the "M" (e.g. 8M, 9M) and this solves some issues with the "min" function
	
    $date=date("Y-m-d");
    $year = substr("$date", 0, 4);
    $nextyear = $year + 1;
    $yearafternext = $nextyear + 1;
    $prevyear = $year -1;
    $lastseason = ($year -1).'-'.$prevyear;
    $thisseason = $prevyear.'-'.$year;
    $nextseason = $year.'-'.$nextyear;
    $seasonafternext = $nextyear.'-'.$yearafternext;
	$league_info = GetLeagueInfo(LEAGUE);
	$league_name = $league_info['league_title'];
    $path = LEAGUE_PATH.LEAGUE.'/'.SEASON.'_league_table.txt';
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
			    $league_table = json_decode($league_table);
				$team_ids[] = $league_table->id;
				$team_names[] = $league_table->name;
			}
	    }
		$number_of_teams = count($team_ids);
	} else $number_of_teams = 0;
	
    if (!isset($_GET['number_of_teams'])){
	    $PB_output .= '<div class="header"><h2>Select the league season to update</h2></div>';
	    $PB_output .= '<div class="panel grid-40 left-1">As you know teams get relegated and promoted, you are about to add a new season to an existing league, so the first thing to do is select the league you want to add the season to, if it hasn&rsquo;t been set already in the url used to access this page.Some real world league format may change especially if the league is developing, in that case you may add more teams or remove teams if needs be. The maximum number of teams per league is '.$PB_CONFIG['max_number_teams'].' and the minimum is '.$PB_CONFIG['min_number_teams'].', set in <span class="em i">[settings_file.php]</span>. You can&rsquo;t go above or below the options set. If you want to add more teams <a href="'.SELF.'?mode=admin&amp;section=setup&amp;action=file_setup">change settings</a></div>';
	    $PB_output .= '<div class="right-1 grid-2"><form action="" method="GET" class="">';
		    $PB_output .= '<fieldset>';
			    $PB_output .= '<input name="mode" type="hidden" value="'.MODE.'"><input name="section" type="hidden" value="'.SECTION.'">';
			    $PB_output .= '<label for="number_of_teams"><span class ="admin_hints">Current # of teams</span><br />';
	            $PB_output .= '<input type="text" name="number_of_teams" class="grid-1" size="3" value="'.$number_of_teams.'" required />';
	            //$PB_output .= '<input type="hidden" name="number_of_teams" size="3" value="20">';
				if(file_exists($League_DB_File)){
				    $PB_output .= '<label for="league">League*</label>';
				    $PB_output .= "<select name=\"league\">";
	                $results = $JSON->decode(file_get_contents($League_DB_File));
		            if(is_array($results)) {
			            foreach($results as $league_list => $key ){
				            (LEAGUE==$results[$league_list]->league_name) ? $selected = "SELECTED" : $selected = "";
							$PB_output .= '<option value="'.$results[$league_list]->league_name.'" '.$selected.'>'.$results[$league_list]->league_title.'</option>'."\n";
			            }
		            } else {
			            $results = file($League_DB_File);
                        foreach($results as $league_list_rows){
						    $league_row = $JSON->decode($league_list_rows);
						    (LEAGUE==$league_row->league_name) ? $selected = "SELECTED" : $selected = "";
						    $PB_output .= "<option value=\"$league_row->league_name\" $selected>$league_row->league_title</option>\n";
					    }
	                }
					$PB_output .= "</select>";
	            } else {
	                $PB_output .= '<div class="message user_status"><p>The leagues database file was not found. The cause of this may be: the database file may have been accidentally deleted or it does&rsquo;nt exists.</p></div>';
					$PB_output .= '<div class="message unspecific"><p><a href="'.SELF.'?mode=admin&amp;section=addleague">Create a new league</a></p></div>';
	            }
				$PB_output .= '<center><input type="submit" class="continue" value="Continue..." /></center>';
	        $PB_output .= '</fieldset>';
		$PB_output .= '</form></div>';
	} else if (isset($_GET['number_of_teams']) && $_GET['number_of_teams'] <= $PB_CONFIG['max_number_teams'] && $_GET['number_of_teams'] > $PB_CONFIG['min_number_teams']){
	    $number_of_teams = $_GET['number_of_teams'];
		
        if (isset($_POST['submit']) AND isset($_POST['season'])  AND isset($_GET['number_of_teams']) AND isset($_GET['mode']) AND $_GET['mode']=="admin" AND isset($_GET['section']) AND $_GET['section']=="addseason" AND isset($_GET['c']) AND $_GET['c']=="ok") {
            if (isset($_POST['team_name'])) {
                $PB_output .= '<div class="header"><h2>Creating '.$league_name.' new season File</h2></div>';
				
				$team_name = $_POST['team_name'];
	        	$season = $_POST['season'];
		    	
		        $NewLeague_FileName = LEAGUE_PATH.LEAGUE.'/'.$season.'_league_table.txt' ;
		        
			    if (strlen($NewLeague_FileName)>0 && !file_exists($NewLeague_FileName)){
		            // Create the necessary folders which will contain the new league data
				    // The league folder
		    	    $new_season_folder = LEAGUE_PATH.LEAGUE."/".$season."/";
				    if(!file_exists($new_season_folder)){
					    $PB_output .= '<div class="message unspecific">';
					    if(mkdir($new_season_folder,DIR_WRITE_MODE)){ 
						     $PB_output .= 'Season folder created successfully</br>';
					    
				            // The fixtures folder
		    	            $fixtures_folder = LEAGUE_PATH.LEAGUE."/$season/fixtures/";
				            if(mkdir($fixtures_folder,DIR_WRITE_MODE)) $PB_output .= 'fixtures folder created successfully</br>';
				            else $PB_output .= 'fixtures folder creation un-successful</br>';
							// The results folder
		    	            $results_folder = LEAGUE_PATH.LEAGUE."/$season/results/";
				            if( mkdir($results_folder,DIR_WRITE_MODE)) $PB_output .= 'results folder created successfully</br>';
			                else $PB_output .= 'results folder creation un-successful</br>';
							// The icons folder
		    	            $icon_upload_folder = LEAGUE_PATH.LEAGUE."/$season/icons/";
				            if( mkdir($icon_upload_folder,DIR_WRITE_MODE)) $PB_output .= 'icons folder created successfully</br>';
						    else $PB_output .= 'icons folder creation un-successful</br>';
						} else $PB_output .= 'Season folder creation un-successful</br>';
						$PB_output .= '</div>';
				    } else $PB_output .= '<div class="message user_status"><p>Season folder already exists</p></div>';
			        $final_data = "";
			        $file = @fopen($NewLeague_FileName,"a");
			        // write the txt file
		            if ($file != false){
				        for($i=0;$i<$number_of_teams;$i++){
					        if(strlen($team_name[$i]) > 0){
					            $data[$i] = array('id' => $id,'name'=>$team_name[$i],'pl'=>'','win'=>'','lose'=>'','draw'=>'','gf'=>'','ga'=>'','gd'=>'','pts'=>'');
				            }
		                    $id++;
				        }
					    $final_data .= $JSON->encode($data);
		                fwrite($file,$JSON->clearWhitespaces($final_data)."\n");
			            fclose($file);
					    // If upload is successful.
		                $PB_output .= '<div class="message"><p><strong><font color="green">League Table File saved successfully.</font></strong></p>';
		                $PB_output .= '<p><a href="'.SELF.'?mode=admin">Go to the admin homepage</a> - <a href="'.SELF.'?mode=admin&amp;section=addseason&amp;league='.LEAGUE.'">Create another season</a></p></div>';
		            } else $PB_output .= '<p class="message error"><strong>Wrong file name!</strong></p>'; // If upload is un-successful.
			    } else {
				    $PB_output .= '<div class="message error"><p>The file '.$NewLeague_FileName.', already exists, changing the season may fix the problem</p></div>';
				    $PB_output .= '<div class="message unspecific"><p><a href="javascript:history.go(-1)">&larr;&nbsp;Back</a></p></div>';
				}
				// end creation txt file 
				
	            for($i=0;$i<$number_of_teams;$i++){
		            if(isset($_FILES['teamIcon']) AND $_FILES['teamIcon']!=NULL){
			            $icon = $_FILES['teamIcon']['name'];
		                if(strlen($icon[$i]) > 0){
		                    //$ext = @end(explode(".", $_FILES['teamIcon']['name'][$i]));
		                    $ext = explode(".", $_FILES['teamIcon']['name'][$i]);

			                if($rename_upload_file){
			                    //list($usec, $sec) = explode(" ", microtime());
			                    //$fileNAMES[$i] = $sec."_".$usec;
							    $fileNAMES[$i] = sanitize_filename($team_name[$i]);
			                } else {
		                  	    $xperiods = str_replace("." . $ext, "", $_FILES['teamIcon']['name'][$i]);
			                    $fileNAMES[$i] = str_replace(".", "", $xperiods);
			                }

		       	            if(!$UTIL->isValidExt('.'.$ext[1], $ifxs)){
		                	    $PB_output .= '<div class="message error"><p>FAILED: '. $_FILES['teamIcon']['name'][$i] .' ERROR: Not a valid image extension.</p></div>'."\n";
		    	            } elseif($_FILES['teamIcon']['size'][$i] > ($max_size_in_kb*1024)){
		                  	    $PB_output .= '<div class="message error"><p>FAILED: '. $_FILES['teamIcon']['name'][$i] .' ERROR: File size too large.</p></div>'."\n";
			                } elseif(file_exists($icon_upload_folder.$fileNAMES[$i] .".". $ext[1])){
			                    $PB_output .= '<div class="message error"><p>FAILED: '. $fileNAMES[$i] .'.'. $ext[1] .' ERROR: File already exists.</p></div>'."\n";
			                } else {
			    	            if(move_uploaded_file($_FILES['teamIcon']['tmp_name'][$i], $icon_upload_folder.$fileNAMES[$i] .".". $ext[1])){
			                      	$PB_output .= '<div class="message"><p>UPLOADED: '. $fileNAMES[$i] .'.'. $ext[1] ."</p></div>\n";
				                    
								} else {
			         	            $PB_output .= '<div class="message error"><p>FAILED: '. $_FILES['teamIcon']['name'][$i] .' ERROR: Undetermined.</p></div>'."\n";
			     	            }
			                }
		                }
		            }
	            }
	        }
        } else {
			$PB_output .= '<div class="header"><h2>Enter Team Information</h2></div>';
			$PB_output .= '<div class="panel">Replace the names of the teams who are no longer in the league with the new team names in the <strong>team name column</strong>. Select an image/logo (32x32 recommended) to attatch to each teams to be used as team icon, and that&rsquo;s it.</div>';
            $PB_output .= '<form action="'.SELF.'?mode=admin&amp;section=addseason&amp;league='.LEAGUE.'&amp;number_of_teams='.$number_of_teams.'&amp;c=ok" method="POST" enctype="multipart/form-data" name="uploadform" id="uploadform">';
		        $PB_output .= '<fieldset><legend><strong>Main information (required):</strong></legend>';
				    
					$PB_output .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.$max_upload_size.'">';
					if ($showmin!=NULL and $showmin!="0") { 
			            $PB_output .= '<p><span class ="admin_hints">Your server configuration allows you to upload files up to '.$showmin.'MB</span></p>';
				    }
			    	$PB_output .= "<p>Allowed image types: ." . implode($allowed_file_types, " ."). "</p>\n";
			       
				    $PB_output .= '<table class="table grid-full" style="border-collapse:separate;border-spacing:5px;">';
				        $PB_output .= '<tr>';
						    $PB_output .= '<td style="text-align:center;">';
						    	$PB_output .= "<label for=\"season\">Season*</label><select name=\"season\">".
                                    "<option value=\"$thisseason\" selected>$thisseason</option>".
                                    "<option value=\"$nextseason\">$nextseason</option>".
                                    "<option value=\"$seasonafternext\">$seasonafternext</option>".
                                "</select> "; 
						    $PB_output .= '</td>';
						$PB_output .= '</tr>';
				    $PB_output .= '</table>';
					
				    $PB_output .= '<table class="table add grid-full">';
						$PB_output .= '<thead class="table-header" ><tr><th style="width:100px !important;" scope="col">#</th><th scope="col">Team Icon</th><th scope="col">Team Name</th></tr></thead>';
                        $PB_output .= '<tbody>';
						    for($i=0;$i<$number_of_teams;$i++){
	                            $PB_output .= "<tr class=\"new-row\"><td style=\"width:100px !important;\">Team ID : $team_ids[$i] </td><td><input type=\"file\" name=\"teamIcon[]\" /></td><td><input type=\"text\" name=\"team_name[]\" size=\"100\" maxlength=\"50\" value=\"$team_names[$i]\"/></td></tr>";
	                            $PB_output .= '<tr class="row-spacer"><td colspan="3">&nbsp;</td></tr>';
						    }
                        $PB_output .= '</tbody>';
                    $PB_output .= '</table>';
				
			        $PB_output .= '<p class="admin_hints">Fields marked with * are required.</p>';
			    $PB_output .= '</fieldset>';
			
		    	$PB_output .= '<center><input type="Submit" name="submit" value="Add Season" onClick="showNotify(\'Creating....league....season\');" class="save" /><a class="cancel" href="'.SELF.'?mode=admin" title="cancel and return to the admin index">Cancel</a></center><br />';
	        $PB_output .= '</form>';
	    }
	} else if (isset($_GET['number_of_teams']) && $_GET['number_of_teams'] > $PB_CONFIG['max_number_teams']){
	    $PB_output .= '<div class="message user_status">A max number of <strong>'.$PB_CONFIG['max_number_teams'].'</strong> teams can be added, <strong>'.$_GET['number_of_teams'].'</strong> teams were added. <a href="'.SELF.'?mode=admin&amp;section=addseason">Return</a></div>';
	} else if (isset($_GET['number_of_teams']) && $_GET['number_of_teams'] < $PB_CONFIG['min_number_teams']){
	    $PB_output .= '<div class="message user_status">A min number of <strong>'.$PB_CONFIG['min_number_teams'].'</strong> teams can be added, <strong>'.$_GET['number_of_teams'].'</strong> teams were added. <a href="'.SELF.'?mode=admin&amp;section=addseason">Return</a></div>';
	}
	} else $PB_output .= '<div class="message user_status">A league name <strong>'.LEAGUE.'</strong> does not exists, it may have been accidentally deleted or it was renamed, so a new season <strong>can&rsquo;t be added.</strong>. <a href="'.SELF.'?mode=admin&amp;section=addleague">create a league</a></div>';
	
	$PB_output .= '</div>';
	$PB_output .= '</div>';
	echo $PB_output; 
?>