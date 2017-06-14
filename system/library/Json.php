<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');

/**
 * Marker constant for Json::decode(), used to flag stack state
 */
define('JSON_SLICE', 1);

/**
 * Marker constant for Json::decode(), used to flag stack state
 */
define('JSON_IN_STR',  2);

/**
 * Marker constant for Json::decode(), used to flag stack state
 */
define('JSON_IN_ARR',  3);

/**
 * Marker constant for Json::decode(), used to flag stack state
 */
define('JSON_IN_OBJ',  4);

/**
 * Marker constant for Json::decode(), used to flag stack state
 */
define('JSON_IN_CMT', 5);

/**
 * Behavior switch for Json::decode()
 */
define('JSON_LOOSE_TYPE', 16);

/**
 * Behavior switch for Json::decode()
 */
define('JSON_SUPPRESS_ERRORS', 32);

class Json{
    public $dateFormat = 'd-m-Y H:i:s';
	public $skipnative = true;
	//Configuration
	/**
	* constructs a new JSON instance
	*
	* @param int $use object behavior flags; combine with boolean-OR
	*
	*						possible values:
	*						- Json_LOOSE_TYPE:  loose typing.
	*								"{...}" syntax creates associative arrays
	*								instead of objects in decode().
	*						- Json_SUPPRESS_ERRORS:  error suppression.
	*								Values which can't be encoded (e.g. resources)
	*								appear as NULL instead of throwing errors.
	*								By default, a deeply-nested resource will
	*								bubble up with an error, so all return values
	*								from encode() should be checked with isError()
	*/
	function Json($use = 0){
		$this->use = $use;
	}

    /** Replace repeated white spaces to single space
       * @param string $string
       * @return string
	*/

    function clearWhitespaces($string) {
        return trim(preg_replace('/\s+/s', " ", $string));
    }

 /**
	* reduce a string by removing leading and trailing comments and whitespace
	*
	* @param	$str	string	string value to strip of comments and whitespace
	*
	* @return string  string value stripped of comments and whitespace
	* @access private
	*/
	function reduce_string($str){
		$str = preg_replace(array(
				// eliminate single line comments in '// ...' form
				'#^\s*//(.+)$#m',

				// eliminate multi-line comments in '/* ... */' form, at start of string
				'#^\s*/\*(.+)\*/#Us',

				// eliminate multi-line comments in '/* ... */' form, at end of string
				'#/\*(.+)\*/\s*$#Us'

			), '', $str);

		// eliminate extraneous space
		return trim($str);
	}

 /**
	* array-walking function for use in generating JSON-formatted name-value pairs
	*
	* @param	string  $name name of key to use
	* @param	mixed $value  reference to an array element to be encoded
	*
	* @return string  JSON-formatted name-value pair, like '"name":value'
	* @access private
	*/
	function name_value($name, $value){
		$encoded_value = $this->encode($value);

		if(Json::isError($encoded_value)) {
			return $encoded_value;
		}

		return $this->encode(strval($name)) . ':' . $encoded_value;
	}

    function JSON_name_value($name, $value) {
        return (sprintf("%s:%s", $this->encode(strval($name)), $this->encode($value)));
    }

