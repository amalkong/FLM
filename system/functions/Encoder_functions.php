<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
/**
 * Remove Invisible Characters
 *
 * This prevents sandwiching null characters between ascii characters, like Java\0script.
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('remove_invisible_characters')){
	function remove_invisible_characters($str, $url_encoded = TRUE){
		$non_displayables = array();
		// every control character except newline (dec 10) carriage return (dec 13), and horizontal tab (dec 09)
		if ($url_encoded){
			$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
		}
		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127
		do{
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);
		return $str;
	}
}
// ------------------------------------------------------------------------
if ( ! function_exists('html_escape')){
	function html_escape($var){
		if (is_array($var)) return array_map('html_escape', $var);
		else return htmlspecialchars($var, ENT_QUOTES, config_item('charset'));
	}
}
    function sanitize($text, $html = true) {
	    if(strpos($text,null) !== false) die("Hijacking attempt, dying....");
            // Convert problematic ascii characters to their true values
	    $search = array("40","41","58","65","66","67","68","69","70","71","72","73","74","75","76","77","78","79","80","81",
		    "82","83","84","85","86","87","88","89","90","97","98","99","100","101","102","103","104","105","106","107",
		    "108","109","110","111","112","113","114","115","116","117","118","119","120","121","122"
		);
        $replace = array("(",")",":","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u",
		    "v","w","x","y","z","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z"
		);
	    $entities = count($search);
	    for ($i=0;$i < $entities;$i++) $text = preg_replace("#(&\#)(0*".$search[$i]."+);*#si", $replace[$i], $text);
	        // the following is based on code from bitflux (http://blog.bitflux.ch/wiki/)
	        // Kill hexadecimal characters completely
	        $text = preg_replace('#(&\#x)([0-9A-F]+);*#si', "", $text);
            	// remove any attribute starting with "on" or xmlns
	        $text = preg_replace('#(<[^>]+[\\"\'\s])(onmouseover|onmousedown|onmouseup|onmouseout|onmousemove|onclick|ondblclick|onload|xmlns)[^>]*>#iU', ">", $text);
	    do {
		    $oldtext = $text;
		    preg_replace('#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i', "", $text);
	        // remove javascript: and vbscript: protocol
	    } while ($oldtext != $text);
	        $text = preg_replace('#([a-z]*)=([\`\'\"]*)script:#iU', '$1=$2nojscript...', $text);
	        $text = preg_replace('#([a-z]*)=([\`\'\"]*)javascript:#iU', '$1=$2nojavascript...', $text);
	        $text = preg_replace('#([a-z]*)=([\'\"]*)vbscript:#iU', '$1=$2novbscript...', $text);
	        $text = preg_replace('#(<[^>]+)style=([\`\'\"]*).*expression\([^>]*>#iU', "$1>", $text);
	        $text = preg_replace('#(<[^>]+)style=([\`\'\"]*).*behaviour\([^>]*>#iU', "$1>", $text);
	    
		if ($html == true) $text = htmlspecialchars($text,true);// $text = htmlspecialchars($text, ENT_COMPAT, 'UTF-8', false);
		return $text;
    }
	
	## Rename uploaded file - LESS STRICT
	function sanitize_filename($text,$rep='_'){
    	//Special Characters to be removed
    	$sp = array(" ", "-","--","__","___","____", "!", "'", '"', "<", "\\", "/", "?", "*", "%", ";", ":", "~");
    	//loop through specified characters
    	for($i = 0; $i < count($sp); $i++) {
    		//Replace Disallowed character with empty string
    		$text = str_replace($sp[$i], $rep, $text);	
    	}
    	$text = strtolower($text);//Change the text to lowercase
	    $text = strip_tags($text); // remove HTML tags.
	    //$text = preg_replace('!\s+!','_',$text); // change space chars to underscores.
	    //$text = stripslashes($text); //remove slashes in the file name
	    $text = str_replace("&", "_and_", $text);
    	//Return string
    	return $text;
		
    }
	## Depurate Content from non accepted characters
    function depurateContent($content) {
	    $content = stripslashes($content);				
	    $content = str_replace('<', '&lt;', $content);
	    $content = str_replace('>', '&gt;', $content);
	    $content = str_replace('& ', '&amp; ', $content);
	    $content = str_replace('’', '&apos;', $content);
	    $content = str_replace('"', '&quot;', $content);
	    $content = str_replace('©', '&#xA9;', $content);
	    $content = str_replace('&copy;', '&#xA9;', $content);
	    $content = str_replace('℗', '&#x2117;', $content);
	    $content = str_replace('™', '&#x2122;', $content);
	    return $content;
    }
	## Rename uploaded file - STRICT
    function renamefilestrict ($filetorename) { // strict rename policy (just characters from a to z and numbers... no accents and other characters). This kind of renaming can have problems with some languages (e.g. oriental)
	    $filetorename = preg_replace("[^a-z0-9._]", "", str_replace(" ", "_", str_replace("%20", "_", strtolower($filetorename))));
	    return $filetorename;
    }

## Validate e-mail address
    function validate_email($address) { //validate email address
	    $match = preg_match('{^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$}',$address);
		if($match) return ($address);
		else return false;
		
    }
	/* validate an email address */
	function validateEmail($email) {
		if (! isset ( $email ) || $email == "") {
			return "The email is a required field and can not be empty<br><br>";
		}
		
		if (strlen ( $email ) < 8) {
			return "The email is too short. Must be at least 8 characters<br><br>";
		}
		
		$version = substr ( phpversion (), 0, 3 );
		if (PHP_VERSION_ID >= 50300) {
			if (! preg_match ( "/^[a-z0-9][_\.a-z0-9-]+@([a-z0-9][0-9a-z-]+\.)+([a-z]{2,4})/", $email )) {
				return "The email you entered is not valid. Enter a valid email address.<br><br>";
			}
		} else {
			if (! ereg ( "^[a-z0-9][_\.a-z0-9-]+@([a-z0-9][0-9a-z-]+\.)+([a-z]{2,4})", $email )) {
				return "The email you entered is not valid. Enter a valid email address.<br><br>";
			}
		}
		return "";
	}
	function checkPasswordStrength($password, $username = false) {
        $returns = array(
            'strength' => 0,
            'error'    => 0,
            'text'     => ''
        );

        $length = strlen($password);

        if ($length < 8) {
            $returns['error']    = 1;
            $returns['text']     = 'The password is not long enough';
        } else {
            //check for a couple of bad passwords:
            if ($username && strtolower($password) == strtolower($username)) {
                $returns['error']    = 4;
                $returns['text']     = 'Password cannot be the same as your Username';
            } elseif (strtolower($password) == 'password') {
                $returns['error']    = 3;
                $returns['text']     = 'Password is too common';
            } else {
                preg_match_all ("/(.)\1{2}/", $password, $matches);
                $consecutives = count($matches[0]);

                preg_match_all ("/\d/i", $password, $matches);
                $numbers = count($matches[0]);

                preg_match_all ("/[A-Z]/", $password, $matches);
                $uppers = count($matches[0]);

                preg_match_all ("/[^A-z0-9]/", $password, $matches);
                $others = count($matches[0]);

                //see if there are 3 consecutive chars (or more) and fail!
                if ($consecutives > 0) {
                    $returns['error']    = 2;
                    $returns['text']     = 'Too many consecutive characters';

                } elseif ($others > 1 || ($uppers > 1 && $numbers > 1)) {
                    //bulletproof
                    $returns['strength'] = 5;
                    $returns['text']     = 'Virtually Bulletproof';
                } elseif (($uppers > 0 && $numbers > 0) || $length > 14) {
                    //very strong
                    $returns['strength'] = 4;
                    $returns['text']     = 'Very Strong';
                } else if ($uppers > 0 || $numbers > 2 || $length > 9) {
                    //strong
                    $returns['strength'] = 3;
                    $returns['text']     = 'Strong';
                } else if ($numbers > 1) {
                    //fair
                    $returns['strength'] = 2;
                    $returns['text']     = 'Fair';
                } else {
                    //weak
                    $returns['strength'] = 1;
                    $returns['text']     = 'Weak';
                }
            }
        }
        return $returns;
    }
	/**
	 * Clean a string of html that may be used as file content
	 *
	 * @param string $text The string to be cleansed. Passed by reference
	 */
	function cleanText(&$text){
		tidyFix($text);
		rmPHP($text);
		//FixTags($text);
	}

	/**
	 * Remove php tags from $text
	 *
	 * @param string $text The html content to be checked. Passed by reference
	 */
	function rmPHP(&$text){
		$search = array('<?','<?php','?>');
		$replace = array('&lt;?','&lt;?php','?&gt;');
		$text = str_replace($search,$replace,$text);
	}

	/**
	 * Removes any NULL characters in $string.
	 * @since 3.0.2
	 * @param string $string
	 * @return string
	 */
	function NoNull($string){
		$string = preg_replace('/\0+/', '', $string);
		return preg_replace('/(\\\\0)+/', '', $string);
	}

	/**
	 * Clean a string that may be used as an internal file path
	 *
	 * @param string $path The string to be cleansed
	 * @return string The cleansed string
	 */
	function CleanArg($path){
		//all forward slashes
		$path = str_replace('\\','/',$path);

		//remove directory style changes
		$path = str_replace(array('../','./','..'),array('','',''),$path);

		//change other characters to underscore
		//$pattern = '#\\.|\\||\\:|\\?|\\*|"|<|>|[[:cntrl:]]#';
		$pattern = '#\\||\\:|\\?|\\*|"|<|>|[[:cntrl:]]#u';
		$path = preg_replace( $pattern, '_', $path ) ;

		//reduce multiple slashes to single
		$pattern = '#\/+#';
		$path = preg_replace( $pattern, '/', $path ) ;

		return $path;
	}

	/**
	 * Use HTML Tidy to validate the $text
	 * Only runs when $config['HTML_Tidy'] is off
	 *
	 * @param string $text The html content to be checked. Passed by reference
	 */
	function tidyFix(&$text,$ignore_config = false){
		global $config;
		if( !$ignore_config ){
			if( empty($config['HTML_Tidy']) || $config['HTML_Tidy'] == 'off' ){
				return true;
			}
		}

		if( !function_exists('tidy_parse_string') ){
			return false;
		}

		$options = array();
		$options['wrap'] = 0;						//keeps tidy from wrapping... want the least amount of space changing as possible.. could get rid of spaces between words with the str_replaces below
		$options['doctype'] = 'omit';				//omit, auto, strict, transitional, user
		$options['drop-empty-paras'] = true;		//drop empty paragraphs
		$options['output-xhtml'] = true;			//need this so that <br> will be <br/> .. etc
		$options['show-body-only'] = true;
		$options['hide-comments'] = false;
		//$options['anchor-as-name'] = true;		//default is true, but not alwasy availabel. When true, adds an id attribute to anchor; when false, removes the name attribute... poorly designed, but we need it to be true
		//
		//	php4
		//
		if( function_exists('tidy_setopt') ){
			$options['char-encoding'] = 'utf8';
			tidyOptions($options);
			$tidy = tidy_parse_string($text);
			tidy_clean_repair();

			if( tidy_get_status() === 2){
				// 2 is magic number for fatal error
				// http://www.php.net/manual/en/function.tidy-get-status.php
				$tidyErrors[] = 'Tidy found serious XHTML errors: <br/>'.nl2br(htmlspecialchars( tidy_get_error_buffer($tidy)));
				return false;
			}
			$text = tidy_get_output();
		//
		//	php5
		//
		}else{
			$tidy = tidy_parse_string($text,$options,'utf8');
			tidy_clean_repair($tidy);

			if( tidy_get_status($tidy) === 2){
				// 2 is magic number for fatal error
				// http://www.php.net/manual/en/function.tidy-get-status.php
				$tidyErrors[] = 'Tidy found serious XHTML errors: <br/>'.nl2br(htmlspecialchars( tidy_get_error_buffer($tidy)));
				return false;
			}
			$text = tidy_get_output($tidy);
		}
		return true;
	}

	//for php4
	function tidyOptions($options){
		foreach($options as $key => $value){
			tidy_setopt($key,$value);
		}
	}
?>