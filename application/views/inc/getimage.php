<?php
//Define variable
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $path = isset($_GET['filePath']) ? $_GET['filePath'] : '';
    $filename = isset($_GET['fileName']) ? $_GET['fileName'] : '';
    //$image = $_GET['image'];
    $image = $path.$filename;
    $mimetype = isset($_GET['mimetype']) ? $_GET['mimetype'] : 'image/jpeg';
    //Then, check for hacking attempts (Remote Code Execution), and block them.
    if (strpos($image, 'thumb') === false) {
	    if (preg_match('#([.*])([/])([A-Za-z0-9.]{0,11})#', $image, $matches)) {
		    if ($image != $matches[0]) {
			    unset($image);
			    die('A hacking attempt has been detected. For security reasons, we\'re blocking any code execution.');
		    }
	    }
    } elseif (strpos($image, 'thumb') !== false) {
	    if (preg_match('#([.*])([/])thumb([/])([A-Za-z0-9.]{0,11})#', $image, $matches)) {
		    if ($image != $matches[0]) {
			    unset($image);
			    die('A hacking attempt has been detected. For security reasons, we\'re blocking any code execution.');
		    }
	    }
    }

    //...if no hacking attempts found:
    //Check if file exists.
    if (file_exists($image)) {
	    //Generate the image, make sure it doesn't end up in the visitors buffer.
	    if ($AllowDownload && $action == "download") {
            if (is_file($image)) {
                header("Content-Disposition: attachment; filename=$filename");
		    }
	    }
	    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
	    header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
	    header('Pragma: no-cache');
	    header("Content-Length: ".filesize($image));
	    //header('Content-Type: image/jpeg');
	    header("Content-Type: $mimetype");
	    echo readfile($image);
    }

    //If image doesn't exist, send 404 header.
    else
	    header('HTTP/1.0 404 Not Found');
?>