    function encode($var) {
        if (!$this->skipnative && function_exists('json_encode')){
            return json_encode($var);
        }
        switch (gettype($var)) {
            case 'boolean': return $var ? 'true' : 'false';
            case 'NULL': return 'null';
            case 'integer': return sprintf('%d', $var);
            case 'double':
            case 'float': return sprintf('%f', $var);
            case 'string':
                // STRINGS ARE EXPECTED TO BE IN ASCII OR UTF-8 FORMAT
                $ascii = '';
                $strlen_var = strlen($var);
                /* Iterate over every character in the string, escaping with a slash or encoding to UTF-8 where necessary */
                for ($c = 0; $c < $strlen_var; ++$c) {
                    $ord_var_c = ord($var{$c});
                    switch ($ord_var_c) {
                        case 0x08: $ascii .= '\b'; break;
                        case 0x09: $ascii .= '\t'; break;
                        case 0x0A: $ascii .= '\n'; break;
                        case 0x0C: $ascii .= '\f'; break;
                        case 0x0D: $ascii .= '\r'; break;
                        // double quote, slash, slosh
                        case 0x22:
                        case 0x2F:
                        case 0x5C: $ascii .= '\\'.$var{$c}; break;
                        // characters U-00000000 - U-0000007F (same as ASCII)
                        case (($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)): $ascii .= $var{$c}; break;
                        case (($ord_var_c & 0xE0) == 0xC0):
                            // characters U-00000080 - U-000007FF, mask 110XXXXX
                            // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                            $char = pack('C*', $ord_var_c, ord($var{$c+1}));
                            $c+=1;
                            //$utf16 = mb_convert_encoding($char, 'UTF-16', 'UTF-8');
                            $utf16 = utf8_to_utf16be($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                        break;
                        case (($ord_var_c & 0xF0) == 0xE0):
                            // characters U-00000800 - U-0000FFFF, mask 1110XXXX
                            // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                            $char = pack('C*', $ord_var_c,ord($var{$c+1}),ord($var{$c+2}));
                            $c+=2;
                            //$utf16 = mb_convert_encoding($char, 'UTF-16', 'UTF-8');
                            $utf16 = utf8_to_utf16be($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                        break;
                        case (($ord_var_c & 0xF8) == 0xF0):
                            // characters U-00010000 - U-001FFFFF, mask 11110XXX
                            // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                            $char = pack('C*', $ord_var_c,ord($var{$c+1}),ord($var{$c+2}),ord($var{$c+3}));
                            $c+=3;
                            //$utf16 = mb_convert_encoding($char, 'UTF-16', 'UTF-8');
                            $utf16 = utf8_to_utf16be($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                        break;
                        case (($ord_var_c & 0xFC) == 0xF8):
                            // characters U-00200000 - U-03FFFFFF, mask 111110XX
                            // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                            $char = pack('C*', $ord_var_c,ord($var{$c+1}),ord($var{$c+2}),ord($var{$c+3}),ord($var{$c+4}));
                            $c+=4;
                            //$utf16 = mb_convert_encoding($char, 'UTF-16', 'UTF-8');
                            $utf16 = utf8_to_utf16be($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                        break;
                        case (($ord_var_c & 0xFE) == 0xFC):
                            // characters U-04000000 - U-7FFFFFFF, mask 1111110X
                            // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                            $char = pack('C*', $ord_var_c,ord($var{$c+1}),ord($var{$c+2}),ord($var{$c+3}),ord($var{$c+4}),ord($var{$c+5}));
                            $c+=5;
                            //$utf16 = mb_convert_encoding($char, 'UTF-16', 'UTF-8');
                            $utf16 = utf8_to_utf16be($char);
                            $ascii .= sprintf('\u%04s', bin2hex($utf16));
                        break;
                    }
                }
                return '"'.$ascii.'"';
            case 'array':
                // treat as a JSON object
                if (is_array($var) && count($var) && (array_keys($var) !== range(0, count($var) - 1))) {
                    return sprintf('{%s}', join(',', array_map(array($this, 'name_value'),array_keys($var),array_values($var))));
                }
                // treat it like a regular array
                return sprintf('[%s]', join(',', array_map(array($this, 'encode'), $var)));
            case 'object':
                $vars = get_object_vars($var);
                return sprintf('{%s}', join(',', array_map(array($this, 'name_value'),array_keys($vars),array_values($vars))));
            default: return '';
        }
    }
	
	function _Decode($file){
	    $data = json_decode( file_get_contents( $file ) );
		return $data;
	}
	
 /**
	* decodes a JSON string into appropriate variable
	*
	* @param	string  $str	JSON-formatted string
	*
	* @return mixed number, boolean, string, array, or object
	*				corresponding to given JSON input string.
	*				See argument 1 to Json() above for object-output behavior.
	*				Note that decode() always returns strings
	*				in ASCII or UTF-8 format!
	* @access public
	*/
	function decode($str){
		$str = $this->reduce_string($str);

		switch (strtolower($str)) {
			case 'true': return true;
			case 'false': return false;
			case 'null': return null;

			default:
				$m = array();

				if (is_numeric($str)) {
					// Lookie-loo, it's a number

					// This would work on its own, but I'm trying to be
					// good about returning integers where appropriate:
					// return (float)$str;

					// Return float or int, as appropriate
					return ((float)$str == (integer)$str) ? (integer)$str : (float)$str;

				} elseif (preg_match('/^("|\').*(\1)$/s', $str, $m) && $m[1] == $m[2]) {
					// STRINGS RETURNED IN UTF-8 FORMAT
					$delim = substr($str, 0, 1);
					$chrs = substr($str, 1, -1);
					$utf8 = '';
					$strlen_chrs = strlen($chrs);

					for ($c = 0; $c < $strlen_chrs; ++$c) {
						$substr_chrs_c_2 = substr($chrs, $c, 2);
						$ord_chrs_c = ord($chrs[$c]);
						switch (true) {
							case $substr_chrs_c_2 == '\b':
								$utf8 .= chr(0x08);
								++$c;
								break;
							case $substr_chrs_c_2 == '\t':
								$utf8 .= chr(0x09);
								++$c;
								break;
							case $substr_chrs_c_2 == '\n':
								$utf8 .= chr(0x0A);
								++$c;
								break;
							case $substr_chrs_c_2 == '\f':
								$utf8 .= chr(0x0C);
								++$c;
								break;
							case $substr_chrs_c_2 == '\r':
								$utf8 .= chr(0x0D);
								++$c;
								break;

							case $substr_chrs_c_2 == '\\"':
							case $substr_chrs_c_2 == '\\\'':
							case $substr_chrs_c_2 == '\\\\':
							case $substr_chrs_c_2 == '\\/':
								if (($delim == '"' && $substr_chrs_c_2 != '\\\'') ||
								($delim == "'" && $substr_chrs_c_2 != '\\"')) {
									$utf8 .= $chrs[++$c];
								}
								break;

							case preg_match('/\\\u[0-9A-F]{4}/i', substr($chrs, $c, 6)):
								// single, escaped unicode character
								$utf16 = chr(hexdec(substr($chrs, ($c + 2), 2)))
									. chr(hexdec(substr($chrs, ($c + 4), 2)));
								$utf8 .= $this->utf162utf8($utf16);
								$c += 5;
								break;

							case ($ord_chrs_c >= 0x20) && ($ord_chrs_c <= 0x7F):
								$utf8 .= $chrs[$c];
								break;

							case ($ord_chrs_c & 0xE0) == 0xC0:
								// characters U-00000080 - U-000007FF, mask 110XXXXX
								//see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
								$utf8 .= substr($chrs, $c, 2);
								++$c;
								break;

							case ($ord_chrs_c & 0xF0) == 0xE0:
								// characters U-00000800 - U-0000FFFF, mask 1110XXXX
								// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
								$utf8 .= substr($chrs, $c, 3);
								$c += 2;
								break;

							case ($ord_chrs_c & 0xF8) == 0xF0:
								// characters U-00010000 - U-001FFFFF, mask 11110XXX
								// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
								$utf8 .= substr($chrs, $c, 4);
								$c += 3;
								break;

							case ($ord_chrs_c & 0xFC) == 0xF8:
								// characters U-00200000 - U-03FFFFFF, mask 111110XX
								// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
								$utf8 .= substr($chrs, $c, 5);
								$c += 4;
								break;

							case ($ord_chrs_c & 0xFE) == 0xFC:
								// characters U-04000000 - U-7FFFFFFF, mask 1111110X
								// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
								$utf8 .= substr($chrs, $c, 6);
								$c += 5;
								break;
						}
					}
					return $utf8;

				} elseif (preg_match('/^\[.*\]$/s', $str) || preg_match('/^\{.*\}$/s', $str)) {
					// array, or object notation

					if ($str[0] == '[') {
						$stk = array(JSON_IN_ARR);
						$arr = array();
					} else {
						if ($this->use & JSON_LOOSE_TYPE) {
							$stk = array(JSON_IN_OBJ);
							$obj = array();
						} else {
							$stk = array(JSON_IN_OBJ);
							$obj = new stdClass();
						}
					}

					array_push($stk, array('what'  => JSON_SLICE,'where' => 0,'delim' => false));

					$chrs = substr($str, 1, -1);
					$chrs = $this->reduce_string($chrs);

					if ($chrs == '') {
						if (reset($stk) == JSON_IN_ARR) {
							return $arr;
						} else {
							return $obj;
						}
					}

					//print("\nparsing {$chrs}\n");
					$strlen_chrs = strlen($chrs);
					for ($c = 0; $c <= $strlen_chrs; ++$c) {
						$top = end($stk);
						$substr_chrs_c_2 = substr($chrs, $c, 2);
						if (($c == $strlen_chrs) || (($chrs[$c] == ',') && ($top['what'] == JSON_SLICE))) {
							// found a comma that is not inside a string, array, etc.,
							// OR we've reached the end of the character list
							$slice = substr($chrs, $top['where'], ($c - $top['where']));
							array_push($stk, array('what' => JSON_SLICE, 'where' => ($c + 1), 'delim' => false));
							//print("Found split at {$c}: ".substr($chrs, $top['where'], (1 + $c - $top['where']))."\n");

							if (reset($stk) == JSON_IN_ARR) {
								// we are in an array, so just push an element onto the stack
								array_push($arr, $this->decode($slice));

							} elseif (reset($stk) == JSON_IN_OBJ) {
								// we are in an object, so figure
								// out the property name and set an
								// element in an associative array,
								// for now
								$parts = array();
								if (preg_match('/^\s*(["\'].*[^\\\]["\'])\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
									// "name":value pair
									$key = $this->decode($parts[1]);
									$val = $this->decode($parts[2]);

									if ($this->use & JSON_LOOSE_TYPE) {
										$obj[$key] = $val;
									} else {
										$obj->$key = $val;
									}
								} elseif (preg_match('/^\s*(\w+)\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
									// name:value pair, where name is unquoted
									$key = $parts[1];
									$val = $this->decode($parts[2]);

									if ($this->use & JSON_LOOSE_TYPE) {
										$obj[$key] = $val;
									} else {
										$obj->$key = $val;
									}
								}

							}

						} elseif ((($chrs[$c] == '"') || ($chrs[$c] == "'")) && ($top['what'] != JSON_IN_STR)) {
							// found a quote, and we are not inside a string
							array_push($stk, array('what' => JSON_IN_STR, 'where' => $c, 'delim' => $chrs[$c]));
							//print("Found start of string at {$c}\n");

						} elseif (($chrs[$c] == $top['delim']) &&
								($top['what'] == JSON_IN_STR) &&
								((strlen(substr($chrs, 0, $c)) - strlen(rtrim(substr($chrs, 0, $c), '\\'))) % 2 != 1)) {
							// found a quote, we're in a string, and it's not escaped
							// we know that it's not escaped becase there is _not_ an
							// odd number of backslashes at the end of the string so far
							array_pop($stk);
							//print("Found end of string at {$c}: ".substr($chrs, $top['where'], (1 + 1 + $c - $top['where']))."\n");

						} elseif (($chrs[$c] == '[') &&
								in_array($top['what'], array(JSON_SLICE, JSON_IN_ARR, JSON_IN_OBJ))) {
							// found a left-bracket, and we are in an array, object, or slice
							array_push($stk, array('what' => JSON_IN_ARR, 'where' => $c, 'delim' => false));
							//print("Found start of array at {$c}\n");

						} elseif (($chrs[$c] == ']') && ($top['what'] == JSON_IN_ARR)) {
							// found a right-bracket, and we're in an array
							array_pop($stk);
							//print("Found end of array at {$c}: ".substr($chrs, $top['where'], (1 + $c - $top['where']))."\n");

						} elseif (($chrs[$c] == '{') &&
								in_array($top['what'], array(JSON_SLICE, JSON_IN_ARR, JSON_IN_OBJ))) {
							// found a left-brace, and we are in an array, object, or slice
							array_push($stk, array('what' => JSON_IN_OBJ, 'where' => $c, 'delim' => false));
							//print("Found start of object at {$c}\n");

						} elseif (($chrs[$c] == '}') && ($top['what'] == JSON_IN_OBJ)) {
							// found a right-brace, and we're in an object
							array_pop($stk);
							//print("Found end of object at {$c}: ".substr($chrs, $top['where'], (1 + $c - $top['where']))."\n");

						} elseif (($substr_chrs_c_2 == '/*') &&
								in_array($top['what'], array(JSON_SLICE, JSON_IN_ARR, JSON_IN_OBJ))) {
							// found a comment start, and we are in an array, object, or slice
							array_push($stk, array('what' => JSON_IN_CMT, 'where' => $c, 'delim' => false));
							$c++;
							//print("Found start of comment at {$c}\n");

						} elseif (($substr_chrs_c_2 == '*/') && ($top['what'] == JSON_IN_CMT)) {
							// found a comment end, and we're in one now
							array_pop($stk);
							$c++;

							for ($i = $top['where']; $i <= $c; ++$i)
								$chrs = substr_replace($chrs, ' ', $i, 1);

							//print("Found end of comment at {$c}: ".substr($chrs, $top['where'], (1 + $c - $top['where']))."\n");
						}
					}
					if (reset($stk) == JSON_IN_ARR) {
						return $arr;
					} elseif (reset($stk) == JSON_IN_OBJ) {
						return $obj;
					}
				}
		}
	}

	public function saveFile($fileName,$title,$date,$time,$author,$email,$image,$category,$keywords,$summary,$details,$album_name,$media_file){
		if (strlen($fileName)>0){
		    //Save the title and details status.
	            $data = array(
			    'title' => $this->sanitize($title),
				'date' => $date,
			    'time' => $time,
				'author' => $this->sanitize($author),
				'email' => $this->sanitize($email),
				'image' => $this->sanitize($image),
				'category' => $this->sanitize($category),
	            'keywords' => $this->sanitize($keywords),
				'summary' => $this->sanitize($summary, false),
				'details' => $this->sanitize($details, false),
				'album_name' => $this->sanitize($album_name),
				'media_file' => $this->sanitize($media_file)
                );
	        $final_data = $this->JSON_Encode($data);
		    $file = @fopen($fileName,"w");
			
		    if ($file != false){
			    fwrite($file,$final_data."\n");
			    fclose($file);
			    return 1;
		    }
		    return -2;
	    }
	    return -1;
    }
	/**
	* @todo Ultimately, this should just call PEAR::isError()
	*/
	function isError($data, $code = null){
		if (class_exists('pear')) {
			return PEAR::isError($data, $code);
		} elseif (is_object($data) && (get_class($data) == 'Json_error' ||
								is_subclass_of($data, 'Json_error'))) {
			return true;
		}

		return false;
	}
}

if (class_exists('PEAR_Error')) {

	class Json_Error extends PEAR_Error{
		function Json_Error($message = 'unknown error', $code = null,$mode = null, $options = null, $userinfo = null){
			parent::PEAR_Error($message, $code, $mode, $options, $userinfo);
		}
	}

} else {

	/**
	* @todo Ultimately, this class shall be descended from PEAR_Error
	*/
	class Json_Error{
		function Json_Error($message = 'unknown error', $code = null,$mode = null, $options = null, $userinfo = null){

		}
	}

}
?>