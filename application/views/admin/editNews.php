<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    if (($roleID < 3))  {
       echo'<div class="box"><div class="message error"><p>Access Denied!,You are not authorised to access this section, your user role does not permit you to add or edit news articles</p></div></div>';
	   include(BASE_PATH.'footer.php');
	   exit;
    }
    // if use js editor = yes load tinymce editor
	//if($CFG->config['use_js_editor'] == 'YES' && file_exists(PLUGIN_PATH.'tinymce/tinymce.php')) 
	require(PLUGIN_PATH.'tinymce/tinymce.php');
	
	if(!isset($PB_output)) $PB_output = NULL;
	$filenamechanged = NULL; // declare variable for duplicated filenames
	$filesuffix = NULL; // declare variable for duplicated filenames
	$image_new_name = NULL; // declare variable for image name
	$longdescmax =4000; #set max characters variable. "max 4000 characters" for long description/details field
	$img_dir = ARTICLES_IMAGE_PATH;
    // Determine max upload file size through php script reading the server parameters (and the form parameter specified in config.php. We find the minimum value: it should be the max file size allowed...
	$max_upload_form_size_MB = $max_upload_size/1048576; // convert max upload size set in config.php in megabytes
	$max_upload_form_size_MB = round($max_upload_form_size_MB, 2);
	$showmin = min($max_upload_form_size_MB, ini_get('upload_max_filesize')+0, ini_get('post_max_size')+0); // min function
	// Note: if I add +0 it eliminates the "M" (e.g. 8M, 9M) and this solves some issues with the "min" function
	
	$PB_output .= '<div class="box">';
    if (isset($_POST['submit']) AND isset($_GET['mode']) AND $_GET['mode']=="admin" AND $_GET['c']=="ok") { 
        if(ACTION == 'edit') $PB_output .= '<h3>Editing '.(isset($news_title) ? $news_title : 'News File').'</h3>';
        else $PB_output .= '<h3>Saving news article</h3>';
		if (isset($_POST['title']) AND $_POST['title']!=NULL AND isset($_POST['summary']) AND $_POST['summary']!=NULL){
		    if (isset($news_id)) $id = $archive_id = $news_id;
		    else{
			    $articles = GetDirContents(ARTICLES_PATH, 'files');
		        //Are there any articles?
			    if ($articles == false) $id = 1;
		        else $id = count($articles) + 1;

		        if (file_exists($News_Archive_DB_File)) {
		            $fp = @fopen($News_Archive_DB_File, 'r');  
		            $array = explode("\n", fread($fp, filesize($News_Archive_DB_File))); 
			        $listed = count($array);
			        for($x=0;$x<$listed;$x++) {	// start loop, each line of file
				        $temp = explode("}",$array[$x]); // explode the line and assign to temp
                        $archive_id = $x + 1;
			        }
		        } else $archive_id = 1;
		    }
		    $title = isset($_POST['title']) ? $_POST['title'] : 'Untitled';
			$sanitized_name = sanitize_filename($title);
	        $summary = $_POST['summary'];
		    $author = isset($_POST['author']) ? $_POST['author'] : $PB_CONFIG['author'];
	        $email = isset($_POST['email']) ? $_POST['email'] : $PB_CONFIG['admin_email'];
			
		    if(isset($news_date))$newsdate = $news_date;
		    else $newsdate = date('Y-m-d');
			
		    if(isset($news_moddate)) $newsdate_modified = date('Y-m-d');
		    else $newsdate_modified = false;
			
	        if (isset($_POST['category']) AND $_POST['category'] != NULL) $category = $_POST['category'];
	        else $category = 'un-categorized';
			
		    $cat_image = isset($_POST['cat_image']) ? $_POST['cat_image'] : '';
	        $details = isset($_POST['details']) ? $_POST['details'] : '';
	        $keywords = isset($_POST['keywords']) ? $_POST['keywords'] : '';
	        $publish = isset($_POST['publish_article']) ? $_POST['publish_article'] : 'NO';
	    
	        if (strlen($details)<$longdescmax) {
    		    //if long description IS NOT too long, go on executing... cleaning/sanitize input
	            // processing  KEYWORDS, supports a maximum of 12 keywords for searching: don't know how many keywords u can add in a feed. Anyway it's better to add a few keyword, so we display a warning if user submits more than 12 keywords
	            if (isset($keywords) AND $keywords != NULL) {
		            $singlekeyword=explode(",",$keywords); // divide keywords
                    //if more than 12 keywords
		            if ($singlekeyword < 12) $PB_output .= "<p class='message user_status'>You submitted more than 12 keywords for article...</p>";
	            }

	            //processing Author
	            if (isset($author) AND $author != NULL) { //if a different author is specified
		            if (!validate_email($email)) { //if author doesn't have a valid email address, just ignore it and use default author
		                $PB_output .= "<p>Author's email address not present or not valid. Author will be IGNORED</p>";
		                $author = NULL; //ignore author
		                $email = NULL; //ignore email
	                }
                } else $email = NULL; //ignore email,if author's name doesn't exist unset also email field
                
			    //start processing image 
			    if(isset($_FILES['newsImage']) AND $_FILES['newsImage']!=NULL ){
	                $img= $_FILES['newsImage']['name']; // image img
		            //$error= $_FILES['userfile']['error'];
	                $tempname= $_FILES['newsImage']['tmp_name'];
	                // echo "<br /><br /><br />$img - err $error - temp: $tempname<br /><br /><br />";
	    
                    $PB_output .= "<p><b>Now processing the article image...</b></p>";
                    $PB_output .= "<p>Original filename: <i>$img</i></p>";
                    $img_ext=explode(".",$img); // divide filename from extension
			        $n = strrpos($img,".");
                    $img_ext[1] = substr($img,$n,strlen($img)-$n);
                    $img_ext[1] = str_replace('.','',$img_ext[1]);
                    //processing img extension
	                //if img extension is accepted, go on.... img name depuration!!!! Important... By default Phot_ex uses a "strict" depuration policy (just characters from a to z and numbers... no accents and other characters).
	                //if (STRICTFILENAMEPOLICY == "YES") $img_ext[0] = renamefilestrict ($img_ext[0]); #enable this to have a very strict filename policy
	                
	                if($rename_upload_file) $filenamechanged = $sanitized_name;#replace certain characters with underscores
				    else $filenamechanged = $img_ext[0];# no renaming policy, leave file name as is
				
			        $uploadFile = $img_dir . $filenamechanged.".".$img_ext[1] ;
	                while (file_exists("$uploadFile")) { //cicle: if file already exists add an incremental suffix
		                $filesuffix++;
		                $uploadFile = $img_dir . $filenamechanged . $filesuffix.".".$img_ext[1] ;
	                }
	                $PB_output .= "New Name: <i>$filenamechanged$filesuffix.$img_ext[1]</i><br>";
	                $uploadFile == NULL ;

		            //uploading image file associated to article
		            if (is_uploaded_file($tempname)) {
			             $PB_output .= "<p><b>Image present: processing...</b></p>";
					    // control accepted image format
			            if ($img_ext[1]=="jpg" OR $img_ext[1]=="jpeg" OR $img_ext[1]=="gif"OR $img_ext[1]=="png" OR $img_ext[1]=="JPG" OR $img_ext[1]=="JPEG" OR $img_ext[1]=="GIF"OR $img_ext[1]=="PNG") {
				            //move file from the temp directory to the upload directory
				            if (move_uploaded_file($tempname, $uploadFile)) {
						        // Assign a new name to the image
							    $image_new_name = "$filenamechanged$filesuffix.$img_ext[1]";
				               $PB_output .= "<p><font color=\"green\">Image sent.</font></p>"; // If upload is successful.
				            } else { // if IMAGE upload is not successful
				                $image_new_name = NULL;
				                $PB_output .= "<p><font color=\"red\">Error: image NOT sent! (ignored)</font></p>";
				            }
			            } else { // if the image extension is not valid: IGNORE the image
				            $image_new_name = NULL;
				            $PB_output .= "<p>Not valid image extension: Accepted extensions are: jpg, gif, png.</p>";
			            }
		            } else { //If upload is not successfull
		                $PB_output .= "<div class=\"message error\">";
						    $PB_output .= "<p><b><font color=\"red\">FILE ERROR : Upload Failed</font></b></p>";
		                    $PB_output .= "<p><b>The main reasons for the failure of upload could be:</b></p>";
		                    $PB_output .= "<p> - You didn't assign writing permission to the article folder and the uploaded file can't be saved on the server.</p>";
		                    $PB_output .= "<p> - Your upload image is bigger than upload max filesize on your server.</p>";
		                    $PB_output .= '<p><b>Useful information for debugging:</b> <a href="'.SELF.'?mode=admin&amp;section=systeminfo">Your server configuration</a></p>';
						$PB_output .= '</div';
						$PB_output .= '<div class="message unspecific"><p><input type="button" value="Back" onClick="history.back()"></p></div';
					}
			    } else $image_new_name = $_POST['newsImage'];
			//creating .txt file associated to article
			$archiveFileName = DATABASE_PATH.'news_articles_archive.txt' ;
			$fileName = ARTICLES_PATH.$sanitized_name.'.txt' ;
		    if (strlen($fileName)>0){
		        //Save the title,summary etc.
	            $data = array('id' => sanitize($id),'title' => sanitize($title),'author' => sanitize($author),'email' => sanitize($email),'date' => $newsdate,'date_modified' => $newsdate_modified,'news_image' => sanitize($image_new_name),'cat_image' => sanitize($cat_image),'category' => sanitize($category),'keywords' => sanitize($keywords),'summary'=> sanitize($summary),'details' => sanitize($details, false),'publish' => $publish);
				$final_data = $JSON->encode($data);
		        $file = @fopen($fileName,"w");
			    // write the txt file
		        if ($file != false){
			        fwrite($file,$JSON->clearWhitespaces($final_data)."\n");
			        fclose($file);
				    // If successful.
		            $PB_output .= '<div class="message"><p><b><font color="green">Article saved successfully.</font></b></p></div>';
		            $PB_output .= '<div class="message user_status"><p><a href="'.SELF.'?mode=home">Go to Home Page</a> |or| <a href="'.SELF.'?mode=admin&amp;section=addnews">Create Article</a> |or| <a href="'.SELF.'?mode=viewArticle&amp;newsArticle='.$sanitized_name.'" target="_blank">View article</a></p></div>';
			        if(isset($_POST['add_to_archive']) && $_POST['add_to_archive'] == 'YES'){
					    $archive_data = array('id' => sanitize($archive_id),'title' => sanitize($title),'author' => sanitize($author),'email' => sanitize($email),'date' => $newsdate,'date_modified' => $newsdate_modified,'news_image' => sanitize($image_new_name),'cat_image' => sanitize($cat_image),'category' => sanitize($category),'keywords' => sanitize($keywords),'summary'=> sanitize($summary),'details' => sanitize($details, false),'publish' => $publish);
					    $archive_final_data = $JSON->encode($archive_data);
				        $archivefile = @fopen($archiveFileName,"a");
			            // write the txt file
		                if ($archivefile != false){
			                fwrite($archivefile,$JSON->clearWhitespaces($archive_final_data)."\n");
			                fclose($archivefile);
				            // If upload is successful.
		                    $PB_output .= '<div class="message"><p><b><font color="green">Article added to the archive file successfully.</font></b></p>';
		                }
					}
				}
				// end creation txt file
			    } else $PB_output .= '<p class="message error"><b><font color="green">Wrong file name!</font></b></p>'; // If upload is un-successful.
                //if long description is more than max characters allowed
			} else $PB_output .= '<div class="message error"<strong>The Long Description is too long...</strong><p>Long description can be up to $longdescmax characters - Actual Length: <font color=red>".strlen($details)."</font> characters.</p><form><input type=\"button" value=\"Back" onClick="history.back()"></form></div>';
			// end of long desc lenght checking
        } else {
    	    //if file, description or title not present...
		    if (!isset($_POST['title']) || $_POST['title'] != NULL) $PB_output .= '<div class="message error"><p>Error: No <b>title</b>, You must supply a news title and text</p></div>';
	        else if (!$_POST['summary'] || $_POST['summary'] != NULL) $PB_output .= '<div class="message error"><p>Error: No <b>summary</b> present. You must supply a news entry </p></div>';
		   $PB_output .= '<p><form><input type="button" value="Back" onClick="history.back()"></form></p>';
        }
    } else {
        $PB_output .= '<form action="?mode=admin&amp;'.(isset($_GET['action']) ? 'section='.SECTION.'&amp;action='.$_GET['action'].'&amp;filetype=article&amp;newsArticle='.$file : 'section='.SECTION).'&amp;c=ok" method="POST" enctype="multipart/form-data" name="uploadform" id="uploadform">'."\n";
            $PB_output .= '<fieldset><legend><b>Main information (required):</b></legend>';
				if ($showmin!=NULL AND $showmin!="0") $PB_output .= '<p><span class ="admin_hints">Your server configuration allows you to upload files up to '.$showmin.'MB</span></p>';
				$PB_output .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.$max_upload_size.'">';
			    
				$PB_output .= '<div class="wrapper left-1 grid-45">';
			    
				    $PB_output .= '<label for="title">Article Title* : </label>
			        <input id="title" type="text" name="title" class="grid-3" maxlength="50" value="'.((isset($news_title)) ? $news_title : '').'" required /><br />';
				
				    $PB_output .= '<div class="clear">&nbsp;</div>';
				
				$PB_output .= '<label for="summary">Short Summary* : &not;</label>
			    <span class="admin_hints">Type your news post in the box below(max 255 characters)</span><br />
			    <textarea name="summary" id="summary" type="text" onKeyDown="limitText(this.form.summary,this.form.countdown,255);" 
			        onKeyUp="limitText(this.form.summary,this.form.countdown,255);" cols="50" rows="3" maxlength="255">'.((isset($news_summary)) ? $news_summary : '' ).'</textarea>
			    <br />
			    <span class="admin_hints"><input class="grid-20" name="countdown" type="text" value="255" size="3" readonly> remaining characters.</span> 
			    <br />';
				$PB_output .= '</div>';
				
				$PB_output .= '<div class="wrapper right-1 grid-45">';
                    if(file_exists($Category_DB_File)){
                        $PB_output .= '<span class="admin_hints">Select Category To Post article Under</span><br/>
                        <label for="cat_image">Category : </label>';
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
						            $PB_output .= '<option value="'.$cat_image.'" '.((isset($news_category) && $news_category  == $cat_name) ? 'selected' : '' ).'>'.$cat_name.'</option>'."\n";
						        }
			                }
			                $PB_output .= '</select>';
			            } else $PB_output .= '<div class="message user_status"><p>[0] category found!</p></div>'."\n";
                        $PB_output .= '<br/>';
				    } else $PB_output .= '<div class="message user_status"><p>'._MISSING_CATEGORY_DB_FILE.'</p></div>'."\n";
                
				    if(isset($publish) && $publish == 'YES') $PB_output .= '<span class="admin_hints">This article has been published</span><br/>';
                    else $PB_output .= '<span class="admin_hints">Select whether to publish article or not</span><br/>';
				    $PB_output .= '<label for="publish_article">Publish Article ? : </label><select name="publish_article">';
                        $PB_output .= "<option>-- would you like to publish article --</option>\n";
                        $PB_output .= '<option value="YES" '.((isset($publish) && $publish == 'YES') ? 'selected' : '' ).'>Yes</option>'."\n";
                        $PB_output .= '<option value="NO" '.((isset($publish) && $publish == 'NO') ? 'selected' : '' ).'>No</option>'."\n";
                    $PB_output .= '</select><br/>';
					
				    $PB_output .= '<span class="admin_hints">Select whether to add article to archives or not</span><br/>';
				    $PB_output .= '<label for="add_to_archive">Add To Archive ? : </label><select name="add_to_archive">';
                        $PB_output .= '<option value="NO">No</option>'."\n";
                        $PB_output .= '<option value="YES">Yes</option>'."\n";
                    $PB_output .= '</select>';
				$PB_output .= '</div>';
				
				$PB_output .= '<div class="clear">&nbsp;</div>';
			    $PB_output .= '<p class="admin_hints">Fields marked with * are required.</p>';
			$PB_output .= '</fieldset>';
			$PB_output .= '<p><input type="checkbox" value="add extra information to this article" onClick="return kadabra(\'main\');">add extra information to this article</p>';
			//$PB_output .= '<br />';
			$PB_output .= '<div id="main" style="display:none">';
				$PB_output .= '<fieldset>';
				    $PB_output .= '<legend><b>Extra information (optional):</b></legend>';
					
					$PB_output .= '<div class="wrapper right-1 grid-45">';
				    $PB_output .= '<span style="display:block;" class="head grid-auto"><p>These items are ready to be implemented in this article : &not;</p></span>';
	                    $PB_output .= show_gallery_insert_box();
	                    $PB_output .= show_link_insert_box();
	                    $PB_output .= show_image_insert_box(ARTICLES_IMAGE_PATH); //choose image from any other image folder, such as upload image folder or from a gallery album if it exists.
				    $PB_output .= '</div>';
					
				    $PB_output .= '<label for="newsImage">News Image : </label>';
					if(ACTION == 'edit') $PB_output .= '<input name="newsImage" id="newsImage" type="text" value="'.$news_image.'" readonly />';
					else $PB_output .= '<input name="newsImage" id="newsImage" type="file" />';
					
		            $PB_output .= '<br />';
					
				    $PB_output .= '<span class="admin_hints">You can specify a different author for this article, otherwise the default author will be the article owner.</span><br />
				    <label for="author">Author&rsquo;s Name : &not;</label><br />
				    <input name="author" type="text" id="author" class="grid-2" value="'.((isset($news_author)) ? $news_author : '' ).'" maxlength="255">
			    	<br />';
					
				    $PB_output .= '<label for="email">Author&rsquo;s Email : &not;</label><br />
				    <input name="email" type="text" id="email" class="grid-2" value="'.((isset($news_author_email)) ? $news_author_email : '' ).'" maxlength="255"><br />';
					
				    $PB_output .= '
				    <label for="keywords">Tags / Keywords : &not; </label><span class="admin_hints">Separate keywords by comma</span><br />
					<input name="keywords" type="text" onkeyup="cnt(this,document.uploadform.counttotalwords)" class="grid-2" value="'.((isset($news_keywords)) ? $news_keywords : '' ).'" maxlength="255"><br />';
				    $PB_output .= '<span class="admin_hints"><input style="width:10% !important;" type="text" name="counttotalwords" value="0" size="3" onkeyup="cnt(document.uploadform.keywords,this)" readonly /> words</span><br />';
					
				    $PB_output .= '<label for="details">Details : &not;</label> <span class="admin_hints">(HTML tags accepted)</span><br />
				    <textarea id="details" name="details"  class="'.((defined('WYSIWYG_TEXTAREA_CLASS')) ? WYSIWYG_TEXTAREA_CLASS : '' ).'" cols="80" rows="8">'.((isset($news_details)) ? $news_details : '' ).'</textarea>
				    <br />';
					
				$PB_output .= '</fieldset>';
			$PB_output .= '</div><br />';
			
			$PB_output .= '<center><input type="submit" name="submit" value="Save News" onClick="showNotify(\'Saving...News....Article\');" class="save" /><a class="cancel" href="'.SELF.'?mode=admin&amp;section='.SECTION.'" title="cancel article creation and return to the '.SECTION.' section">Cancel</a></center><br />';
	    $PB_output .= '</form>';
	}
	$PB_output .= '</div>';
	
	if(ACTION == 'edit') return $PB_output;
	else echo $PB_output;
?>