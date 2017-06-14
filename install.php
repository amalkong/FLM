<?php
   define('PBD_FLM', TRUE);
// FLM requires PHP 5.2+
    if(!defined('PHP_VERSION_ID')){
        $version = PHP_VERSION;
        define('PHP_VERSION_ID', ($version{0} * 10000 + $version{2} * 100 + $version{4}));
    }

    if(PHP_VERSION_ID < 50207){
        define('PHP_MAJOR_VERSION',     $version{0});
        define('PHP_MINOR_VERSION',     $version{2});
        define('PHP_RELEASE_VERSION',     $version{4});
    }

    // if the user is running a PHP version less the 5.2.1, WE MUST DIE IN A FIRE!
    if(PHP_VERSION_ID < 50201){
        $message = "Your installed PHP version is less then 5.2.1. FLM requires at least PHP v5.2.1+ to run correctly. The latest version is highly reccomended. FLM will not run until this basic requirement is met, and has quit.";
        return $message;
    }
    include('application/config/constants.php');
    include('application/config/config.php');
    
    include('system/functions/Common_functions.php');
    include('system/functions/Encoder_functions.php');
    include('system/functions/Auth_functions.php');
    
	$PB_output = NULL;
	$filesuffix = NULL; // declare variable for duplicated filenames
	$image_new_name = NULL; // declare variable for image name
	$meta_descmax =1000; #set max characters variable. "max 1000 characters" for meta description field
	$descmax =4000; #set max characters variable. "max 4000 characters" for admin bio field
    // Determine max upload file size
	$showmin = (round($max_size_in_kb / 1048576 * 100) / 100);
	$step = isset( $_GET['step'] ) ? $_GET['step'] : "intro";
	$title = cleanPageTitles($step).'';
	$per_page_array = array('1','2','3','4','5','10','15','20','25');
 ?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title.' >> FLM Install >> '.strftime( "%b %d %Y %H:%M", time());?></title>
        <script type="text/javascript" src="application/assets/js/jquery-1.7.2.js"></script>
        <script type="text/javascript">
	    function setOpacity(id, opacity){
			var element = document.getElementById(id).style;
			element.opacity = (opacity / 100);	// std
			element.MozOpacity = (opacity / 100);	// firefox
			element.filter = 'alpha(opacity=' + opacity + ')';	// IE
			element.KhtmlOpacity = (opacity / 100);	// Mac
		}

		function fadeOpacity(id, opacityStart, opacityEnd, msToFade){
			if (msToFade > 0){
				var frames = Math.round((msToFade / 1000) * <?=FADE_FRAME_PER_SEC;?>);
				var msPerFrame = Math.round(msToFade / frames);
				var opacityPerFrame = (opacityEnd - opacityStart) / frames;
				var opacity = opacityStart;
			
				for (frame = 1; frame <= frames; frame++){
					setTimeout('setOpacity(\'' + id + '\',' + opacity + ')',(frame * msPerFrame));
					opacity += opacityPerFrame;
				}
				if (opacityEnd == 0){
					setTimeout('document.getElementById(\'' + id + '\').style.visibility=\'hidden\'',((frames+1) * msPerFrame));
				}else{
					setTimeout('setOpacity(\'' + id + '\',' + opacityEnd + ')',((frames+1) * msPerFrame));
				}
			}else{
				setOpacity(id, opacityEnd);
				if (opacityEnd == 0){
					document.getElementById(id).style.visibility='hidden';
				}
			}
		}
		var headerUrl = "<?php echo HEADER_URL;?>";
		var avatarUrl = "<?php echo AVATAR_URL;?>";
		var textureUrl = "<?php echo TEXTURES_URL;?>";
		function nextTexture() {
                if (document.setupform.texture[current+1]) {
                    document.images.showTexture.src = textureUrl + document.setupform.texture[current+1].value;
                    document.setupform.texture.selectedIndex = ++current;
                } else firstTexture();
			    fadeOpacity('texture_box_image', 0, <?php echo OVERLAY_OPACITY;?>, <?php echo FADE_DURATION_MS;?>);
            }
		function firstTexture() {
                current = 0;
                document.images.showTexture.src = textureUrl + document.setupform.texture[0].value;
                document.setupform.texture.selectedIndex = 0;
        }
		
        function changeTexture() {
		    var url = "<?php echo TEXTURES_URL;?>";
            current = document.setupform.texture.selectedIndex;
            document.images.showTexture.src = textureUrl + document.setupform.texture[current].value;
			fadeOpacity('texture_box_image', 0, <?php echo OVERLAY_OPACITY;?>, <?php echo FADE_DURATION_MS;?>);
        }
		function firstAvatar() {
                current = 0;
                document.images.showAvatar.src = avatarUrl + document.setupform.avatar[0].value;
                document.setupform.avatar.selectedIndex = 0;
        }
        function nextAvatar() {
                if (document.setupform.avatar[current+1]) {
                    document.images.showAvatar.src = avatarUrl + document.setupform.avatar[current+1].value;
                    document.setupform.avatar.selectedIndex = ++current;
                } else firstAvatar();
			    fadeOpacity('avatar_box_image', 0, <?php echo OVERLAY_OPACITY;?>, <?php echo FADE_DURATION_MS;?>);
            }
			
		function changeAvatar() {
		    var url = "<?php echo AVATAR_URL;?>";
            current = document.setupform.avatar.selectedIndex;
            document.images.showAvatar.src = avatarUrl + document.setupform.avatar[current].value;
			fadeOpacity('avatar_box_image', 0, <?php echo OVERLAY_OPACITY;?>, <?php echo FADE_DURATION_MS;?>);
        }
		// Headers
		function nextHeader() {
                if (document.setupform.header[current+1]) {
                    document.images.showHeader.src = headerUrl + document.setupform.header[current+1].value;
                    document.setupform.header.selectedIndex = ++current;
                } else firstHeader();
			    fadeOpacity('header_box_image', 0, <?php echo OVERLAY_OPACITY;?>, <?php echo FADE_DURATION_MS;?>);
            }
		function firstHeader() {
                current = 0;
                document.images.showHeader.src = headerUrl + document.setupform.header[0].value;
                document.setupform.header.selectedIndex = 0;
        }
         
		function changeHeader() {
		    var url = "<?php echo HEADER_URL;?>";
            current = document.setupform.header.selectedIndex;
            document.images.showHeader.src = headerUrl + document.setupform.header[current].value;
			fadeOpacity('header_box_image', 0, <?php echo OVERLAY_OPACITY;?>, <?php echo FADE_DURATION_MS;?>);
        }
		/* -------------------------------------------------------------------------- */		
		function limitText(limitField, limitCount, limitNum) {
	        if (limitField.value.length > limitNum) {
		        limitField.value = limitField.value.substring(0, limitNum);
	        } else {
		        limitCount.value = limitNum - limitField.value.length;
	        }
        } 
	
        function cnt(w,x){
            var y=w.value;
            var r = 0;
            a=y.replace(/\s/g,' ');
            a=a.split(',');
            for (z=0; z<a.length; z++) {if (a[z].length > 0) r++;}
            x.value=r;
        }

		function showNotify( str ) {
		    var elem = document.getElementById("status_notification");
		    elem.style.display = "block";
		    elem.style.visibility = "visible";

		    if ( elem.currentStyle && elem.currentStyle.position == "absolute" ) {
			    elem.style.top = '0';
		    }
		    elem.innerHTML = str;
	    }
	</script>
		<link rel="stylesheet" href="application/assets/ss/default.css" media="all" />
		<link rel="stylesheet" href="application/assets/css/install.css" media="all" />
	</head>

    <body>
	    <div class="container">
		    <header class="header"><h1 class="header">Football League Manager Installer</h1></header>
	        <nav class="steps-nav"><center><ul><li>Intro</li><li>Step 1</li><li>Step 2</li><li>Step 3</li><li>Step 4</li><li>Finish</li></ul></center></nav>
