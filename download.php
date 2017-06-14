<?php //defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
########### Security code, avoids cross-site scripting (Register Globals ON)
if (isset($_REQUEST['GLOBALS']) OR isset($_REQUEST['absoluteurl']) OR isset($_REQUEST['amilogged']) OR isset($_REQUEST['theme_path'])) { exit; } 
########### End

/*  MEDIA FORMATS SUPPORTED BY FLM
 Here you can add new formats :-)
 Specify file format supported by FLM: 
 */
$flm_filetypes = array(); //filetypes array to handle multiple filetypes 

$flm_filetypes[0]="mp3";
$flm_filetypes[1]="mpg";
$flm_filetypes[2]="mpeg";
$flm_filetypes[3]="mov";
$flm_filetypes[4]="wav";
$flm_filetypes[5]="wma";
$flm_filetypes[6]="wmv";
$flm_filetypes[7]="ogg";
$flm_filetypes[8]="wma";
$flm_filetypes[9]="3gp"; //video mobile phones
$flm_filetypes[10]="amr"; //audio mobile phones
$flm_filetypes[11]="mp4";
$flm_filetypes[12]="asf";
$flm_filetypes[13]="avi";
$flm_filetypes[14]="flv"; //flash video
$flm_filetypes[15]="jpg";
$flm_filetypes[16]="jpeg";
$flm_filetypes[17]="pdf";
$flm_filetypes[18]="aif";
$flm_filetypes[19]="aiff";
$flm_filetypes[20]="m4a";
$flm_filetypes[21]="m4v";


## NOTE: every $flm_filetypes[k] must have a corresponding $filemimetypes[k] below, containing its "mime type"

$filemimetypes = array();

$filemimetypes[0]="audio/mpeg";
$filemimetypes[1]="video/mpeg";
$filemimetypes[2]="video/mpeg";
$filemimetypes[3]="video/quicktime";
$filemimetypes[4]="audio/x-wav";
$filemimetypes[5]="audio/x-ms-wma";
$filemimetypes[6]="video/x-ms-wmv";
$filemimetypes[7]="application/ogg";
$filemimetypes[8]="audio/x-ms-wma";
$filemimetypes[9]="video/3gpp";
$filemimetypes[10]="audio/amr";
$filemimetypes[11]="video/mp4";
$filemimetypes[12]="video/x-ms-asf";
$filemimetypes[13]="video/x-msvideo";
$filemimetypes[14]="video/x-flv";
$filemimetypes[15]="image/jpeg";
$filemimetypes[16]="image/jpeg";
$filemimetypes[17]="application/pdf";
$filemimetypes[18]="audio/x-aiff";
$filemimetypes[19]="audio/x-aiff";
$filemimetypes[20]="audio/x-m4a";
$filemimetypes[21]="video/x-m4v";

# FORCE DOWNLOAD OF SUPPORTED FILES (doesn't play in the browser, but forces download)
 function checkFileType ($filetype,$flm_filetypes,$filemimetypes) {
	    $i=0;
	    $bool=false;
	    $fileData = array();

	    while (($i < sizeof($flm_filetypes)) && $bool==false) {
		    if ($filetype==$flm_filetypes[$i]) {
		     	$fileData[0]=$flm_filetypes[$i];
			    $fileData[1]=$filemimetypes[$i];
			    $bool=true;
		    }
		    $i+=1;
	    }
	    return $fileData;
    }
$uploads_path = "application/data/uploads/";
if (!file_exists($uploads_path)) mkdir($uploads_path,0777); // if uploads directory doesn't exist, make it

$filename = isset($_GET['fileName']) ? $_GET['fileName'] : '';
$path = isset($_GET['filePath']) ? $_GET['filePath'] : '';
$filename = str_replace("/", "", $filename); // Replace / in the filename.. avoid downloading of file outside flm root directory
$filename = str_replace("\\", "", $filename); // Replace \ in the filename.. avoid downloading of file outside flm root directory
//$filename_path = $uploads_path."$filename"; // absolute path of the filename to download
$filename_path = $path."$filename"; // absolute path of the filename to download

if (file_exists("$filename_path") ) { // check real existence of the file. Avoid possible cross-site scripting attacks
	$file_media = explode(".",$filename); //divide filename from extension
	$fileData = checkFileType($file_media[1],$flm_filetypes,$filemimetypes);

	if ($fileData != NULL) { //This IF avoids notice error in PHP4 of undefined variable $fileData[0]
		$flm_filetype=$fileData[0]; $filemimetype=$fileData[1];
		if ($file_media[1]=="$flm_filetype" AND $file_media[1]!=NULL) {// SECURITY OPTION: if extension is supported (file to download must have a known episode extension)
			### required by internet explorer
			if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off');
			###
			header("Pragma: public"); // required
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false); // required for certain browsers 
			header("Content-Type: $filemimetype");
			header("Content-Disposition: attachment; filename=".$filename );
			//header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($filename_path));
			readfile("$filename_path");
			exit();
		}
	}
} else {
   
	$message = "<p>File doesn't exist or Variable not correct. Cannot Download.</p><p>No cross-site scripting allowed with FLM :-P</p>";	
    if(isset($PB_output)) $PB_output .= $message;
	else echo $message;
 }

?>