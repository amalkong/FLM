<?php
    define('PBD_FLM', TRUE);
	//if(!defined('MODE')) define('MODE' , false);
	define('PATH', str_replace('\\','/',dirname(dirname(dirname(__dir__)))).'/');
	if(file_exists(PATH.'system/Engine.php')) require(PATH.'system/Engine.php');
	else require('./system/Engine.php');
	
	$UTIL->timer_start();
	$admin_email = $CFG->config['admin_email'];
	$error = '';
echo _DOCTYPE;
//echo'<!DOCTYPE html>';

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $CFG->config['language'] ;?>" lang="<?php echo $CFG->config['language'] ;?>">
    <head> 
	    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CFG->config['charset'] ;?>" />
        <meta http-equiv="Content-Style-Type" content="text/css" />
	    <meta http-equiv="Language" content="<?php echo $CFG->config['language'] ;?>" />
        
        <title><?php echo ucfirst($CFG->config['site_title_full']).' New User Registry Page'; ?></title>
		<!-- Mobile Specifics -->
        <meta name="HandheldFriendly" content="true">
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1,user-scalable=no">
        <!--<meta name="viewport" content="initial-scale = 0.6, width = device-width">-->
		<!-- Mobile Internet Explorer ClearType Technology -->
        <!--[if IEMobile]>  <meta http-equiv="cleartype" content="on">  <![endif]-->
		<?php echo $required_js_scripts;?>
        <script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
			    <?php if($CFG->config['skin'] == 'default' || $CFG->config['skin'] == '') echo '$.backstretch("'.TEXTURES_URL.$CFG->config['texture'].'");'?>
			    /*-----------------------------------------------------------------------------------*/
                /*	IMAGE HOVER
                /*-----------------------------------------------------------------------------------*/		
		
		        $('.quick-flickr-item').addClass("frame");
                $('.frame a').prepend('<span class="more"></span>');
                //----------------------------------------------
                $('.frame').mouseenter(function(e) {
                    $(this).children('a').children('span').fadeIn(300);
                }).mouseleave(function(e) {
                    $(this).children('a').children('span').fadeOut(200);
                });
            });
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
		<link rel="icon" href="<?php echo BASE_URL.'favicon.ico';?>" type="image/x-icon" />
	    <link rel="shortcut icon" href="<?php echo BASE_URL.'favicon.ico';?>" type="image/x-icon" />
	    <link rel="apple-touch-icon" href="<?php echo BASE_URL.'apple-touch-icon.png';?>" />
		<link rel="stylesheet" href="<?php echo SKIN_FILE; ?>" type="text/css" />
    </head>
    <body>
        <header id="header" class="head-section">
			<div class="logo-wrapper" align="center"><img src="<?php echo HEADER_URL.'header.png'?>" width="100%" height="140"></div>
			<div class="header pbd-black_gray-bkg"><h1><?php echo $CFG->config['site_title_full'].$separator.'New Member Registry Page';?></h1></div>
	    </header>
	    <hr/><a name="TOP">&nbsp;</a>
        <div id="container">
		    
		    <section id="contentBody">
			    <div class="sidebar box grid-1">
                    <p class="text">Enter necessary details to register as a new member.</p><br/>
                    <a href="<?php echo VIEW_URL.'admin/login.php';?>">Back  To Login</a> | <a href="<?php echo SELF.'?mode=home'; ?>">Home</a>
				</div>
		    <div class="content grid-3">
		        <div class="box2">
                <?php if ((!isset($_POST['submitBtn'])) || ($error != '')) {?>
                    <div class="head"><h2>Register user</h2></div>
				    <div class="hRule">&nbsp;</div>
					<form id="loginform" action="<?php echo SELF; ?>" method="post" name="registerform">
                        <fieldset><legend>Enter Required Information</legend>
					        <table class="grid-4">
                                <tr><td>Username*:</td><td colspan="2"><input class="grid-3" name="username" type="text" maxlength="30" required /></td></tr>
                                <tr><td>Password*:</td><td colspan="2"><input class="grid-3" name="password1" type="password" maxlength="30" required /></td></tr>
                                <tr><td>Confirm password*:</td><td colspan="2"><input class="grid-3" name="password2" type="password" maxlength="30" required /></td></tr>
                                <tr><td>Display Name*:</td><td colspan="2"><input id="displayname" class="grid-3" name="displayname" type="text" maxlength="30" required /></td></tr>
                                <tr><td>E-Mail*:</td><td colspan="2"><input id="email" class="grid-3" name="email" type="text" required /></td></tr>
								<tr><td align="right">Choose Avatar:</td>
								    <td><select name="avatar" onchange="changeAvatar();">
								        <?php
				                        if(is_dir(AVATAR_PATH)){
	                                        $avatars=GetDirContents(AVATAR_PATH,'files');
	                                        foreach( $avatars as $avatar) echo '<option value="'.$avatar.'">'.$avatar.'&nbsp;</option>';
				                        } else echo'<div class="message user_status">Avatar path is not set.</div>';
		                                ?>
									</select></td>
						            <td align="center"><div id="avatar_box_image" style="cursor:pointer;"><img src="<?php echo AVATAR_URL.$CFG->config['avatar']?>" name="showAvatar" onclick="nextAvatar();" width="100px" height="50px" title="click to show the next avatar" /></div></td>
								</tr>
								<tr><td align="right">Show Avatar:</td><td colspan="2">
								        <select name="show_avatar">';
					                        <option>Show your avatar?</option>
					                        <option value="YES">Yes</option>
                                            <option value="NO">No</option>
                                        </select>
								    </td>
								</tr>
								<tr><td align="right" valign="middle">User Role*:</td>
								    <td>
								        <select name="role">
					                        <option value="user" selected>User</option>
                                            <option value="editor">Editor</option>
											<option value="admin">Admin</option>
                                        </select> 
							        </td>
									<td>
									    <p class="admin_hints"><span style="border-bottom:1px dashed #555;">Role Markers</span><br>
                                        *User - Access general pages, but can not post news<br>
                                        *Editor - Post and edit all news<br>
                                        *Admin - Full access
									    </p>
									</td>
								</tr>
								
								<tr><td align="right" valign="top">Email User Their Login Details ? *:</td>
								    <td>
                                       <select name="emailuser">
                                            <option value="no">No</option> 
                                            <option value="yes">Yes</option>
                                        </select>
								    </td>
									<td><p class="admin_hints">(Must have supplied a valid email address!)</p></td>
								</tr>
								<tr><td colspan="3"><center><input class="register" type="submit" name="submitBtn" value="Register" /><a href="<?php echo SELF.'?mode=home'?>" class="cancel" title="Cancel and return to home page" >Cancel</a></center></td></tr>
                            </table>
                            <p class="admin_hints">Fields marked with * are required.</p>
						</fieldset>
                    </form>
                <?php 
                }
			
                if (isset($_POST['submitBtn'])){
			        if (!$_POST['username'] || !$_POST['displayname'] || !$_POST['role']  || !$_POST['password1'] || !$_POST['password2']) {
		                show_error('You did not fill in a required field.',204,'Fill All Required Fields');
	                }
					if ((strlen($_POST['username']) > 30) || (strlen($_POST['displayname']) > 30) || (strlen($_POST['password1']) > 30)) {
		                show_error('Maximum character count for the fields,username,password and displayname 30, one or more field exceeded this amount.',204,'Exceed Maximum Character Count');
	                }
				    // Get user input
		            $username  = sanitize($_POST['username']);
		            $password1 = sanitize($_POST['password1']);
		            $password2 = sanitize($_POST['password2']) ;
		            $role = $_POST['role'];
		            $email = $_POST['email'];
		            $displayname = sanitize($_POST['displayname']);
		            $show_avatar = isset($_POST['show_avatar']) ? $_POST['show_avatar'] : 'NO';
		            $avatar = $_POST['avatar'];
                    if (!validate_email($email)) $email = NULL; //ignore email
	                
		            // Try to register the user
		            $error = registerUser($username,$password1,$password2,$role,$displayname,$email,$avatar,$show_avatar);
                
                    echo '<div class="head"><h2>'.($error == '' ? 'Registered' : '<font color="red">Registry Problem</font>').'</h2></div>';
				    echo '<div class="hRule">&nbsp;</div>';
                    echo '<div id="result" class="panel grid-auto">';
					    if($email == NULL) echo "<p>User email address not present or not valid.</p>";
	                    if ($error == '') {
	                        echo "<div class=\"message\">User: $username was registered successfully! The user information has been added to the database.</div>";
		                    echo '<div class="message unspecific"><a href="login.php">You can login here</a></div>';
	                
					        if ($_POST['email'] !='' AND $_POST['emailuser'] == 'yes') {
                                /* recipients */
                                $to = "$email";
                                /* subject */
                                $subject = 'New user registration';
                                /* message */
                                $message = 'You are receiving this email as a new account has been created for you at '.$CFG->config['site_title_full'].'
                                Please visit the above link to sign in and edit your profile. Your login details are as follows
                                username:  '.$username.'
                                Password:  '.$password.'
                                PLEASE CHANGE YOUR PASSWORD FROM THE EDIT PROFILE SCREEN ONCE LOGGED IN !
                                If you have any questions, please email: '.$admin_email.'';
                                /* additional headers */
                                $headers .= "From: $admin_email\r\n";
                                /* and now mail it */
                                mail($to, $subject, $message, $headers);
							    echo'<p>An email has been sent to the user giving them their login details</p>';
                            }
					    } else echo $error.'<br/><a href="javascript:history.back()">Back</a>';
				    echo"</div>";
                }
		        ?>
				<div class="clear">&nbsp;</div>
		        </div>
		    </div>
    <?php include(BASE_PATH."footer.php");?>