<?php
        $PB_output .= '<div class="panel border">';
	    switch($step){
	        case 'intro':
		        $PB_output .= '<div class="news-clips">';
		            $PB_output .= '<p>Welcome and thanks for installing Football League Manager. The installation process is quick and easy, just enter a few details relating to the site and thats it!</p>';
			        $PB_output .= '<div class="message unspecific"><strong>readme.txt</strong><br><textarea rows="8" cols="90">'.file_get_contents('readme.txt').'</textarea></div>';
					
					$PB_output .= '<a href="?step=step_1">Next Step</a>';
			    $PB_output .= '</div>';
		    break;
		
		    case 'step_1':
	        include(CONFIG_PATH.'charsets.php');
	        include(CONFIG_PATH.'languages.php');
		    if (isset($_POST['saveMeta']) AND isset($_GET['step']) AND $_GET['step']=="step_1" AND isset($_GET['c']) AND $_GET['c']=="ok") {
				if (strlen($_POST['description'])<$meta_descmax) {
			        $fileName = CONFIG_PATH.'settings_meta.php';
		            $fp = @fopen($fileName,"w");
		            if ($fp != false){
				        fwrite($fp,"<?php\n");
				            fwrite($fp,'$PB_CONFIG[\'site_title_full\'] = "'.$_POST['site_title_full']."\";\n");
				            fwrite($fp,'$PB_CONFIG[\'short_name\'] = "'.$_POST['short_name']."\";\n");
			                fwrite($fp,'$PB_CONFIG[\'subtitle\'] = "'.$_POST['subtitle']."\";\n");
			                fwrite($fp,'$PB_CONFIG[\'keywords\'] = "'.$_POST['keywords']."\";\n");
			                fwrite($fp,'$PB_CONFIG[\'description\'] = "'.$_POST['description']."\";\n");
			                fwrite($fp,'$PB_CONFIG[\'author\'] = "'.$_POST['author']."\";\n");
			                fwrite($fp,'$PB_CONFIG[\'charset\'] = "'.$_POST['charset']."\";\n");
			                fwrite($fp,'$PB_CONFIG[\'language\'] = "'.$_POST['language']."\";\n");
			            fwrite($fp,"?>\n");
		                fclose($fp);
				        // If successful.
		                $PB_output .= '<div class="message"><p><b><font color="green">Meta Settings saved successfully.</font></b>.</p></div>';
		                $PB_output .= '<div class="message unspecific"><a href="?step=intro">Previous Step</a> - <a href="?step=step_2">Next Step</a></div>';
					} else $PB_output .= '<div class="message error"><p>ERROR: unable to open '.$fp.',<b>Wrong file name!</b></p></div>'; // If upload is un-successful.
                } else {
			        $PB_output .= "<div class=\"message user_status\">
			            <b>The <b>meta description</b> is too long...</b><p>Meta Description can be up to $meta_descmax characters - Actual Length: <font color=red>".strlen($admin_bio)."</font> characters.</p>
		                <form><input type=\"button\" value=\"Back\" onClick=\"history.back()\"></form>
			        </div>";
		        }
		    } else {
			    $PB_output.='<div align="center" class="head"><h2>'.$title.'<br/> Meta Data Settings</h2></div>';
	            $PB_output.='<form method="post" action="?step=step_1&amp;c=ok" id="setupform">';
		            $PB_output .= '<fieldset><legend><b>Site Meta information (required):</b></legend>'."\n";
					$PB_output.='<center><table width="100%" align="center">';
		                $PB_output.='<tr><td align="right">Site Title:</td><td><input type="text" size="100" name="site_title_full" value="" required /></td></tr>';
		                $PB_output.='<tr><td align="right">Site Title(Short):</td><td><input type="text" size="100" name="short_name" value="" /></td></tr>';
	                    $PB_output.='<tr><td align="right">Subtitle:</td><td><input type="text" size="100" name="subtitle" value="" /></td></tr>';
						$PB_output.='<tr><td align="right">Meta Author:</td><td><input type="text" size="100" name="author" value="" /></td></tr>';
		                $PB_output .='<tr><td align="right">Meta Keywords:</td><td><input name="keywords" type="text" onkeyup="cnt(this,document.setupform.counttotalwords)" size="100" value="" maxlength="255"><br /><span class ="admin_hints"><input type="text" name="counttotalwords" style="width:10% !important;" value="0" size="3" onkeyup="cnt(document.setupform.keywords,this)" readonly> words,Separate keywords by comma</span></td></tr>';
	                    $PB_output.='<tr><td align="right">Meta Description:</td><td><textarea cols="100" name="description"></textarea></tr>';
		                $PB_output.='<tr><td align="right">Charset:</td><td><select name="charset">';
			                foreach( $_charsets as $charset) $PB_output.='<option value="'.$charset.'">'.$charset.'&nbsp;</option>';
		                $PB_output.='</select></td></tr>';
		
		                $PB_output.='<tr><td align="right">Meta Language:</td><td><select name="language">';
		                    foreach( $_languages as $language => $key) $PB_output.='<option value="'.$language.'">'.$key.'</option>';
		                $PB_output.=' </select></td></tr>';
		                $PB_output.='<tr><td colspan="2"><center><input class="save" type="submit" name="saveMeta" value="Save &amp; Continue" onClick="showNotify(\'Saving...metadata settings...please...wait...\');" /></center>';
		            $PB_output.='</table></center></fieldset>';
		        $PB_output.='</form>';
		    }
		    break;
		
		    case 'step_2':
		    include(CONFIG_PATH.'formats_date.php');
		    if (isset($_POST['saveFile']) AND isset($_GET['step']) AND $_GET['step']=="step_2" AND isset($_GET['c']) AND $_GET['c']=="ok") { 
		        $fileName = CONFIG_PATH.'settings_file.php';
		        $fp = @fopen($fileName,"w");
				if ($fp != false){
				    fwrite($fp,"<?php\n");
				    fwrite($fp,'$PB_CONFIG[\'itemsPerPage\'] = "'.$_POST['itemsPerPage']."\";\n");
				    fwrite($fp,'$PB_CONFIG[\'max_recent\'] = "'.$_POST['max_recent']."\";\n");
				    fwrite($fp,'$PB_CONFIG[\'max_number_teams\'] = "'.$_POST['max_number_teams']."\";\n");
				    fwrite($fp,'$PB_CONFIG[\'min_number_teams\'] = "'.$_POST['min_number_teams']."\";\n");
				    fwrite($fp,'$PB_CONFIG[\'max_number_uploads\'] = "'.$_POST['max_number_uploads']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'log_threshold\'] = "'.$_POST['log_threshold']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'log_date_format\'] = "'.$_POST['log_date_format']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'display_dirsize\'] = "'.$_POST['display_dirsize']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'allow_create_file\'] = "'.$_POST['allow_create_file']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'allow_delete_file\'] = "'.$_POST['allow_delete_file']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'allow_edit_file\'] = "'.$_POST['allow_edit_file']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'use_js_editor\'] = "'.$_POST['use_js_editor']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'show_ads_banner_top\'] = "'.$_POST['show_ads_banner_top']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'show_ads_banner_side\'] = "'.$_POST['show_ads_banner_side']."\";\n");
			        fwrite($fp,"?>\n");
		            fclose($fp);
				    $PB_output .= '<div class="message"><p><b><font color="green">File Settings saved successfully.</font></b>.</p></div>';
		            $PB_output .= '<div class="message unspecific"><a href="?step=step_1">Previous Step</a> - <a href="?step=step_3">Next Step</a></div>';
				} else $PB_output .= '<div class="message error"><p>ERROR: unable to open '.$fp.',<b>Wrong file name!</b></p></div>'; // If upload is un-successful.
                
			} else {
			    $PB_output.='<div class="head"><h2>'.$title.',<br/> File Settings</h2></div>';
				$PB_output.='<form method="post" action="?step=step_2&amp;c=ok" id="setupform">';
		            $PB_output .= '<fieldset><legend><b>Main information (required):</b></legend>'."\n";
		            $PB_output .= '<table width="100%" align="center">';
		                $PB_output.='<tr><td align="right">Items Per Page:</td><td><select name="itemsPerPage">';
						    foreach($per_page_array as $itemsPerPage){
							    $PB_output .='<option value="'.$itemsPerPage.'"';
								    if($itemsPerPage == 6) $PB_output .='selected';
								$PB_output .= '>'.$itemsPerPage.'</option>';
						    }
						$PB_output.='</select></td></tr>';
						
						$PB_output.='<tr><td align="right">Maximum Recent Articles:</td><td><select name="max_recent">';
						    foreach($per_page_array as $max_recent){
							    $PB_output .='<option value="'.$max_recent.'"';
								    if($max_recent == 3) $PB_output .='selected';
								$PB_output .= '>'.$max_recent.'</option>';
						    }
						$PB_output.='</select></td></tr>';
						
						$PB_output.='<tr><td align="right">Maximum Number Uploads:</td><td><select name="max_number_uploads">';
						    foreach($per_page_array as $max_number_uploads){
							    $PB_output .='<option value="'.$max_number_uploads.'"';
								    if($max_number_uploads == 10) $PB_output .='selected';
								$PB_output .= '>'.$max_number_uploads.'</option>';
						    }
						$PB_output.='</select></td></tr>';
						
		                $PB_output.='<tr><td align="right">Maximum Teams Per League:</td><td><input type="text" name="max_number_teams" value="20" /></td></tr>';
		                $PB_output.='<tr><td align="right">Minimum Teams Per League:</td><td><input type="text" name="min_number_teams" value="10" /></td></tr>';
		                
		                $PB_output.='<tr><td align="right">Log Date Format:</td><td><select name="log_date_format">';
		                    foreach( $_date_formats as $format) {
		                        $PB_output.='<option value="'.$format.'">'.$format.'</option>';
	                        }
		                $PB_output.=' </select></td></tr>';
						
		                $PB_output.='<tr><td align="right">Log Treshold:</td><td><select name="log_threshold">';
		                    $PB_output.='<option value="1">1</option>';
                            $PB_output.='<option value="0" selected>0</option>';
                        $PB_output.='</select></td></tr>';
		
	    	            $PB_output.='<tr><td align="right">Show Directory Size:</td><td><select name="display_dirsize">';
		                    $PB_output.='<option value="YES">Yes</option>';
                            $PB_output.='<option value="NO" selected>No</option>';
                        $PB_output.=' </select></td></tr>';
		
		                $PB_output.='<tr><td align="right">Allow Create File:</td><td><select name="allow_create_file">';
		                    $PB_output.='<option value="YES" selected>Yes</option>';
                            $PB_output.='<option value="NO">No</option>';
		                $PB_output.='</select></td></tr>';
		
		                $PB_output.='<tr><td align="right">Allow Delete File:</td><td><select name="allow_delete_file">';
	        	            $PB_output.='<option value="YES" selected>Yes</option>';
                            $PB_output.='<option value="NO">No</option>';
	     	            $PB_output.='</select></td></tr>';
		
		                $PB_output.='<tr><td align="right">Allow Edit File:</td><td><select name="allow_edit_file">';
		                    $PB_output.='<option value="YES" selected>Yes</option>';
                            $PB_output.='<option value="NO">No</option>';
                        $PB_output.='</select></td></tr>';
		
		                $PB_output.='<tr><td align="right">Use JS Editor:</td><td><select name="use_js_editor">';
		                    $PB_output.='<option value="YES">Yes</option>';
                            $PB_output.='<option value="NO" selected>No</option>';
		                $PB_output.='</select></td></tr>';
				
		                $PB_output.='<tr><td align="right">Show Top Ad Banners:</td><td><select name="show_ads_banner_top">';
		                    $PB_output.='<option value="YES">Yes</option>';
                            $PB_output.='<option value="NO" selected>No</option>';
		                $PB_output.='</select></td></tr>';
				
		                $PB_output.='<tr><td align="right">Show Side Ads Banners:</td><td><select name="show_ads_banner_side">';
		                    $PB_output.='<option value="YES">Yes</option>';
                            $PB_output.='<option value="NO" selected>No</option>';
		                $PB_output.='</select></td></tr>';
				
		                $PB_output.='<tr><td colspan="2"><center><input class="save" type="submit" name="saveFile" value="Save Setup" onClick="showNotify(\'Saving...file settings...please...wait...\');" /></center></td></tr>';
		            $PB_output.='</table>';
		            $PB_output.='</fieldset>';
		        $PB_output.='</form>';
			}
		    break;
			
		    case 'step_3':
	            if (isset($_POST['saveAdmin']) AND isset($_GET['step']) AND $_GET['step']=="step_3" AND isset($_GET['c']) AND $_GET['c']=="ok") { 
		            $admin_username = $_POST['admin_username'];
					$admin_password = $_POST['admin_password'];
					$password2 = $_POST['password2'];
					$role = 'superadmin';
					$display_name = sanitize($_POST['display_name']);
	            	$admin_email = isset($_POST['admin_email']) ? $_POST['admin_email'] : '';
	              	$admin_bio = $_POST['admin_bio']; 
	                $header = isset($_POST['header']) ? $_POST['header'] : 'header.png';
	                $avatar = isset($_POST['avatar']) ? $_POST['avatar'] : 'no_avatar.jpg';
	                $show_avatar = isset($_POST['show_avatar']) ? $_POST['show_avatar'] : 'NO';
	                $texture = isset($_POST['texture']) ? $_POST['texture'] : '1.jpg';
	                $skin = isset($_POST['skin']) ? $_POST['skin'] : 'default';
			
	                if (strlen($admin_bio)<$descmax) {
    		            //if long description IS NOT too long, go on executing... cleaning/depurate input
	                    if (isset($admin_username) AND $admin_username != NULL) { //if a different admin is specified
		                    $PB_output .= "<p>Admin Username specified...</p>";
		                    if (!validate_email($admin_email)) { //if author doesn't have a valid email address, just ignore it and use default author
		                        $PB_output .= "<p>Admin's email address not present or not valid. Admin Username will be IGNORED</p>";
		                        $admin_username = NULL; //ignore admin username
		                        $admin_email = NULL; //ignore email
	                        }
                        } else $email = NULL; //if author's name doesn't exist unset also email field //ignore email
                        
		                // Try to register the user
		                $error = registerUser($admin_username,$admin_password,$password2,$role,$display_name,$admin_email,$avatar,$show_avatar);
						if ($error == '') {
	                        $PB_output .= "<div class=\"message\">Super Admin: $username was registered successfully!</div>";
		                   
		            	    $fileName = CONFIG_PATH.'settings_admin.php';
                            if (strlen($fileName)>0){
		                        $fp = @fopen($fileName,"w");
		                        if ($fp != false){
				                    fwrite($fp,"<?php\n");
		                            fwrite($fp,'$PB_CONFIG[\'admin_password\'] = "'.$admin_password."\";\n");//fwrite($fp,'$PB_CONFIG[\'admin_password\'] = "'.sha1($_POST['admin_password'])."\";\n");
					                fwrite($fp,'$PB_CONFIG[\'admin_username\'] = "'.$admin_username."\";\n");
					                fwrite($fp,'$PB_CONFIG[\'display_name\'] = "'.$display_name."\";\n");
					                fwrite($fp,'$PB_CONFIG[\'admin_email\'] = "'.$admin_email."\";\n");
					                fwrite($fp,'$PB_CONFIG[\'admin_bio\'] = "'.$admin_bio."\";\n");
				                    fwrite($fp,'$PB_CONFIG[\'header\'] = "'.$header."\";\n");
									fwrite($fp,'$PB_CONFIG[\'show_avatar\'] = "'.$show_avatar."\";\n");
				                    fwrite($fp,'$PB_CONFIG[\'avatar\'] = "'.$avatar."\";\n");
				                    fwrite($fp,'$PB_CONFIG[\'texture\'] = "'.$texture."\";\n");
				                    fwrite($fp,'$PB_CONFIG[\'skin\'] = "'.$skin."\";\n");
			                        fwrite($fp,"?>\n");
		                            fclose($fp);
				                    // If successful.
		                            $PB_output .= '<div class="message"><p><b><font color="green">Admin Settings saved successfully.</font></b>.</p></div>';
		                            $PB_output .= '<div class="message unspecific"><a href="?step=step_2">Previous Step</a> - <a href="?step=step_4">Step 4</a></div>';
				                } else $PB_output .= '<div class="message error"><p>ERROR: unable to open '.$fp.',<b>Wrong file name!</b></p></div>'; // If upload is un-successful.
			                } else $PB_output .= '<div class="message error"><p><b>File not found!</b></p></div>'; // If upload is un-successful.
                        } else echo $error;
                    } else {
			            $PB_output .= "<div class=\"message user_status\">
			                <b>The <b>Ad Text</b> is too long...</b><p>Ad Text can be up to $descmax characters - Actual Length: <font color=red>".strlen($admin_bio)."</font> characters.</p>
		                    <form><input type=\"button\" value=\"Back\" onClick=\"history.back()\"></form>
		              	</div>";
	               	}
                } else {
	                $PB_output.='<div class="head"><h2>'.$title.',<br/> File Settings</h2></div>';
		            $PB_output .= '<form action="?step=step_3&amp;c=ok" method="POST" enctype="multipart/form-data" name="setupform" id="setupform">'."\n";
                    $PB_output .= '<fieldset><legend><b>Enter admin information (required):</b></legend>'."\n";
				        if ($showmin!=NULL and $showmin!="0") $PB_output .= '<p><span class ="admin_hints">Your site configuration allows you to upload files up to '.$showmin.'MB</span></p>';
				        $PB_output .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.$max_size_in_kb.'">';
			    
		                $PB_output.='<table width="100%" align="center">';
	                        $PB_output.='<tr><td align="right">Admin Username:</td><td colspan="2"><input type="text" name="admin_username" size="50" maxlength="50" value="'.$PB_CONFIG['admin_username'].'" /></td></tr>'."\n";
	                        $PB_output.='<tr><td align="right">Admin Password:</td><td colspan="2"><input type="text" name="admin_password" size="50" maxlength="255" value="'.$PB_CONFIG['admin_password'].'" /></td></tr>'."\n";
	                        $PB_output.='<tr><td align="right">Repeat Password:</td><td colspan="2"><input type="text" name="password2" size="50" maxlength="255" value="" /></td></tr>'."\n";
	                        $PB_output.='<tr><td align="right">Display Name:</td><td colspan="2"><input type="text" name="display_name" size="50" maxlength="50" value="'.$PB_CONFIG['display_name'].'" /></td></tr>'."\n";
	                        $PB_output.='<tr><td align="right">Admin Email:</td><td colspan="2"><input type="text" name="admin_email" size="50" maxlength="50" value="'.$PB_CONFIG['admin_email'].'" /></td></tr>'."\n";
			    	        $PB_output .= '<tr><td align="right">Show Avatar:</td><td colspan="2"><select name="show_avatar">';
					            $PB_output.='<option value="YES" selected>Yes</option>';
                                $PB_output.='<option value="NO">No</option>';
                            $PB_output .= '</select></td></tr>'."\n";
				
				            $PB_output.='<tr><td align="right">Site Header:</td><td><select name="header" onchange="changeHeader();">';
				                if(is_dir(HEADER_PATH)){
	                            $headers=GetDirContents(HEADER_PATH,'files');
	                            foreach( $headers as $header) {
		                            $PB_output.='<option value="'.$header.'">'.$header.'&nbsp;</option>';
	                            }
				                } else $PB_output .= '<div class="message user_status">avatar path is not set.</div>';
		                        $PB_output.='</select></td>';
				                $PB_output.='<td align="center"><div id="header_box_image" style="cursor:pointer;"><img src="'.HEADER_URL.$headers[0].'" name="showHeader" onclick="nextHeader();" width="100px" height="50px" title="click to show the next header" /></div></td>';
                            $PB_output.='</tr>'."\n";
				
				            $PB_output.='<tr><td align="right">Avatar:</td><td><select name="avatar" onchange="changeAvatar();">';
				                if(is_dir(AVATAR_PATH)){
	                            $avatars=GetDirContents(AVATAR_PATH,'files');
	                            foreach( $avatars as $avatar) {
		                            $PB_output.='<option value="'.$avatar.'">'.$avatar.'&nbsp;</option>';
	                            }
				                } else $PB_output .= '<div class="message user_status">avatar path is not set.</div>';
		                        $PB_output.='</select></td>';
				                $PB_output.='<td align="center"><div id="avatar_box_image" style="cursor:pointer;"><img src="'.AVATAR_URL.$avatars[0].'" name="showAvatar" onclick="nextAvatar();" width="100px" height="50px" title="click to show the next avatar" /></div></td>';
                            $PB_output.='</tr>'."\n";
				
			    	        $PB_output.='<tr><td align="right">Background Textures:</td>
					            <td><select name="texture" onchange="changeTexture();">';
			      	            if(is_dir(TEXTURES_PATH)){
	                            $textures=GetDirContents(TEXTURES_PATH,'files');
					                foreach( $textures as $texture) {
		                                $PB_output.='<option value="'.$texture.'">'.$texture.'&nbsp;</option>';
		                            } 
				                } else $PB_output .= '<div class="message user_status">textures path is not set.</div>';
		                        $PB_output.='</select></td>';
		                        $PB_output.='<td align="center"><div id="texture_box_image" style="cursor:pointer;"><img src="'.TEXTURES_URL.$textures[0].'" name="showTexture" onclick="nextTexture();" width="100px" height="50px" title="click to show the next background texture" /></div></td>';
                            $PB_output.='</tr>'."\n";
				
				            $PB_output.='<tr><td align="right">Skin:</td>
					            <td colspan="2"><select name="skin">';
				                if(is_dir(SKIN_PATH)){
	                                $files=GetDirContents(SKIN_PATH,'dirs');
	                                foreach( $files as $file) {
                                    //$file=substr($file,10);
		                            if($file != ".." && $file != ".") {
		                                $PB_output.='<option value="'.$file.'">'.$file.'&nbsp;</option>';
		                            }
	                                }
				                } else $PB_output .= '<div class="message user_status">Skin path is not set.</div>';
		                       $PB_output.='</select></td>';
		                    $PB_output.='</tr>'."\n";
				
			                $PB_output.='<tr><td align="right">Admin Bio:</td><td colspan="2"><textarea name="admin_bio" id="admin_bio" rows="8" cols="80" onKeyDown="limitText(this.form.admin_bio,this.form.countdown,4000);" onKeyUp="limitText(this.form.admin_bio,this.form.countdown,4000);" size="50" maxlength="4000">'.$PB_CONFIG['admin_bio'].'</textarea>
			                    <br/><span class ="admin_hints"><input class="grid-20" name="countdown" type="text" value="4000" class ="admin_hints" size="3" readonly /> remaining characters.</span> 
			                </td></tr>'."\n";
				
			            $PB_output .= '</table>'."\n";
			        $PB_output .= '</fieldset>'."\n";
			
		         	$PB_output .= '<center><input type="submit" name="saveAdmin" value="Save &amp; Continue" onClick="showNotify(\'Saving...admin...settings...please...wait...\');" class="save" /></center>'."\n";
		            $PB_output .= '</form>'."\n";
		        }
		    break;
		    case 'step_4':
			    $PB_output.='<div class="head"><h2>'.$title.',<br/> Creating Settings Bootstrap File</h2></div>';
		        if(file_exists(CONFIG_PATH.'settings_admin.php') && file_exists(CONFIG_PATH.'settings_meta.php') && file_exists(CONFIG_PATH.'settings_file.php')){
		            $fileName = CONFIG_PATH.'settings.php';
                    if (strlen($fileName)>0){
		                $fp = @fopen($fileName,"w");
		                if ($fp != false){
				            fwrite($fp,"<?php\n");
				            fwrite($fp,"include(CONFIG_PATH.'settings_admin.php');\n");
				            fwrite($fp,"include(CONFIG_PATH.'settings_meta.php');\n");
				            fwrite($fp,"include(CONFIG_PATH.'settings_file.php');\n");
		                    fwrite($fp,"?>\n");
					    	// If successful.
		                    $PB_output .= '<div class="message"><p><b><font color="green">The Bootstrap Settings File saved successfully.</font></b>.</p></div>';
							$PB_output .= '<div class="message unspecific"><a href="?step=step_3">Previous Step</a> - <a href="?step=finish">Finish Setup</a></div>';
						}
				    } else $PB_output .= '<div class="message error"><p><b>File not found!</b></p></div>'; // If upload is un-successful.
			    } else {
			        if(!file_exists(CONFIG_PATH.'settings_meta.php')) $PB_output = '<div class="message user_status"><p>The meta settings file does not exists, return to <a href="?step=step_1">step 1</a> to create the file.</p></div>';
			        if(!file_exists(CONFIG_PATH.'settings_file.php')) $PB_output = '<div class="message user_status"><p>The file settings file does not exists, return to <a href="?step=step_2">step 2</a> to create the file.</p></div>';
			        if(!file_exists(CONFIG_PATH.'settings_admin.php')) $PB_output = '<div class="message user_status"><p>The admin settings file does not exists, return to <a href="?step=step_3">step 3</a> to create the file.</p></div>';
			    }
		    break;
			case 'finish':
				if(isset($_GET['deleteInstaller']) == 'true'){
				    if(unlink('install.php')) {
					    $PB_output .= '<div class="message "><p>Installer deleted successfully</p></div>';
		            } else $PB_output .= '<div class="message user_status"><p>Installer removal un-successfully, you have to delete the installer manually.</p></div>';
					$PB_output .= '<div class="message unspecific"><a href="?step=step_4">Previous Step</a> - <a href="index.php?mode=home">Home Page</a> - <a href="index.php?mode=admin">Login To Admin</a></div>';
		        } else if(file_exists('install.php')) $PB_output .= '<div class="message unspecific">Site setup completed successfully, it is recommended that you delete this file <em>install.php</em> before you continue. - <a href="?step=finish&amp;deleteInstaller=true">delete install.php</a></div>';
			break;
	    }
	    $PB_output .= '</div>';
	    echo $PB_output;
?>         <div id="status_notification"></div>     
            <div class="footer">
                <div class="site-generator-wrapper">
				    <p>&copy; 2014. All rights reserved. <?php if($step == 'finish') echo '<a href="index.php?mode=admin">Site Admin</a>';?></p>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
		<script type="text/javascript">
		    <?php
			    if($step == 'intro') echo "$('.steps-nav ul li:eq(0)').addClass('active');\n";
			    if($step == 'step_1') echo "$('.steps-nav ul li:eq(1)').addClass('active');\n";
			    if($step == 'step_2') echo "$('.steps-nav ul li:eq(2)').addClass('active');\n";
			    if($step == 'step_3') echo "$('.steps-nav ul li:eq(3)').addClass('active');\n";
			    if($step == 'step_4') echo "$('.steps-nav ul li:eq(4)').addClass('active');\n";
			    if($step == 'finish') echo "$('.steps-nav ul li:eq(5)').addClass('active');\n";
			?>
		</script>
    </body>
</html>