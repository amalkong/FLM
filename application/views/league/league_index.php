<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    if(!isset($PB_output)) $PB_output = NULL;
	$f_sortby = isset($_GET['f_sortby']) ? $_GET['f_sortby'] : 'title';
	$f_ascdsc = isset($_GET['f_ascdsc']) ? $_GET['f_ascdsc'] : "DESC";
	$GLOBALS['f_sortby'] = $f_sortby;
    $GLOBALS['f_ascdsc'] = $f_ascdsc;
	
    $league_files = array();
	$league_sortby_array = array("filename"=>"Name", "id"=>"Id", "title"=>"Title");	
	$url_prefix = SELF.'?mode='.MODE.'&amp;section='.SECTION;
	$urlVars = $url_prefix."&amp;f_sortby=$f_sortby&amp;f_ascdsc=$f_ascdsc";
	
	$PB_output .= '<div class="content">'; 
	    $PB_output .= '<div class="box">'; 
            $PB_output .= '<div class="header"><h2>All Registered leagues of '.$CFG->config['site_title_full'].'</h2></div>';
		    
		    if(file_exists($League_DB_File)){
				$league_files = GetLeagues();
		        usort($league_files,'sort_file');
	                
			    if( count($league_files) == 0 ) $PB_output .= '<div class="message user_status"><p>There are currently no leagues registered.</p></div>';
                else {
			        $total_leagues = count($league_files);
				    $numPages = ceil( count($league_files) / $CFG->config['itemsPerPage'] );
                    if(isset($_GET['p'])) {
	                    $currentPage = $_GET['p'];
                        if($currentPage > $numPages) $currentPage = $numPages;
                    } else $currentPage=1;
        
                    $start = ( $currentPage * $CFG->config['itemsPerPage'] ) - $CFG->config['itemsPerPage'];
	               
	                $PB_output .='<div class="link-bar"><div id="sortby-top" class="right-1 grid-" valign="bottom"><span class="left-2">Sort by: </span><ul class="sort-link right-1">';
						foreach($league_sortby_array as $cat=>$v){
		                    $PB_output .= '<li>
							    <div class="i left-1">&nbsp;|&nbsp;'.(($f_sortby == $cat) ? '<strong>'.$v.'</strong>' : $v).'</div>
								<div class="right-1" style="width:6px;margin-right:5px;">
								    <a class="icon asc" href="'.$url_prefix.'&amp;f_sortby='.$cat.'&amp;f_ascdsc=ASC" title="sort files by '.$cat.' ascending"></a>
									<a class="icon desc" href="'.$url_prefix.'&amp;f_sortby='.$cat.'&amp;f_ascdsc=DESC" title="sort files by '.$cat.' descending"></a>
								</div>
							</li>';
	                    }
					$PB_output .='</ul></div><div class="clear">&nbsp;</div></div>';
					
				    $PB_output .= '<table class="table leagues grid-full"><caption>All Leagues</caption>';
				        $PB_output .= '<thead><tr class="row-spacer"><th>Logo</th><th>League Name</th><th>Tables</th><th>Fixtures</th><th>Results</th></tr></thead>';
                    
				    	$PB_output .= '<tbody>';
	                        for( $i=$start; $i<$start + $CFG->config['itemsPerPage']; $i++ ) {
					            if( isset($league_files[$i][3]) ) {
								    $league_title = $league_files[$i][2];
								    $league_name = $league_files[$i][3];
								    $league_image = $league_files[$i][9];
		                            $league_add_url = SELF.'?mode=admin&amp;section=addseason&amp;league='.$league_files[$i][3];
		                            $league_delete_url = SELF.'?mode=admin&amp;section=manage&amp;action=delete&amp;filetype=league&amp;league='.$league_files[$i][3];
                                    $league_edit_url = SELF.'?mode=admin&amp;section=manage&amp;action=edit&amp;filetype=league&amp;league='.$league_files[$i][3];
                                    $season_path = $UTIL->Check_For_Slash(LEAGUE_PATH.$league_name,true);
									$league_seasons = GetDirContents($season_path,'dirs');
									
								    if($league_image != NULL && file_exists(LOGO_PATH.$league_image)) $league_logo = LOGO_URL.$league_image;
								    else $league_logo = LOGO_URL.'flm.png';
							        $img_alt = strlen($league_title) > 18 ? substr($league_title,0,16)."..." : $league_title;
			                        
									$PB_output .= '<tr class="league-row">';
									    $PB_output .= '<td class="league-image"><img src="'.$league_logo.'" alt="'.$img_alt.'" /></td>';
                                        $PB_output .= '<td class="league-name">'.$league_title.'</td>';
										if(count($league_seasons) > 1){ 
										    $PB_output .= '<td colspan="3">';
											foreach($league_seasons as $season_rows){
   											    $PB_output .= '<table class="inner-table"><tr>';
										        $PB_output .= '<td class="table-view-link"><a href="'.SELF.'?mode=leagues&amp;section=leagueTable&amp;league='.$league_name.'&amp;season='.$season_rows.'" title="view the league table for '.$league_name.', season'.$separator.$season_rows.'" >'.$season_rows.' tbl</a></td>';
										        $PB_output .= '<td class="table-view-link"><a href="'.SELF.'?mode=leagues&amp;section=fixtures&amp;league='.$league_name.'&amp;season='.$season_rows.'" title="view the match fixtures for '.$league_name.', season'.$separator.$season_rows.'">'.$season_rows.' fxt</a></td>';
										        $PB_output .= '<td class="table-view-link"><a href="'.SELF.'?mode=leagues&amp;section=results&amp;league='.$league_name.'&amp;season='.$season_rows.'" title="view the match results for '.$league_name.', season'.$separator.$season_rows.'">'.$season_rows.' rlt</a></td>';
											    $PB_output .= '</tr></table>';
											}
											$PB_output .= '</td>';
				                        } else {
								            $PB_output .= '<td class="table-view-link"><a href="'.SELF.'?mode=leagues&amp;section=leagueTable&amp;league='.$league_name.'&amp;season='.((count($league_seasons) > 1 ) ? end($league_seasons) : $league_seasons[0]).'">Tables</a></td>';
                                            $PB_output .= '<td class="table-view-link"><a href="'.SELF.'?mode=leagues&amp;section=fixtures&amp;league='.$league_name.'&amp;season='.((count($league_seasons) > 1 ) ? end($league_seasons) : $league_seasons[0]).'">Fixtures</a></td>';
                                            $PB_output .= '<td class="table-view-link"><a href="'.SELF.'?mode=leagues&amp;section=results&amp;league='.$league_name.'&amp;season='.((count($league_seasons) > 1 ) ? end($league_seasons) : $league_seasons[0]).'">Results</a></td>';
                                        }
						            $PB_output .= '</tr>';
							        $PB_output .= '<tr class="row-spacer"><td colspan="5">';
									    if($roleID >= 4) $PB_output .= '<a href="'.$league_add_url.'">Add Season</a> | <a href="'.$league_delete_url.'">Delete</a> | <a href="'.$league_edit_url.'">Edit</a>';
									$PB_output .= '&nbsp;</td></tr>';
						        } else {
							        if( isset($league_files[$i][3]) ) $PB_output .= $league_files[$i][3];
							    }
						    }
						$PB_output .= '</tbody>';
					    $PB_output .= '<tfoot>';
						    $PB_output .= '<tr class="last-row foot pad-5">';
						        $PB_output .= '<td colspan="3"><span align="left" class="left-1">There are currently: <strong>'.$total_leagues.'</strong> leagues registered, totalling '.(($CFG->config['display_dirsize'] == 'YES') ? $UTIL->size('dir',LEAGUE_PATH) : '???').' in size, including the (fixtures,results &amp; icons) folders.</span></td>';
						        $PB_output .='<td colspan="2"><span align="right" class="paginate-wrapper right-1 grid-4"> | '.$UTIL->print_pagination($numPages,$urlVars,$currentPage).'</span></td>';
		                    $PB_output .= '</tr>';
					    $PB_output .= '</tfoot>';
			        $PB_output .= '</table>';
			    }
	        } else {
	            $PB_output .= '<div class="message user_status"><p>'._MISSING_LEAGUE_DB_FILE.'</p></div>';
	            if($roleID >= 4) $PB_output .= '<div class="message unspecific"><p><a href="'.SELF.'?mode=admin&amp;section=systeminfo">Ensure that the <span class="b i">leagues DB</span> file exists</a> or <a href="'.SELF.'?mode=leagues&amp;section=fixtures">Return to leagues</a> or <a href="'.SELF.'?mode=admin&amp;section=addleague">Create a new league</a></p></div>';
	        }
        $PB_output .= '</div>';
    $PB_output .= '</div>';
	echo $PB_output;
?>