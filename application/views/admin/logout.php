<?php
    define('PBD_FLM', TRUE);
	define('PATH', str_replace('\\','/',dirname(dirname(dirname(__dir__)))).'/');
	if(file_exists(PATH.'system/Engine.php')) require(PATH.'system/Engine.php');
	else require('../../../system/Engine.php');
    //require_once(SYSTEM_PATH.'Auth_functions.php');
	if ((isset($_SESSION['validUser'])) && ($_SESSION['validUser'] == true)) $logged_in = 1;
    else $logged_in = 0;
	// true - redirect them to login page.
	// false - stay on logout page.
	if(!isset($CFG->config['redirect']) || $CFG->config['redirect'] == '')$redirect = false;
	else $redirect = $CFG->config['redirect'];
	
	$login_url = VIEW_URL.'admin/login.php';
	$home_url = BASE_URL.'index.php?mode=home';
    if ($logged_in == 0) {
	    show_error('You are not logged in so you cannot log out. <a href="'.$login_url.'">Login</a> or <a href="'.$home_url.'">Return Home</a> ',307,'Logout Error');
    }
	// true redirect them to login page.
	if($redirect) logoutUser(true);
	else{
	    include(BASE_PATH.'header.php');
		    echo'<div class="box">';
	        logoutUser(false);
			echo'</div>';
		include(BASE_PATH.'footer.php');
	}
?>