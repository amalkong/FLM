<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    if (($roleID < 3))  {
       echo'<div class="box"><div class="message error"><p>Access Denied!,You are not authorised to access this section, your user role does not permit you to add or edit news categories</p></div></div>';
	   include(BASE_PATH.'footer.php');
	   exit;
    }
    if(!isset($PB_output)) $PB_output = NULL;
    $filenamechanged = NULL; // declare variable for duplicated filenames
	$image_new_name = NULL; // declare variable for image name
	
	// Determine max upload file size through php script reading the server parameters (and the form parameter specified in config.php. We find the minimum value: it should be the max file size allowed...
	$max_upload_form_size_MB = $max_upload_size/1048576; // convert max upload size set in config.php in megabytes
	$max_upload_form_size_MB = round($max_upload_form_size_MB, 2);
	$showmin = min($max_upload_form_size_MB, ini_get('upload_max_filesize')+0, ini_get('post_max_size')+0); // min function
	// Note: if I add +0 it eliminates the "M" (e.g. 8M, 9M) and this solves some issues with the "min" function
	
	$img_dir = CATEGORY_IMAGE_PATH;
	$PB_output .= '<div class="content">';
	    $PB_output .= '<div class="box">';
    if(isset($_POST['submitBtn']) AND isset($_GET['section']) AND $_GET['section'] == 'addcategory' AND isset($_GET['c']) AND $_GET['c'] == 'ok'){
	    if(isset($_POST['catName'])){
			$PB_output .= '<div class="header"><h2>Results</h2></div>';
			if (file_exists($Category_DB_File)) {
		        $fp = @fopen($Category_DB_File, 'r');  
		        $array = explode("\n", fread($fp, filesize($Category_DB_File))); 
			    $listed = count($array);
			    for($x=0;$x<$listed;$x++) {	// start loop, each line of file
				    $temp = explode(":",$array[$x]); // explode the line and assign to temp
                    $cat_id = $x + 1;
			    }
				fclose($fp);
		    } else $cat_id = 0;
			$cat_name = strip_tags(stripslashes($_POST['catName']));
			$sanitized_name = sanitize_filename($_POST['catName']);
			//-------------------------------//processing img extension
			if(isset($_FILES['catImage']['name']) AND $_FILES['catImage']['name'] !=NULL ){
	            $cat_img= $_FILES['catImage']['name']; // image img
		        //$error= $_FILES['userfile']['error'];
	            $tempname= $_FILES['catImage']['tmp_name'];
	            // echo "<br /><br /><br />$img - err $error - temp: $tempname<br /><br /><br />";
	            
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
	            $PB_output .= "New Name: <i>$filenamechanged .".".$img_ext[1]</i><br>";
	            //$uploadFile == NULL ;

		       if (is_uploaded_file($tempname)) {
			        $PB_output .= "<p><b>Image present: processing...</b></p>";
					// control accepted image format
					
		       	    if(!$UTIL->isValidExt('.'.$img_ext[1], $ifxs)){
		                $PB_output .= '<div class="message error"><p>FAILED: '. $_FILES['catImage']['name'] .' Not a valid image extension : Accepted extensions are: jpg, gif, png.</p></div>'."\n";
		    	    } elseif($_FILES['catImage']['size'] > ($max_size_in_kb*1024)){
		                $PB_output .= '<div class="message error"><p>FAILED: '. $_FILES['catImage']['name'] .' File size too large.</p></div>'."\n";
			        } elseif(file_exists($uploadFile)){
			            $PB_output .= '<div class="message error"><p>FAILED: a file name '. $filenamechanged .".".$img_ext[1] .' already exists.</p></div>'."\n";
			        } else {
			            //move file from the temp directory to the upload directory
				        if (move_uploaded_file($tempname, $uploadFile)) {
						        // Assign a new name to the image
							    $image_new_name = "$filenamechanged.$img_ext[1]";
								$PB_output .= '<div class="message"><p>UPLOADED: '. $image_new_name ."</p></div>\n"; // If upload is successful.
				        } else { // if IMAGE upload is not successful
				            $image_new_name = NULL;
							$PB_output .= '<div class="message error"><p>FAILED: '. $_FILES['catImage']['name'] .' ERROR: Undetermined.</p></div>'."\n";
				        }
			        }
		        } else { //If upload is not successfull
		                $PB_output .= "<div class=\"message error\">";
						    $PB_output .= "<p><b><font color=\"red\">FILE ERROR : Upload Failed</font></b></p>";
		                    $PB_output .= "<p><b>The main reasons for the failure of upload could be:</b></p>";
		                    $PB_output .= "<p> - You didn't assign writing permission to the article image folder and the uploaded file can't be saved on the server.</p>";
		                    $PB_output .= "<p> - Your upload image is bigger than upload max filesize on your server.</p>";
		                    $PB_output .= '<p><b>Useful information for debugging:</b> <a href="'.SELF.'?mode=admin&amp;section=systeminfo">Your server configuration</a></p>';
						$PB_output .= '</div';
						$PB_output .= '<div class="message unspecific"><p><input type="button" value="Back" onClick="history.back()"></p></div';
			    }
			} else $PB_output .= '<div class="message user_status"><p>Category image wasn&rsquo;t detected!</p></div>';
				
			//----------------------------------------
			$line = "$cat_id:$cat_name:$image_new_name";
			$fp = fopen ($Category_DB_File, "a");
			if ($fp != false){
			    fwrite ($fp, "\n");
			    fwrite ($fp, $line);
			    fclose ($fp);
			    $PB_output .='<div class="message"><p>Category has been added successfully.</p></div>';
			    $PB_output .='<div class="message unspecific"><a href="'.SELF.'?mode=admin&amp;section=addcategory">add another category</a> |or| <a href="'.SELF.'?mode=home">return to home page</a> |or| <a href="'.SELF.'?mode=admin">return to admin index</a></div>';
			} else $PB_output .='<div class="message"><p><strong>ERROR:</strong> , Category creation un-successfully, wrong file name!.</p></div>';
		
		} else {
		    $PB_output .'<div class="message user_status">';
		        if (!isset($_POST['catName'])) $PB_output .= "Category name not present!";
		        $PB_output .="<p>Please go back and fill in the form properly, <a href=\"javascript:history.back()\">back</a>!</p>";
	        $PB_output .='</div>';
		}
	} else {
		$PB_output .='<div class="box2">';
		    $PB_output .= '<h2>Add New Category</h2>';
		    $PB_output .='<form id="loginform" action="'.SELF.'?mode=admin&amp;section=addcategory&amp;&amp;c=ok" method="post" enctype="multipart/form-data">
                <fieldset><legend>Enter Required Information</legend>';
				if ($showmin!=NULL AND $showmin!="0") $PB_output .= '<p><span class ="admin_hints">Your server configuration allows you to upload files up to '.$showmin.'MB</span></p>';
				$PB_output .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.$max_upload_size.'">';
			    
			            $PB_output .='<table class="grid-4">
                            <tr><td>Category Name*:</td><td><input class="grid-3" name="catName" type="text" maxlength="30" value="" required /></td></tr>
                            <tr><td align="right">Category Image:</td><td><input type="file" name="catImage" /></td></tr>
							<tr><td colspan="2"><center><input class="save" type="submit" name="submitBtn" value="Save" onClick="showNotify(\'Saving....new....category...name\');" /><a href="'.SELF.'?mode=home?>" class="cancel" title="Cancel and return to home page" >Cancel</a><input type="reset" value="Reset" /></center></td></tr>
                        </table>
                        <p class="admin_hints">Fields marked with * are required.</p>
					</fieldset>
            </form>';
        $PB_output .='</div>';
	}
	$PB_output .='</div>';
	$PB_output .='</div>';
	echo $PB_output;
?>