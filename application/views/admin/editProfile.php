<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    if ($logged_in == 0 OR $_SESSION['userName'] == '') { 
	    die('You are not logged in, please login first<a href="javascript:history.back()">back</a>');
    }
    
    if(isset($_POST['submitBtn']) AND isset($_GET['action']) AND $_GET['action'] == 'edit' AND isset($_GET['c']) AND $_GET['c'] == 'ok'){
	    if(isset($_POST['username']) && isset($_POST['password1']) && isset($_POST['displayname']) && isset($_POST['email']) && isset($_POST['id']) >= 0 && isset($_POST['password1']) == isset($_POST['password2']) && (strlen(isset($_POST['password1'])) < 6)){
			$PB_output .= '<h3>Result</h3>';
			$id = $_POST['id'];
			$user_name = strip_tags(stripslashes($_POST['username']));		
			$password1 = strip_tags(stripslashes($_POST['password1']));
			$password2 = strip_tags(stripslashes($_POST['password2']));		
			$displayname = strip_tags(stripslashes($_POST['displayname']));
			$email = strip_tags(stripslashes($_POST['email']));
			$role = $_POST['role'];
			$avatar = $_POST['avatar'];
			$show_avatar = $_POST['show_avatar'];
			
			$fp = @fopen($Users_DB_File, 'r');
			$array = explode("\n", fread($fp, filesize($Users_DB_File))); 
			for($x=0;$x<sizeof($array);$x++) {	// start loop, each line of file
				$temp = explode(":",$array[$x]); // explode the line and assign to temp
		        $line[$x] = "$temp[0]:$temp[1]:$temp[2]:$temp[3]:$temp[4]:$temp[5]:$temp[6]";
			}
            $hpassword = md5($password1);
			$line[$id] = "$user_name:$hpassword:$role:$displayname:$email:$avatar:$show_avatar";
			sort($line);
			$fp = fopen ($Users_DB_File, "w");
			if ($fp != false){
			    fwrite ($fp, $line[0]);
			    for($i=1; $i<sizeof($line);$i++){
				    fwrite ($fp, "\n");
				    fwrite ($fp, $line[$i]);
			    }
			    fclose ($fp);
			    $PB_output .='<div class="message"><p>Profile has been edited successfully. It is recommended that you <a href="'.VIEW_URL.'admin/logout.php">logout</a> and <strong>login</strong> back again.</p></div>';
			}
		} else {
		    $PB_output .'<div class="message user_status">';
		        if ($_POST['password1'] != $_POST['password2']) $PB_output .= "Passwords are not identical!";
	            elseif (strlen($_POST['password1']) < 6) $PB_output .= "Password is to short!";
	            elseif (!$_POST['username']) $PB_output .= "Username not present!";
	            elseif (!$_POST['displayname']) $PB_output .= "Display name not present!";
	
		        $PB_output .="<p>Please go back and fill in the form properly, <a href=\"javascript:history.back()\">back</a>!</p>";
	        $PB_output .='</div>';
		}
	} else {
        if($_GET['profile'] == $is_validUser['user_name'] || $roleID > 5){
            if($_GET['id'] >= 0){
		        $id = $_GET['id'];
		        $fp = @fopen($Users_DB_File, 'r');
		        $array = explode("\n", fread($fp, filesize($Users_DB_File))); 
		        for($x=0;$x<sizeof($array);$x++) {	// start loop, each line of file
		            $temp = explode(":",$array[$x]); // explode the line and assign to temp
		            $line[$x] = "$temp[0]|$temp[1]|$temp[2]|$temp[3]|$temp[4]|$temp[5]|$temp[6]";
		        }
 
		        $mod = explode("|", $line[$id]);

		        $PB_output .='<div class="box2">';
		            if(ACTION == 'edit') $PB_output .= '<h3>Editing '.$mod[0].' Profile</h3>';
		            $PB_output .='<form id="loginform" action="'.$url_prefix.'&amp;action=edit&amp;filetype=profiles&amp;profile='.$mod[0].'&amp;id='.$id.'&amp;c=ok" method="post" name="registerform">
                        <fieldset><legend>Enter Required Information</legend>
			            	<table class="grid-4">
                                <tr><td>Username*:</td><td colspan="2"><input class="grid-3" name="username" type="text" maxlength="30" value="'.$mod[0].'" required /></td></tr>
                                <tr><td>Password*:</td><td colspan="2"><input class="grid-3" name="password1" type="password" maxlength="30" value="'.$mod[1].'" required /></td></tr>
                                <tr><td>Confirm password*:</td><td colspan="2"><input class="grid-3" name="password2" type="password" maxlength="30" value="'.$mod[1].'" required /></td></tr>
                                <tr><td>Display Name*:</td><td colspan="2"><input id="displayname" class="grid-3" name="displayname" type="text" maxlength="30" value="'.$mod[3].'" required /></td></tr>
                                <tr><td>E-Mail*:</td><td colspan="2"><input id="email" class="grid-3" name="email" type="text" value="'.$mod[4].'" required /></td></tr>
					            <tr><td align="right">Choose Avatar:</td>
					         	    <td><select name="avatar" onchange="changeAvatar();">';
				                        if(is_dir(AVATAR_PATH)){
	                                        $avatars=GetDirContents(AVATAR_PATH,'files');
	                                        foreach( $avatars as $avt) $PB_output .= '<option value="'.$avt.'" '.(($mod[5] == $avt) ? 'selected' : '').'>'.$avt.'&nbsp;</option>';
				                        } else $PB_output .='<div class="message user_status">Avatar path is not set.</div>';
						            $PB_output .='</select></td>
						            <td align="center"><div id="avatar_box_image" style="cursor:pointer;"><img src="'.AVATAR_URL.$mod[5].'" name="showAvatar" onclick="nextAvatar();" width="100px" height="50px" title="click to show the next avatar" /></div></td>
					            </tr>
					            <tr><td align="right">Show Avatar:</td><td colspan="2">
					            	<select name="show_avatar">';
					                    $PB_output .='<option value="YES" '.(($mod[6] == 'YES') ? 'selected' : '').'>Yes</option>';
                                            $PB_output .='<option value="NO" '.(($mod[6] == 'NO') ? 'selected' : '' ).'>No</option>';
                                        $PB_output .='</select>';
								$PB_output .='</td></tr>
								<tr><td align="right" valign="middle">User Role*:</td>
								    <td>';
								        $PB_output .='<select name="role">';
					                        $PB_output .= '<option value="user" '.(($mod[2] == 'user') ? 'selected' : '').'>User</option>';
                                            $PB_output .= '<option value="editor" '.(($mod[2] == 'editor') ? 'selected' : '' ).'>Editor</option>';
											$PB_output .= '<option value="admin" '.(($mod[2] == 'admin') ? 'selected' : '' ).'>Admin</option>';
                                        $PB_output .='</select>'; 
							        $PB_output .='</td>
									<td>
									    <p class="admin_hints"><span style="border-bottom:1px dashed #555;">Role Markers</span><br>
                                        *User - Access general pages, but can not post news<br>
                                        *Editor - Post and edit all news<br>
                                        *Admin - Full access
									    </p>
									</td>
								</tr>
								<tr><td colspan="3"><center><input class="save" type="submit" name="submitBtn" value="Save Profile" /><a href="'.SELF.'?mode=home?>" class="cancel" title="Cancel and return to home page" >Cancel</a><input type="reset" value="Reset" /></center></td></tr>
                            </table>
                            <p class="admin_hints">Fields marked with * are required.</p>
						</fieldset>
						<input type="hidden" name="id" value="'.$id.'">
                    </form>';
                $PB_output .='</div>';
	        } else $PB_output .= '<div class="message user_status"><p>No ID specified, please <a href="'.SELF.'?mode=profiles">select an item</a> to be edited first!</p></div>';
	    } else{
	        if($_GET['profile'] != $is_validUser['user_name']) $PB_output .= '<div class="message error"><p>You are trying to edit a profile other than yours.<span class="em i">YOU CAN&rsquo;T DO THAT!</span></p></div>';
	    }
	}
?>
        <script type="text/javascript" charset="utf-8">
			var avatarUrl = "<?php echo AVATAR_URL;?>";
			function nextAvatar() {
                if (document.registerform.avatar[current+1]) {
                    document.images.showAvatar.src = avatarUrl + document.registerform.avatar[current+1].value;
                    document.registerform.avatar.selectedIndex = ++current;
                } else firstAvatar();
			    fadeOpacity('avatar_box_image', 0, <?php echo OVERLAY_OPACITY;?>, <?php echo FADE_DURATION_MS;?>);
            }
		    function firstAvatar() {
                current = 0;
                document.images.showAvatar.src = avatarUrl + document.registerform.avatar[0].value;
                document.registerform.avatar.selectedIndex = 0;
            }
         
			function changeAvatar() {
                current = document.registerform.avatar.selectedIndex;
                document.images.showAvatar.src = avatarUrl + document.registerform.avatar[current].value;
			    fadeOpacity('avatar_box_image', 0, <?php echo OVERLAY_OPACITY;?>, <?php echo FADE_DURATION_MS;?>);
            }
		</script>