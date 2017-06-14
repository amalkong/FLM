<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    if (($roleID < 3))  {
       echo'<div class="message error"><p>Access Denied!,You are not authorised to access this section, your user role does not permit you to add or edit pages.</p></div>';
	   include(BASE_PATH.'footer.php');
	   exit;
    }
    $PAGE =& load_class('Page','library');
	// if use js editor = yes load tinymce editor
	if($CFG->config['use_js_editor'] == 'YES' && file_exists(PLUGIN_PATH.'tinymce/tinymce.php')) require(PLUGIN_PATH.'tinymce/tinymce.php');
	
    if(!isset($PB_output)) $PB_output = NULL;
	$first_file = NULL;
	
	$page_list = GetDirContents(PAGE_PATH,'files');
	if(count($page_list) > 0 ) $first_file = $page_list[0];
	
	$selected_page = isset($_GET['page']) ? $_GET['page'] : $first_file;
	
	$ext = pathinfo($selected_page, PATHINFO_EXTENSION);
	$page_file = ($ext == '') ? PAGE_PATH.$selected_page.'.php' : PAGE_PATH.$selected_page;
	$stripped_page_file = removeFileExt($selected_page);
	$create_url = SELF.'?mode=admin&amp;section=addpage';
	$edit_url = SELF.'?mode=admin&amp;section=manage&amp;action=edit&amp;filetype=page&amp;page='.$selected_page;
	$return_url = SELF.'?mode=admin';
	
	if(SECTION == 'updatepage' || ACTION == 'edit'){
		if(file_exists($page_file)) include($page_file);
	    else {
	        $PB_output .= '<div class="message user_status">'.
			    '<p><b>No Page Found</b> ...</p><br/><p>The page '.$separator.'<em>'.basename($page_file).'</em>, selected for editing was <b>not found or it does not exists</b>. Ensure that the pages folder is set to an existing directory in the file <em>[constants.php]</em> ...</p>'.
                '<p><a href="'.$create_url.'">Create a new page</a> or <a href="javascript:history.back()">Return to previous page</a> or </p>'.
		    '</div>'."\n";
		    return false;
	    }
	}
	$file_sections_count = ((isset($file_sections)) ? count($file_sections) : (isset($_GET['section_count']) ? $_GET['section_count'] : 1 ) );
	
	?>
<script language="javascript">
var sections = new Array();
// sec_cnt will never decrease
var sec_cnt=<?php echo ($file_sections_count + 1);?>;

