<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
	if(!isset($PB_output)) $PB_output=NULL;
	
	$i = $ci = "";
    $pic = ( isset($_GET['pic']) ? GetCommand("pic") : "" );
	$idir = GetCommand('dir');
    $page = ( isset($_GET["p"])  ? GetCommand("p") : 0 );
	$f_sortby = ( isset($_GET['f_sortby']) ? GetCommand("f_sortby") : "title" );
	$f_ascdsc = ( isset($_GET['f_ascdsc']) ? GetCommand("f_ascdsc") : "DESC" );
	$album = ( isset($_GET['album']) ? GetCommand("album") : "" );
	$GLOBALS['f_sortby'] = $f_sortby;
    $GLOBALS['f_ascdsc'] = $f_ascdsc;
	
	$url_prefix = SELF.'?mode='.MODE;
	if(isset($_GET['section'])) $url_prefix .='&amp;section='.$_GET['section'];
	if(isset($_GET['action'])) $url_prefix .='&amp;action='.$_GET['action'];
	if(isset($_GET['album'])) $url_prefix .='&amp;album='.$_GET['album'];
	$return_url = SELF.'?mode='.MODE;
	
    //$path = 'application/data/img/';
    $path = GALLERY_PATH;
    $path = $UTIL->Check_for_slash($path,true);
	
	$ignore  = array('.', '..','thumbs','Thumbs.db','photothumb.db','girlsxxx');
	$albums = array();
	$image_files = array();
	$random_pics = array();
	$image_sortby_array = array("title"=>"Name", "size"=>"Size","date"=>"Date");	
	foreach($image_sortby_array as $cat=>$v){
		$sortby_html[] = '<div class="sort-link left-1">&nbsp;|&nbsp;<span class="i left-1">'.(($f_sortby == $cat) ? "<strong>$v</strong>" : "$v").'</span>'.
		"<span class=\"left-1\"><a class=\"icon asc\" href=\"$url_prefix&amp;f_sortby=$cat&amp;f_ascdsc=ASC\" title=\"sort files by $cat ascending\"></a><a class=\"icon desc\" href=\"$url_prefix&amp;f_sortby=$cat&amp;f_ascdsc=DESC\" title=\"sort files by $cat descending\"></a></span></div>";
	}
	$PB_output .='<div class="content box">';
    $PB_output .='<div class="gallery push-center">';
	//-------------------------------------------

    if (!isset($_GET['album'])) {
        // display list of albums
		$albums = GetAlbums($path);
        usort($albums,'sort_file');
		
        if( count($albums) == 0 ) $PB_output .= '<div class="message user_status"><p>There are currently no albums.</p></div>';
        else {
		    $numPages = ceil( count($albums) / $CFG->config['albumsPerPage'] );
            if(isset($_GET['p'])) {
	            $currentPage = $_GET['p'];
                if($currentPage > $numPages) $currentPage = $numPages;
            } else $currentPage=1;
 
            $start = ( $currentPage * $CFG->config['albumsPerPage'] ) - $CFG->config['albumsPerPage'];
			$total_albums = count($albums);
			$sortby_htmo = implode(" ", $sortby_html);
	        
		    $PB_output .= '<div class="header"><span class="title">'.$CFG->config['site_title_full'].' - All Albums : <span class="i b">'.$total_albums.' '.(($total_albums > 1) ? ' Albums' :'Album').' found</span></div>';
			$PB_output .= '<div class="wrapperleft-1 grid-4"><div class="left-1 grid-1 head">PAGE &mdash;&nbsp;[ '.$currentPage.' ]&nbsp;&mdash;</div><div id="sortby" class="right-1 grid-2"><span class="left-1">Sort by: </span>'.$sortby_htmo.'</div></div>';
	        $PB_output .= '<div class="clear"></div>';
			$PB_output .= '<div class="hRule">&nbsp;</div>';
            
	        for( $i=$start; $i<$start + $CFG->config['albumsPerPage']; $i++ ) {
	            if( isset($albums[$i][0]) ) {
				    $edit_url = SELF.'?mode=admin&amp;section=manage&amp;action=edit&amp;filetype=galleries&amp;album='.$albums[$i][0].'&amp;id='.$albums[$i][1];
		            $delete_url = SELF.'?mode=admin&amp;section=manage&amp;action=delete&amp;filetype=gallery&amp;album='.$albums[$i][0].'&amp;id='.$albums[$i][1];
			
			        $PB_output .='<div class="album-wrapper shadow">'.
						'<a href="'.$url_prefix.'&amp;album='. urlencode($albums[$i][0]) .'" title="View images from -- '.$albums[$i][0].' folder"><div class="caption">'. cleanPageTitles($albums[$i][2]) .'</div></a>'.
						'<div class="album-thumb">'.
							'<a href="'.$url_prefix.'&amp;album='. urlencode($albums[$i][0]) .'" title="View images from -- '.$albums[$i][2].' photo album">'.
			                    '<img src="'. $albums[$i][9] .'" width="'.$CFG->config['thumb_width'].'" alt="'. $albums[$i][9] .'" />'.
						    '</a>'.	
							'<div class="clear-p2"></div>'.
						    '<div class="description">'. Truncate($albums[$i][8]) .'</div>'.
					    '</div>'.
						'<div class="clear-p2"></div>';
						if($roleID > 4) $PB_output .='<div class="link"><a href="'.$edit_url.'" title="Edit The Album : '.$albums[$i][0].'">Rename</a>&nbsp;|&nbsp;<a href="'.$delete_url.'" title="Delete The Album : '.$albums[$i][0].'">Delete</a></div>';
						//'<div class="spacer"></div>'.
						//'<a href="'.SELF.'?mode='.MODE.'&amp;album='. urlencode($albums[$i][0]) .'" title="View images from -- '.$albums[$i][2].' photo album">'.
						    //'<span class="caption">'. $albums[$i][2] .'</span>'.
						//'</a>'.
						$PB_output .='<div class="clear-p2"></div>'.
					'</div>'."\n";
		        } 
	        }
			//-----------------------------------------
			$urlVars = $url_prefix."&amp;album=".urlencode($album)."&amp;f_sortby=".$f_sortby."&amp;f_ascdsc=".$f_ascdsc;
			$PB_output .= '<div class="clear"></div>';
			$PB_output .= '<div class="paginate-wrapper" class="right- mini">There are : <strong>'.$total_albums.'</strong> album'.(($total_albums > 1) ? 's' :'').' | Totalling '. (($CFG->config['display_dirsize'] == 'YES') ? $UTIL->size('dir',$path) : '???').' in size | '. $UTIL->print_pagination($numPages,$urlVars,$currentPage).'</div>';
        }
    } else {
        // display photos in album
        $src_folder = $UTIL->Check_for_slash($path.$_GET['album'],true);
		$src_url = $UTIL->Check_for_slash($path.$_GET['album'],true);
		$image_files = GetAlbumsPhotos($src_folder,$src_url,true);
		usort($image_files,"sort_file");
			
        if ( count($image_files) == 0 ) $PB_output .= '<div class="message user_status"><p>Sorry to inform you, but the album '.$separator.$album.' appear&rsquo;s to be empty, no photos could be found!</p><br/><a href="'.SELF.'?mode=viewGallery">Return to all galleries</a></div>';
        else {
            $numPages = ceil( count($image_files) / $CFG->config['thumbsPerPage'] );
            if(isset($_GET['p'])) {
	            $currentPage = $_GET['p'];
                if($currentPage > $numPages) $currentPage = $numPages;
            } else $currentPage=1;
            
            $start = ( $currentPage * $CFG->config['thumbsPerPage'] ) - $CFG->config['thumbsPerPage'];
            $total_images = count($image_files);
			
			if(isset($CFG->config['show_image_info']) && $CFG->config['show_image_info'] == 'YES') $thumb_pic_height = 'auto';
	        else $thumb_pic_height = '120px';
	
            $PB_output .= '<div class="titlebar header">
                <div class="left-1"><span class="title">'. $_GET['album'] .' - <a href="'.$return_url.'">View All Albums</a></span></div>
                <div class="right-1">'.$total_images.' images</div>
            </div>';
			$PB_output .= '<div class="hRule">&nbsp;</div>';
			if(file_exists($Gallery_DB_File)) {
			    $PB_output .= '<div class="panel">';
			        $decode = GetAlbumInfo($album);
				    if(is_array($decode) && $decode['album_name'] == $album){
				        $PB_output .= '<div class="box2">';
						    $PB_output .= '<h3>'.$decode['album_title'].'</h3>';
						    $PB_output .= '<div class="description">'.$decode['album_description'].'</div>';
				        $PB_output .= '</div>';
					}
				$PB_output .= '</div>'; 
			}
			
            $PB_output .='<div class="clear"></div>';
            
			//if($CFG->config['js_gallery_plugin'] == 'colorbox' OR $CFG->config['js_gallery_plugin'] == 'aSlideshow'){
			if($CFG->config['js_gallery_plugin'] != 'default' && file_exists(PLUGIN_PATH.$CFG->config['js_gallery_plugin'].'/'.$CFG->config['js_gallery_plugin'].'.php')){
			    include( PLUGIN_PATH.$CFG->config['js_gallery_plugin'].'/'.$CFG->config['js_gallery_plugin'].'.php');
                $PB_output .= '<div class="clear"></div>';
                $PB_output .= '<div align="center" class="paginate-wrapper">';
                    $urlVars = $url_prefix."&amp;album=".urlencode($_GET['album']);
                    $UTIL->print_pagination($numPages,$urlVars,$currentPage);
                $PB_output .= '</div>';
				$PB_output .= '<div class="clear"></div>';
				$PB_output .= '<div class="hRule">&nbsp;</div>';
                $PB_output .= ' <div class="right-1 mini">This folder has : <strong>'.$total_images.'</strong> Images | Totalling '. $UTIL->size('dir',$src_folder).' in size</div>';
			} else if($CFG->config['js_gallery_plugin'] == 'default' || ($CFG->config['js_gallery_plugin'] != 'default' && !file_exists(PLUGIN_PATH.$CFG->config['js_gallery_plugin'].'/'.$CFG->config['js_gallery_plugin'].'.php'))){
			    if ($handle = opendir($src_folder)) {
	                $i = 1;
	                while (false !== ($file = readdir($handle))) {
		                if ($file != "." && $file != ".." && $file != "Thumbs.db" && !is_dir($src_folder.'/'.$file)) {
			                $img[$i] = $file;
			                if ($pic == $img[$i]) {
			    	            $ci = $i;
			                }
			                $i++;
		                }
	                }
	                closedir($handle);
		
	                $ti = $i - 1;
	                $pi = $ci - 1;
	                if ($pic == "") {
		                $ni = $ci + 2;
	                } else {
		                $ni = $ci + 1;
	                }
	                $prevNext = "";
	                if ($pi > 0) {
		                $piFile = $img[$pi];
		                $prevNext .= '<div class="prevNext prev"><a href="' . $_SERVER['PHP_SELF'] . '?action='.$action.'&amp;album='.urlencode($_GET['album']).'&amp;pic=' . $piFile . '#imgTop" title="show previous image">&#171;</a></div>';
	                } else $prevNext .= "<div class='prevNext prev'>&#171;</div>";
	                
	                $prevNext .= " <div class='prevNext separator'>|</div> ";
	                if ($ni <= $ti) {
		                $niFile = $img[$ni];
		                $prevNext .= '<div class="prevNext next"><a href="' . $_SERVER['PHP_SELF'] . '?action='.$action.'&amp;album='.urlencode($_GET['album']).'&amp;pic=' . $niFile . '#imgTop" title="show next image">&#187;</a></div>';
	                } else $prevNext .= '<div class="prevNext next">&#187;</div>';
	                
	                if ($pic == '') $pic = $img[1];
                }
			
                $gallery_pic = $src_folder.'/'.$pic;
			    //$PB_output .='<style>html,body {background:#ccc url('.$gallery_pic.') ;}background-size{100% 100% !important}.img {font-size: 0.7em;border: 1px solid #ccc;text-align: center;padding: 5px 2px;}</style>';
                $PB_output .='<center><table id="imgTop" class="gallery-table" border="0" align="center">';
	                $PB_output .='<tr align="center"><td><p class="p">You are currently viewing '.$separator.$pic.'</p></td></tr>';
					$PB_output .=' <tr align="center"><td class="prevNext-wrapper">'.$prevNext.'</td></tr>';
	                $PB_output .=' <tr align="center"><td><div class="image-frame">';
					    $PB_output .=' <div class="image-info">'.Image_info($gallery_pic).'</div>';
					    $PB_output .='<img style="max-width:'.$image_max_width.';" src="'.$gallery_pic.'" alt=" '.$gallery_pic.'" border="0" /><a href="'.$return_url.'"><div class="return-btn" title="Return to gallery" ></div></a><div class="zoom-btn" title="Zoom photo in">&nbsp;</div>';
					$PB_output .=' </div></td></tr>';
                    $PB_output .=' <tr align="center"><td class="prevNext-wrapper">'.$prevNext.'</td></tr>';
				$PB_output .='</table></center>';
			
	            if (!$image_file = imagecreatefromstring(file_get_contents($gallery_pic))){
		            exit;
	            }
	            $original_img_width = imagesx($image_file).'px';
	            $original_img_height = imagesy($image_file).'px';
	            $zoom_script = "";
	            $zoom_script .= "<script type=\"text/javascript\">
                    $(document).ready(function(){
	                    var move = -1;\n";
                        if($original_img_width <= '150') $zoom_script .= 'zoom = 0.5;';
			            else $zoom_script .= 'zoom = 1.5;';
			            
	                    $zoom_script .= "$('#side-panel').fadeOut(100).css({'display':'none'});";
						$zoom_script .= "$('.image-frame').hover(function() {
                            $(this).find('div.image-info').stop(false,true).fadeIn(800);
                            $(this).find('div.zoom-btn').stop(false,true).fadeIn(800);
                            $(this).find('div.return-btn').stop(false,true).fadeIn(800);
                        },
                        function() {
                            $(this).find('div.image-info').stop(false,true).fadeOut(1000);
                            $(this).find('div.zoom-btn').stop(false,true).fadeOut(1000);
                            $(this).find('div.return-btn').stop(false,true).fadeOut(1000);
                        });
		                $('.zoom-btn').click(function(){
			                width = $('.image-frame').width() * zoom;
                            height = $('.image-frame').height() * zoom;
	                        if($('.image-frame img').hasClass('active')) {
				                $('.image-frame img').removeClass('active');
				                $('.gallery').css({'overflow' : 'hidden'});
		                        $('.image-frame img').animate({'width':'$original_img_width', 'height':'$original_img_height', 'top':'35px', 'left':'35px','transition':'width 1.45s, height 1.45s ease-in-out'}, {duration:600});
				            }else{
		                        $('.image-frame img').addClass('active');
				               	$('.gallery').css({'overflow' : 'auto'});
					            //$('.image-frame img').animate({'width':$('.image-frame').width(), 'height':$('.image-frame').height(), 'top':'10px', 'left':'10px','transition':'width 1.45s, height 1.45s ease-in-out'}, {duration:600}); 
				            	$('.image-frame img').animate({'width':width, 'height':height, 'top':move, 'left':move,'transition':'width 1.45s, height 1.45s ease-in-out'}, {duration:600});
				            }
	                    });
	                });
                </script>";
	            $PB_output .= $zoom_script; 
		    }
        }
	}
	$PB_output .= '</div>';
	$PB_output .= '</div>'; 
	if(ACTION == 'galleries') return $PB_output;
	else echo $PB_output;
?>