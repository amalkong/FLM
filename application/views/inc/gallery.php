<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
	if(!isset($PB_output)) $PB_output=NULL;
	$page_htmo = $imgl = $i = $ci = "";
	$pic = ( isset($_GET['pic']) ) ? $_GET['pic'] : '';
    
    $page = GetCommand("p");
	$idir = GetCommand('dir');
    $page = ( isset($_GET["p"]) ) ? $_GET["p"] : 0;
	$url_prefix = SELF.'?mode='.MODE.'&amp;section='.SECTION;
	if(isset($_GET['action'])) $url_prefix .='&amp;action='.ACTION;
	$return_url = SELF.'?mode='.MODE.'&amp;section='.SECTION.'&amp;action='.ACTION;
    $path = 'application/data/img/';
    $path = $UTIL->Check_for_slash($path,true);
	if(!isset($dirlist)) $dirlist = getDirContents($path,'dirs');
	$selected_dir = isset($_GET['dir']) ? $idir : $dirlist[0];
	
    define('DIR',$selected_dir);
	
	$sortLinks = '<div><a href="'.$url_prefix.'&amp;dir='.DIR.'&amp;f_sortby=name&amp;ascdsc='.$ascdsc.'&amp;p='.$page.'">Name</a> | <a href="'.$url_prefix.'&amp;dir='.DIR.'&amp;f_sortby=date&amp;ascdsc='.$ascdsc.'&amp;p='.$page.'">Date</a> | <a href="'.$url_prefix.'&amp;dir='.DIR.'&amp;f_sortby=size&amp;ascdsc='.$ascdsc.'&amp;p='.$page.'">Size</a> | </div>';
	
	$ignore  = array('.', '..','thumbs','Thumbs.db','girlsxxx');
	$dirs = array();
	$image_files = array();
	$random_pics = array();
	
	foreach($image_sortby_array as $cat=>$v){
		$sortby_html[] = '<div class="sort-link left-1">&nbsp;|&nbsp;<span class="i left-1">'.(($f_sortby == $cat) ? "<strong>$v</strong>" : "$v").'</span>'.
		"<span class=\"left-1\"><a class=\"icon asc\" href=\"$url_prefix&amp;f_sortby=$cat&amp;f_ascdsc=ASC\" title=\"sort files by $cat ascending\"></a><a class=\"icon desc\" href=\"$url_prefix&amp;f_sortby=$cat&amp;f_ascdsc=DESC\" title=\"sort files by $cat descending\"></a></span></div>";
	}
	$PB_output .='<div class="panel"><p>In this section you can rename or delete any image found in the background textures, headers, avatars, news pics or logos folder.</p></div>';
    $PB_output .='<div class="gallery push-center">';
        if (!isset($_GET['dir'])) {
		    // display list of dirs
            //$folders = scandir($path, 0);
            foreach($dirlist as $dir) {
	            if(!in_array($dir, $ignore) && is_dir($UTIL->Check_for_slash($path.$dir,true))) {
		             //$cat = rawurlencode($dir);//$caption = substr($dir,0,20);
					$rand_dirs = glob($UTIL->Check_for_slash($path,true).$dir.'/*.*', GLOB_NOSORT);
				    if(count($rand_dirs) == 0) $rand_pic  = IMG_URL.'unknown.png';
                    if($UTIL->isValidExt($rand_dirs[array_rand(@$rand_dirs)],$ifxs)) $rand_pic = $rand_dirs[array_rand(@$rand_dirs)];
					$tmp = array();
			        $tmp[0] = $dir;
					$tmp[2] = strlen($dir) > 18 ? substr($dir,0,16)."..." : $dir;
					$tmp[3] = 'This is the <span class="em i">'.cleanPageTitles($dir).'</span> folder';
					$tmp[5] = date('d-m-Y H:i:s',filemtime($path.$dir));
					$tmp[7] = ($CFG->config['display_dirsize'] == 'YES') ? $UTIL->size('file',$path.$dir) : '???';
					$tmp[9] = $rand_pic;
				    
				    //if($UTIL->isValidExt($rand_pic,$ifxs)) array_push( $random_pics, $rand_pic );
			        array_push($dirs,$tmp);
			    }
	        }
		    usort($dirs,'sort_file');
		    if(count($dirs) == 0) $PB_output .= '<div class="message user_status"><p class="em i">No directory found</p></div>';
		    else {
		        $numPages = ceil( count($dirs) / $CFG->config['itemsPerPage'] );
                if(isset($_GET['p'])) {
	                $currentPage = $_GET['p'];
                    if($currentPage > $numPages) $currentPage = $numPages;
                } else $currentPage = 1;
            
	            $total_items = count($dirs);
		        $start = ( $currentPage * $CFG->config['itemsPerPage'] ) - $CFG->config['itemsPerPage'];
		        
	            $sortby_htmo = implode(" ", $sortby_html);
	        
		        $PB_output .= '<div class="page-num head">PAGE &mdash;&nbsp;[ '.$currentPage.' ]&nbsp;&mdash;</div>';
			    $PB_output .=  '<div class="hRule">&nbsp;</div>';
		        $PB_output .= '<div class="titlebar">'.
			        '<div class="left-1 grid-2"><span class="title">'.$CFG->config['site_title_full'].'</span> - Data Image Directory/ : '.$total_items.' '.(($total_items > 1) ? 'Directories' :'Directory').' found</div>'.
                    '<div id="sortby" class="right-1 grid-45"><span class="left-1">Sort by: </span>'.$sortby_htmo.'</div>'.
                '</div>'; 
                $PB_output .= '<div class="clear">&nbsp;</div>';
				$PB_output .= '<div class="hRule">&nbsp;</div>';//echo '<div class="clear"></div>';
	            for( $i=$start; $i<$start + $CFG->config['itemsPerPage']; $i++ ) {
	                if( isset($dirs[$i][0]) ) {
			            $PB_output .= '<div class="album-wrapper shadow ">'.
					        '<a href="'.$url_prefix.'&amp;dir='. urlencode($dirs[$i][0]) .'" title="View images from -- '.$dirs[$i][0].' folder"><div class="caption">'. cleanPageTitles($dirs[$i][2]) .'</div></a>'.
						    '<div class="album-thumb">'.
						        '<a href="'.$url_prefix.'&amp;dir='. urlencode($dirs[$i][0]) .'" title="View images from -- '.$dirs[$i][0].' folder"><img src="'. $dirs[$i][9] .'" style="max-width:'.$CFG->config['thumb_width'].';" alt="'. $dirs[$i][9] .'" /></a>'.
							    '<div class="description">'. $dirs[$i][3] .', folder size : '.$dirs[$i][7].'</div>'.
					        '</div>'.
						'</div>';
		            } 
	            }
			    $PB_output .= '<div class="clear"></div>';
				//-----------------------------------------
	            $urlVars = $url_prefix."&amp;f_sortby=$f_sortby&amp;f_ascdsc=$f_ascdsc";
			    $PB_output .= '<div class="paginate-wrapper" class="right- mini">There are : <strong>'.$total_items.'</strong> dir'.(($total_items > 1) ? 's' :'').' | Totalling '. (($CFG->config['display_dirsize'] == 'YES') ? $UTIL->size('dir',$path) : '???').' in size | '. $UTIL->print_pagination($numPages,$urlVars,$currentPage).'</div>';
		        /**/
		    }
        } else {
	        // display photos in folder
            $src_folder = $UTIL->Check_for_slash($path.$_GET['dir'],true);
		    $src_url = $UTIL->Check_for_slash($path.$_GET['dir'],true);
			$image_files = GetAlbumsPhotos($src_folder,$src_url);
            
		    usort($image_files,"sort_file");
			
            if ( count($image_files) == 0 ) $PB_output .= '<div class="message user_status"><p>There are no image in this folder!</p></div>';
            else {
                $numPages = ceil( count($image_files) / $CFG->config['itemsPerPage'] );
                if(isset($_GET['p'])) {
	                $currentPage = $_GET['p'];
                    if($currentPage > $numPages) $currentPage = $numPages;
                } else $currentPage=1;
			    $start = ( $currentPage * $CFG->config['itemsPerPage'] ) - $CFG->config['itemsPerPage'];
                $total_images = count($image_files);
				
	            $sortby_htmo = implode(" ", $sortby_html);
	        
		        $PB_output .= '<div class="page-num head">PAGE &mdash; [ '.$currentPage.' ] &mdash;&raquo;</div>';
			    $PB_output .=  '<div class="hRule">&nbsp;</div>';
		        $PB_output .= '<div class="titlebar">'.
			        '<div class="left-1"><span class="title">'.$CFG->config['site_title_full'].'</span> - '.ucfirst(DIR).' Image Directory : '.$total_images.' '.DIR.' - <a href="'.$return_url.'">View All Directory</a></div>'.
                    '<div id="sortby" class="right-1 grid-45"><span class="left-1">Sort by: </span>'.$sortby_htmo.'</div>'.
                '</div>';
				if(isset($CFG->config['show_image_info']) && $CFG->config['show_image_info'] == 'YES') $thumb_pic_height = 'auto';
			    else $thumb_pic_height = '120px';
				
			    $PB_output .=  '<div class="hRule">&nbsp;</div>';
                $PB_output .= '<div class="clear"></div>';
                for( $i=$start; $i<$start + $CFG->config['itemsPerPage']; $i++ ) {
		            if( isset($image_files[$i][0]) && is_file( $src_folder . $image_files[$i][0] ) ) {
			            $info = Image_info_basic($src_folder .$image_files[$i][0]);
						$date = $image_files[$i][5];
						$title = strlen($image_files[$i][2]) > 18 ? substr($image_files[$i][2],0,16)."..." : $image_files[$i][2];
						$src_img = $src_url.$image_files[$i][0];
						$alt = $title . " -|- Last modified: " . $date;
		                $edit_url = SELF.'?mode=admin&amp;section=manage&amp;action=edit&amp;filetype=image&amp;dir='.DIR.'&amp;pic='.$image_files[$i][0];
		                $delete_url = SELF.'?mode=admin&amp;section=manage&amp;action=delete&amp;filetype=image&amp;dir='.DIR.'&amp;pic='.$image_files[$i][0];
			            
		                $PB_output .= ' <div class="image-wrapper shadow pad-5" style="height:'.$thumb_pic_height.';">';
					        $PB_output .= '<div class="title description">'.$title.'</div>';
		     	            if(isset($CFG->config['show_image_info']) && $CFG->config['show_image_info'] == 'NO') $PB_output .= '<div class="date description">Created on '.$date.'</div>';
			                $PB_output .= '<div class="thumb-wrapper shadow">'; 
	                            $PB_output .= '<div class="thumb">';
		                            $PB_output .= ' <a href="'.$src_img.'" class="albumpix" rel="albumpix" title="'.$title.'"><img src="'.$src_img.'" alt="'.$alt.'" width="'.$CFG->config['thumb_width'].'" title="'.$alt.'" /></a>';
				                $PB_output .= '</div>';
				            $PB_output .= '</div>'."\n";
						    $PB_output .= '<div class="clear">&nbsp;</div>';
					        $PB_output .= '<div class="description"><strong>'.$title.' Info</strong><br/><span>'.$info.'</span></div>';
				            if(isset($_SESSION['userRole']) && $_SESSION['userRole'] == 'superadmin')
						        $PB_output .= '<div class="link description"><a href="'.$edit_url.'" title="Edit : '.$image_files[$i][0].'">Rename</a>|<a href="'.$delete_url.'" title="Delete : '.$image_files[$i][0].'">Delete</a></div>';
				        $PB_output .= '</div>';
	                } else {
		                if( isset($image_files[$i][0]) ) $PB_output .= $image_files[$i][0];
             		}
                }
                $urlVars = $url_prefix."&amp;dir=".urlencode(DIR)."&amp;f_sortby=".$f_sortby."&amp;f_ascdsc=".$f_ascdsc;
				
				$PB_output .= '<div class="clear"></div>';
                $PB_output .= '<div align="center" class="paginate-wrapper">';
                    $UTIL->print_pagination($numPages,$urlVars,$currentPage);
                $PB_output .= '</div>';
            }
	    } 
	$PB_output .= '</div>';
?>