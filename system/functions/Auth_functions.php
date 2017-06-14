<?php
    session_start();

    function registerUser($user,$pass1,$pass2,$role='user',$dsname,$email,$avatar,$show_avatar){
	    global $Users_DB_File;
	    
		$errorText = '';
	    // Check passwords
	    if ($pass1 != $pass2) $errorText = "Passwords are not identical!";
	    elseif (strlen($pass1) < 6) $errorText = "Password is to short!";
	
	    // Check user existance	
	    $pfile = fopen($Users_DB_File,"a+");
        rewind($pfile);

        while (!feof($pfile)) {
            $line = fgets($pfile);
            $tmp = explode(':', $line);
            if ($tmp[0] == $user) {
                $errorText = "The selected user name is taken!";
                break;
            }
        }
	
        // If everything is OK -> store user data
        if ($errorText == ''){
		    // Secure password string
		    $userpass = md5($pass1);
    	
		    fwrite($pfile, "$user:$userpass:$role:$dsname:$email:$avatar:$show_avatar\r\n");
        }
    
        fclose($pfile);
	    return $errorText;
    }

    function getRoleId(){
        if ((isset($_SESSION['validUser'])) && ($_SESSION['validUser'] == true)) {
	        $is_validUser = getUser($_SESSION['userName']);
	   
	        if($is_validUser['user_role'] == 'superadmin') $roleID = 5;
	        if($is_validUser['user_role'] == 'admin') $roleID = 4;
	        if($is_validUser['user_role'] == 'editor') $roleID = 3;
	        if($is_validUser['user_role'] == 'user') $roleID = 1;
	        if($is_validUser['user_role'] == '') $roleID = 0;
	        return $roleID;
	    }
	    
		return false;
    }
	
    function getUser($name){
	    global $Users_DB_File;
	    
		$users = array();
	    $users_info = array();
	    
		if(file_exists($Users_DB_File)){
		    $fp = @fopen($Users_DB_File, 'r');
		    $line = file($Users_DB_File);
	        //$line = explode("\n", fread($fp, filesize($Users_DB_File))); 
		    $listed = count($line);
		    for($x=0;$x<$listed;$x++) {	// start loop, each line of file
			    $tmp = explode(":",$line[$x]); // explode the line and assign to tmp
                $users['user_name'] = $tmp[0];
                $users['user_password'] = $tmp[1];
                $users['user_role'] = $tmp[2];
			    if($tmp[3] != '') $users['display_name'] = $tmp[3];
			    else $users['display_name'] = $tmp[0];
			    $users['user_email'] = $tmp[4];
			    $users['user_avatar'] = $tmp[5];
			    $users['show_avatar'] = $tmp[6];
		        
				array_push($users_info,$users);
		    }
		    fclose($fp);
		
		    $total_users= count($users_info);
		    if($total_users > 0){
	            for($i=0;$i<$total_users;$i++){
			        if($name == $users_info[$i]['user_name']) {
			            $user_arr['user_name'] = $users_info[$i]['user_name'];
			            $user_arr['user_role'] = $users_info[$i]['user_role'];
			            $user_arr['user_password'] = $users_info[$i]['user_password'];
			            $user_arr['display_name'] = $users_info[$i]['display_name'];
			            $user_arr['user_email'] = $users_info[$i]['user_email'];
			            $user_arr['user_avatar'] = $users_info[$i]['user_avatar'];
			            $user_arr['show_avatar'] = $users_info[$i]['show_avatar'];
				        return $user_arr;
			        }
			    }
		    }
	    }
	
	    return false;
    }

    function loginUser($user,$pass){
        global $Users_DB_File;
		
	    $errorText = '';
	    $validUser = false;
	
	    // Check user existance	
	    $pfile = fopen($Users_DB_File,"r");
        rewind($pfile);

        while (!feof($pfile)) {
            $line = fgets($pfile);
            $tmp = explode(':', $line);
            if ($tmp[0] == $user) {
                // User exists, check password
                if (trim($tmp[1]) == trim(md5($pass))){
            	    $validUser= true;
            	    $_SESSION['userName'] = $user;
            	    $_SESSION['userRole'] = $tmp[2];
                }
                break;
            }
        }
        fclose($pfile);

        if ($validUser != true) $errorText = "Invalid username or password!";
    
        if ($validUser == true) $_SESSION['validUser'] = true;
        else $_SESSION['validUser'] = false;
	
	    return $errorText;	
    }

    function logoutUser($redirect=false){
        $redirect_url = VIEW_URL.'admin/login.php';
        $home_url = BASE_URL.'index.php';
	    
		// kill session variables
		unset($_SESSION['validUser']);
	    unset($_SESSION['userName']);
        //unset($_SESSION['password']);
        $_SESSION = array(); // reset session array
        session_destroy();   // destroy session.
	
	    // redirect them to anywhere you like.
        if($redirect) header("Location: $redirect_url");
	    else echo '<div class="message"><p>You are now logged out.<a href="'.$home_url.'">return to home page</a></p></div>';
    }

    function checkUser(){
        $url = VIEW_URL.'admin';
	    if ((!isset($_SESSION['validUser'])) || ($_SESSION['validUser'] != true)){
		    if(isset($_GET['mode']) && $_GET['mode'] =='admin') header("Location: $url/login.php");
		    //exit;
	    }
    }
?>