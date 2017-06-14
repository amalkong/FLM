<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
	$f_sortby = isset($_GET['f_sortby']) ? $_GET['f_sortby'] : 'title';
	$f_ascdsc = isset($_GET['f_ascdsc']) ? $_GET['f_ascdsc'] : "DESC";
	$GLOBALS['f_sortby'] = $f_sortby;
    $GLOBALS['f_ascdsc'] = $f_ascdsc;
	$df_sortby = "pts"; // default sorting method ["pts","gd","ga","gf","name","win","lose","draw","pl"]
    $df_ascdsc = "DESC";// default ordering (ascending or descending) ["ASC","DESC"]
    $t_sortby = ( isset($_GET["t_sortby"]) ) ? $_GET["t_sortby"] : $df_sortby;
    $t_ascdsc = ( isset($_GET["t_ascdsc"]) ) ? $_GET["t_ascdsc"] : $df_ascdsc;
	$GLOBALS['t_sortby'] = $t_sortby;
    $GLOBALS['t_ascdsc'] = $t_ascdsc;
    $season = ( isset($_GET["season"]) ) ? $_GET["season"] : '';
    $file_name = ( isset($_GET["team"]) ) ? $_GET["team"] : '';
	//------------------------------------------------
	$league_files = array();
	$league_tables = array();
	$league_sortby_array = array("name"=>"Name", "id"=>"Id", "title"=>"Title");	
	$allLeagueSeasons = GetDirContents(LEAGUE_PATH.LEAGUE,'dirs');
	if(!isset($allLeagues))$allLeagues = GetDirContents(LEAGUE_PATH,'dirs'); 
	//------------------------------------------------
	$url_prefix = SELF.'?mode='.MODE.'&amp;section='.SECTION.'&amp;league='.LEAGUE.'&amp;season='.SEASON;
	$urlVars = $url_prefix."&amp;f_sortby=$f_sortby&amp;f_ascdsc=$f_ascdsc";
    $create_url = SELF."?mode=admin&amp;section=addleague";
	$x = 1;   
	$ci = "";
	$prevNext_link = NULL;
	echo'<div class="content" id="__Top">';
	    echo'<div class="box2">';
	        switch ( SECTION ) {
                case 'section_index': 
				    echo'<div class="head"><h2>Select Profile</h2></div>';
		            echo'<div class="panel">';
					    echo'<p>Select the profile you would like to view, bare in mind that some user profiles may be private and locked from non-adminitrative personnels. The league teams profiles are open to view if available.</p>';
		                echo'<ul>'.
		                   '<li><a href="'.SELF.'?mode='.MODE.'&amp;section=userprofile">User Profile</a></li>'.
		                   '<li><a href="'.SELF.'?mode='.MODE.'&amp;section=teamprofile">Team Profile</a></li>'.
						'</ul>';
			        echo'</div>';
		        break;
		        case 'userprofile':
				    // Check user existance	
	                $Users_DB_File = DATABASE_PATH."users.txt";
	                $users_info = array();
	                $users = array();
	                if(file_exists($Users_DB_File)){
		                $line = file($Users_DB_File);
		                $listed = count($line);
		                for($x=0;$x<$listed;$x++) {	// start loop, each line of file
			                $tmp = explode(":",$line[$x]); // explode the line and assign to tmp
                            $users['user_name'] = $tmp[0];
                            $users['user_password'] = $tmp[1];
                            $users['user_role'] = $tmp[2];
		                 	if($tmp[3] != '') $users['display_name'] = $tmp[3];
		                	else $users['display_name'] = $tmp[0];
		                	$users['user_email'] = $tmp[4];
		                	$users['user_avatar'] = $tmp[5];
		                 	$users['show_avatar'] = $tmp[6];
		                    array_push($users_info,$users);
		                }
						
	                    $total_users= count($users_info);
				        echo'<div class="head"><h2>'.ucfirst(SECTION).',<span class="small">'.$total_users.' users found</span></h2></div>';
					    echo '<p><a href="'.SELF.'?mode='.MODE.'&amp;section=teamprofile&amp;league='.LEAGUE.'&amp;season='.SEASON.'">View Team profiles</a></p>';
					    if($roleID >= 1) echo '<div class="message user_status"><p>Only registered users can edit their own profile.</p></div>';
						if($total_users > 0){
	                        for($i=0;$i<$total_users;$i++){
							    $id = $i;
								$edit_url = SELF.'?mode=admin&amp;section=manage&amp;action=edit&amp;filetype=profile&amp;profile='.$users_info[$i]['user_name'].'&amp;id='.$id;
								$delete_url = SELF.'?mode=admin&amp;section=manage&amp;action=delete&amp;filetype=profile&amp;profile='.$users_info[$i]['user_name'].'&amp;id='.$id;
								($users_info[$i]['user_email'] == '' ? $email = 'Not Available' : $email = '<a href="mailto:'.$users_info[$i]['user_email'].'">&nbsp;'.$users_info[$i]['user_email'].'</a>');
								
							    //if(($users_info[$i]['show_avatar'] == 'YES') && file_exists(AVATAR_PATH.$users_info[$i]['user_avatar'])) $avatar_img = AVATAR_URL.$users_info[$i]['user_avatar'];
							    if(file_exists(AVATAR_PATH.$users_info[$i]['user_avatar'])) $avatar_img = AVATAR_URL.$users_info[$i]['user_avatar'];
								else $avatar_img = AVATAR_URL.'no_avatar.png';
								
								if($i %2) echo'<div class="panel right-1 " style="width:46%;margin-right:auto;">';
		                        else echo'<div class="panel left-1" style="width:46%;margin-right:5px;clear:both;">';
                                    echo '<h3>'.$users_info[$i]['user_name'].' Profile</h3>';
                                    echo'<div class="left-1 grid-1"><p align="center"><img src="'.$avatar_img.'" alt="'.$users_info[$i]['display_name'].' user avatar" /></p></div>';
                                    echo'<ul class="UL-list grid-3 right-1">';
									    echo'<li>Display Name&nbsp;:&nbsp;'.$users_info[$i]['display_name'].'</li>';
                                        echo '<li>Role&nbsp;:&nbsp;'.$users_info[$i]['user_role'].'</li>';
                                        echo'<li>Email&nbsp;:&nbsp;'.$email.'</li>';
										if($roleID >= 1) echo '<li><a href="'.$edit_url.'">edit</a> | <a href="'.$delete_url.'">delete</a>'.$separator.'profile</li>';
                                    echo'</ul>';
                                echo'</div>';
							}
					    } else echo '<div class="message user_status"><span class="em i">No registered Users was found or profile is private.</span></div>';
					} else echo '<div class="message user_status">The <span class="em i">Users DB File</span> was not found.</div>';
	            break;
		
	            case 'teamprofile':
                    echo'<div class="head"><h2>'.ucfirst(SECTION).' For All Registered Leagues of '.$CFG->config['site_title_full'].'</h2></div>';
		            echo '<p><a href="'.SELF.'?mode='.MODE.'&amp;section=userprofile&amp;league='.LEAGUE.'&amp;season='.SEASON.'">View User profiles</a></p>';
		            if(file_exists($League_DB_File)){
					    if(!isset($_GET['team'])){
						    $league_files = getLeagues(); 
		                    usort($league_files,'sort_file');
						    $total_leagues = count($league_files);
						    if( count($league_files) == 0 ) echo '<div class="message user_status"><p>There are currently no leagues registered.</p></div>';
                            else {
				                $numPages = ceil( count($league_files) / $CFG->config['itemsPerPage'] );
                                if(isset($_GET['p'])) {
	                                $currentPage = $_GET['p'];
                                    if($currentPage > $numPages) $currentPage = $numPages;
                                } else $currentPage=1;
        
                                $start = ( $currentPage * $CFG->config['itemsPerPage'] ) - $CFG->config['itemsPerPage'];
	                            if(count($allLeagues) > 0){
								    echo'<div class="link-bar">';
		                                echo'<form id="sortby-top" class="grid-9 right-1" action="" method="GET">';
		                                    echo'<input name="mode" type="hidden" value="'.MODE.'"><input name="section" type="hidden" value="'.SECTION.'">';
				                            echo'<select name="season">';
								            foreach($allLeagues as $ldir){
				                                $leagueSeasons = GetDirContents(LEAGUE_PATH.$ldir,'dirs');
				                                $leagueSeasons_arr = word_freq($leagueSeasons);
	                                            if(count($leagueSeasons_arr) > 0){
												    // TODO: find a way to display matching seasons once
										            foreach($leagueSeasons_arr as $season_rows=>$key) echo '<option value="'.$season_rows.'" '.((SEASON == $season_rows) ? 'SELECTED' : '').'>'.$season_rows.'</option>';
						                        } else echo '0 league season found';
								            }
									        echo'</select>';
				        	                echo'<input type="submit" title="view another fixture" class="update" value="" />';
				                        echo'</form>';
							            echo'<div class="clear">&nbsp;</div>';
			                        echo'</div>';
								} else echo '0 leagues found';
								
				                echo '<table class="table competitions grid-full"><caption>All Leagues</caption>';
				                echo '<thead><tr class="row-spacer"><th scope="col">Logo</th><th scope="col">League Name</th>';
					                if(SECTION =='manage') echo '<th scope="col">Delete League</th><th scope="col">Edit League</th><th scope="col">Delete Season</th>';
					                else echo '<th scope="col">Tables</th><th scope="col">Fixtures</th><th scope="col">Results</th>';
					            echo '</tr></thead>';
                    	
				    	        echo '<tbody>';
	                            for( $i=$start; $i<$start + $CFG->config['itemsPerPage']; $i++ ) {
					                if( isset($league_files[$i][3]) ) {
									    $league_name = $league_files[$i][3];
									    $league_file_name = $league_name.'/'.$season.'_league_table.txt';
                                        $league_path = LEAGUE_PATH.$league_file_name;
										
										if(file_exists(LEAGUE_PATH.$league_name) && is_dir(LEAGUE_PATH.$league_name))$thisLeagueSeasons = GetDirContents(LEAGUE_PATH.$league_name,'dirs');
									    if(file_exists($league_path)){
										  
		                                    $league_add_url = SELF.'?mode=admin&amp;section=addseason&amp;league='.$league_files[$i][3];
		                                    $league_delete_url = SELF.'?mode=admin&amp;section=manage&amp;action=delete&amp;filetype=league&amp;league='.$league_files[$i][3].'&amp;season='.$season;
                                            $league_edit_url = SELF.'?mode=admin&amp;section=manage&amp;action=edit&amp;filetype=league&amp;league='.$league_files[$i][3].'&amp;season='.$season;
                            
								            if($league_files[$i][9] != NULL && file_exists(LOGO_PATH.$league_files[$i][9])) $league_logo = LOGO_URL.$league_files[$i][9];
								            else $league_logo = LOGO_URL.'flm.png';
							                $img_alt = strlen($league_files[$i][2]) > 18 ? substr($league_files[$i][2],0,16)."..." : $league_files[$i][2];
			                        
									        $league_tables = GetTable($league_name);
                                            usort($league_tables,"sort_table");
											
									        echo '<tr class="competition-row">';
								                echo '<td class="hover-enabled competition-image"><img src="'.$league_logo.'" alt="'.$img_alt.'" /></td>';
                                                echo '<td class="competition-name">'.$league_files[$i][2].'</td>';
								                echo '<td class="table-view-link"><a href="index.php?mode=leagues&amp;section=leagueTable&amp;league='.$league_files[$i][3].'&amp;season='.SEASON.'">Tables</a></td>';
                                                echo '<td class="table-view-link"><a href="index.php?mode=leagues&amp;section=fixtures&amp;league='.$league_files[$i][3].'&amp;season='.SEASON.'">Fixtures</a></td>';
                                                echo '<td class="table-view-link"><a href="index.php?mode=leagues&amp;section=results&amp;league='.$league_files[$i][3].'&amp;season='.SEASON.'">Results</a></td>';
						                    echo '</tr>';
										    if($roleID >= 4){
							                    echo '<tr class="row-spacer"><td colspan="5">';
									                echo '<a href="'.$league_add_url.'">Add Season</a> | <a href="'.$league_delete_url.'">Delete</a> | <a href="'.$league_edit_url.'">Edit</a>';
									             echo '&nbsp;</td></tr>';
										    }
									        echo '<tr><td colspan="5">';
		                                    
										    	echo '<ul class="profile-team-list">';
			                                        foreach($league_tables as $key => $table){
													    $x = ($key + 1);
						                                $logo_tmp_name = str_replace(array(' ','&nbsp;','-'),'_',$table[2]);
						                                $logo_png = $league_name.'/'.SEASON.'/icons/'.$logo_tmp_name.'.png';
						                                $logo_gif = $league_name.'/'.SEASON.'/icons/'.$logo_tmp_name.'.gif';
						                                $logo_jpg = $league_name.'/'.SEASON.'/icons/'.$logo_tmp_name.'.jpg';
							
							                            if(file_exists(LEAGUE_PATH.$logo_png)) $team_logo = LEAGUE_URL.$logo_png;
							                            elseif(file_exists(LEAGUE_PATH.$logo_gif)) $team_logo = LEAGUE_URL.$logo_gif;
							                            elseif(file_exists(LEAGUE_PATH.$logo_jpg)) $team_logo = LEAGUE_URL.$logo_jpg;
							                            else $team_logo = IMG_URL.'football_classic1.png';
					         
				                                        echo '<li class="">'; 
					                                        echo '<div class="pid">'.$x.'</div>
							                                <div class="pimg"><img src="'.$team_logo.'" alt="'.$table[2].' logo" /></div>
				        	                                <div class="pname"><a title="view '.$table[2].' profile" href="'.SELF.'?mode=profiles&amp;section=teamprofile&amp;league='.$league_name.'&amp;season='.$season.'&amp;team='.$table[2].'">'.ucwords($table[2]).'</a></div>';
					                                    echo '</li>';
			                                        }
											    echo '</ul>';
	                                       
									        echo '</td></tr>';
						                } /*else {
	                                            echo '<tr><td colspan="5"><div class="message user_status"><p>'._MISSING_LEAGUE_TABLE_FILE.'</p><p><a href="'.$create_url.'">Create a new league</a></p></div></td></tr>'."\n";
		                                }*/
									} else {
							            if( isset($league_files[$i][3]) ) echo $league_files[$i][3];
							        }
						        }
						        echo '</tbody>';
								echo '<tfoot>';
						            echo '<tr class="last-row foot pad-5">';
						                echo '<td colspan="2"><span>There are currently: <strong>'.$total_leagues.'</strong> leagues registered</span></td>';
						                echo'<td colspan="3"><span align="right" class="paginate-wrapper right-1 grid-45"> | '.$UTIL->print_pagination($numPages,$urlVars,$currentPage).'</span></td>';
		                            echo '</tr>';
					            echo '</tfoot>';
						        echo '</table>';
	                        }
						} else {
						    $league_file_name = LEAGUE.'/'.SEASON.'_league_table.txt';
                            $league_path = LEAGUE_PATH.$league_file_name;	
						    if(file_exists($league_path)){
								$league_name = LEAGUE;
								$league_tables = GetTable($league_name);
                                usort($league_tables,"sort_table");
								if( count($league_tables) == 0 ) echo '<div class="message user_status"><p>There are currently no leagues registered.</p></div>';
                                else {
				                    $numPages = ceil( count($league_tables) / 1 );
                                    if(isset($_GET['p'])) {
	                                    $currentPage = $_GET['p'];
                                        if($currentPage > $numPages) $currentPage = $numPages;
                                    } else $currentPage=1;
        
                                    $start = ( $currentPage * 1 ) - 1;
	                            
			                        foreach($league_tables as $table){
									    $prevNext_link_file_titles[$x] = $table[2];
										$tables[$x] = $table[2];
										if ($file_name == $table[2]) $ci = $x;
					
						                    $logo_tmp_name = str_replace(array(' ','&nbsp;','-'),'_',$table[2]);
						                    $logo_png = $league_name.'/'.SEASON.'/icons/'.$logo_tmp_name.'.png';
						                    $logo_gif = $league_name.'/'.SEASON.'/icons/'.$logo_tmp_name.'.gif';
						                    $logo_jpg = $league_name.'/'.SEASON.'/icons/'.$logo_tmp_name.'.jpg';
							
							                if(file_exists(LEAGUE_PATH.$logo_png)) $team_logo = LEAGUE_URL.$logo_png;
							                elseif(file_exists(LEAGUE_PATH.$logo_gif)) $team_logo = LEAGUE_URL.$logo_gif;
							                elseif(file_exists(LEAGUE_PATH.$logo_jpg)) $team_logo = LEAGUE_URL.$logo_jpg;
							                else $team_logo = IMG_URL.'football_classic1.png';
					         
							                if($GLOBALS['t_sortby'] == 'pts'){
							                    if($x>0 && $x<5) $row_class = 'champions-league-row';
							                    else if($x>4 && $x<8) $row_class = 'europa-league-row';
							                    else if($x>17) $row_class= 'relegation-row';
							                    else $row_class = 'regular-row';
							                } else $row_class = 'regular-row';
							   
								            if($_GET['team'] == $table[2]){
										        $profile_filename = getTeamProfile($table[2]);
									        	echo '<div class="panel grid-4 " >';
										            echo '<h3 style="text-algn:center;">'.((strlen($table[2]) <=3) ? strtoupper($table[2]) : ucwords($table[2])).' Team Profile</h3>';
										            echo '<p>'.$profile_filename[1].'</p>';
										            echo '<div class="spacer">&nbsp;</div>';
										            echo '<h3>Current team position and statistics</h3>';
											
								            echo '<table class="full-table grid-full">';
			                                    echo '<thead><tr class="table-header"><th scope="col">Position</th><th scope="col">&nbsp;</th><th>Team</th><th scope="col">Match Played</th><th scope="col">Wins</th><th scope="col">Loss</th><th scope="col">Draws</th><th scope="col">Goals Scored</th><th scope="col">Goals Against</th><th scope="col">Goal Difference</th><th scope="col">Points</th></tr></thead>';
					                            echo '<tbody>';
									                echo '<tr class="'.$row_class.'">';
					                                    echo '<td class="position" align="center">'.$x.'</td>
							                            <td class="logo"><img style="width:35px;" src="'.$team_logo.'" alt="'.$table[2].' logo" /></td>
				        	                            <td title="'.$table[2].'" class="team-name"><a href="'.SELF.'?mode=profiles&amp;section=teamprofile&amp;league='.$league_name.'&amp;team='.$table[2].'">'.ucwords($table[2]).'</a></td>
					                                    <td class="played">'.$table[3].'</td>
				        	                            <td class="wins">'.$table[4].'</td>
					                                    <td class="loss">'.$table[5].'</td>
					                                    <td class="draws">'.$table[6].'</td>
				        	                            <td class="for">'.$table[7].'</td>
					                                    <td class="against">'.$table[8].'</td>
                                                        <td class="difference">'.$table[9].'</td>
                                                        <td class="points">'.$table[10].'</td>';
				                                    echo '</tr>';
											    echo '</tbody>';
				                            echo '</table>';/**/
										echo '</div>';
									        }
									        $x++;
			                            //} else {
							             //   if( isset($league_tables[$i][0]) ) echo $league_tables[$i][0];
							            //}
						            }
									if(count($tables) > 0){
	                                    $ti = $x - 1; 
	                                    $pi = $ci - 1;
	                                    if ($file_name == "") $ni = $ci + 2;
	                                    else $ni = $ci + 1;
	                                    if ($file_name == "") $file_name = $tables[0];
				 
	                                    if ($pi > 0) { 
		                                    $prev_file_title = $prevNext_link_file_titles[$pi];
		                                    $piFile = urlencode($tables[$pi]);
		                                    $prevNext_link .= "<span class=\"three-nav nav-prev\"><a href=\"" . $url_prefix . "&amp;team=" . $piFile . "#__Top\" title=\"show the previous profile : $prev_file_title\">&larr;&nbsp;$prev_file_title</a></span>";
	                                    } else $prevNext_link .= '<span class="three-nav nav-prev">&nbsp;</span>';
		 
		                                $prevNext_link .= '<span class="three-nav nav-center"><a href="'.$url_prefix.'" title="click to return to all team profiles">Return</a></span>';
	    
		                                if ($ni <= $ti) {
		                                    $next_file_title = $prevNext_link_file_titles[$ni];
		                                    $niFile = urlencode($tables[$ni]);
		                                    $prevNext_link .= "<span class=\"three-nav nav-next\"><a href=\"" . $url_prefix . "&amp;team=" . $niFile . "#__Top\" title=\"show the next profile : $next_file_title\">$next_file_title&nbsp;&rarr;</a></span>";
	                                    } else $prevNext_link .= '<span class="three-nav nav-next">&nbsp;</span>';
	                                } else $prevNext_link .= '<p>No profile found</p>';
	                                echo'<div class="spacer"></div>';
			                        echo'<div class="post-nav grid-full">'.$prevNext_link.'</div>';
				                    echo'<div class="clear"></div>';
								    //echo '<span align="right" class="paginate-wrapper right-1 grid-4">'.$UTIL->print_pagination($numPages,$urlVars,$currentPage).'</span>';
						        }
						    }
	                    } 
		            } else echo '<div class="message user_status"><p>'._MISSING_LEAGUE_DB_FILE.'</p><p><a href="'.$create_url.'">Create a new league</a></p></div>'."\n";
				break;
	        }
	    echo'<div class="clear">&nbsp;</div>';
	    echo'</div>';
	echo'</div>';
?>