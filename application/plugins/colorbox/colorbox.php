<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    define('COLORBOX_SKIN','example3'); // options : -- default,example1,example2,example3,example4,example5,example6
    define('COLORBOX_URL', PLUGIN_URL.'colorbox/');
	define('COLORBOX_CSS_URL', COLORBOX_URL.'css/');
	define('COLORBOX_JS_URL', COLORBOX_URL.'js/');
	
	$PB_output .= '<style type="text/css">
	#colorbox a,.cbox a{background:normal !important;border:0 !important;-webkit-transition: all 1s !important;-o-transition: all 1s !important;-moz-transition: all 1s !important;}
    
	</style>';
    $PB_output .= '<script type="text/javascript" src="'.COLORBOX_JS_URL.'jquery.colorbox-min.js"></script>'."\n";
    $PB_output .= '<link href="'.COLORBOX_URL.COLORBOX_SKIN.'/colorbox.css" media="screen" rel="stylesheet" type="text/css" />'."\n";

    $PB_output .= '<script type="text/javascript">'.
    '$(document).ready(function(){
		$(".albumpix").colorbox({rel:"albumpix"});
		$("ins").append("<em></em>");
	    $(".albumpix").hover(function() {
            var image = $(this).attr("href");
            var title = $(this).attr("title");
            var description = $(this).attr("content");
			$("ins em").html(" (" + title + ")");
            /* // $("#preview-gallery").hide();
            $("#preview-gallery").fadeIn("slow");
            $("#title").html(title);
            $("#description").html(description);*/
            return false;
	    }); 
    });'.
    '</script>';

	$PB_output .=  '<div class="hRule">&nbsp;</div>';
	$PB_output .= '<ins>&nbsp;</ins>'."\n";
    $PB_output .= '<div class="clear"></div>';
    
    for( $i=$start; $i<$start + $CFG->config['thumbsPerPage']; $i++ ) {
		if( isset($image_files[$i][0]) && is_file( $src_folder . $image_files[$i][0] ) ) {
			$info = Image_info_basic($src_folder .$image_files[$i][0]);
			$date = $image_files[$i][5];
			$title = strlen($image_files[$i][2]) > 18 ? substr($image_files[$i][2],0,16)."..." : $image_files[$i][2];
			$title = cleanPageTitles($title); 
			$src_img = $src_url.$image_files[$i][0];
			$src_img_thumb = $src_url.'thumbs/'.$image_files[$i][0];
			$alt = $title . " -|- Last modified: " . $date;
		    $edit_url = SELF.'?mode=admin&amp;section=manage&amp;action=edit&amp;filetype=albumPic&amp;album='.$album.'&amp;pic='.$image_files[$i][0];
		    $delete_url = SELF.'?mode=admin&amp;section=manage&amp;action=delete&amp;filetype=albumPic&amp;album='.$album.'&amp;pic='.$image_files[$i][0];
			
		    $PB_output .= ' <div class="image-wrapper shadow pad-5 cbox" style="height:'.$thumb_pic_height.';">';
				$PB_output .= '<div class="title">'.$title.'</div>';
		     	
				if(isset($CFG->config['show_image_info']) && $CFG->config['show_image_info'] == 'NO') $PB_output .= '<div class="date description">Created on '.$date.'</div>';
			    
				$PB_output .= '<div class="thumb-wrapper shadow">'; 
	                $PB_output .= '<div class="thumb"><a href="'.$src_img.'" class="albumpix" rel="albumpix" title="'.$title.'"><img src="'.$src_img.'" alt="'.$alt.'" width="'.$CFG->config['thumb_width'].'" title="'.$alt.'" /></a></div>';
				$PB_output .= '</div>'."\n";
				
				$PB_output .= '<div class="spacer">&nbsp;</div>';
				$PB_output .= '<div class="description"><strong>'.$title.' Info</strong><br/><span>'.$info.'</span></div>';
				
				if(isset($_SESSION['userRole']) && $_SESSION['userRole'] == 'superadmin') $PB_output .= '<div class="link description"><a href="'.$edit_url.'" title="Edit : '.$image_files[$i][0].'">Rename</a>|<a href="'.$delete_url.'" title="Delete : '.$image_files[$i][0].'">Delete</a></div>';
			
			$PB_output .= '</div>';
	    } else {
		    if( isset($image_files[$i][0]) ) $PB_output .= $image_files[$i][0];
        }
    }
?>