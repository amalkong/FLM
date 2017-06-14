<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    if ((isset($_GET['filetype'])) AND $_GET['filetype'] == 'profile')  {
	    if(!isset($PB_output))$PB_output = NULL;
	} else {
        if (($roleID < 4))  {
            echo'<div class="box"><div class="message error"><p>Access Denied!,You are not authorised to access this section, your user role does not permit you to manage all files, you have to be an <strong>admin</strong> or <strong>super admin</strong>.</p></div></div>';
	        include(BASE_PATH.'footer.php');
	        exit;
        }
		if(!isset($PB_output))$PB_output = NULL;
    }
    $PAGE =& load_class('Page','library');

	$f_sortby = ( isset($_GET['f_sortby']) ? GetCommand("f_sortby") : "title" );
	$f_ascdsc = ( isset($_GET['f_ascdsc']) ? GetCommand("f_ascdsc") : "DESC" );
	$GLOBALS['f_sortby'] = $f_sortby;
    $GLOBALS['f_ascdsc'] = $f_ascdsc;
	
	$sortby = ( isset($_GET['sortby']) ? GetCommand("sortby") : "title" );
	$ascdsc = ( isset($_GET['ascdsc']) ? GetCommand("ascdsc") : "DESC" );
	$GLOBALS['sortby'] = $sortby;
    $GLOBALS['ascdsc'] = $ascdsc;
	
	$lg_sortby = ( isset($_GET['lg_sortby']) ? GetCommand("lg_sortby") : "name" );
	$album = ( isset($_GET['album']) ? GetCommand("album") : "" );
	$i = $ccount = 0;
	
	$url_prefix = SELF.'?mode='.MODE.'&amp;section='.SECTION;
	$return_url = $url_prefix.'&amp;action='.ACTION;
	
	$article_content_list = getArticles(100,$f_sortby,$f_ascdsc);
	$total_articles = count($article_content_list);
	
	$page_content_list = GetDirContents(PAGE_PATH,'files');
	$total_pages = count($page_content_list);
	
	$league_content_list = GetDirContents(LEAGUE_PATH,'dirs');
	$total_leagues = count($league_content_list);
	
	$gallery_content_list = GetDirContents(GALLERY_PATH,'dirs');
	$total_galleries = count($gallery_content_list);
	
	if(file_exists($Category_DB_File)){
		$open = @fopen($Category_DB_File, 'r');
		$cat_array = explode("\n", fread($open, filesize($Category_DB_File)));
		$total_cat = count($cat_array);
		fclose($open);
	} else $total_cat = 0;
	
    $article_sortby_array = array("filename"=>"File Name", "author"=>"Article Author","email"=>"Author Email", "title"=>"Article Title","date"=>"Publish Date","category"=>"Article Category");	
    $page_sortby_array = array("filename"=>"File Name", "author"=>"File Author", "size"=>"File Size", "title"=>"Title","moddate"=>"Modified Date","createdate"=>"Date Created","count"=>"Char. Count");	
	$image_sortby_array = array("title"=>"Name", "size"=>"Size","date"=>"Date");
    $league_sortby_array = array("name"=>"Name", "id"=>"Id", "title"=>"Title");	
	                    
	$PB_output .= '<div class="content">';
	    $PB_output .='<div class="link-container-top"><center>
		    <a href="'.$url_prefix.'" class="icon icon_view_list" title="return to File Management Index">&nbsp;</a> | 
			<a href="'.$url_prefix.'&amp;action=articles">Articles</a> | 
			<a href="'.$url_prefix.'&amp;action=pages">Pages</a> | 
			<a href="'.$url_prefix.'&amp;action=leagues">Leagues</a> | 
			<a href="'.$url_prefix.'&amp;action=images">Images</a> | 
			<a href="'.$url_prefix.'&amp;action=galleries">Galleries</a> | 
			<a href="'.$url_prefix.'&amp;action=categories">Categories</a>';
			if(ACTION == 'edit' || ACTION == 'delete') $PB_output .=' | <a href="'.$_SERVER['REQUEST_URI'].'">'.ACTION.'</a>';
			if(ACTION == 'browser') $PB_output .=' | <a href="'.$_SERVER['REQUEST_URI'].'">'.ACTION.'</a>';
		$PB_output .='</center></div>';
	    $PB_output .= '<div class="box">';
		    $PB_output .= '<div class="header"><h2>'.$CFG->config['site_title_full'].' Management Page</h2>';
		    if(ACTION == 'articles' || ACTION == 'pages' || ACTION == 'images' ){
		        $PB_output .='<span class="title panel grid-4"><a href="#" onclick="return kadabra(\'sort_table\');" title="toggle table select menu">click to show file sorting panel</a></span><br/>';
		        $PB_output .= '<center><div id="sort_table" class="grid-4" style="display:none;"><form name="f" action="" method="GET">';
                    $PB_output .= '<input name="mode" type="hidden" value="'.MODE.'"><input name="section" type="hidden" value="'.SECTION.'"><input name="action" type="hidden" value="'.ACTION.'">';
			        $PB_output .= '<table border="0" cellpadding="0"><tr>';
					    $PB_output .= '<td colspan="" align="center">sort by&nbsp;<select name="f_sortby">
                            <option value="-1">-- sort by --</option>';
							if(ACTION == 'articles') foreach($article_sortby_array as $skey =>$sort) $PB_output .= '<option value="'.$skey.'" '.(($skey==$GLOBALS['f_sortby']) ? 'selected' : '').'>'.$sort.'</option>';
							elseif(ACTION == 'pages') foreach($page_sortby_array as $skey =>$sort) $PB_output .= '<option value="'.$skey.'" '.(($skey==$GLOBALS['f_sortby']) ? 'selected' : '').'>'.$sort.'</option>';
							elseif(ACTION == 'images') foreach($image_sortby_array as $skey =>$sort) $PB_output .= '<option value="'.$skey.'" '.(($skey==$GLOBALS['f_sortby']) ? 'selected' : '').'>'.$sort.'</option>';
						$PB_output .= '</select></td>';
					    $PB_output .= '<td align="center">ascending&nbsp;<input name="f_ascdsc" value="ASC" type="radio" '.(($f_ascdsc=="ASC") ? 'CHECKED' : '').'></td>';
                        $PB_output .= '<td align="center">descending&nbsp;<input name="f_ascdsc" value="DESC" type="radio" '.(($f_ascdsc=="DESC") ? 'CHECKED' : '').'></td>';
				        $PB_output .= '<td align="center"><input type="submit" class="update" value="" title="sort files"></td>';
                    $PB_output .= '</tr></table>';
                $PB_output .= '</form></div></center>';
		    }
        $PB_output .= '</div>';
		switch ( ACTION ) {
		    case 'action_index':
			    $PB_output .= '<div class="box2">';
			        $PB_output .= '<div class="left-1 grid-2">';
					    $PB_output .= '<p class="title">In this section, manage all news articles, pages, leagues, categories and images.</p>';
			            $PB_output .= '<ul>';
						    $PB_output .= '<li>A total of <span class="i b">'.$total_articles.' News Articles</span> were found.</li>';
			                $PB_output .= '<li>A total of <span class="i b">'.$total_pages.' Pages</span> were found.</li>';
			                $PB_output .= '<li>A total of <span class="i b">'.$total_leagues.' Leagues</span> were found.</li>';
			                $PB_output .= '<li>A total of <span class="i b">'.$total_cat.' Categories</span> were found.</li>';
			                $PB_output .= '<li>A total of <span class="i b">'.$total_galleries.' Galleries</span> were found.</li>';
					    $PB_output .= '</ul>';
					$PB_output .= '</div>';
					$PB_output .= '<ul class="UL-list grid-2 right-1">';
			            $PB_output .= '<li><a class="block right-1" href="'.$url_prefix.'&amp;action=articles">Manage Articles<div class="icon icon_article right-1">&nbsp;</div></a></li>';
			            $PB_output .= '<li><a class="block right-1" href="'.$url_prefix.'&amp;action=pages">Manage Pages<div class="icon icon_page right-1">&nbsp;</div></a></li>';
			            $PB_output .= '<li><a class="block right-1" href="'.$url_prefix.'&amp;action=categories">Manage Categories<div class="icon icon_category right-1">&nbsp;</div></a></li>';
			            $PB_output .= '<li><a class="block right-1" href="'.$url_prefix.'&amp;action=leagues">Manage Leagues<div class="icon icon_league right-1">&nbsp;</div></a></li>';
			            $PB_output .= '<li><a class="block right-1" href="'.$url_prefix.'&amp;action=images">Manage Images<div class="icon icon_image right-1">&nbsp;</div></a></li>';
			            $PB_output .= '<li><a class="block right-1" href="'.$url_prefix.'&amp;action=galleries">Manage Galleries<div class="icon icon_gallery right-1">&nbsp;</div></a></li>';
			            //$PB_output .= '<li class="left-1"><a href="'.$url_prefix.'&amp;action=">Manage </a><div class="icon icon_ right-1">&nbsp;</div></li>';
			        $PB_output .= '</ul>';
			        $PB_output .= '<div class="clear">&nbsp;</div>';
			    $PB_output .= '</div>';
		    break;
			
		    case 'articles':
	            $PB_output .= '<div class="panel wrapper head"><p class="title">A total of '.$total_articles.' News Articles were found.</p></div>';
	            usort($article_content_list,"sort_article");
			    if(count($article_content_list) > 0){
			        $numPages = ceil( count($article_content_list) / $CFG->config['itemsPerPage'] );
                    if(isset($_GET['p'])) {
	                    $currentPage = $_GET['p'];
                        if($currentPage > $numPages) $currentPage = $numPages;
                    } else $currentPage=1;
 
                    $start = ( $currentPage * $CFG->config['itemsPerPage'] ) - $CFG->config['itemsPerPage'];
			
                    for( $i=$start; $i<$start + $CFG->config['itemsPerPage']; $i++ ) {
			            if( isset($article_content_list[$i][0]) ) {
		                    if(is_really_writable(ARTICLES_PATH.$article_content_list[$i][0])) $article_state = "<font color=green>Writable</font>";
		                    else $article_state = "<font color=red>Not Writable, <a href=\"http://www.perlservices.net/en/faq/cute_ftp.shtml\" target=\"_blank\">CHMOD \"$article_content_list[$i][0]\"!</a></font>";
		                    $article_dbsize = $UTIL->size('file',ARTICLES_PATH.$article_content_list[$i][0]);
		                    $article_mod_date = date('d-M-Y h:i:s A',filemtime(ARTICLES_PATH.$article_content_list[$i][0]));
		
		                    $article_filename = removeFileExt($article_content_list[$i][0]);
			                $article_title = $article_content_list[$i][2];
			                $article_author = $article_content_list[$i][3];
                            $article_date = $article_content_list[$i][5];
                            $article_moddate = $article_content_list[$i][6];
				            $article_image = $article_content_list[$i][9];
			                $recent_cat_image = $article_content_list[$i][10];
			                $article_summary = $article_content_list[$i][11];
			                $article_details = $article_content_list[$i][12];
                            
			                $article_ryear = substr($article_date, 0, 4);
                            $article_rmonth = substr($article_date, 5,-3);
                            $article_rday = substr($article_date, 8);
				            if(strlen($article_details) > 0) $article_char_count = strlen($article_details);
				            else $article_char_count = strlen($article_summary);
						
			                $article_view_url ='index.php?mode=viewArticle&amp;newsArticle='.$article_filename;
			                $article_edit_url ='index.php?mode=admin&amp;section=manage&amp;action=edit&amp;filetype=article&amp;newsArticle='.$article_filename;
			                $article_delete_url ='index.php?mode=admin&amp;section=manage&amp;action=delete&amp;filetype=article&amp;newsArticle='.$article_filename;
					        $img_alt = substr($article_title,0,10);
				            if(file_exists(DATA_IMAGE_PATH."news_pics/$article_image")) $news_thumb = DATA_IMAGE_URL.'news_pics/'.$article_image;
				            else $news_thumb = DATA_IMAGE_URL."topic/$recent_cat_image";
				    
				            if($i %2) $PB_output .= '<div class="panel right-1" style="width:46%;margin-right:auto;">';
		                    else $PB_output .= '<div class="panel left-1" style="width:46%;margin-right:5px;">';
						        $PB_output .= '<div class="wrapper meta left-">';
			  		                $PB_output .= '<div class="frame left-1" style="width:67px;height:67px;"><a href="'.$article_view_url.'"><img src="'.$news_thumb.'" alt="'.$img_alt.'" width="67px" height="67px" /></a></div>';
					                $PB_output .= '<div class="left-1 pull-3">
					                    <span class="title"><a href="'.$article_view_url.'">'.$article_title.'</a></span><br/>
					                    <span class="date">Posted by '.ucfirst($article_author).' on '.$article_rday.'-'.$article_rmonth.'-'.$article_ryear.'</span>
				                    </div>';
					         	$PB_output .= '</div>';
						        $PB_output .= '<div class="wrapper left-">';
				                    $PB_output .= '<ul class="UL-list">';
				                        $PB_output .= '<li>File State'.$separator.'<span class="btn-highlight"><b>'.$article_state.'</b></span></li>';
		    	                        $PB_output .= '<li>File Size'.$separator.'<span class="btn-highlight"><b>'.$article_dbsize.'</b></span></li>';
		     	                        $PB_output .= '<li>Last Modified'.$separator.'<span class="btn-highlight"><b>'.$article_mod_date.'</b></span></li>';
		    	                        $PB_output .= '<li>Chars. Count'.$separator.'<span class="btn-highlight"><b>'.$article_char_count.'</b> characters</span></li>';
		    	                        $PB_output .= '<li><a href="'.$article_edit_url.'"><span class="btn-highlight"><b>Edit</b></span></a> - <a href="'.$article_delete_url.'"><span class="btn-highlight"><b>Delete</b></span></a></li>';
	                                $PB_output .= '</ul>';
	                            $PB_output .= '</div>';
	                        $PB_output .= '</div>'."\n";
				        }
				    }
				    $PB_output .= '<div class="clear"></div>';
                   
				    $PB_output .= '<div class="foot pad-5">';
						$PB_output .= '<span class="left-1 grid-45"><p class="title">There are currently a total of: <strong>'.$total_articles.'</strong> articles found, totalling '.$UTIL->size('dir',ARTICLES_PATH).' in size.</p></span>';
						$PB_output .= '<div align="center" class="paginate-wrapper right-1 grid-45">';
                            $urlVars = $url_prefix."&amp;action=".ACTION."&amp;sortby=$sortby&amp;ascdsc=$ascdsc";
                            $PB_output .= $UTIL->print_pagination($numPages,$urlVars,$currentPage);
                        $PB_output .= '</div>';
					    $PB_output .= '<div class="clear">&nbsp;</div>';
					$PB_output .= '</div>';
	            } else {
	                $PB_output .= '<div class="message user_status"><p><b>No News Article File Found</b> ...</p></div>'."\n";
                }
		    break;
			
	        case 'pages':
			    $page_files = array();
			    $PB_output .= '<div class="clear">&nbsp;</div>';
			    $PB_output .= '<div class="head"><p class="title">A total of '.$total_pages.' Pages were found, add a new section, edit a section or delete the page.</p></div>';
			    if(is_array($page_content_list) && count($page_content_list) > 0){
			        foreach($page_content_list as $page_row){
					    if(is_really_writable(PAGE_PATH.$page_row)) $page_state[] = "<font color=green>Writable</font>";
		                else $page_state[] = "<font color=red>Not Writable, <a href=\"http://www.perlservices.net/en/faq/cute_ftp.shtml\" target=\"_blank\">CHMOD</a> \"site.dat\"!</font>";
		                
					    $PAGE->GetFile(PAGE_PATH.$page_row);
						$tmp = array();
					    $tmp[0] = $page_row;
						$tmp[2] = $PAGE->file_metadata['title'];
						$tmp[3] = $PAGE->file_metadata['author'];
						$tmp[4] = $PAGE->file_stats['createDate'];
						$tmp[5] = date('d-M-Y h:i:s A',filemtime(PAGE_PATH.$page_row));
						$tmp[7] = $UTIL->size('file',PAGE_PATH.$page_row);
					    
					    if(count($PAGE->file_sections <= 1)){
			                for($i=0;$i<(count($PAGE->file_sections));$i++){
						        $tmp[8] = strlen($PAGE->file_sections[$i]['content']);
				            }
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
				            $page_view_url ='index.php?mode=home&amp;section=pages&amp;page='.$page_files[$i][0];
			                $page_edit_url ='index.php?mode=admin&amp;section=manage&amp;action=edit&amp;filetype=page&amp;page='.$page_files[$i][0];
			                $page_delete_url ='index.php?mode=admin&amp;section=manage&amp;action=delete&amp;filetype=page&amp;page='.$page_files[$i][0];
					
					        if($i %2) $PB_output .= '<div class="panel right-1" style="width:46%;margin-right:auto;">';
		                    else $PB_output .= '<div class="panel left-1" style="width:46%;margin-right:5px;">';
					            $PB_output .= '<div class="wrapper meta left-">';
			  		                //$PB_output .= '<div class="frame left-1" style="width:67px;height:67px;"><a href="'.$article_view_url.'"><img src="'.$news_thumb.'" alt="'.$img_alt.'" width="67px" height="67px" /></a></div>';
					                $PB_output .= '<div class="left-1 pull-3">
					                    <span class="title"><a href="'.$page_view_url.'">'.$page_files[$i][2].'</a></span><br/>
					                    <span class="date">Page created by '.ucfirst($page_files[$i][3]).' on '.$page_files[$i][4].'</span>
				                    </div>';
						        $PB_output .= '</div>';
					         	$PB_output .= '<div class="wrapper left-">';
						            $PB_output .= '<ul class="UL-list">';
					                    $PB_output .= '<li>'.$page_files[$i][0].'</li>';
						                $PB_output .= '<li>File State'.$separator.'<span class="btn-highlight"><b>'.$page_state[$i].'</b></span></li>';
		    	                        $PB_output .= '<li>File Size'.$separator.'<span class="btn-highlight"><b>'.$page_files[$i][7].'</b></span></li>';
		     	                        $PB_output .= '<li>Last Modified'.$separator.'<span class="btn-highlight"><b>'.$page_files[$i][5].'</b></span></li>';
		    	                        $PB_output .= '<li>Chars. Count'.$separator.'<span class="btn-highlight"><b>'.$page_files[$i][8].'</b> characters</span></li>';
		    	                        $PB_output .= '<li><a href="'.$page_edit_url.'"><span class="btn-highlight"><b>Edit</b></span></a> - <a href="'.$page_delete_url.'"><span class="btn-highlight"><b>Delete</b></span></a></li>';
					                $PB_output .= '</ul>';
			                    $PB_output .= '</div>';
			                $PB_output .= '</div>';
			            }
					}
					$PB_output .= '<div class="clear"></div>';
                   
				    $PB_output .= '<div class="foot pad-5">';
						$PB_output .= '<span class="left-1 grid-45"><p class="title">There are currently a total of: <strong>'.$total_pages.'</strong> pages found, totalling '.$UTIL->size('dir',PAGE_PATH).' in size.</p></span>';
						$PB_output .= '<div align="center" class="paginate-wrapper right-1 grid-45">';
                            $urlVars = $url_prefix."&amp;action=".ACTION."&amp;f_sortby=$f_sortby&amp;f_ascdsc=$f_ascdsc";
                            $PB_output .= $UTIL->print_pagination($numPages,$urlVars,$currentPage);
                        $PB_output .= '</div>';
					    $PB_output .= '<div class="clear">&nbsp;</div>';
					$PB_output .= '</div>';
			    } else $PB_output .= '<div class="message user_status"><p><b>No page File Found</b> ...</p></div>'."\n";
			break;
			
		    case 'leagues':
				$league_files = array();
			    $PB_output .= '<div class="panel wrapper head"><p class="title">All Registered leagues of '.$PB_CONFIG['site_title_full'].'</p></div>';
	            if(file_exists($League_DB_File)){
                    $results = $JSON->decode(file_get_contents($League_DB_File));
		            if(is_array($results)) {
			            foreach($results as $league_list => $key ){
						    $tmp=array();
				            $tmp[0] = $results[$league_list];
				            $tmp[1] = $results[$league_list]->league_id;
							$tmp[2] = $results[$league_list]->league_title;
				            $tmp[3] = $results[$league_list]->league_name;
				            $tmp[9] = $results[$league_list]->league_image;
							array_push($league_files,$tmp);
			            }
		            } else {
			            $results = file($League_DB_File);
		                foreach($results as $league_list){
						    $tmp = array();
							$tmp[0] = $league_list;
		                    $league_list = $JSON->decode($league_list);
			                
			                $tmp[1] = $league_list->league_id;
							$tmp[2] = $league_list->league_title;
			                $tmp[3] = $league_list->league_name;
			                $tmp[9] = $league_list->league_image;
			                array_push($league_files,$tmp);
		                }
	                }
		            usort($league_files,'sort_article');
	                
			        if( count($league_files) == 0 ) $PB_output .= '<div class="message user_status"><p>There are currently no leagues registered.</p></div>';
                    else {
			         	$total_leagues = count($league_files);
				        $numPages = ceil( count($league_files) / $CFG->config['itemsPerPage'] );
                        if(isset($_GET['p'])) {
	                        $currentPage = $_GET['p'];
                            if($currentPage > $numPages) $currentPage = $numPages;
                        } else $currentPage=1;
        
                        $start = ( $currentPage * $CFG->config['itemsPerPage'] ) - $CFG->config['itemsPerPage'];
				       
	                    $PB_output .='<div class="link-bar"><div id="sortby-top" class="right-1 grid-"><span class="left-2">Sort by: </span><ul class="sort-link right-1">';
						foreach($league_sortby_array as $cat=>$v){
		                    $PB_output .= '<li>
							    <div class="i left-1">&nbsp;|&nbsp;'.(($lg_sortby == $cat) ? '<strong>'.$v.'</strong>' : $v).'</div>
								<div class="right-1" style="width:6px;margin-right:5px;">
								    <a class="icon asc" href="'.$url_prefix.'&amp;action='.ACTION.'&amp;f_sortby='.$cat.'&amp;f_ascdsc=ASC" title="sort files by '.$cat.' ascending"></a>
									<a class="icon desc" href="'.$url_prefix.'&amp;action='.ACTION.'&amp;f_sortby='.$cat.'&amp;f_ascdsc=DESC" title="sort files by '.$cat.' descending"></a>
								</div>
							</li>';
	                    }
						$PB_output .='</ul></div><div class="clear">&nbsp;</div></div>';
				
	                    for( $i=$start; $i<$start + $CFG->config['itemsPerPage']; $i++ ) {
					        if( isset($league_files[$i][3]) ) {
		                        $league_seasons = GetDirContents(LEAGUE_PATH.$league_files[$i][3],'dirs');
							    $total_seasons = count($league_seasons);
							    
								$league_view_url = SELF.'?mode=leagues&amp;section=leagueTable&amp;league='.$league_files[$i][3].'&amp;season='.((count($league_seasons) > 1 ) ? end($league_seasons) : $league_seasons[0]);
		                        $league_add_url = SELF.'?mode=admin&amp;section=addseason&amp;league='.$league_files[$i][3];
		                        $league_delete_url = SELF.'?mode=admin&amp;section=manage&amp;action=delete&amp;filetype=league&amp;league='.$league_files[$i][3];
                                $league_edit_url = SELF.'?mode=admin&amp;section=manage&amp;action=edit&amp;filetype=league&amp;league='.$league_files[$i][3];
                            
		                        $img_alt = strlen($league_files[$i][9]) > 18 ? substr($league_files[$i][3],0,16)."..." : $league_files[$i][3];
			                    if($i %2) $PB_output .= '<div class="panel right-1" style="width:46%;margin-right:auto;">';
		                        else $PB_output .= '<div class="panel left-1" style="width:46%;margin-right:5px;clear:both;">';
						            $PB_output .= '<div class="wrapper meta left-">';
			  		                    $PB_output .= '<div class="frame left-1" style="width:67px;height:67px;"><a href="'.$league_view_url.'">';
							                if($league_files[$i][9] != NULL && file_exists(LOGO_PATH.$league_files[$i][9])) $PB_output .= '<img src="'.LOGO_URL.$league_files[$i][9].'" alt="'.$img_alt.'" width="67px" height="67px" />';
                                            else $PB_output .= '<img src="'.LOGO_URL.'flm.png" alt="'.$img_alt.' logo" width="67px" height="67px" />';
							            $PB_output .= '</a></div>';
					                    $PB_output .= '<div class="left-1 pull-3">'.
					                        '<span class="title"><a href="'.$league_view_url.'">'.$league_files[$i][2].'</a></span><br/>'.
					                        //'<span class="date">Posted by '.ucfirst($article_author).' on '.$article_rday.'-'.$article_rmonth.'-'.$article_ryear.'</span>'.
				                        '</div>';
							        $PB_output .= '</div>';
                                    $PB_output .= '<div class="wrapper">';
                                        $PB_output .= '<ul class="UL-list">';
                                            $PB_output .= '<li class="table-view-link">';
                                                $PB_output .= '<span class="btn-highlight"><a href="'.$league_add_url.'">Add Season</a></span>';
                                                $PB_output .= '<span class="btn-highlight"><a href="'.$league_delete_url.'">Delete League</a></span>';
                                                $PB_output .= '<span class="btn-highlight"><a href="'.$league_edit_url.'">Edit League</a></span>';
                                            $PB_output .= '</li>';
						                    $PB_output .= '<li>'.$league_files[$i][2].' Seasons : <span class="b i">['.$total_seasons.']</span> found';
						                        $PB_output .= '<ul>';
									                foreach($league_seasons as $season_row){
												        $season_delete_url =SELF.'?mode=admin&amp;section=manage&amp;action=delete&amp;filetype=season&amp;league='.$league_files[$i][3].'&amp;season='.$season_row;
												        $season_edit_url =SELF.'?mode=admin&amp;section=manage&amp;action=edit&amp;filetype=league&amp;league='.$league_files[$i][3].'&amp;season='.$season_row;
												        //if($total_seasons > 1)$season_delete_url ='index.php?mode=admin&amp;section=manage&amp;action=delete&amp;filetype=season&amp;league='.$leagueTypes[$key].'&amp;season='.$season_row;
												        //else $season_delete_url = $league_delete_url;
				                                        $PB_output .= '<li><a href="'.$season_edit_url.'" title="edit the league table file for the season '.$season_row.'">Edit</a> | <a href="'.$season_delete_url.'" title="delete this season : '.$season_row.', from the league : '.$league_files[$i][3].'">Delete</a> Season'.$separator.$season_row.'</li>';
							                        }
									            $PB_output .= '</ul>';
									        $PB_output .= '</li>';
									    $PB_output .= '</ul>';
						            $PB_output .= '</div>';
						        $PB_output .= '</div>';
						    } else {
							    if( isset($league_files[$i][3]) ) $PB_output .= $league_files[$i][3];
							}
						}
						$urlVars = $url_prefix."&amp;action=".ACTION."&amp;f_sortby=$f_sortby&amp;f_ascdsc=$f_ascdsc";
						$PB_output .= '<div class="clear">&nbsp;</div>';
						$PB_output .= '<div class="foot pad-5">';
						    $PB_output .= '<span class="left-1 grid-45"><p class="title">There are currently a total of: <strong>'.$total_leagues.'</strong> leagues registered, totalling '.(($CFG->config['display_dirsize'] == 'YES') ? $UTIL->size('dir',LEAGUE_PATH) : '???').' in size, including the (fixtures,results &amp; icons) folders.</p></span>';
							$PB_output .= '<span align="right" class="paginate-wrapper right-1 grid-45"> | Page: '.$UTIL->print_pagination($numPages,$urlVars,$currentPage).'</span>';
						    $PB_output .= '<div class="clear">&nbsp;</div>';
						$PB_output .= '</div>';
			        }
	            } else {
	                $PB_output .= '<div class="message user_status"><p>'._MISSING_LEAGUE_DB_FILE.'</p></div>';
	                $PB_output .= '<div class="message unspecific"><p><a href="'.SELF.'?mode=home">Go to the homepage</a> or <a href="?mode=leagues&amp;section=fixtures">Return to leagues</a> or <a href="?mode=admin&amp;section=addleague">Create a new league</a></p></div>';
	            }
			break;
			
			case 'categories':
			    $categories= array();
	            if(file_exists($Category_DB_File)){
		            $fp = @fopen($Category_DB_File, 'r');
		            $array = explode("\n", fread($fp, filesize($Category_DB_File))); 
		            for($x=0;$x<sizeof($array);$x++) {	// start loop, each line of file
		                $temp = explode(":",$array[$x]); // explode the line and assign to temp
		                $tmp=array();
						$tmp[1] = $temp[0];
						$tmp[2] = $temp[1];
						$tmp[9] = $temp[2];
		                array_push($categories,$tmp);
					}
                    fclose($fp);

					usort($categories,'sort_file');
			        $total_categories = count($categories);
			        if($total_categories > 0){
					    $numPages = ceil( count($categories) / $CFG->config['itemsPerPage'] );
                        if(isset($_GET['p'])) {
	                        $currentPage = $_GET['p'];
                            if($currentPage > $numPages) $currentPage = $numPages;
                        } else $currentPage=1;
        
                        $start = ( $currentPage * $CFG->config['itemsPerPage'] ) - $CFG->config['itemsPerPage'];
				       
			            for( $i=$start; $i<$start + $CFG->config['itemsPerPage']; $i++ ) {
							if( isset($categories[$i][1]) ) {
								//$PB_output .= '<li><a href="?mode=filter&amp;filterby=category&amp;category='.$categories[$i][1].'&amp;categoryID='.$categories[$i][0].'">'.$categories[$i][1].'</a></li>';
								$id = $categories[$i][1];
								$cat_name = $categories[$i][2];
								$cat_image = $categories[$i][9];
								
								$edit_url = SELF.'?mode=admin&amp;section=manage&amp;action=edit&amp;filetype=categories&amp;category='.$cat_name.'&amp;id='.$id;
								$delete_url = SELF.'?mode=admin&amp;section=manage&amp;action=delete&amp;filetype=category&amp;category='.$cat_name.'&amp;id='.$id;
								
							    if(file_exists(CATEGORY_IMAGE_PATH.$cat_image) && $UTIL->isValidExt(CATEGORY_IMAGE_PATH.$cat_image,$ifxs)) $cat_img = CATEGORY_IMAGE_URL.$cat_image;
								else $cat_img = LOGO_URL.'flm.png';
								
								if($i %2) $PB_output .='<div class="panel right-1 " style="width:46%;margin-right:auto;">';
		                        else $PB_output .='<div class="panel left-1" style="width:46%;margin-right:5px;clear:both;">';
                                    $PB_output .= '<h3>Category'.$separator.$cat_name.'</h3>';
                                    $PB_output .='<div class="left-1 grid-1"><p align="center"><img src="'.$cat_img.'" alt="'.$cat_name.' category image" /></p></div>';
                                    $PB_output .='<ul class="UL-list grid-3 right-1">';
									    $PB_output .= '<li>Category ID&nbsp;:&nbsp;'.$id.'</li>';
									    $PB_output .='<li>Category Name&nbsp;:&nbsp;'.$cat_name.'</li>';
                                        $PB_output .= '<li><a href="'.$edit_url.'">edit</a> | <a href="'.$delete_url.'">delete</a>'.$separator.'category</li>';
                                    $PB_output .='</ul>';
                                $PB_output .='</div>';
						    } else {
								if( isset($categories[$i][1]) ) $PB_output .= $categories[$i][1];
						    }
						}
						$urlVars = $url_prefix."&amp;action=".ACTION."&amp;f_sortby=$f_sortby&amp;f_ascdsc=$f_ascdsc";
						$PB_output .= '<div class="clear">&nbsp;</div>';
						$PB_output .= '<div class="foot pad-5">';
						    $PB_output .= '<span class="left-1 grid-45"><p class="title">There are currently a total of: <strong>'.$total_categories.'</strong> categories registered, totalling '.$UTIL->size('file',$Category_DB_File).' in size.</p></span>';
							$PB_output .= '<span align="right" class="paginate-wrapper right-1 grid-45"> | Page: '.$UTIL->print_pagination($numPages,$urlVars,$currentPage).'</span>';
						    $PB_output .= '<div class="clear">&nbsp;</div>';
						$PB_output .= '</div>';
	                } else $PB_output .= '<div class="message user_status"><p><b>0 category Found</b> ...</p></div>'."\n";
		        } else $PB_output .= '<div class="message user_status"><p>'._MISSING_CATEGORY_DB_FILE.'</p></div>'."\n";
			break;
			
			case 'galleries': include(VIEW_PATH.'viewGallery.php'); break;
			
			case 'images':
			    include(VIEW_PATH.'inc/gallery.php');
			break;
			
		    case 'edit':
			    if(isset($_GET['filetype'])){
			        if($_GET['filetype'] == 'article'){
			            $file = $_GET['newsArticle'];
				        $fileType = 'article';
				        $path = $UTIL->Check_For_Slash(ARTICLES_PATH,true);
				        $img_path = $UTIL->Check_For_Slash(ARTICLES_IMAGE_PATH,true);
						$ext = pathinfo($path.$file, PATHINFO_EXTENSION);
	                    $news_file = ($ext == '') ? $file.'.txt' : $file;
						
	                    if (file_exists($path.$news_file)) {
		                    $selected_news = $path.$news_file;
	                        $news_row = $JSON->decode(file_get_contents($selected_news));
                            $news_id = $news_row->id;
		                    $news_title = $news_row->title;
		                    $news_author = $news_row->author;
		                    $news_author_email = $news_row->email;
                            $news_summary = $news_row->summary;
                            $news_details = $news_row->details;
                            $news_date = $news_row->date;
                            $news_moddate = $news_row->date_modified;
	                        $date = $news_row->date;
                            $news_category = $news_row->category;
                            $news_keywords = $news_row->keywords;
		                    $cat_image = $news_row->cat_image;
                            $news_image = $news_row->news_image;
                            $publish = $news_row->publish;
            
		                    if($news_keywords != "" || $news_keywords != NULL) $tags = explode(',',$news_keywords);
		                    else $tags = "";
		
		                    $year = substr("$date", 0, 4);
                            $month = substr("$date", 5,-3);
                            $day = substr("$date", 8);
		                    $full_date = "$day-$month-$year";
			
		                    if($news_details == "" || $news_details == NULL) $news_article = $news_summary;
		                    else $news_article = $news_details;
			
						    include(VIEW_PATH.'admin/editNews.php');
						}else $PB_output .= '<div class="message user_status">The article selected for editing'.$separator.$news_file.', does not exist,or the path supplied is not valid.</div>';
			        } elseif($_GET['filetype'] == 'page')  {
                        $file = $_GET['page'];
				        $fileType = 'page';
			    	    $path = $UTIL->Check_For_Slash(PAGE_PATH,true);
					    $img_path = '';
						include(VIEW_PATH.'admin/editPage.php');
			        } elseif($_GET['filetype'] == 'league')  {
                        $file = $_GET['league'];
				        $fileType = 'league';
			    	    $path = $UTIL->Check_For_Slash(LEAGUE_PATH,true);
					    $img_path = '';
						include(VIEW_PATH.'admin/updateLeague.php');
			        } elseif($_GET['filetype'] == 'fixtures')  {
					    $file = $_GET['fixture'];
				        $fileType = 'fixture';
			    	    $path = LEAGUE_PATH.LEAGUE.'/'.SEASON.'/fixtures/';
					    $img_path = '';
						include(VIEW_PATH.'admin/editFixture.php');
					} elseif($_GET['filetype'] == 'profiles')  {
					    $file = $_GET['profile'];
				        $fileType = 'profile';
			    	    $path = DATABASE_PATH.'users.txt';
					    $img_path = '';
						include(VIEW_PATH.'admin/editProfile.php');
					} elseif($_GET['filetype'] == 'categories')  {
					    $file = $_GET['category'];
				        $fileType = 'category';
			    	    $path = DATABASE_PATH.'categories.txt';
					    $img_path = '';
						include(VIEW_PATH.'admin/editCategory.php');
					} elseif($_GET['filetype'] == 'galleries')  {
					    $file = $_GET['album'];
				        $fileType = 'album';
						$galleryInfo = GetAlbumInfo($file);
						include(VIEW_PATH.'admin/editGallery.php');
					} elseif($_GET['filetype'] == 'albumPic')  {
					    $file = $_GET['pic'];
				        $fileType = 'pic';
			    	    $path = GALLERY_PATH.$_GET['album'];
					    //$img_path = '';
						//$galleryInfo = GetAlbumInfo($file);
						include(VIEW_PATH.'admin/rename.php');
					} elseif($_GET['filetype'] == 'image')  {
					    $file = $_GET['pic'];
				        $fileType = 'pic';
			    	    $path = GALLERY_PATH.$_GET['dir'];
					    //$img_path = '';
						//$galleryInfo = GetAlbumInfo($file);
						include(VIEW_PATH.'admin/rename.php');
					}
			    } else $PB_output .= '<div class="message error">ERROR : file type not set, the type of file to be edited is not set.</div>';
			break;
			
		    case 'delete':
			    if(isset($_GET['filetype'])){
		            if($_GET['filetype'] == 'article'){
			            $file = $_GET['newsArticle'];
				        $fileType = 'article';
				        $path = $UTIL->Check_For_Slash(ARTICLES_PATH,true);
				        $img_path = $UTIL->Check_For_Slash(ARTICLES_IMAGE_PATH,true);
			        } elseif($_GET['filetype'] == 'page')  {
                        $file = $_GET['page'];
				        $fileType = 'page';
			    	    $path = $UTIL->Check_For_Slash(PAGE_PATH,true);
					    $img_path = '';
			        } elseif($_GET['filetype'] == 'season') {
                        $file = $_GET['season'];
				        $fileType = 'season';
			    	    $path = $UTIL->Check_For_Slash(LEAGUE_PATH.$_GET['league'],true);
					    $img_path = '';
			        } elseif($_GET['filetype'] == 'league')  {
                        $file = $_GET['league'];
				        $fileType = 'league';
			    	    $path = $UTIL->Check_For_Slash(LEAGUE_PATH,true);
					    $img_path = '';
			        } elseif($_GET['filetype'] == 'profile')  {
                        $file = $_GET['profile'];
				        $fileType = 'profile';
			    	    $path = $UTIL->Check_For_Slash(LEAGUE_PATH,true);
					    $id = $_GET['id'];
			        } elseif($_GET['filetype'] == 'category')  {
                        $file = $_GET['category'];
				        $fileType = 'category';
			    	    $path = DATABASE_PATH;
					    $id = $_GET['id'];
						$img_path = CATEGORY_IMAGE_PATH;
			        } elseif($_GET['filetype'] == 'gallery')  {
                        $file = $_GET['album'];
				        $fileType = 'album';
			    	    $path = GALLERY_PATH;
					    $id = $_GET['id'];
			        } elseif($_GET['filetype'] == 'image')  {
                        $file = $_GET['pic'];
				        $fileType = 'pic';
			    	    $path = $UTIL->Check_For_Slash(GALLERY_PATH.$_GET['dir'],true);
			        } elseif($_GET['filetype'] == 'albumPic')  {
                        $file = $_GET['pic'];
				        $fileType = 'pic';
			    	    $path = $UTIL->Check_For_Slash(GALLERY_PATH.$_GET['album'],true);
			        }
			
			        //$PB_output .= $path.$file;
			        $PB_output .= '<div class="grid-4 panel">';
			        if(!isset($_POST['YES']) AND !isset($_POST['NO'])){
			            $PB_output .= '<center> 
                            <p>Are you sure you want to delete the '.$fileType.' <span class="b">'.$file.'</span> ?<br/>
                            <form action="" method="post">
	                            <input class="save" type="submit" name="YES" value="YES : Delete" title="Delete : '.$file.'" /> | <input class="cancel" type="submit" name="NO" value="NO : Cancel" title="Cancel" />
	                        </form>
			            </center>';
			        } else if(isset($_POST['YES']) AND isset($_GET['filetype']) AND ($fileType == 'season' || $fileType == 'league' || $fileType == 'album') AND is_dir($UTIL->Check_For_Slash($path.$file,true))){
                        $target = $path.$file;
					    $dn=dirname($path);
                        if (file_exists($target)) {
					        if($UTIL->destroyDir($target,false)) {
							    $PB_output .= '<div class="message"><p>The '.$file.' folder has been deleted successfully.</p></div>';
							    if($_GET['filetype'] == 'season' && unlink($path.$file.'_league_table.txt')) $PB_output .= '<div class="message"><p>The file <span class="em">['.$file.'_league_table.txt]</span> has been deleted successfully.</p></div>';
                                else $PB_output .= '<div class="message error"><p>The file <span class="em">['.$file.'_league_table.txt]</span> hasn&rsquo;t been deleted . Maybe it has been deleted already.</p></div>';
							} else $PB_output .= '<div class="message error"><p>An error occured! The '.$file.' folder hasn&rsquo;t been deleted.</p></div>';
							$PB_output .= '<div class="message unspecific"><p><a href="'.$return_url.'">Return</a></p></div>';
					    }else $PB_output .= '<div class="message user_status"><p>File or path not valid, it does not exists.</p></div>';
					    
			        } else if(isset($_POST['YES']) AND isset($_GET['filetype']) AND ($_GET['filetype'] == 'image' || $_GET['filetype'] == 'albumPic') AND $UTIL->isValidExt($path.$file,$ifxs) ){
			            $target = $path.$file;
					    if (file_exists($target)) {
                            unlink($target);
					        $PB_output .=  '<div class="message"><p>The '.$fileType.$separator.'<b>'.basename($target).'</b> has been deleted successfully</p></div>'."\n";
					    } else $PB_output .= '<div class="message error"><p><b>File</b> or <b>Path</b> not valid</p></div>';
					    
					} else if(isset($_POST['YES']) AND isset($_GET['filetype']) AND ($_GET['filetype'] == 'article' || $_GET['filetype'] == 'page') AND is_file($path.$file) ){
                        $target = $path.$file;
                        if (file_exists($target)) {
                            unlink($target);
					        $PB_output .=  '<div class="message"><p>The '.$fileType.$separator.'<b>'.basename($target).'</b> has been deleted successfully</p></div>'."\n";
					  
						    $file_image = $img_path.$file;
                            if ($fileType == 'article') {
                                if (file_exists($file_image)) {
                                    unlink($file_image);
                                    $PB_output .=  '<div class="message"<p>The article image file '.$separator.'<b>'.$file_image.'</b> has been deleted successfully</p></div>'."\n";
						       
			                    } else $PB_output .= '<div class="message user_status"><p>The article image file '.$separator.'<b>'.$file_image.'</b> does not exist</p></div>'."\n";
						    }
						} else $PB_output .= '<div class="message error"><p><b>File</b> or <b>Path</b> not valid</p></div>';
					    
						$PB_output .=  '<div class="message unspecific"><p><a href="'.$return_url.'">Return to file manager index</a></p></div>';
			        } else if(isset($_POST['YES']) AND isset($_GET['filetype']) AND $_GET['filetype'] == 'category' AND isset($_GET['id']) AND $_GET['id'] >= 0){
					    $mod = array();
					    if($id >= 0){
				            $fp = @fopen($Category_DB_File, 'r');
				            $array = explode("\n", fread($fp, filesize($Category_DB_File))); 
				            // this could have been coded much simplier, but in order to delete the category image, this was the best solution I could come up with.
							for($x=0;$x<sizeof($array);$x++) {	// start loop, each line of file
		                        $temp = explode(":",$array[$x]); // explode the line and assign to temp
		                        $tmp=array();
						        $tmp[1] = $temp[0];
					        	$tmp[2] = $temp[1];
						        $tmp[9] = $temp[2];
		                        array_push($mod,$tmp);
					        }
							// check if a category image exists. If it does, delete it.
							if(file_exists($img_path.$mod[$id][9]) && $UTIL->isValidExt($img_path.$mod[$id][9],$ifxs)){
							    if(unlink($img_path.$mod[$id][9])) $PB_output .= '<div class="message"><p>The Category image'.$separator.$mod[$id][2].' was found and deleted successfully</p></div>';
							    else $PB_output .= '<div class="message error"><p>Unable to delete the category image'.$separator.$mod[$id][2].' although it was found!</p></div>';
							}
							
							unset($mod[$id]); // delete it!
				            if(count($mod) > 0){
					            //$new = array_values(array_unique($mod));
					            $new = array_values($mod);
				            } else $new = $mod;
							
							/* //unset($array[$id]); // delete it!
							if(count($array) > 0){
					            $new = array_values(array_unique($array));
				            } else $new = $array;*/
							
				            $fp = fopen ($Category_DB_File, "w");
				            sort($new);
				            fwrite ($fp, '0'.':'.$new[0][2].':'.$new[0][9]); // write the first category array, assigning it's [id] to 0, then start looping the other lines(increment starts at 1) it is written this way to eliminate new line after the last array has been written.
				            for($i=1; $i<sizeof($new);$i++){
							    fwrite ($fp, "\n");
				                fwrite ($fp, $i.':'.$new[$i][2].':'.$new[$i][9]);
				            }
				            fclose ($fp);
								
				            $PB_output .= '<div class="message"><p>The category <span class="em i">'.$separator.$file.'</span> has been deleted successfully</p></div>';
							$PB_output .= '<div class="message unspecific"><p><a href="'.SELF.'?mode=home">return to homepage</a></p></div>';
						
						} else $PB_output .= '<div class="message error"><p>No ID specified, please <a href="javascript:history.back()">go back and select an item</a> to be edited first!</p></div>';
			        
					} else if(isset($_POST['YES']) AND isset($_GET['filetype']) AND $_GET['filetype'] == 'profile' AND isset($_GET['id']) AND $_GET['id'] >= 0){
					    if($file == $is_validUser['user_name'] || $roleID > 5){
						    if($id >= 0){
				                $fp = @fopen($Users_DB_File, 'r');
				                $array = explode("\n", fread($fp, filesize($Users_DB_File))); 
				                unset($array[$id]); // delete it!
				                if(count($array) > 0){
					                $new = array_values(array_unique($array));
				                } else $new = $array;
				                $fp = fopen ($Users_DB_File, "w");
				                sort($new);
				                fwrite ($fp, $new[0]);
				                for($i=1; $i<sizeof($new);$i++){			
				                    fwrite ($fp, "\n");
				                    fwrite ($fp, $new[$i]);
				                }
				                fclose ($fp);
				                $PB_output .= '<div class="message"><p>The profile <span class="em i">'.$separator.$file.'</span> has been deleted successfully</p></div>';
				                
							    // kill session variables
		                        unset($_SESSION['validUser']);
	                            unset($_SESSION['userName']);
                                //unset($_SESSION['password']);
                                $_SESSION = array(); // reset session array
                                session_destroy();   // destroy session.
	
								$PB_output .= '<div class="message"><p>The session <span class="em i">'.$separator.$file.'</span> has been destroyed successfully</p></div>';
		                        
								$PB_output .= '<div class="message unspecific"><p><a href="'.SELF.'?mode=home">return to homepage</a></p></div>';
							} else $PB_output .= '<div class="message error"><p>No ID specified, please <a href="javascript:history.back()">go back and select an item</a> to be edited first!</p></div>';
					    
						} else{
	                        if($file != $is_validUser['user_name']) $PB_output .= '<div class="message error"><p>You are trying to edit a profile other than yours.<span class="em i">YOU CAN&rsquo;T DO THAT!</span></p></div>';
	                    }
					} else if(isset($_POST['NO'])) $PB_output .= '<div class="message user_status"><p>The file'.$separator.$file.' was not deleted, you canceled, <a href="'.$return_url.'">return to file manager index</a></p></div>';
			        else $PB_output .= '<div class="message error"><p><span class="em i">An error occured!</span><br/><strong>Debug Info:</strong><br/> Path'.$separator.$path.'<br/> File'.$separator.$file.', <a href="'.$return_url.'">return to file manager index</a></p></div>';
			        
			        $PB_output .= '</div>';
				} else $PB_output .= '<div class="message error">ERROR : file type not set, the type of file to be deleted is not set.</div>';
		    break;
			case 'browser':
			case 'view':
			    include(VIEW_PATH.'admin/browser.php');
		    break;
		}
	    $PB_output .= '</div>'."\n";
    $PB_output .= '</div>'."\n";
	echo $PB_output;
?>