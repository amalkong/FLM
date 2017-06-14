<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    $PB_output = NULL;
	$f_sortby = isset($_GET['f_sortby']) ? $_GET['f_sortby'] : 'title';
	$f_ascdsc = isset($_GET['f_ascdsc']) ? $_GET['f_ascdsc'] : "DESC";
	$GLOBALS['f_sortby'] = $f_sortby;
    $GLOBALS['f_ascdsc'] = $f_ascdsc;
	$CFG->config['itemsPerPage'] = 2;
	$page_sortby_array = array("filename"=>"File Name", "author"=>"File Author", "title"=>"Title","moddate"=>"Modified Date","createdate"=>"Date Created","count"=>"Length");	
	$url_prefix = SELF."?mode=".MODE."&amp;section=".SECTION."&amp;page=".PAGE;
	
		    $PB_output .= '<div class="header"><h2>'.$CFG->config['site_title_full'].' '.cleanPageTitles(PAGE).'</h2>';
		        $PB_output .='<span class="title panel grid-4"><a href="#" onclick="return kadabra(\'sort_table\');" title="toggle table select menu">click to show page sorting panel</a></span><br/>';
		        $PB_output .= '<center><div id="sort_table" class="grid-4" style="display:none;"><form name="f" action="" method="GET">';
                    $PB_output .= '<input name="mode" type="hidden" value="'.MODE.'"><input name="section" type="hidden" value="'.SECTION.'"><input name="page" type="hidden" value="'.PAGE.'">';
			        $PB_output .= '<table border="0" cellpadding="0"><tr>';
					    $PB_output .= '<td colspan="" align="center">sort by&nbsp;<select name="f_sortby">
                            <option value="-1">-- sort by --</option>';
							foreach($page_sortby_array as $skey =>$sort) $PB_output .= '<option value="'.$skey.'" '.(($skey==$GLOBALS['f_sortby']) ? 'selected' : '').'>'.$sort.'</option>';
							$PB_output .= '</select></td>';
					    $PB_output .= '<td align="center">ascending&nbsp;<input name="f_ascdsc" value="ASC" type="radio" '.(($f_ascdsc=="ASC") ? 'CHECKED' : '').'></td>';
                        $PB_output .= '<td align="center">descending&nbsp;<input name="f_ascdsc" value="DESC" type="radio" '.(($f_ascdsc=="DESC") ? 'CHECKED' : '').'></td>';
				        $PB_output .= '<td align="center"><input type="submit" class="update" value="" title="sort files"></td>';
                    $PB_output .= '</tr></table>';
                $PB_output .= '</form></div></center>';
        $PB_output .= '</div>';
		$PB_output .= '<div class="clear">&nbsp;</div>';
    switch( ACTION ){
        case 'action_index':
            $PB_output .= '<div id="__Top" class="box2">'."\n";
				$PB_output .= $PAGE->showPageContent(PAGE_PATH,'page');
			$PB_output .= '</div>'."\n";
	    break;
		case 'page_list':
		    $page_files = array();
			$PB_output .='<div class="grid-4 box2">';
			    if(is_array($allPages) && count($allPages) > 0) {
			        foreach($allPages as $page_row){
					    $PAGE->GetFile(PAGE_PATH.$page_row);
						$tmp = array();
					    $tmp[0] = $page_row;
						$tmp[2] = $PAGE->file_metadata['title'];
						$tmp[3] = $PAGE->file_metadata['author'];
						$tmp[4] = $PAGE->file_stats['createDate'];
						$tmp[5] = date('d-M-Y h:i:s A',filemtime(PAGE_PATH.$page_row));
						$tmp[7] = $UTIL->size('file',PAGE_PATH.$page_row);
					    
					    if(count($PAGE->file_sections >= 1)){
			                for($i=0;$i<(count($PAGE->file_sections));$i++) $tmp[8] = $PAGE->file_sections[$i]['content'];
		                } else $tmp[8] = $PAGE->file_sections[0]['content'];
						
						array_push($page_files,$tmp);
				    }
					usort($page_files,"sort_file");
				}
				
				if(count($page_files) > 0){
				    $numPages = ceil( count($page_files) / $CFG->config['itemsPerPage'] );
                    if(isset($_GET['p'])) {
	                    $currentPage = $_GET['p'];
                        if($currentPage > $numPages) $currentPage = $numPages;
                    } else $currentPage=1;
 
                    $start = ( $currentPage * $CFG->config['itemsPerPage'] ) - $CFG->config['itemsPerPage'];
			
                    for( $i=$start; $i<$start + $CFG->config['itemsPerPage']; $i++ ) {
					    if( isset($page_files[$i][0]) ) {
						    $page_name = $page_files[$i][0];
						    $page_title = $page_files[$i][2];
						    $page_author = $page_files[$i][3];
						    $page_date = $page_files[$i][4];
							$date_text = 'Page created by '.ucfirst($page_author).' on '.$page_date;
							$page_content = Truncate($page_files[$i][8],60);
							
				            $page_view_url = SELF.'?mode=home&amp;section=pages&amp;page='.$page_name;
			                $page_edit_url = SELF.'?mode=admin&amp;section=manage&amp;action=edit&amp;filetype=page&amp;page='.$page_name;
			                $page_delete_url = SELF.'?mode=admin&amp;section=manage&amp;action=delete&amp;filetype=page&amp;page='.$page_name;
					
					        $PB_output .= '<div class="panel grid-4">';
								$PB_output .= $PAGE->show_page_box($page_name,$page_title,$date_text,$page_content);
	                            //unset($page); 
						        if($roleID > 6){
						            $PB_output .= '<div class="wrapper left-">';
						                $PB_output .= '<ul class="UL-list">';
					                        $PB_output .= '<li>'.$page_name.'</li>';
		    	                            $PB_output .= '<li>File Size'.$separator.'<span class="btn-highlight"><b>'.$page_files[$i][7].'</b></span></li>';
		     	                            $PB_output .= '<li>Last Modified'.$separator.'<span class="btn-highlight"><b>'.$page_files[$i][5].'</b></span></li>';
		    	                            $PB_output .= '<li>Chars. Count'.$separator.'<span class="btn-highlight"><b>'.$page_files[$i][8].'</b> characters</span></li>';
		    	                            $PB_output .= '<li><a href="'.$page_edit_url.'"><span class="btn-highlight"><b>Edit</b></span></a> - <a href="'.$page_delete_url.'"><span class="btn-highlight"><b>Delete</b></span></a></li>';
					                    $PB_output .= '</ul>';
			                        $PB_output .= '</div>';
								}
			                $PB_output .= '</div>';
			            }
					}
					$PB_output .= '<div class="clear"></div>';
                   
				    $PB_output .= '<div class="foot pad-5">';
						$PB_output .= '<div align="center" class="paginate-wrapper right-1 grid-4">';
                            $urlVars = SELF."?mode=".MODE."&amp;section=".SECTION."&amp;action=".ACTION."&amp;f_sortby=$f_sortby&amp;f_ascdsc=$f_ascdsc";
                            $PB_output .= $UTIL->print_pagination($numPages,$urlVars,$currentPage);
                        $PB_output .= '</div>';
					    $PB_output .= '<div class="clear">&nbsp;</div>';
					$PB_output .= '</div>';
				} else $PB_output .= '<div class="message user_status"><p><b>No page File Found</b> ...</p></div>'."\n";
                
			$PB_output .= '</div>';
		break;
    }
	echo $PB_output;
?>