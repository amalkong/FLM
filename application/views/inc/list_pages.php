<?php
    $PB_output = NULL;
	$section_num = isset($_REQUEST['section_num']) ? $_REQUEST['section_num'] : isset($_GET['section_num']) ? $_GET['section_num'] : 0;
    // Get the current page
	$currentPage = trim(isset($_REQUEST['pf']) ? $_REQUEST['pf'] : '');
	
	$PB_output .='<div class="box2 grid-auto">';
        if(is_array($allPages) && count($allPages) > 0) {
	    // Pagination settings
        $numPages = ceil(count($allPages) / $CFG->config['itemsPerPage']);
        if(!$currentPage || $currentPage > $numPages) $currentPage = 0;  
        $start = $currentPage * $CFG->config['itemsPerPage'];  
        $end = (($currentPage * $CFG->config['itemsPerPage']) + $CFG->config['itemsPerPage']);  
        // Extract ones we need  
        foreach($allPages as $key => $val) {  
            if($key >= $start && $key < $end) $pagedData[] = $allPages[$key];  
        }
	
		//$PB_output .='<h3 class="head">other pages</h3>';
		$PB_output .='<ul class="bullet-list">';
            foreach($pagedData as $pageFile) {
				if(file_exists(PAGE_PATH.$pageFile) && is_file(PAGE_PATH.$pageFile)){
				    $title = cleanPageTitles( $pageFile );
					$pageFile = removeFileExt($pageFile);
				    if( PAGE == $pageFile ) {
				        $current_title = '<strong>'.$title.'</strong>';
					    $idc = ' class="current"';
				    } else {
				        $current_title = $title;
					    $idc = '';
			        }
				
					if(SECTION == 'index') $PB_output .= '<li'.$idc.'><a href="?mode='.MODE.'&amp;section=pages&amp;page=' . urlencode($pageFile).'&amp;pf=' . ($currentPage).'&amp;section_num='.$section_num.'" title="'.$title.'">'. $current_title .'</a></li>';
                    else $PB_output .= '<li'.$idc.'><a href="?mode='.MODE.'&amp;section='.SECTION.'&amp;page=' . urlencode($pageFile).'&amp;pf=' . ($currentPage) .'" title="'.$title.'">'.$current_title.'</a></li>';
		        }
			}
		$PB_output .='</ul>';
	    $PB_output .='<div class="post-nav grid-full">';
            if($currentPage > 0 && $currentPage < $numPages) $PB_output .= '<span class="nav-prev"><a href="'.SELF.'?mode='.MODE.'&amp;section='.SECTION.'&amp;page='.urlencode(PAGE).'&amp;pf='.($currentPage - 1).'">&laquo; Previous</a></span>';  
            if($numPages > $currentPage && ($currentPage + 1) < $numPages) $PB_output .= '<span class="nav-next"><a class="right-1" href="'.SELF.'?mode='.MODE.'&amp;section='.SECTION.'&amp;page='.urlencode(PAGE).'&amp;pf='.($currentPage + 1).'">Next &raquo;</a></span>';
		$PB_output .='</div>';
		//$PB_output .='<div class="clear">&nbsp;</div>';
	
	    } else $PB_output .= '<div class="message user_status"><p><b>No page File Found</b> ...</p></div>'."\n";
    $PB_output .='</div>';
	echo $PB_output;
?>