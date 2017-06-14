<?php
    define('PBD_FLM', TRUE);
	define('PATH', str_replace('\\','/',dirname(dirname(dirname(__dir__)))).'/');
	if(file_exists(PATH.'system/Engine.php')) require(PATH.'system/Engine.php');
	else require('./system/Engine.php');
	
	if ((isset($_SESSION['validUser'])) && ($_SESSION['validUser'] == true)) $logged_in = 1;
    else $logged_in = 0;
	$logout_url = VIEW_URL.'admin/logout.php';
	$home_url = BASE_URL.'index.php?mode=home';
    if (($logged_in == 1) && (isset($_SESSION['userName'])) && (isset($_POST['username'])) && ($_SESSION['userName'] == $_POST['username'])){
	    show_error('You are already logged in with the user name supplied. <a href="'.$logout_url.'">Log Out</a> or <a href="'.$home_url.'">Return Home</a> ',307,'Log In Error');
    }
	$UTIL->timer_start();
    $error = '0';
echo _DOCTYPE;
//echo'<!DOCTYPE html>';
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $CFG->config['language'] ;?>" lang="<?php echo $CFG->config['language'] ;?>">
    <head> 
	    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CFG->config['charset'] ;?>" />
        <meta http-equiv="Content-Style-Type" content="text/css" />
	    <meta http-equiv="Language" content="<?php echo $CFG->config['language'] ;?>" />
        
        <title><?php echo ucfirst($CFG->config['site_title_full']).' Login Page'; ?></title>
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
		</script>
		<link rel="icon" href="<?php echo BASE_URL?>favicon.ico" type="image/x-icon" />
	    <link rel="shortcut icon" href="<?php echo BASE_URL?>favicon.ico" type="image/x-icon" />
	    <link rel="apple-touch-icon" href="<?php echo BASE_URL?>apple-touch-icon.png" />
		<link rel="stylesheet" href="<?php echo SKIN_FILE; ?>" type="text/css" />
	</head>
    <body>
	    <header id="header" class="head-section">
			<div class="logo-wrapper" align="center"><img src="<?php echo HEADER_URL.$CFG->config['header'];?>" width="100%" height="140"></div>
			<div class="header pbd-black_gray-bkg"><h1><?php echo $CFG->config['site_title_full'].$separator.' Login Page';?></h1></div>
	    </header>
	    <div class="hRule">&nbsp;</div>
        <div id="container">
		    <section id="contentBody">
			    <?php if ((!isset($_POST['submitBtn']))) {?>
			        <div class="sidebar box grid-1">
					    <?php
						    echo'<div class="icon icon_login_lock_large">&nbsp;</div>';
						    echo'<p class="text">Login to proceed to the admin panel<br/>Not a member ?, <a href="'.VIEW_URL.'admin/register.php" title="Register as a new user">click to register as a new user</a>.</p>';
					    ?>
					    <br/><a href="<?php echo BASE_URL.'index.php?mode=home'; ?>">return to home page</a>
                    </div>
					<div class="content grid-3">
		                <div class="box2">
				            <form id="loginform" class="box2 grid-4" action="<?php echo SELF; ?>" method="post" name="loginform">
                                <fieldset><legend>Enter Required Information</legend>
					                <table width="100%" class="table">
                                        <tr><td>Username* : </td><td><input name="username" type="text" class="grid-3" required/></td></tr>
                                        <tr><td>Password* : </td><td><input name="password" type="password" class="grid-3" required /></td></tr>
                                        <tr><td colspan="2"><center><input type="submit" name="submitBtn" value="Login" class="save" /><a href="<?php echo BASE_URL.'index.php?mode=home'?>" class="cancel" title="Cancel and return to home page" >Cancel</a><a href="<?php echo VIEW_URL.'admin/register.php'?>" class="register" title="Register as a new user">Register</a></center></td></tr>
                                    </table>
									<p class="admin_hints">Fields marked with * are required.</p>
					            </fieldset> 
                            </form>
							<div class="clear">&nbsp;</div>
		                </div>
		            </div>
                            <?php 
                } else if (isset($_POST['submitBtn'])){
			        /* check they filled in what they were supposed to and authenticate */
             	    if(!$_POST['username'] | !$_POST['password']) echo('<div class="box2"><div class="message user_status"><p>You did not fill in a required field.<br/><a href="javascript:history.back()">Back</a></p></div></div>');
	                //else if ((isset($_SESSION['validUser'])) && ($_SESSION['validUser'] == true) && (isset($_SESSION['userName'])) && ($_SESSION['userName'] == $_POST['username'])) echo'<div class="message error">You are already logged in</div>';
					else {
						// Get user input
	                    $username =  $_POST['username'];
	                    $password = $_POST['password'];
                                
	                    // Try to login the user
	                    $error = loginUser($username,$password);
						echo'<div class="sidebar box grid-1">';
						    if ($error == '') {
						        echo'<div class="icon icon_success">&nbsp;</div>';
						        echo'<p class="text">You are now logged in, according to your user rights, you can add, edit and delete news articles, leagues and league cups.</p>';
					        } else{
						        echo'<div class="icon icon_error">&nbsp;</div>';
						        echo'<p class="text">You are still not logged in, you made an error.</p>';
					        }
					        echo'<br/><a href="../../index.php?mode=home">return to home page</a>';
                        echo'</div>';
				        echo'<div class="content grid-3">
		                    <div class="box2">';
							    echo '<div class="head"><h2>'.($error == '' ? 'Login result:' : '<font color="red">Login Problem</font>').'</h2></div>';
				                echo '<div class="hRule">&nbsp;</div>';
                                echo '<div id="result" class="panel grid-auto">';
                                   echo'<p>';
	                                    if ($error == '') echo '<div class="message"><p>Welcome '.$username.' !, you are logged in!<br/><a href="'.BASE_URL.'index.php?mode=admin">Now you can visit the <b>Admin</b> page!</a> | or | <a href="'.BASE_URL.'index.php?mode=home">return Home page !</a></p></div>';
	                                    else echo $error.'<br/><a href="javascript:history.back()">Back</a>';
					                echo'</p>';
	                            echo'</div>';
								echo'<div class="clear">&nbsp;</div>';
		                    echo'</div>';
		                echo'</div>';
			        }
                }
        include(BASE_PATH."footer.php");?>