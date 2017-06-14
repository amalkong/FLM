<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    if (($roleID < 4))  {
       echo'<div class="message error"><p>Access Denied!,You are not authorised to access this section, your user role does not permit you to add or edit any league.</p></div>';
	   exit;
    }
    if(!isset($PB_output)) $PB_output = NULL;
	$filesuffix = NULL; // declare variable for duplicated filenames
	$image_new_name = NULL; // declare variable for image name
	
	// Determine max upload file size through php script reading the server parameters (and the form parameter specified in config.php. We find the minimum value: it should be the max file size allowed...
	// convert max upload size set in config.php in megabytes
	$max_upload_size_MB = $max_upload_size/1048576;
	$max_upload_size_MB = round($max_upload_size_MB, 2);
	$showmin = min($max_upload_size_MB, ini_get('upload_max_filesize')+0, ini_get('post_max_size')+0); // min function
	// Note: if I add +0 it eliminates the "M" (e.g. 8M, 9M) and this solves some issues with the "min" function
	
	
    $date=date("Y-m-d");

    $year = substr("$date", 0, 4);
    $month = substr("$date", 5,-3);
    $day = substr("$date", 8);

    $nextyear = $year + 1;
    $prevyear = $year -1;
    $lastseason = ($year -1).'-'.$prevyear;
    $thisseason = $prevyear.'-'.$year;
    $nextseason = $year.'-'.$nextyear;
	
	$fileNAMES = array();
    $data = array();
    $data2 = array();
	
	$PB_output .= '<div class="content">';
	$PB_output .= '<div class="box">';
    if (!isset($_GET['number_of_teams'])){
	    $PB_output .= '<div class="header"><h2>Select the number of teams in the new league</h2></div>';
	    $PB_output .= '<div class="panel grid-2 left-1">You are about to create a new league, so the first thing to do is select the number of teams in the new league. The maximum number of teams per league is '.$PB_CONFIG['max_number_teams'].' and the minimum is '.$PB_CONFIG['min_number_teams'].'.</div>';
	    $PB_output .= '<div class="right-1 grid-40"><form action="" method="GET" class="">';
		    $PB_output .= '<fieldset>';
			    $PB_output .= '<input name="mode" type="hidden" value="'.MODE.'"><input name="section" type="hidden" value="'.SECTION.'">';
			    $PB_output .= '<label for="number_of_teams"><span class ="admin_hints">Select # of teams, max # 20</span></label><br />';
	            $PB_output .= '<input type="text" name="number_of_teams" class="grid-30" size="3" value="'.$PB_CONFIG['max_number_teams'].'" required />';
	            //$PB_output .= '<input type="hidden" name="number_of_teams" size="3" value="20">';
				$PB_output .= '<label for="season">Season*</label>';
				$PB_output .= "<select name=\"season\">".
                    "<option value=\"$thisseason\" selected>$thisseason</option>".
                    "<option value=\"$nextseason\">$nextseason</option>".
                "</select><br />"; 
				$PB_output .= '<center><input class="continue" type="submit" value="Continue..." /></center>';
	        $PB_output .= '</fieldset>';
		$PB_output .= '</form></div>';
	} else if (isset($_GET['number_of_teams']) && $_GET['number_of_teams'] <= $PB_CONFIG['max_number_teams'] && $_GET['number_of_teams'] > $PB_CONFIG['min_number_teams']){
	    $number_of_teams = $_GET['number_of_teams'];
		
        if (isset($_POST['submit']) AND isset($_GET['number_of_teams']) AND isset($_GET['mode']) AND $_GET['mode']=="admin" AND isset($_GET['section']) AND $_GET['section']=="addleague" AND isset($_GET['c']) AND $_GET['c']=="ok") {
            if (isset($_POST['league_name'])) {
                $PB_output .= '<div class="header"><h2>Creating New League</h2></div>';
				
				$team_name = $_POST['team_name'];
				$profile = $_POST['profile'];
				$league_title = sanitize($_POST['league_title']);
	        	$season = $_POST['season'];
	        	$league_name = $_POST['league_name'];
		    	$sanitized_name = sanitize_filename($league_name);
			
		        $NewLeague_FileName = LEAGUE_PATH.$sanitized_name.'/'.$season.'_league_table.txt' ;
		        $NewLeague_InfoFile = DATABASE_PATH.'leagues.txt' ;
            
			    if (file_exists($NewLeague_InfoFile)) {
		            $fp = @fopen($NewLeague_InfoFile, 'r');  
		            $array = explode("\n", fread($fp, filesize($NewLeague_InfoFile)));
			        $listed = count($array);
			        for($x=0;$x<$listed;$x++) {		// start loop, each line of file
				        $temp = explode("}",$array[$x]);	// explode the line and assign to temp
                        $team_id = $x + 1;
			        }
		        } else $team_id = 1;
		        
				$id = 1;
			    if (strlen($NewLeague_FileName)>0){
				    // Create the necessary folders which will contain the new league data
				    $new_league_folder = LEAGUE_PATH.$sanitized_name;
				    if(!file_exists($new_league_folder) && mkdir($new_league_folder,DIR_WRITE_MODE)){
						$PB_output .= '<div class="message">League folder created successfully</div>';
						$new_season_folder = LEAGUE_PATH.$sanitized_name."/".$season."/";
					    if(mkdir($new_season_folder,DIR_WRITE_MODE)){
						    $PB_output .= '<div class="message unspecific">';
				            // The fixtures folder
		    	            $fixtures_folder = LEAGUE_PATH.$sanitized_name."/$season/fixtures/";
				            if(mkdir($fixtures_folder,DIR_WRITE_MODE)) $PB_output .= '<font color="green">fixtures folder created successfully</font></br>';
				            else $PB_output .= '<font color="red">fixtures folder creation un-successful</font><br/>';
							// The results folder
		    	            $results_folder = LEAGUE_PATH.$sanitized_name."/$season/results/";
				            if( mkdir($results_folder,DIR_WRITE_MODE)) $PB_output .= '<font color="green">results folder created successfully</font></br>';
			                else $PB_output .= '<font color="red">results folder creation un-successful</font><br/>';
							// The icons folder
		    	            $icon_upload_folder = LEAGUE_PATH.$sanitized_name."/$season/icons/";
				            if( mkdir($icon_upload_folder,DIR_WRITE_MODE)) $PB_output .= '<font color="green">icons folder created successfully</font></br>';
						    else $PB_output .= '<font color="red">icons folder creation un-successful</font><br/>';
							// The profiles folder
		    	            $profiles_folder = LEAGUE_PATH.$sanitized_name."/$season/profiles/";
				            if( mkdir($profiles_folder,DIR_WRITE_MODE)) $PB_output .= '<font color="green">profiles folder created successfully</font></br>';
						    else $PB_output .= '<font color="red">profiles folder creation un-successful</font><br/>';
							// The headers folder
		    	            $logo_upload_folder = DATA_IMAGE_PATH."logos/";
                            if(!file_exists($logo_upload_folder)) {
							    if(mkdir($logo_upload_folder,DIR_WRITE_MODE)) $PB_output .= '<font color="green">logo folder created successfully</font></br>';
			                    else $PB_output .= '<font color="red">logo folder creation un-successful</font><br/>';
							}
						
						    $PB_output .= '</div>';
				            // end folder creation
							  
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

		       	                        if(!in_array(strtolower($ext[1]), $allowed_file_types)){
		                	                $PB_output .= '<div class="message error"><p>FAILED: '. $_FILES['teamIcon']['name'][$i] .' ERROR: File type not allowed.</p></div>'."\n";
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
				            // end icon upload
			               
				            if(isset($_FILES['league_image']['name']) AND $_FILES['league_image']['name'] != NULL){
			                    $league_image = $_FILES['league_image']['name'];
					            $file_ext=explode(".",$league_image); // divide filename from extension
			                    $n = strrpos($league_image,".");
                                $file_ext[1] = substr($league_image,$n,strlen($league_image)-$n);
                                $file_ext[1] = str_replace('.','',$file_ext[1]);
					            //rename the upload icon to match team name
								$image_new_name = sanitize_filename($league_name).'.'.$file_ext[1];
			                    
                                if(!$UTIL->isValidExt(strtolower('.'.$file_ext[1]), $ifxs)) $PB_output .= '<div class="message error"><p>FAILED: '. $_FILES['league_image']['name'] .' ERROR: File type not allowed.</p></div>'."\n";//if icon is not a valid image extension show error message.
		    	                elseif($_FILES['league_image']['size'] > ($max_size_in_kb*1024)) $PB_output .= '<div class="message error"><p>FAILED: '. $_FILES['league_image']['name'] .' ERROR: File size to large.</p></div>'."\n";//if icon is larger than the $max_upload_size show error message.
			                    elseif(file_exists($logo_upload_folder.$image_new_name)) $PB_output .= '<div class="message error"><p>FAILED: '. $image_new_name .' ERROR: File already exists.</p></div>'."\n";//if the icon already exists show error message.
			                    else {
			    	                if(move_uploaded_file($_FILES['league_image']['tmp_name'], $logo_upload_folder.$image_new_name)){
			                            $PB_output .= '<div class="message"><p>UPLOADED: '. $image_new_name."</p></div>\n"; //if icon is uploaded successfully show success message.
					            	} else {
			         	                $PB_output .= '<div class="message error"><p>FAILED: '. $_FILES['league_image']['name'] .' ERROR: Undetermined.</p></div>'."\n";
			     	                }
			                    }
				            } else $image_new_name = NULL;
				
			                if (strlen($NewLeague_InfoFile)>0){
				                $infodata = array('league_id' => sanitize($team_id),'league_name' => $sanitized_name,'league_title' => sanitize($league_title),'league_image' => sanitize_filename($image_new_name));
					            $final_infodata = $JSON->encode($infodata);
					
		                        $league_info = @fopen($NewLeague_InfoFile,"a");
			                    // write the txt file
		                        if ($league_info != false){
			                        fwrite($league_info,$final_infodata."\n");
			                        fclose($league_info);
				                    // If upload is successful.
		                            $PB_output .= '<div class="message"><p><b><font color="green">League Info registered successfully.</font></b></p></div>';
		                            //$PB_output .= '<div class="message unspecific"><p><a href="'.BASE_URI.'index.php">Go to the homepage</a> - <a href="?mode=admin&amp;section=addleague">Create another league</a></p></div>';
		                        }
		                        // end creation txt file
				
			                } else $PB_output .= '<p class="message error"><b>ERROR:</b> League Info wasn&rsquo;t registered,<b>Wrong file name!</b></p>'; // If upload is un-successful.
                            $final_data = "";
			                $file = @fopen($NewLeague_FileName,"a");
			                // write the txt file
		                    if ($file != false){
				                for($i=0;$i<$number_of_teams;$i++){
					                if(strlen($team_name[$i]) > 0){
					                    $data[$i] = array('id' => $id,'name'=>$team_name[$i],'pl'=>'','win'=>'','lose'=>'','draw'=>'','gf'=>'','ga'=>'','gd'=>'','pts'=>'');
				                        //-----------------------------------
										$data2 = array('team_name'=>$team_name[$i],'team_profile'=> (($profile[$i] != NULL) ? $profile[$i] : 'This team profile currently un-available'));
										$NewProfile_FileName = $profiles_folder.$team_name[$i].'.txt';
										$team_profile = @fopen($NewProfile_FileName,"w");
										$final_data2 = $JSON->encode($data2);
		                                fwrite($team_profile,$JSON->clearWhitespaces($final_data2)."\n");
			                            fclose($team_profile);
									}
		                            $id++;
				                }
					            $final_data .= $JSON->encode($data);
		                        fwrite($file,$JSON->clearWhitespaces($final_data)."\n");
			                    fclose($file);
					            // If upload is successful.
		                        $PB_output .= '<div class="message"><p><b><font color="green">League Table File saved successfully.</font></b></p></div>';
		                        $PB_output .= '<div class="message unspecific"><p><a href="'.BASE_URI.'index.php?mode=home">Go to the homepage</a> - <a href="'.$_SERVER['PHP_SELF'].'?mode=admin&amp;section=addleague">Create another league</a></p></div>';
		                    } else $PB_output .= '<div class="message error"><p><strong>ERROR:</strong> League Table File wasn&rsquo;t saved,<strong>Wrong file name!</strong></p></div>'; // If upload is un-successful.
				
						} else $PB_output .= '<div class="message error"><p><b>ERROR:</b> can&rsquot create the league folder.</p></div>';
			        } elseif(file_exists($new_league_folder)) $PB_output .= '<div class="message user_status"><p>League folder creation un-successful, the league <span class="em i">'.basename($new_league_folder).'</span> already exists.<a href="index.php?mode=admin&amp;section=addseason&amp;league='.$sanitized_name.'&amp;season='.$season.'">Add season to <strong>'.$sanitized_name.'</strong></a></p></div>';
			        elseif(!mkdir($new_league_folder,DIR_WRITE_MODE)) $PB_output .= '<div class="message error"><p>League folder does&rsquo;t exists, but still can&rsquo;t create it somehow</p></div>';
				}
				// end creation txt file 
	        } else $PB_output .= '<div class="message error"><p><strong>ERROR:</strong> League Name wasn&rsquo;t set ,<a href="javascript:history.back()">back</a></p></div>'; // If empty league name.
				
        } else {
			$PB_output .= '<div class="header"><h2>Enter Team Information</h2></div>';
            $PB_output .= '<form action="?mode=admin&amp;section=addleague&amp;number_of_teams='.$number_of_teams.'&amp;c=ok" method="POST" enctype="multipart/form-data" name="uploadform" id="uploadform">';
		        $PB_output .= '<fieldset><legend><strong>Main information (required):</strong></legend>';
				    
					$PB_output .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.$max_upload_size.'">';
					if ($showmin!=NULL and $showmin!="0") { 
			            $PB_output .= '<p><span class ="admin_hints">Your server configuration allows you to upload files up to '.$showmin.'MB</span></p>';
				    }
			    	$PB_output .= "<p>Allowed image types: ." . implode($allowed_file_types, " ."). "</p>\n";
			       
				    $PB_output .= '<table class="table grid-full" style="border-collapse:separate;border-spacing:5px;">';
			            $PB_output .= '<tr>';
			                $PB_output .= '<td align="center" style="border-right:1px dotted #ddd;"><span class="admin_hints">This is the name the league file will be saved as, keep it simple. eg [myleague,spl]</span><br />
					            <label for="league_name">League Name*</label>
			                    <input id="league_name" type="text" name="league_name" class="grid-3" maxlength="50" required />
						    </td>';
			    
				            $PB_output .= '<td align="center"><span class="admin_hints">This is the <b>page title</b> that will be displayed when this league is selected,keep it short[50 chars. max].</span><br />
					            <label for="league_title">League Title*</label>
			                    <input id="league_title" type="text" name="league_title" class="grid-3" maxlength="50" required />
					        </td>';
					    $PB_output .= '</tr>';
			    
				        $PB_output .= '<tr><td colspan="" align="center" style="border-top:1px dotted #ddd;">
					        <span class ="admin_hints">Attach an image to display as a header when the league is selected.</span><br />
					        <label for="league_title">League Header Image*</label>
			                <input type="file" name="league_image" size="50" />
					        </td>';
						    $PB_output .= '<td style="border-top:1px dotted #ddd;text-align:center;">';
						    	$PB_output .= "<select name=\"season\">".
                                    "<option value=\"$thisseason\" selected>$thisseason</option>".
                                    "<option value=\"$nextseason\">$nextseason</option>".
                                "</select> "; 
						    $PB_output .= '</td>';
						$PB_output .= '</tr>';
				    $PB_output .= '</table>';
					
				    $PB_output .= '<table class="table add grid-full">';
						$PB_output .= '<thead class="table-header" ><tr><th style="width:100px !important;" scope="col">#</th><th scope="col">Team Icon</th><th scope="col">Team Name</th></tr></thead>';
                        $PB_output .= '<tbody>';
						    for($i=0;$i<$number_of_teams;$i++){
						        $teamnum = $i + 1;
	                            $PB_output .= "<tr class=\"new-row\"><td style=\"width:100px !important;\">Team : $teamnum </td><td><input type=\"file\" name=\"teamIcon[]\" /></td><td><input type=\"text\" name=\"team_name[]\" size=\"100\" maxlength=\"50\"/></td></tr>";
	                            $PB_output .= '<tr class="row-spacer"><td colspan="3"><textarea rows="" cols="" name=profile[]></textarea></td></tr>';
	                            $PB_output .= '<tr class="row-spacer"><td colspan="3">&nbsp;</td></tr>';
						    }
                        $PB_output .= '</tbody>';
                    $PB_output .= '</table>';
				
			        $PB_output .= '<p class="admin_hints">Fields marked with * are required.</p>';
			    $PB_output .= '</fieldset>';
			    $PB_output .= '<center><input type="Submit" name="submit" value="Add League" onClick="showNotify(\'Creating....new....league\');" class="save" /><a class="cancel" href="?mode=admin" title="cancel and return to the admin index">Cancel</a></center><br />';
	        $PB_output .= '</form>';
	    }
	} else if (isset($_GET['number_of_teams']) && $_GET['number_of_teams'] > $PB_CONFIG['max_number_teams']){
	    $PB_output .= '<div class="message user_status">A max number of <b>'.$PB_CONFIG['max_number_teams'].'</b> teams can be added, <b>'.$_GET['number_of_teams'].'</b> teams were added. <a href="?mode=admin&amp;section=addleague">Return</a></div>';
	} else if (isset($_GET['number_of_teams']) && $_GET['number_of_teams'] < $PB_CONFIG['min_number_teams']){
	    $PB_output .= '<div class="message user_status">A min number of <b>'.$PB_CONFIG['min_number_teams'].'</b> teams can be added, <b>'.$_GET['number_of_teams'].'</b> teams were added. <a href="?mode=admin&amp;section=addleague">Return</a></div>';
	}
	$PB_output .= '</div>';
	$PB_output .= '</div>';
	echo $PB_output;
?>