function add_section() {
    //var nb_section=sections.length;
    var section = document.getElementById("section");
    //var new_section = document.createElement("li");
    var new_section = document.createElement("div");
	//sections[nb_section]="round"+sec_cnt;
    new_section.className="on";
    //new_section.innerHTML='<a href="javascript:show_tab(\'round'+sec_cnt+'\',tablist);">['+(sec_cnt+1)+']</a>';
    new_section.innerHTML='<div class="admin_hints" id="section'+sec_cnt+'">Page Section '+sec_cnt +'<br/>'+
        '<label for="type">Content Type : </label>'+
	    '<select name="type[]">'+
	        '<option value="text">Text</option>'+
	        '<option value="form">Form</option>'+
	        '<option value="gallery">Gallery</option>'+
	        '<option value="audio">Audio</option>'+
	        '<option value="video">Video</option>'+
	    '</select><br/>'+
	    '<label for="page_content">Page Content '+sec_cnt+' : </label><br/>'+
	    '<textarea class="<?php echo ((defined('WYSIWYG_TEXTAREA_CLASS')) ? WYSIWYG_TEXTAREA_CLASS : '') ?>" rows="8" cols="80" name="page_content[]"></textarea>'+
	'</div>';
    section.appendChild(new_section);
	sec_cnt++;
}
</script>
	<?php
	//$page_file = VIEW_PATH.PAGE;
	if(SECTION == 'updatepage' || ACTION == 'edit') $page_title = 'Currently Editing [['.$stripped_page_file.']] Page';
	else if (isset($_POST['submit']) AND isset($_GET['mode']) AND $_GET['mode']=="admin" AND $_GET['c']=="ok") $page_title = 'Creating New Page';
	else $page_title = 'Create A New Page, enter new page details';
	
	$PB_output .= '<div class="content box">';
	    $PB_output .= '<div class="header"><h2>'.$page_title.'</h2></div>';
	    //if (isset($_POST['submit']) AND isset($_GET['mode']) AND $_GET['mode']=="admin" AND isset($_GET['section']) AND $_GET['section']=="updatepage" AND isset($_GET['c']) AND $_GET['c']=="ok") {
		if (isset($_POST['submit']) AND isset($_GET['mode']) AND $_GET['mode']=="admin" AND $_GET['c']=="ok") {
			if (isset($_POST['page_name'])) {
		        $pagename = $_POST['page_name'];
				$sanitized_fileName = sanitize_filename($pagename);
			    $fileName = $sanitized_fileName;
				
			    $meta_title = isset($_POST['title']) ? $_POST['title'] : 'un-titled';
		        $meta_author = isset($_POST['author']) ? $_POST['author'] : 'Amalkong';
		        $meta_keywords = isset($_POST['keywords']) ? $_POST['keywords'] : '';
		        $meta_description = isset($_POST['description']) ? $_POST['description'] : '';
			
		        $file_type = isset($_POST['file_type']) ? $_POST['file_type'] : 'text';
		        $content_type = isset($_POST['type']) ? $_POST['type'] : 'text';
		        $content = isset($_POST['page_content']) ? $_POST['page_content'] : '';
			    
			    $sections = array($content_type,$content);
				$meta_data = array('title'=>$meta_title,'author'=>$meta_author,'keywords'=>$meta_keywords,'description'=>$meta_description,'file_type'=>$file_type);
			    
			    if(SECTION == 'updatepage' || ACTION == 'edit') {
   				    $file_number = $_POST['file_number'];
					//$file_number = getFileNumber(PAGE_PATH);
				    $createDate = $_POST['createDate'];
		            $modified = isset($_POST['modified']) ? $_POST['modified'] : '';
				    $stats = array('createDate'=>$createDate,'modified'=>$modified,'file_number'=>$file_number);
			        
				    $PB_output .= $PAGE->SavePage($fileName,$stats,$meta_data,$content,$content_type, false );
				}else if(SECTION == 'addpage') {
				    $PB_output .= $PAGE->SaveNewPage($fileName,$meta_data,$content, $content_type );
				}
		    }
	    } else {
	        $PB_output .= '<form action="?mode=admin&amp;'.(isset($_GET['action']) ? 'section='.SECTION.'&amp;action='.$_GET['action'].'&amp;filetype=page&amp;page='.$stripped_page_file : 'section='.SECTION).'&amp;c=ok" method="POST" enctype="multipart/form-data" name="uploadform" id="uploadform">';
		        $PB_output .= '<fieldset><legend><b>Main information (required):</b></legend>';
			        $PB_output .= '<span class="admin_hints">The name the page will be saved as.</span><br/>';
				    $PB_output .= '<input type="text" '.(isset($file_metadata) ? 'value="'.$stripped_page_file.'"' : ' value="" placeholder="enter page name here"').' name="page_name" required />';
				    $PB_output .= '<div class="wrapper left-1">';
				        $PB_output .= '<span class="admin_hints">Page Meta Information, the page title will not only be used in the meta tags, but also when the page is displayed.</span><br/>';
				        if(isset($file_metadata)){
						    $PB_output .= '<label for="file_type">Page File Type : </label><select name="file_type">';
							    $PB_output .= '<option value="text" '.(($file_metadata['file_type'] == 'text') ? 'selected' : '' ).'>Text</option>';
								$PB_output .= '<option value="gallery" '.(($file_metadata['file_type'] == 'gallery') ? ' selected' : '' ).'>Gallery</option>';
								$PB_output .= '<option value="media" '.(($file_metadata['file_type'] == 'media') ? 'selected' : '' ).'>Media</option>';
							$PB_output .= '</select><br/>';
				            $PB_output .= '<label for="title">Page Title : </label><br/><input type="text" value="'.$file_metadata['title'].'" name="title"/>';
				            $PB_output .= '<label for="author">Page Author : </label><br/><input type="text" value="'.$file_metadata['author'].'" name="author"/>';
				            $PB_output .= '<label for="keywords">Page Keywords : </label><br/><input type="text" value="'.$file_metadata['keywords'].'" name="keywords"/>';
				            $PB_output .= '<label for="description">Page Description : </label><br/><textarea cols="40" rows="4" name="description" maxlength="255">'.$file_metadata['description'].'</textarea><br/>';
						} else {
						    $PB_output .= '<label for="file_type">Page File Type : </label><select name="file_type">';
							    $PB_output .= '<option value="text">Text</option>';
							    $PB_output .= '<option value="gallery">Gallery</option>';
							    $PB_output .= '<option value="media">Media</option>';
							$PB_output .= '</select><br/>';
				            $PB_output .= '<label for="title">Page Title : </label><br/><input type="text" value="" name="title"/>';
				            $PB_output .= '<label for="author">Page Author : </label><br/><input type="text" value="" name="author"/>';
				            $PB_output .= '<label for="keywords">Page Keywords : </label><br/><input type="text" value="" name="keywords"/>';
				            $PB_output .= '<label for="description">Page Description : </label><br/><textarea cols="20" rows="4" name="description" maxlength="255"></textarea><br/>';
							
				        }
				    $PB_output .= '</div>';
				    if(isset($file_stats)){
				        $PB_output .= '<div class="wrapper right-1">';
				            $PB_output .= '<span class="admin_hints">Page Statistics Information</span><br/>';
				            $PB_output .= '<label for="createDate">Date Created : </label><br/><input type="text" value="'.$file_stats['createDate'].'" name="createDate"/>';
				            $PB_output .= '<label for="modified">Last Modified : </label><br/><input type="text" value="'.$file_stats['modified'].'" name="modified"/>';
				            $PB_output .= '<label for="file_number">File Number : </label><br/><input type="text" class="grid-20" value="'.$file_stats['file_number'].'" name="file_number"/>';
				        $PB_output .= '</div>';
				    }
				    $PB_output .= '<div class="clear">&nbsp;</div>';
				
					$PB_output .= '<div class="wrapper right-1 grid-30">';
				    $PB_output .= '<span style="display:block;" class="head grid-auto"><p>These items are ready to be implemented in this page:</p></span>';
	                    $PB_output .= show_link_insert_box();
						$PB_output .= show_gallery_insert_box();
	                    $PB_output .= show_image_insert_box(IMAGE_UPLOAD_PATH);
				    $PB_output .= '</div>';
					
				    $PB_output .= '<div class="wrapper left-1 grid-66">';
				        if(isset($file_sections)){
					        $PB_output .= '<span style="display:block;" class="head grid-auto">Page Main Content/Body, edit the section content.</span><br/>';
							$PB_output .= '<div class="clear">&nbsp;</div>';
							foreach($file_sections as $key => $section){
					            $PB_output .= '<label for="file_type">Content Type : </label><select name="type[]">';
							        $PB_output .= '<option value="text" '.(($file_sections[$key]['type'] == 'text') ? 'selected' : '' ).'>Text</option>';
									$PB_output .= '<option value="form" '.(($file_sections[$key]['type'] == 'form') ? 'selected' : '' ).'>Form</option>';
									$PB_output .= '<option value="gallery" '.(($file_sections[$key]['type'] == 'gallery') ? 'selected' : '' ).'>Gallery</option>';
									if(isset($file_metadata) && $file_metadata['file_type'] == 'media'){
									    $PB_output .= '<option value="audio" '.(($file_sections[$key]['type'] == 'audio') ? 'selected' : '' ).'>Audio</option>';
										$PB_output .= '<option value="video" '.(($file_sections[$key]['type'] == 'video') ? 'selected' : '' ).'>Video</option>';
									}
							    $PB_output .= '</select><br/>';
					            $PB_output .= '<label for="page_content">Page Section '.($key+1).' : </label><br/><textarea class="'.((defined('WYSIWYG_TEXTAREA_CLASS')) ? WYSIWYG_TEXTAREA_CLASS : '').'" name="page_content[]" cols="80" rows="8">'.depurateContent($file_sections[$key]['content']).'</textarea>';
					        }
				        } else {
					        $PB_output .= '<span style="display:block;" class="head grid-auto">Page Main Content/Body, enter the main information to be displayed on the page.</span><br/>';
					        $PB_output .= '<div class="clear">&nbsp;</div>';
							$PB_output .= '<label for="type">Content Type : </label><select name="type[]">';
							    $PB_output .= '<option value="text">Text</option>';
							    $PB_output .= '<option value="gallery">Gallery</option>';
							    $PB_output .= '<option value="form">Form</option>';
							$PB_output .= '</select><br/>';
					        $PB_output .= '<label for="page_content">Page Section 1 : </label><br/>';
							for($i=0;$i < $file_sections_count;$i++){
						        $PB_output .= '<textarea class="'.((defined('WYSIWYG_TEXTAREA_CLASS')) ? WYSIWYG_TEXTAREA_CLASS : '').'" cols="80" rows="8" name="page_content[]"></textarea><br/>';
					        }
				        }
						$PB_output .= '<div id="section"></div>
						<p><a href="#section" onclick="add_section();">Add New Section</a></p>';
					$PB_output .= '</div>';
		        $PB_output .= '</fieldset>';
		        $PB_output .= '<center><input class="save" type="submit" name="submit" value="Save Page" onClick="showNotify(\'Saving...Page...\');" /><a class="cancel" href="'.$return_url.'" title="cancel and return to the section index" >Cancel</a></center>';
	        $PB_output .= '</form>';
	    }
	$PB_output .= '</div>';
	
	if(ACTION == 'edit') return $PB_output;
	else echo $PB_output;
?>