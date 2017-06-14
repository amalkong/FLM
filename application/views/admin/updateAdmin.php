<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    if (($roleID < 5))  {
       echo'<div class="message error"><p>Access Denied!,You are not authorised to access this section, your user role does not permit you to edit admin information.</p></div>';
	   exit;
    }
	
	require(SYSTEM_PATH.'functions/setup_functions.php');
    $PB_output = NULL;
	$filesuffix = NULL; // declare variable for duplicated filenames
	$image_new_name = NULL; // declare variable for image name
	$descmax =4000; #set max characters variable. "max 4000 characters" for long description/details field
?>    
    <script type="text/javascript">
		var headerUrl = "<?php echo HEADER_URL;?>";
		var avatarUrl = "<?php echo AVATAR_URL;?>";
		var textureUrl = "<?php echo TEXTURES_URL;?>";
		// Background Textures
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
		// Avatars
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
	</script>
<div class="content box">
<?php
    $PB_output .= '<div class="header">';
        if (isset($_POST['submit'])) $PB_output .= '<h2>Updating Admin Information...</h2>';
	    else $PB_output .= '<h2>Update Admin Information</h2>';
	$PB_output .= '</div>';
	
	if (isset($_POST['submit']) AND isset($_GET['mode']) AND $_GET['mode']=="admin" AND isset($_GET['section']) AND $_GET['section']=="updateadmin" AND isset($_GET['c']) AND $_GET['c']=="ok") { 
	    if (isset($_POST['admin_username']) && isset($_POST['admin_password'])) { 
		    $id = 0;
		    $admin_username = $_POST['admin_username'];
		    $admin_password = $_POST['admin_password'];
		    $admin_email = isset($_POST['admin_email']) ? $_POST['admin_email'] : '';
		    $admin_bio = $_POST['admin_bio'];
	        $header = isset($_POST['header']) ? $_POST['header'] : 'header.png';
	        $avatar = isset($_POST['avatar']) ? $_POST['avatar'] : 'no_avatar.jpg';
	        $show_avatar = isset($_POST['show_avatar']) ? $_POST['show_avatar'] : '';
	        $texture = isset($_POST['texture']) ? $_POST['texture'] : '1.jpg';
	        $skin = isset($_POST['skin']) ? $_POST['skin'] : 'default';
		
		    $displayname = strip_tags(stripslashes($_POST['displayname']));
		    $role = $_POST['role'];
		
		    $PB_output .= '<h3>Result</h3>';
		    if (strlen($admin_bio)<$descmax) {
    		    //if long description IS NOT too long, go on executing... cleaning/depurate input
	            if (isset($admin_username) AND $admin_username != NULL) { //if a different admin is specified
		            $PB_output .= "<p>Admin Username specified...</p>";
		            if (!validate_email($admin_email)) { //if author doesn't have a valid email address, just ignore it and use default author
		                $PB_output .= "<p>Admin's email address not present or <strong>invalid</strong>. Admin Username will be IGNORED</p>";
		                //$admin_username = NULL; //ignore admin username
		                $admin_email = NULL; //ignore email
	                }
                } else $email = NULL; //if author's name doesn't exist unset also email field //ignore email
			  
		    	
		    	$fileName = CONFIG_PATH.'settings_admin.php';
                if (strlen($fileName)>0){
		            $fp = @fopen($fileName,"w");
		            if ($fp != false){
				        fwrite($fp,"<?php\n");
		                fwrite($fp,'$PB_CONFIG[\'admin_password\'] = "'.$_POST['admin_password']."\";\n");//fwrite($fp,'$PB_CONFIG[\'admin_password\'] = "'.sha1($_POST['admin_password'])."\";\n");
					    fwrite($fp,'$PB_CONFIG[\'admin_username\'] = "'.$admin_username."\";\n");
					    fwrite($fp,'$PB_CONFIG[\'admin_email\'] = "'.$admin_email."\";\n");
					    fwrite($fp,'$PB_CONFIG[\'admin_bio\'] = "'.$admin_bio."\";\n");
				        fwrite($fp,'$PB_CONFIG[\'header\'] = "'.$header."\";\n");
				        fwrite($fp,'$PB_CONFIG[\'show_avatar\'] = "'.$show_avatar."\";\n");
				        fwrite($fp,'$PB_CONFIG[\'avatar\'] = "'.$avatar."\";\n");
				        fwrite($fp,'$PB_CONFIG[\'texture\'] = "'.$texture."\";\n");
				        fwrite($fp,'$PB_CONFIG[\'skin\'] = "'.$skin."\";\n");
			            fwrite($fp,"?>\n");
		                fclose($fp);
		                unset($_GET['section']);
				        // If update is successful.
		                $PB_output .= '<div class="message"><p><b><font color="green">Admin Settings saved successfully.</font></b>. You may have to reload page to see if certain changes took effects</p></div>';
		            //-------------------------------------------
					    $fp = @fopen($Users_DB_File, 'r');
			            $array = explode("\n", fread($fp, filesize($Users_DB_File))); 
			            for($x=0;$x<sizeof($array);$x++) {	// start loop, each line of file
				            $temp = explode(":",$array[$x]); // explode the line and assign to temp
		                    $line[$x] = "$temp[0]:$temp[1]:$temp[2]:$temp[3]:$temp[4]:$temp[5]:$temp[6]";
		    	        }
                        $hpassword = md5($admin_password);
			            $line[$id] = "$admin_username:$hpassword:$role:$displayname:$admin_email:$avatar:$show_avatar";
			            sort($line);
			            $fp = fopen ($Users_DB_File, "w");
			            if ($fp != false){
			                fwrite ($fp, $line[0]);
			                for($i=1; $i<sizeof($line);$i++){
				                fwrite ($fp, "\n");
				                fwrite ($fp, $line[$i]);
			                }
			                fclose ($fp);
			                $PB_output .='<div class="message"><p>Admin Profile also has been edited successfully. It is recommended that you <a href="'.VIEW_URL.'admin/logout.php">logout</a> and <strong>login</strong> back again.</p></div>';
			            }
					} else $PB_output .= '<p class="message error">ERROR: unable to open '.$fp.',<b>Wrong file name!</b></p>'; // If upload is un-successful.
			    } else $PB_output .= '<p class="message error"><b>File not found!</b></p>'; // If upload is un-successful.
        
            } else $PB_output .= "<div class=\"message user_status\"><p><strong>The Admin Bio is too long...</strong>. Admin Bio can be up to $descmax characters - Actual Length: <font color=red>".strlen($admin_bio)."</font> characters.</p><form><input type=\"button\" value=\"Back\" onClick=\"history.back()\"></form></div>";
	    } else $PB_output .= "<div class=\"message user_status\"><p>The admin username and/or admin password wasn&rsquo;t set</p><form><input type=\"button\" value=\"Back\" onClick=\"history.back()\"></form></div>";
    }
	
		$PB_output .= '<form action="?mode=admin&amp;section=updateadmin&amp;c=ok" method="POST" enctype="multipart/form-data" name="setupform" id="setupform">'."\n";
            $PB_output .= '<fieldset><legend><b>Main information (required):</b></legend>'."\n";
				
		        $PB_output.='<input type="hidden" value="'.$is_validUser['user_role'].'" />';
		        $PB_output.='<table width="100%" align="center">';
	                $PB_output.='<tr><td align="right">Admin Password:</td><td colspan="2"><input type="text" name="admin_password" size="50" maxlength="255" value="'.$PB_CONFIG['admin_password'].'" /></td></tr>'."\n";
	                $PB_output.='<tr><td align="right">Admin Username:</td><td colspan="2"><input type="text" name="admin_username" size="50" maxlength="50" value="'.$PB_CONFIG['admin_username'].'" /></td></tr>'."\n";
	                $PB_output.='<tr><td align="right">Admin Email:</td><td colspan="2"><input type="text" name="admin_email" size="50" maxlength="50" value="'.$PB_CONFIG['admin_email'].'" /></td></tr>'."\n";
	                //$PB_output.='<tr><td><input type="text" name="show_avatar" value="'.$PB_CONFIG['show_avatar'].'" /></td></tr>';
	                $PB_output.='<tr><td>Display Name*:</td><td colspan="2"><input id="displayname" class="grid-3" name="displayname" type="text" maxlength="30" value="'.$is_validUser['display_name'].'" required /></td></tr>';
                    
			    	$PB_output .= '<tr><td align="right">Show Avatar:</td><td colspan="2"><select name="show_avatar">';
					    $PB_output.='<option value="YES" '.(($PB_CONFIG['show_avatar'] == 'YES') ? "selected" : '' ).'>Yes</option>';
                        $PB_output.='<option value="NO" '.(($PB_CONFIG['show_avatar'] == 'NO') ? "selected" : '' ).'>No</option>';
                    $PB_output .= '</select></td></tr>'."\n";
				
				    $PB_output.='<tr><td align="right">Avatar:</td>';
				        $PB_output.='<td>';
				            if(is_dir(AVATAR_PATH)){
	                            $avatars=GetDirContents(AVATAR_PATH,'files');
	                            if(count($avatars > 0)){
								    $PB_output.='<select name="avatar" onchange="changeAvatar();">';
								        foreach( $avatars as $avatar) if($UTIL->isValidExt($avatar,$ifxs)) $PB_output.='<option value="'.$avatar.'" '.(($avatar == $CFG->config['avatar']) ? 'SELECTED' : '' ).'>'.$avatar.'&nbsp;</option>';
				                    $PB_output.='</select>';
								} else $PB_output .= '<div class="message user_status">[0] avatar found.</div>';
				            } else $PB_output .= '<div class="message user_status">Avatar path is not set properly.</div>';
		                $PB_output.='</td>';
				        $PB_output.='<td align="center"><div id="avatar_box_image" style="cursor:pointer;"><img src="'.AVATAR_URL.$CFG->config['avatar'].'" name="showAvatar" onclick="nextAvatar();" width="100px" height="50px" title="click to show the next avatar" /></div></td>';
                    $PB_output.='</tr>'."\n";
				
			    	$PB_output.='<tr><td align="right">Background Textures:</td>';
			    	    $PB_output.='<td>';
						    if(is_dir(TEXTURES_PATH)){
						        $textures=GetDirContents(TEXTURES_PATH,'files');
							    if(count($textures > 0)){
						            $PB_output.='<select name="texture" onchange="changeTexture();">';
					                    foreach( $textures as $texture) if($UTIL->isValidExt($texture,$ifxs)) $PB_output.='<option value="'.$texture.'" '.(($texture == $CFG->config['texture']) ? 'SELECTED' : '' ).'>'.$texture.'&nbsp;</option>';
				                    $PB_output.='</select>';
							    } else $PB_output .= '<div class="message user_status">[O] textures found</div>';
				            } else $PB_output .= '<div class="message user_status">Textures path is not set properly.</div>';
		                $PB_output.='</td>';
		                $PB_output.='<td align="center"><div id="texture_box_image" style="cursor:pointer;"><img src="'.TEXTURES_URL.$CFG->config['texture'].'" name="showTexture" onclick="nextTexture();" width="100px" height="50px" title="click to show the next background texture" /></div></td>';
                    $PB_output.='</tr>'."\n";
				    
					$PB_output.='<tr><td align="right">Site Header:</td>';
					    $PB_output.='<td>';
				            if(is_dir(HEADER_PATH)){
	                            $headers=GetDirContents(HEADER_PATH,'files');
							    if(count($headers > 0)){
							        $PB_output.='<select name="header" onchange="changeHeader();">';
	                                    foreach( $headers as $header) if($UTIL->isValidExt($header,$ifxs)) $PB_output.='<option value="'.$header.'" '.(($header == $CFG->config['header']) ? 'SELECTED' : '').'>'.$header.'&nbsp;</option>';
				                    $PB_output.='</select>';
							    } else $PB_output .= '<div class="message user_status">[0] header found.</div>';
				            } else $PB_output .= '<div class="message user_status">header path is not set properly.</div>';
		                $PB_output.='</td>';
				        $PB_output.='<td align="center"><div id="header_box_image" style="cursor:pointer;"><img src="'.HEADER_URL.$CFG->config['header'].'" name="showHeader" onclick="nextHeader();" width="100px" height="50px" title="click to show the next header" /></div></td>';
                    $PB_output.='</tr>'."\n";
				
				    $PB_output.='<tr><td align="right">Skin:</td>';
					    $PB_output.='<td colspan="2">';
				            if(is_dir(SKIN_PATH)){
	                            $skins=GetDirContents(SKIN_PATH,'dirs');
								if(count($skins > 0)){
								    $PB_output.='<select name="skin">';
	                                    foreach( $skins as $file) {
		                                    if($file != ".." && $file != ".") $PB_output.='<option value="'.$file.'" '.(($file == $CFG->config['skin']) ? 'SELECTED' : '').'>'.$file.'&nbsp;</option>';
	                                    }
								    $PB_output.='</select>';
				                } else $PB_output .= '<div class="message user_status">[0] skin folder found.</div>';
				            } else $PB_output .= '<div class="message user_status">Skin path is not set properly.</div>';
		                $PB_output.='</td>';
		            $PB_output.='</tr>'."\n";
				
			        $PB_output.='<tr><td align="right">Admin Bio:</td><td colspan="2"><textarea name="admin_bio" id="admin_bio" rows="8" cols="80" onKeyDown="limitText(this.form.admin_bio,this.form.countdown,4000);" onKeyUp="limitText(this.form.admin_bio,this.form.countdown,4000);" size="50" maxlength="4000">'.$PB_CONFIG['admin_bio'].'</textarea>
			           <br/><span class ="admin_hints"><input class="grid-20" name="countdown" type="text" value="4000" class ="admin_hints" size="3" readonly /> remaining characters.</span> 
			        </td></tr>'."\n";
				
			    $PB_output .= '</table>'."\n";
			$PB_output .= '</fieldset>'."\n";
			
			$PB_output .= '<center><input type="submit" name="submit" value="Update Admin" onClick="showNotify(\'Updating...,please wait....\');" class="save" /><a href="'.SELF.'?mode=admin" class="cancel" title="Cancel and return to admin index" >Cancel</a></center>'."\n";
		$PB_output .= '</form>'."\n";
		
	    echo $PB_output;
?>
</div>