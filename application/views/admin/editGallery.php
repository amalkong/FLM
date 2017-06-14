<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    if (($roleID < 3))  {
       echo'<div class="message error"><p>Access Denied!,You are not authorised to access this section, your user role does not permit you to upload files.</p></div>';
	   exit;
    }
   
    if (!isset($PB_output)) $PB_output = NULL;
	$first_file = NULL;
	$longdescmax =500; #set max characters variable,"max 500 characters" for long description field

	$gallery_list = GetDirContents(GALLERY_PATH,'dirs');
	if(count($gallery_list) > 0 ) $first_file = $gallery_list[0];
	
	$selected_gallery = isset($_GET['album']) ? $_GET['album'] : $first_file;
	
	if(SECTION == 'updategallery' || ACTION == 'edit') $page_title = 'Currently Editing [['.$album.']] Photo Album';
	else if (isset($_POST['submit']) AND isset($_GET['mode']) AND $_GET['mode']=="admin" AND $_GET['c']=="ok") $page_title = 'Creating New Gallery...';
	else $page_title = 'Create A New Gallery, enter new gallery details below';
	
    $PB_output .= '<div class="content box">';
    if (isset($_GET['mode']) AND $_GET['mode']=="admin" AND isset($_GET['c']) AND $_GET['c']=="ok") {
	    $PB_output .= '<div class="header"><h2>'.$page_title.'</h2></div>';
		
		if (isset($_POST['album_name']) AND $_POST['album_name']!=NULL AND isset($_POST['description']) AND $_POST['description']!=NULL){
	        if (isset($galleryInfo['album_id'])) $album_id = $galleryInfo['album_id'];
		    else{
		        if (file_exists($Gallery_DB_File)) {
		            $fp = @fopen($Gallery_DB_File, 'r');  
		            //$array = explode("\n", fread($fp, filesize($Gallery_DB_File))); 
		            $array = file($Gallery_DB_File); 
			        $listed = count($array);
					$id = 0;// a fail safe, if the $Gallery_DB_File does exist but empty 
			        for($x=0;$x<$listed;$x++) {	// start loop, each line of file
				        //$temp = explode(":",$array[$x]); // explode the line and assign to temp
                        $id = $x + 1;
			        }
					fclose($fp);
		        } else $id = 0;
		    }
			$album_name = $_POST['album_name']; //$album_name = isset($_POST['album_name']) ? $_POST['album_name'] : 'Untitled';
	        
			$description = $_POST['description'];
	        $category = (isset($_POST['category']) AND $_POST['category'] != NULL) ? $_POST['category'] : 'un-categorized';
	        $author = isset($_POST['author']) ? $_POST['author'] : $CFG->config['admin_username'];
	        $explicit = isset($_POST['explicit']) ? $_POST['explicit'] : 'no';
	        
	        
			if (strlen($description)<$longdescmax) { //if description IS NOT too long, go on executing...
			    $album_name = sanitize($album_name);
			    $author = sanitize($author);
			    $description = sanitize($description, false);
			    $category = sanitize($category);
			    $explicit = sanitize($explicit);
				$sanitized_name = sanitize_filename($album_name);
				
				$fileName = $Gallery_DB_File;
				$oldGalleryName = $UTIL->Check_For_Slash(GALLERY_PATH.$album,true);
			    $newGalleryName = $UTIL->Check_For_Slash(GALLERY_PATH.$sanitized_name,true);
		        if(SECTION == 'addgallery') {
				    if (strlen($fileName)>0){
		                //Save the id,album name,title, author and description,category and album content status[ explicit -> yes/no ].
	                    $final_data = "$id:$sanitized_name:$album_name:$author:$description:$category:$explicit";
		                $file = @fopen($fileName,"a");
			            // WRITE THE TXT FILE
		                if ($file != false){
					        $PB_output .= createNewGallery($sanitized_name);
					
			                fwrite($file,$final_data."\n");
			                fclose($file);
		                
					        // If upload is successful.
		                    $PB_output .= '<div class="message "><p><font color="green">The gallery description for <strong>'.$album_name.'</strong>, was added to the <span class="em i">Gallery DB file</span> successfully.</font></p></div>
						    <div class="message user_status"><p><a href="'.SELF.'?mode=home">Go to the homepage</a> |or| <a href="'.SELF.'?mode=admin&amp;section=addgallery">Create another gallery</a></p></div>';
	                    }
	                }//END CREATION TXT FILE
				    //----------------------------------------
				} elseif(ACTION == 'edit') {
			        $fp = @fopen($fileName, 'r');
			        //$array = explode("\n", fread($fp, filesize($fileName))); 
			        $array = file($fileName); 
		        	for($x=0;$x<sizeof($array);$x++) {	// start loop, each line of file
			        	$temp = explode(":",$array[$x]); // explode the line and assign to temp
		                $line[$x] = "$temp[0]:$temp[1]:$temp[2]:$temp[3]:$temp[4]:$temp[5]:$temp[6]";
			        }
					$line[$album_id] = "$album_id:$sanitized_name:$album_name:$author:$description:$category:$explicit";
			        
			        sort($line);
			        $fp = fopen ($fileName, "w");
			        if ($fp != false){
			            fwrite ($fp, $line[0]."\n");
			            for($i=1; $i<sizeof($line);$i++){
				           fwrite ($fp, $line[$i]);
						   fwrite ($fp, "\n");
			            }
			            fclose ($fp);
			            $PB_output .='<div class="message"><p>Gallery Album has been edited successfully, from <span class="em i">'.$album.'</span> to <span class="em i">'.$sanitized_name.'</span></p></div>';
			            if($album != $sanitized_name) $PB_output .= $UTIL->renameFile($oldGalleryName,$newGalleryName);
			            $PB_output .= '<div class="message user_status"><p><a href="'.SELF.'?mode=home">Go to Home Page</a> |or| <a href="'.SELF.'?mode=admin&amp;section=addgallery">Create New Gallery</a> |or| <a href="'.SELF.'?mode=viewGallery&amp;album='.$sanitized_name.'" target="_blank">View Gallery</a></p></div>';
			        
					} else $PB_output .='<div class="message"><p><strong>ERROR:</strong> , Gallery Album edit un-successfully, wrong file name!.</p></div>';
		        }
		    } else {
		        //if summary is more than max characters allowed
           	    $PB_output .= '<div class="message error"><b>The Details is too long...</b><p>Details can be up to '.$longdescmax.' characters - Actual Length: <font color=red>'.strlen($details).'</font> characters.</p>
		            <form><input type="button" value="Back" onClick="history.back()"></form>
				</div>';
            }
		} else { 
		    //if file, summary or album_name not present...
	        $PB_output .= '<div class="message error"><p>Error: No file, summary or album_name present <br />
		        <form><input type="button" value="Back" onClick="history.back()"></form>
		    </p></div>';
        }
    } else {
	        $PB_output .= '<div class="header"><h2>'.$page_title.'</h2></div>';
		    $PB_output .= '<form action="'.SELF.'?mode=admin&amp;'.(isset($_GET['action']) ? 'section='.SECTION.'&amp;action='.$_GET['action'].'&amp;filetype=galleries&amp;album='.$file : 'section='.SECTION).'&amp;c=ok" method="POST" enctype="multipart/form-data" name="uploadform" id="uploadform">'."\n";
            $PB_output .= '<fieldset><legend><b>Main information (required):</b></legend>';
			
			    $PB_output .= '<div class="wrapper left-1 grid-45">';
					$PB_output .= '<label for="album_name">Album Name* : </label>
			        <input name="album_name" id="album_name" type="text" value="'.((isset($galleryInfo['album_title'])) ? $galleryInfo['album_title'] : '').'" size="50" maxlength="255" required /><br />';
			    
				    $PB_output .= '<label for="description">Description* : </label><span class ="admin_hints">(max 5oo characters)</span><br />
			        <textarea name="description" id="description" type="text" onKeyDown="limitText(this.form.description,this.form.countdown,500);" onKeyUp="limitText(this.form.description,this.form.countdown,500);" cols="50" rows="3" maxlength="500" required >'.((isset($galleryInfo['album_description'])) ? $galleryInfo['album_description'] : '').'</textarea><br />
			        <span class ="admin_hints"><input class="grid-10" name="countdown" type="text" value="255" size="3" readonly> remaining characters.</span><br />';
				
				$PB_output .= '</div>';
				
			    $PB_output .= '<div class="wrapper right-1 grid-45">';
				    $PB_output .= '<span class ="admin_hints">You can specify a different creator for this gallery, otherwise the default author will be the gallery owner.</span><br />
				    <label for="author">Creator&rsquo;s Name : </label><br />
				    <input name="author" type="text" value="'.((isset($galleryInfo['album_author'])) ? $galleryInfo['album_author'] : '').'" size="50" maxlength="20"><br />';
				
				    $PB_output .= '<span class="admin_hints">Select Category To Post article Under</span><br/>';
                    if(file_exists($Category_DB_File)){
                        $PB_output .= '<label for="cat_image">Category : </label>';
					    $categories = GetCategories();
						usort($categories,'sort_file');
			            $total_categories = count($categories);
			            if($total_categories > 0){
				            $PB_output .= '<select name="cat_image">';
			                for( $i=0; $i<$total_categories; $i++ ) {
						        if( isset($categories[$i][1]) ) {
							        $cat_id = $categories[$i][1];
							        $cat_name = $categories[$i][2];
					                $cat_image = $categories[$i][8];
						            $PB_output .= '<option value="'.$cat_image.'" '.((isset($galleryInfo['album_category']) && $galleryInfo['album_category']  == $cat_name) ? 'selected' : '' ).'>'.$cat_name.'</option>'."\n";
						        }
			                }
			                $PB_output .= '</select>';
			            } else $PB_output .= '<div class="message user_status"><p>[0] category found!</p></div><br />'."\n";
                        $PB_output .= '<br/>';
				    } else $PB_output .= '<div class="message user_status"><p>'._MISSING_CATEGORY_DB_FILE.'</p></div><br />'."\n";
                
				    $PB_output .= '<span class ="admin_hints">Select YES if this gallery contains explicit or adult content.</span><br />
				    <label for="explicit">Explicit Content ? : </label><select name="explicit">';
			            $PB_output .= '<option value="no" '.((isset($galleryInfo['explicit']) && $galleryInfo['explicit'] == 'NO') ? 'selected' : '').'>No</option>';
					    $PB_output .= '<option value="yes" '.((isset($galleryInfo['explicit']) && $galleryInfo['explicit'] == 'YES') ? 'selected' : '').'>Yes</option>';
				    $PB_output .= '</select><br />';
				$PB_output .= '</div>';
				
				$PB_output .= '<div class="clear">&nbsp;</div>';
				
				$PB_output .= '<span class ="admin_hints">Fields marked with * are required.</span>';
		    $PB_output .= '</fieldset>';
			$PB_output .= '<center><input name="submit" class="save" type="submit" value="Create Gallery" onClick="showNotify('.((ACTION == 'edit') ? '\'Editing...Gallery\'' : '\'Creating...New...Gallery\'' ).');"><a class="cancel" href="'.SELF.'?mode=admin&amp;section='.SECTION.'" title="cancel and return to section index" >Cancel</a></center>';
	    $PB_output .= '</form>';
	}
	$PB_output .= '</div>';
	
	if(ACTION == 'edit') return $PB_output;
	else echo $PB_output;
?>