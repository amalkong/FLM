<?php
    $path = $UTIL->Check_For_Slash($path,true);
    $return_url = '<a href="'.SELF.'?mode='.MODE.'&amp;section='.SECTION.'">Return</a>';
    
	$PB_output .= '<div class="grid-4 panel">';
		if (!isset($_POST["rename"])) {
		    $PB_output .=' <p class="action">It is recommended that you exclude certain characters, mainly dots[.], from the name you are renaming the orignal file to.<br>Rename : '.$file.'br>file path : '.$path.'</p>';
	    	$PB_output .='<div class="">'.
	     		'<form action="" method="post" name="ren">'.
			    	'<input type="hidden" name="action" value="rename" />'.
			    	' From <input type="text" name="orignm" value="'.$file.'" />'.
					' to <input type="text" name="newnm" value="" />'.
				    ' <input class="save" type="submit" name="rename" value="Rename" />'.
			    '</form>'.
		    '</div>';
		} else if (isset($_POST["rename"])) {
		    if (isset($_POST["orignm"]) && isset($_POST["newnm"]) ) {
    	        //Rename File
			    $orignalname = $_POST['orignm'];
			    $new = explode('.',$_POST['orignm']);
				$newname = sanitize($_POST['newnm']);
				$newname = sanitize_filename($_POST['newnm']);
				$image_new_name = $newname.'.'.$new[1];
	            if (preg_match('(\/)', $_POST["orignm"])==1 || preg_match('(\/)', $_POST["newnm"])==1) die("ERROR: Working out of directory is forbidden.".$return_url);
	            elseif (!file_exists($path.$_POST["orignm"])) $PB_output .= "ERROR: The ".$fileType.$separator.$_POST["orignm"]." does not exist!".$return_url;
	            elseif ($_POST["newnm"] == "" || file_exists($path.$_POST["newnm"])) $PB_output .= "ERROR: The new ".$fileType." ".$_POST["newnm"]." already exists or does not have a valid name.".$return_url;
	            else {
				   $PB_output .= $UTIL->renameFile($path.$orignalname,$path.$image_new_name);
				   if (file_exists($path.'thumbs/'.$_POST["orignm"])) $PB_output .= $UTIL->renameFile($path.'thumbs/'.$orignalname,$path.'thumbs/'.$image_new_name);
				}
	        }
	    }
	$PB_output .='</div>';
?>