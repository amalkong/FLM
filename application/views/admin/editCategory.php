<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    if (($roleID < 3))  {
       echo'<div class="box"><div class="message error"><p>Access Denied!,You are not authorised to access this section, your user role does not permit you to add or edit news categories</p></div></div>';
	   include(BASE_PATH.'footer.php');
	   exit;
    }
    $filenamechanged = NULL; // declare variable for duplicated filenames
	$image_new_name = NULL; // declare variable for image name
	$img_dir = CATEGORY_IMAGE_PATH;
	
    if(isset($_POST['submitBtn']) AND isset($_GET['action']) AND $_GET['action'] == 'edit' AND isset($_GET['c']) AND $_GET['c'] == 'ok'){
	    if(isset($_POST['catName']) && isset($_POST['id']) >= 0 ){
			$PB_output .= '<h3>Result</h3>';
			$id = $_POST['id'];
			$cat_id = $_POST['id'];
			$cat_name = strip_tags(stripslashes($_POST['catName']));		
			$cat_img = strip_tags(stripslashes($_POST['catImage']));
			$sanitized_name = sanitize_filename($_POST['catName']);
			//-------------------------------
			//processing img extension
            $PB_output .= "<p><b>Now processing the category image...</b></p>";
            $PB_output .= "<p>Original filename: <i>$cat_img</i></p>";
            $img_ext=explode(".",$cat_img); // divide filename from extension
			$n = strrpos($cat_img,".");
            $img_ext[1] = substr($cat_img,$n,strlen($cat_img)-$n);
            $img_ext[1] = str_replace('.','',$img_ext[1]);
            
	        //if img extension is accepted, go on.... img name depuration!!!! Important... By default Phot_ex uses a "strict" depuration policy (just characters from a to z and numbers... no accents and other characters).
	        //if (STRICTFILENAMEPOLICY == "YES") $img_ext[0] = renamefilestrict ($img_ext[0]); #enable this to have a very strict filename policy
	            
	        $filenamechanged = $sanitized_name;#replace certain characters with underscores
			$uploadFile = $img_dir . $filenamechanged.".".$img_ext[1] ;
	        $image_new_name = $filenamechanged .".".$img_ext[1] ;
	        $PB_output .= "New Name: <i>$image_new_name</i><br>";
	        
			//----------------------------------------
			$fp = @fopen($Category_DB_File, 'r');
			$array = explode("\n", fread($fp, filesize($Category_DB_File))); 
			for($x=0;$x<sizeof($array);$x++) {	// start loop, each line of file
				$temp = explode(":",$array[$x]); // explode the line and assign to temp
		        $line[$x] = "$temp[0]:$temp[1]:$temp[2]";
			}
			$line[$id] = "$cat_id:$cat_name:$image_new_name";
			sort($line);
			$fp = fopen ($Category_DB_File, "w");
			if ($fp != false){
			    fwrite ($fp, $line[0]);
			    for($i=1; $i<sizeof($line);$i++){
				    fwrite ($fp, "\n");
				    fwrite ($fp, $line[$i]);
			    }
			    fclose ($fp);
			    $PB_output .='<div class="message"><p>Category has been edited successfully.</p></div>';
			    $PB_output .= $UTIL->renameFile($img_dir.$cat_img,$uploadFile);
			} else $PB_output .='<div class="message"><p><strong>ERROR:</strong> , Category edit un-successfully, wrong file name!.</p></div>';
		
		} else {
		    $PB_output .'<div class="message user_status">';
		        if (!isset($_POST['catName'])) $PB_output .= "Category name not present!";
		        $PB_output .="<p>Please go back and fill in the form properly, <a href=\"javascript:history.back()\">back</a>!</p>";
	        $PB_output .='</div>';
		}
	} else {
        if($_GET['id'] >= 0){
		    $id = $_GET['id'];
		    $fp = @fopen($Category_DB_File, 'r');
		    $array = explode("\n", fread($fp, filesize($Category_DB_File))); 
		    for($x=0;$x<sizeof($array);$x++) {	// start loop, each line of file
		        $temp = explode(":",$array[$x]); // explode the line and assign to temp
		        $line[$x] = "$temp[0]|$temp[1]|$temp[2]";
		    }
 
		    $mod = explode("|", $line[$id]);

		    $PB_output .='<div class="box2">';
		        if(ACTION == 'edit') $PB_output .= '<h3>Editing The Category'.$separator.$mod[1].'</h3>';
		        $PB_output .='<form id="loginform" action="'.$url_prefix.'&amp;action=edit&amp;filetype=categories&amp;category='.$mod[1].'&amp;id='.$mod[1].'&amp;c=ok" method="post" name="registerform">
                    <fieldset><legend>Enter Required Information</legend>
						<input type="hidden" name="id" value="'.$mod[0].'" />
			            <table class="grid-4">
                            <tr><td>Category Name*:</td><td colspan="2"><input class="grid-3" name="catName" type="text" maxlength="30" value="'.$mod[1].'" required /></td></tr>
                            <tr><td align="right">Category Image:</td>
					         	<td><input type="text" name="catImage" value="'.$mod[2].'" readonly /></td>
						        <td align="center"><div id="avatar_box_image" style="cursor:pointer;"><img src="'.CATEGORY_IMAGE_URL.$mod[2].'" name="showAvatar" onclick="nextAvatar();" width="100px" height="50px" title="click to show the next avatar" /></div></td>
					        </tr>
							<tr><td colspan="3"><center><input class="save" type="submit" name="submitBtn" value="Save" onClick="showNotify(\'Saving....new....category...name\');" /><a href="'.SELF.'?mode=home?>" class="cancel" title="Cancel and return to home page" >Cancel</a><input type="reset" value="Reset" /></center></td></tr>
                        </table>
                        <p class="admin_hints">Fields marked with * are required.</p>
					</fieldset>
                </form>';
            $PB_output .='</div>';
	    } else $PB_output .= '<div class="message user_status"><p>No ID specified, please <a href="'.SELF.'?mode=profiles">select an item</a> to be edited first!</p></div>';
	}
?>