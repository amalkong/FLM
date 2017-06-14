<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    
	$selected_dir = ARTICLES_PATH;
	$i = 1; 
	$ci = "";
	$allArticles = GetDirContents($selected_dir,'files');
	$file_name = isset($_GET['newsArticle']) ? $_GET['newsArticle'] :  ((count($allArticles) > 1) ? end($allArticles) : $allArticles[0]);
    $ext = pathinfo($selected_dir.$file_name, PATHINFO_EXTENSION);
	$news_file = ($ext == '') ? $file_name.'.txt' : $file_name;
	$prevNext_link = NULL;
	if(is_dir($selected_dir)){
	    if ($g_handle = opendir($selected_dir)) {
	        while (false !== ($file = readdir($g_handle))) {
		        if ($UTIL->isValidExt($file,$tfxs)) {
			        $article[$i] = $file;
			        if ($file_name == $article[$i] || $file_name == $UTIL->removeFileExt($article[$i])) $ci = $i;
					
					$prevNext_link_file = $JSON->decode(file_get_contents($selected_dir.$file));
			        $prevNext_link_file_titles[$i] = $prevNext_link_file->title;
			            $i++;
		        }
	        }
	        closedir($g_handle);
        }
	} 
	
	
	if(count($article) > 0){
	    $ti = $i - 1;
	    $pi = $ci - 1;
	    if ($file_name == "") $ni = $ci + 2;
	    else $ni = $ci + 1;
	    if ($file_name == "") $file_name = $article[1];
				
	    if ($pi > 0) {
		    $prev_file_title = $prevNext_link_file_titles[$pi];
		    $piFile = urlencode($UTIL->removeFileExt($article[$pi]));
		    $prevNext_link .= "<span class=\"two-nav nav-prev\"><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=viewArticle&amp;newsArticle=" . $piFile . "#__Top\" title=\"show the previous article : $prev_file_title\">&larr;&nbsp;$prev_file_title</a></span>";
	    } else $prevNext_link .= '<span class="two-nav nav-prev">&nbsp;</span>';
		
	    if ($ni <= $ti) {
		    $next_file_title = $prevNext_link_file_titles[$ni];
		    $niFile = urlencode($UTIL->removeFileExt($article[$ni]));
		    $prevNext_link .= "<span class=\"two-nav nav-next\"><a href=\"" . $_SERVER['PHP_SELF'] . "?mode=viewArticle&amp;newsArticle=" . $niFile . "#__Top\" title=\"show the next article : $next_file_title\">$next_file_title&nbsp;&rarr;</a></span>";
	    } else $prevNext_link .= '<span class="two-nav nav-next">&nbsp;</span>';
	} else $prevNext_link .= 'No article found';
	
	if (file_exists(ARTICLES_PATH.$news_file)) {
		$selected_news = ARTICLES_PATH.$news_file;
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
		
		if($publish == 'YES'){
			if(file_exists(ARTICLES_IMAGE_PATH.$news_image)){
                echo '<div class="main-image"><div class="outer"><span class="inset"><img src="'.ARTICLES_IMAGE_URL.$news_image.'" align="center" alt=" '.$news_image.'" title="'.$news_title.'" /></span></div></div>';
            }
        }
		echo'<div class="content single" id="__Top">';
		    echo'<div class="post format-image box"> ';
				if($publish == 'YES'){
			        echo'<div class="details">';
						echo'<span class="icon-image"><font color="#0099cc" size="2"> Posted by <a href="?mode=filter&amp;filterby=author&amp;author='.$news_author.'">'.ucfirst($news_author).'</a> on <a href="?mode=filter&amp;filterby=date&amp;date='.$full_date.'">'.$full_date.'</a> in <a href="?mode=filter&amp;filterby=category&amp;category='.$news_category.'">'.ucfirst($news_category).'</a></font></span>';
				        echo'<span class="likes"><a href="#" class="likeThis">44</a></span>';
				        echo'<span class="comments"><a href="#">3</a></span>'; 
			        echo'</div>'; 
			        
					echo '<h2 class="title">'.$news_title.'</h2>';
			        echo '<div class="article"><p>'.$news_article.'</p></div>';
				
					if(is_array($tags)){
						echo'<div class="tags">';
						    foreach ($tags as $tag)echo '<a href="?mode=filter&amp;filterby=tags&amp;tag='.$tag.'" title="filter articles by the tag'.$separator.$tag.'">'.$tag.'</a>,';
					    echo '</div>'."\n";
					}
				} else {
				    echo '<div class="message user_status">This article <span class="em i">'.$news_title.'</span>,has not been published yet, view another article</div>';
				}	   
			    
				echo'<div class="spacer"></div>';
			    echo'<div class="post-nav grid-4">'.$prevNext_link.'</div>';
				echo'<div class="clear"></div>';
		    echo'</div>';	
        echo'</div>';
	} else {
		echo '<div class="content box">';
		    echo '<div class="message error">The File '.$separator.'<span class="em i">'.$news_file.'</span> Was Not Found.</div>';
			echo '<div class="message unspecific"><a href="'.$_SERVER['PHP_SELF'].'?mode='.MODE.'&amp;section='.SECTION.'">Return</a></div>';
		    echo'<div class="spacer"></div>';
			echo'<div class="post-nav grid-4">'.$prevNext_link.'</div>';
		    echo'<div class="clear"></div>';
		echo "</div>";
	}
?>