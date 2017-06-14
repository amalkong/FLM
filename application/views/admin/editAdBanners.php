<?php
    if (($roleID < 4))  {
       echo'<div class="message error"><p>Access Denied!,You are not authorised to access this section, your user role does not permit you to add or edit any advertisement banner.</p></div>';
	   exit;
    }
?>
<div class="content box">
<?php
    require(SYSTEM_PATH.'functions/Image_functions.php');
    $PB_output = NULL;
	$filesuffix = NULL; // declare variable for duplicated filenames
	$image_new_name = NULL; // declare variable for image name
	$descmax =255; #set max characters variable. "max 4000 characters" for long description/details field
	
    $ads_folder = DATA_PATH.'ads/';
    if (isset($_POST['submit']) AND isset($_GET['mode']) AND $_GET['mode']=="admin" AND isset($_GET['section']) AND $_GET['section']=="addbanner" OR $_GET['section']=="updatebanner" AND isset($_GET['c']) AND $_GET['c']=="ok") { 
        $PB_output .= '<h3>Creating New Ad Banner</h3>';
		if(isset($_POST['ad_text']) AND isset($_POST['ad_link'])){
		    if (file_exists($Ads_DB_File)) {
		        $fp = @fopen($Ads_DB_File, 'r');  
		        $array = explode("\n", fread($fp, filesize($Ads_DB_File))); 
			    //Are there any banner?
		        //if ($array ==0) $id = 1;
		        //else $id = count($array) + 1;
			    $listed = count($array);
		    	for($x=0;$x<$listed;$x++) {		// start loop, each line of file
				    $temp = explode("}",$array[$x]);	// explode the line and assign to temp
                    $ad_id = $x + 1;
			    }
		    } else $ad_id = 0;
		
		    $ad_text = $_POST['ad_text'];
	        $ad_pic_alt =  $_POST['ad_pic_alt'];
		    $ad_link = $_POST['ad_link'];
	        $show_ad = isset($_POST['show_ad']) ? $_POST['show_ad'] : 'NO';
			
	        if (strlen($ad_text)<$descmax) {
    		    //if long description IS NOT too long, go on executing... cleaning/depurate input
		        //start processing banner
			    if(isset($_FILES['ad_pic']['name']) AND $_FILES['ad_pic']['name'] != NULL ){
	                 $img= $_FILES['ad_pic']['name']; // image img
		            //$error= $_FILES['userfile']['error'];
	                $tempname= $_FILES['ad_pic']['tmp_name'];
	                // echo "<br /><br /><br />$img - err $error - temp: $tempname<br /><br /><br />";
	    
                    $PB_output .= "<p><b>Now processing the banner image...</b></p>";
                    $PB_output .= "<p>Original filename: <i>$img</i></p>";
                    $img_ext=explode(".",$img); // divide filename from extension
			        $n = strrpos($img,".");
                    $img_ext[1] = substr($img,$n,strlen($img)-$n);
                    $img_ext[1] = str_replace('.','',$img_ext[1]);
                    //processing img extension

	                if($rename_upload_file){
	                   $filenamechanged = sanitize_filename($ad_pic_alt); #enable this to have a very strict filename policy
				    } else $filenamechanged = sanitize_filename($img_ext[0]);
					
				    // get size ( [0]=width, [1]=height )
                    $ad_size = getimagesize($tempname);
                    if ( !$ad_size ) {
                        $PB_output .="<div class='message error'>Could not get input image size</div>";
                        return false;
                    }
					
	                if($ad_size[0] <= 300) $size_dir = 'small';
	                else $size_dir = 'large';
					
			        $uploadFile = $ads_folder.$size_dir.'/'. $filenamechanged.".".$img_ext[1] ;
	                while (file_exists("$uploadFile")) { //cicle: if file already exists add an incremental suffix
		                $filesuffix++;
		                if($PB_CONFIG['debug'] == 'YES') $PB_output .= "$filesuffix"; //debug
		                $uploadFile = $ads_folder . $filenamechanged . $filesuffix.".".$img_ext[1] ;
	                }
	                $PB_output .= "New Name: <i>$filenamechanged$filesuffix.$img_ext[1]</i><br>";
	                $uploadFile == NULL ;

		            //creating .txt file associated to logo
		            if (is_uploaded_file($tempname)) {
			            $PB_output .= "<p><b>Image present: processing...</b></p>";
					    // control accepted image format
			            if ($img_ext[1]=="jpg" OR $img_ext[1]=="jpeg" OR $img_ext[1]=="gif"OR $img_ext[1]=="png" OR $img_ext[1]=="JPG" OR $img_ext[1]=="JPEG" OR $img_ext[1]=="GIF"OR $img_ext[1]=="PNG") {
				            //move file from the temp directory to the upload directory
				            if (move_uploaded_file($tempname, $uploadFile)) {
						        // Assign a new name to the image
							    $image_new_name = "$filenamechanged$filesuffix.$img_ext[1]";
				                $PB_output .= "<p><font color=\"green\">Image uploaded successfully.</font></p>"; // If upload is successful.
				            } else { // if IMAGE upload is not successful
				                $image_new_name = NULL;
				                $PB_output .= "<p><font color=\"red\">Error: image NOT uploaded! (ignored)</font></p>";
				            }
							// Images can be resized, but use this function at your own risk.
							// Nothing fatal will happen, it's just the images don't resize properly to my liking
							//create the '120','300' or '900' versions of the image
							//$resized_image_folder = $ads_folder.$size_dir.'/';
							//$PB_output .= resize_image($ads_folder.$image_new_name, $resized_image_folder.$image_new_name,$ad_size,120);
			            } else {
				            $image_new_name = NULL; // if the image extension is not valid: IGNORE the image
				            $PB_output .= "<p>Not valid image extension: Accepted extensions are: jpg, gif, png.</p>";
			            }
		            } else { //If upload is not successfull
		                $PB_output .= "<p><b><font color=\"red\">FILE ERROR : Upload Failed</font></b></p>";
		                $PB_output .= "<p><b>The main reasons for the failure of upload could be:</b></p>";
		                $PB_output .= "<p> - You didn't assign writing permission to the ads folder and the uploaded file can't be saved on the server.</p>";
		                $PB_output .= "<p> - Your file is bigger than upload max filesize on your server.</p>";
		                $PB_output .= "<p><b>Useful information for debugging:</b> <a href=\"?mode=admin&amp;section=systeminfo\">Your server configuration</a></p>";
		                $PB_output .= "<p><form><input type=\"button\" value=\"Back\" onClick=\"history.back()\"></form></p>";
                    }
			    }
			    //creating .txt file associated to article
			    $fileName = $Ads_DB_File;
				
                if (strlen($fileName)>0){
		            //Save the title and content status.
				    $ad_pic = sanitize($image_new_name);
				    $ad_pic_alt = sanitize($ad_pic_alt);
				    $ad_link = sanitize($ad_link);
				    $ad_text = sanitize($ad_text,false);
					$ad_size = sanitize($size_dir);
					
				    $line = "$ad_id:$image_new_name:$ad_pic_alt:$ad_link:$ad_text:$ad_size:$show_ad";
		            $file = @fopen($fileName,"a");
			        // write the txt file
		            if ($file != false){
			            //fwrite($file,"\n");
			            fwrite($file,$line);
			            fclose($file);
				 	
				        // If upload is successful.
		                $PB_output .= '<div class="message"><p><b><font color="green">Ad Banner saved successfully.</font></b></p></div>';
		                $PB_output .= '<div class="message unspecific"><p><a href="'.SELF.'?mode=home">Go to the homepage</a> - <a href="'.SELF.'?mode=admin&amp;section=addbanner">Create another banner</a></p></div>';
		            } else $PB_output .= '<div class="message error"><p><b>Wrong file name!</b></p></div>'; // If upload is un-successful.
		             // end creation txt file
			    } else $PB_output .= '<div class="message error"><p><b><font color="green">File not found!</font></b></p></div>'; // If upload is un-successful.
        
            } else {
				$PB_output .= "<div class=\"message user_status\"><b>The <b>Ad Text</b> is too long...</b><p>Ad Text can be up to $descmax characters - Actual Length: <font color=red>".strlen($ad_text)."</font> characters.</p>
		       <form><input type=\"button\" value=\"Back\" onClick=\"history.back()\"></form></div>";
			}
        } else {
		    //if file, text or link not present...
			$PB_output .= '<div class="message error">';
		    if (!isset($_POST['ad_text']) || $_POST['ad_text'] != NULL) $PB_output .= '<p>Error: No <b>Ad Text</b>, You must supply ad text.</p>';
	        else if (!$_POST['ad_link'] || $_POST['ad_link'] != NULL) $PB_output .= '<p>Error: No <b>link</b> present. You must supply an ad link.</p>';
		    
		    $PB_output .= '<p><form><input type="button" value="Back" onClick="history.back()"></form></p></div>';
		}
    } else {
	    // Determine max upload file size
		$showmin = (round($max_size_in_kb / 1048576 * 100) / 100);
		//GetPageHeader('Upload New Media','h2',$background_class);
        if(SECTION == 'addbanner')$form_url = '?mode=admin&amp;section=addbanner&amp;c=ok';
        else if(SECTION == 'updatebanner')$form_url = '?mode=admin&amp;section=updatebanner&amp;c=ok';
        
		$PB_output .= '<form action="'.$form_url.'" method="POST" enctype="multipart/form-data" name="uploadform" id="uploadform">'."\n";
            $PB_output .= '<fieldset>
			    <legend><b>Main information (required):</b></legend>';
				if ($showmin!=NULL and $showmin!="0") $PB_output .= '<p><span class ="admin_hints">Your site configuration allows you to upload files up to '.$showmin.'MB</span></p>';
				
				$PB_output .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.$max_size_in_kb.'">';
			    
				$PB_output .= '<span class="admin_hints">Upload the actual banner/image</span><br/>';
				$PB_output .= '<label for="ad_pic">Ad Banner Image*</label><input name="ad_pic" id="ad_pic" type="file" required />';
		        $PB_output .= '<br />';
				
				$PB_output .= '<span class ="admin_hints">Set the &lsquo;alt&rsquo; attribute of the banner image, otherwise the default image name will be used.</span><br />
				<label for="ad_pic_alt">Ad Banner &lsquo;alt&rsquo; Name</label><br />
				<input name="ad_pic_alt" type="text" id="ad_pic_alt" size="50" maxlength="50" required />
			    <br />';
					
				$PB_output .= '<label for="ad_text">Ad Text*</label>
			    <span class ="admin_hints">Type infomation regarding the  new banner in the box below(max 255 characters)</span><br />
			    <input name="ad_text" id="ad_text" type="text" value="" onKeyDown="limitText(this.form.ad_text,this.form.countdown,255);" onKeyUp="limitText(this.form.ad_text,this.form.countdown,255);" size="50" maxlength="255" required />
			    <br />
			    <span class ="admin_hints"><input class="grid-10" name="countdown" type="text" value="255" class ="admin_hints" size="3" readonly /> remaining characters.</span> 
			    <br />';
					
			    $PB_output .= '<label for="title">Ad Link*</label>
			    <input id="ad_link" type="text" name="ad_link" size="50" maxlength="255" required /><br />';
			    
				$PB_output .= '<span class="admin_hints">Select whether to show the banner or not</span><br/>
                <select name="show_ad">';
                    $PB_output .= "<option>-- would you like to enable or disable banner --</option>\n";
                    $PB_output .= "<option value=\"YES\">Yes</option>\n";
                    $PB_output .= "<option value=\"NO\">No</option>\n";
                $PB_output .= '</select><br/>';
				
			    $PB_output .= '<p class="admin_hints">Fields marked with * are required.</p>';
			$PB_output .= '</fieldset>';
			
			$PB_output .= '<center><input type="submit" name="submit" value="Create Banner" onClick="showNotify(\'Creating Banner...\');" class="btn-submit" /></center><br />';
	    $PB_output .= '</form>';
	}
	$PB_output .= '<span><div id="status_notification"></div></span>';
	echo $PB_output;
?>
</div>