<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
class Page{
	var $title;
	var $index = "BK";
	var $meta_data = array();
	var $label;
	var $file = false;
	var $file_type = false;
	var $contentBuffer;
	var $fileModTime = 0; /* @deprecated 3.0 */
	var $file_stats = array();
	var $stats = array();
	var $file_sections = array();

	/**
	 * Return the beginning content of a data file
	 *
	 */
	public function FileStart($file, $time=false, $file_stats = array() ){
		if( $time === false ) $time = time();
		//file stats
		$file_stats = (array)$file_stats + Page::GetPageStats($file);
		$file_stats['modified'] = date('l, F jS - Y - h:i:s A');// 'Sunday, March 16th - 2014 - 01:23:02 PM'
        //Are there any pages?
		$file_stats['file_number'] = getFileNumber(PAGE_PATH);

		return '<'.'?'.'php'
				. "\ndefined('PBD_FLM') or die('Not an entry point... Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');"
				. "\n".'$fileModTime = \''.$time.'\';' /* @deprecated 3.0 */
				. "\n".Page::ArrayToPHP('file_stats',$file_stats)
				. "\n\n";
	}

	public function ArrayToPHP($varname,&$array){
		return '$'.$varname.' = '.var_export($array,true).';';
	}

    /**
	 * Save the content for a new page in /data/pages/<title>
	 * @since 1.8a1
	 *
	 */
	public function SaveNewPage($title,$meta_data,$section_content = false,$type='text'){
		global $separator;
		$ext = pathinfo($title, PATHINFO_EXTENSION);
	    $file = ($ext == '') ? $title.'.php' : $title;
		if( empty($title) ){
			return '<div class="message user_status">Empty page title. <a href="javascript:history.back()">Enter a page name</a></div>';
		} else if( file_exists(PAGE_PATH.$file) ){
			return '<div class="message user_status">The page'.$separator.'<em>['.$file.']</em> already exists. <a href="javascript:history.back()">Change page name</a></div>';
		} else {
            for($i=0;$i<(count($section_content));$i++){
			    $file_sections[] = array('type' => $type[$i],'content' => $section_content[$i]);
			}
            $file_metadata = $meta_data;
		    
		    return Page::SaveArray(PAGE_PATH.$file,'file_metadata',$file_metadata,'file_sections',$file_sections);
	    }
	}

	function SavePage( $title,$stats,$meta,$sections,$type,$backup = false ){
	    global $separator;
		$ext = pathinfo($title, PATHINFO_EXTENSION);
	    $file = ($ext == '') ? $title.'.php' : $title;
		$this->file = PAGE_PATH.$file;
        $this->meta_data = $meta;
		$this->file_stats = $stats;
		/*$this->file_sections = $sections;*/
		
		if( empty($title) ){
			return '<div class="message user_status">Empty page title. <a href="javascript:history.back()">Enter a page name</a></div>';
		} else {
		    for($i=0;$i<(count($sections));$i++){
			    $this->file_sections[] = array('type' => $type[$i],'content' => $sections[$i]);
		    }
			
		    if( !is_array($this->meta_data) || !is_array($this->file_sections) ){
			    return false;
		    }

		    if( $backup ) $this->SaveBackup(); //make a backup of the page file
		
		    return Page::SaveArray($this->file,'file_metadata',$this->meta_data,'file_sections',$this->file_sections);
	    }
	}
	
	public function SaveArray(){
		$args = func_get_args();
		$count = count($args);
		if( ($count %2 !== 1) || ($count < 3) ){
			trigger_error('Wrong argument count '.$count.' for Page::SaveArray() ');
			return false;
		}
		$file = array_shift($args);

		$file_stats = array();
		$data = '';
		while( count($args) ){
			$varname = array_shift($args);
			$array = array_shift($args);
			if( $varname == 'file_stats' ){
				$file_stats = $array;
			} else{
				$data .= Page::ArrayToPHP($varname,$array);
				$data .= "\n\n";
			}
		}

		$data = Page::FileStart($file,time(),$file_stats).$data;
		return Page::Save($file,$data);
	}

	/**
	 * Save raw content to a file to the server
	 *
	 * @param string $file The path of the file to be saved
	 * @param string $contents The contents of the file to be saved
	 * @param bool $checkDir Whether or not to check to see if the parent directory exists before attempting to save the file
	 * @return bool True on success
	 */
	static function Save($file,$contents,$checkDir=true){
		//make sure directory exists
		if( $checkDir && !file_exists($file) ){
			$dir = Page::DirName($file);
			if( !file_exists($dir) ){
				Page::CheckDir($dir);
			}
		}

		$exists = file_exists($file);

		$fp = @fopen($file,'wb');
		
		if( !$exists ) @chmod($file,FILE_WRITE_MODE);

		$write = fwrite($fp,$contents);
		fclose($fp);
		//return ($return !== false);
		if($write !== false) $return = '<div class="message">Page saved successfully</div><div class="message unspecific"><a href="'.$_SERVER['PHP_SELF'].'?mode=home&amp;section=pages&amp;page='.removeFileExt(basename($file)).'" target="_blank" >view new page</a> or <a href="'.$_SERVER['PHP_SELF'].'?mode=admin&amp;section=addpage">create another page</a> or <a href="'.$_SERVER['PHP_SELF'].'?mode='.MODE.'">Return to admin index</a></div>';
		else $return = '<div class="message error">ERROR : The page was not saved, check the supplied data</div>';
		
		return $return;
	}

	/**
	 * Check recursively to see if a directory exists, if it doesn't attempt to create it
	 *
	 * @param string $dir The directory path
	 * @param bool $index Whether or not to add an index.hmtl file in the directory
	 * @return bool True on success
	 */
	static function CheckDir($dir,$index=true){
		global $PB_CONFIG,$checkFileIndex;
		if( !file_exists($dir) ){
			$parent = Page::DirName($dir);
			Page::CheckDir($parent,$index);
			//ftp mkdir
			if( isset($PB_CONFIG['useftp']) ){
				if( !gpFiles::FTP_CheckDir($dir) ){
					return false;
				}
			}else{
				if( !@mkdir($dir,DIR_WRITE_MODE) ){
					return false;
				}
				@chmod($dir,DIR_WRITE_MODE); //some systems need more than just the 0755 in the mkdir() function
			}

		}

		//make sure there's an index.html file
		if( $index && $checkFileIndex ){
			$indexFile = $dir.'/index.html';
			if( !file_exists($indexFile) ){
				Page::Save($indexFile,'<html></html>',false);
			}
		}

		return true;
	}

	/**
	 * Convert backslashes to forward slashes
	 *
	 */
	static function WinPath($path){
		return str_replace('\\','/',$path);
	}

	/**
	 * Returns parent directory's path with forward slashes
	 * php's dirname() method may change slashes from / to \
	 *
	 */
	static function DirName( $path, $dirs = 1 ){
		for($i=0;$i<$dirs;$i++){
			$path = dirname($path);
		}
		return Page::WinPath( $path );
	}

//-----------------------------------------------------------------------------------------
	
	/**
	 * Return an array of info about the data file
	 *
	 */
	static function GetPageStats($file){
		$file_stats = array();
		if( file_exists($file) ){
			ob_start();
			include($file);
			ob_end_clean();

			if( !isset($file_stats['modified']) && isset($fileModTime) ){
				$file_stats['modified'] = $fileModTime;
			}
		}else{
			$file_stats['createDate'] = date('l, F jS - Y - h:i:s A');
			$file_stats['createTime'] = time();
		}

		return $file_stats;
	}
	
	public function GetPageMetadata($file){
		$file_metadata = array();
		if( file_exists($file) ){
			ob_start();
			include($file);
			ob_end_clean();

		}else{
			$file_metadata['title'] = 'Un-titled';
			$file_metadata['author'] = 'Amalkong';
			$file_metadata['keywords'] = 'FWM,PBD,football';
			$file_metadata['description'] = '';
		}

		return $file_metadata;
	}

	function GetFile($pageFile){
		$fileModTime = $fileVersion = false;
		$file_sections = $file_metadata = $file_stats = array();
		ob_start();
		if( file_exists($pageFile) ){
			require($pageFile);
		}
		$content = ob_get_clean();
		
		$this->file_metadata = $file_metadata;
		$this->fileModTime = $fileModTime;
		$this->file_stats = $file_stats;
		$this->file_sections = isset($file_sections) ? $file_sections : '';
		
		if( isset($this->file_metadata['file_type']) ) $this->file_type = $this->file_metadata['file_type'];
		else $this->file_type = 'text';
	    
		if( count($this->file_sections) == 0 ){
			$this->file_sections[0] = array('type' => 'text','content' => '<p>Oops, this page no longer has any content.</p>',);
		}

		if( !isset($this->file_stats['modified']) ) $this->file_stats['modified'] = $fileModTime;
	}
	function showPageContent($path,$getVar,$echo=false) {
	    global $UTIL,$pfxs;
		$fileModTime = false;
	    $cmd = GetCommand($getVar);
		$cmd = ($cmd==true) ? $cmd : 'about';
		$ext = pathinfo($cmd, PATHINFO_EXTENSION);
	    $page_file = ($ext == '') ? $cmd.'.php' : $cmd;
		$i = 1;
	    $ci = NULL;
	    $PB_output = NULL;
	    $prevNext_link = NULL;
		$pages=array();
	    if(is_dir($path)){
	        $handle = opendir($path);
	        while (false !== ($file = readdir($handle))) {
		        if ($UTIL->isValidExt($file,$pfxs)) {
			        $pages[$i] = $file;
			        if ($page_file == $pages[$i] || $page_file == $UTIL->removeFileExt($pages[$i])) $ci = $i;
					
					$prevNext_link_file = $this->GetPageMetadata($path.$file);
			        $prevNext_link_file_titles[$i] = $prevNext_link_file['title'];
			        $i++;
		        }
	        }
	        closedir($handle);
			
	        if(isset($pages) && count($pages) > 0){
	            $ti = $i - 1;
	            $pi = $ci - 1;
	            if ($page_file == "") $ni = $ci + 2;
	            else $ni = $ci + 1;
			   
	            if ($page_file == "") $page_file = $pages[0];
				
	            if ($pi > 0) {
		            $prev_file_title = $prevNext_link_file_titles[$pi];
		            $piFile = urlencode($UTIL->removeFileExt($pages[$pi]));
		            $prevNext_link .= "<span class=\"nav-prev\"><a href=\"" . SELF . "?mode=".MODE."&amp;section=".SECTION."&amp;page=" . $piFile . "#__Top\" title=\"show the previous page : $prev_file_title\">&larr;&nbsp;$prev_file_title</a></span>";
	            } else $prevNext_link .= '<span class="nav-prev">&nbsp;</span>';
		
	            if ($ni <= $ti) {
		            $next_file_title = $prevNext_link_file_titles[$ni];
		            $niFile = urlencode($UTIL->removeFileExt($pages[$ni]));
		            $prevNext_link .= "<span class=\"nav-next\"><a href=\"" . SELF . "?mode=".MODE."&amp;section=".SECTION."&amp;page=" . $niFile . "#__Top\" title=\"show the next page : $next_file_title\">$next_file_title&nbsp;&rarr;</a></span>";
	            } else $prevNext_link .= '<span class="nav-next">&nbsp;</span>';
	    
		        //Check if page exists
		        if (file_exists($path.$page_file)) {
				    $this->GetFile($path.$page_file);
					
			        $PB_output .='<h2 style="width:auto;">'.$this->file_metadata['title'].'</h2>';
			        for($i=0;$i<(count($this->file_sections));$i++){
						//if(isset($this->file_sections[$i]['content']) && count($this->file_sections[$i]['content']) > 0){
						    if(count($this->file_sections) > 1) $PB_output .='<div class="page-section-head grid-auto">Section '.($i+1).'</div>';
							if($this->file_type == 'gallery' || $this->file_sections[$i]['type'] == 'gallery'){
							    // TODO: this is just a placeholder, remember to fix/improve it.
							    //$PB_output .= str_replace('{show_gallery(epl_championship_day)}','The gallery selected is weekly highlights',$this->file_sections[$i]['content']);
							
							    $regex = '/\{show_gallery\((.*?)\)}/';
			                    if (preg_match($regex, $this->file_sections[$i]['content'])) {
				                    //Split content in chunks.
				                    $content = preg_split($regex, $this->file_sections[$i]['content'], null, PREG_SPLIT_DELIM_CAPTURE);
				                    //$PB_output.= $content[0];
				                    //$PB_output.= show_gallery($content[1]);
				                    $PB_output.= preg_replace($regex,show_gallery($content[1]),$this->file_sections[$i]['content']);
				                    
			                    } else $PB_output.= $this->file_sections[$i]['content'];
							} else $PB_output .='<div class="page-content">'.$this->file_sections[$i]['content'].'</div>';
		                //} else $PB_output .= '<p>Oops, this page no longer has any content.</p>';
					}
			        $PB_output .='<br/><hr/><p>Page created on '.$this->file_stats['createDate'].'</p>';
		        } else{  
		            //If page doesn't exist, show error message.
		    	    $PB_output ='<div class="message user_status"><p>A page name was not set/selected or This page could not be found.<br/><a href="index.php?mode=home&amp;section=pages&amp;action=page_list">Select page</a></p></div>';
		        }
	        } else {
	            $prevNext_link .= 'No page found';
		        if(count($pages) == 0) $prevNext_link .= 'The Page Folder Is Empty';
	        }
		    $PB_output .='<div class="spacer"></div>';
			$PB_output .='<div class="post-nav grid-full">'.$prevNext_link.'</div>';
		    $PB_output .='<div class="clear"></div>';
		} else $PB_output .= '<div class="message error"><p>The pages path set, is not valid</p></div>';
	    
		if($echo) echo $PB_output;
		else return $PB_output;
    }

    function show_page_box($file,$title,$date,$content,$echo=false) {
	    global $roleID;
	    $PB_output = NULL;
	    $filename = removeFileExt(basename($file));
	    $title = cleanPageTitles($title);
		$PB_output .= '<div class="menudiv">'."\n";
			$PB_output .= '<span class="menudiv-head left-1">';
			    $PB_output .= '<span class=""><img src="'.IMG_URL.'other/page.png" alt="" /></span>';
			    $PB_output .= '<span class="menudiv-title">'.$title.'</span>';
			    $PB_output .= '<span class="menudiv-date">'.$date.'</span>';
			$PB_output .= '</span>'."\n";
			
			$PB_output .= '<span class="menudiv-links right-1">';
			    $PB_output .= '<span><a href="index.php?mode='.MODE.'&amp;section=pages&page='.urlencode($filename).'" target="_blank"><i class="icon icon_view" title="View '.$title.'">&nbsp;</i></a></span>'."\n";
			    if($roleID > 3) {
				    $PB_output .= '<span><a href="?mode=admin&amp;section=pages&amp;action=updatepage&amp;page='.urlencode($filename).'"><i class="icon icon_edit" title="Edit '.$title.'">&nbsp;</i></a></span>'."\n";
			        $PB_output .= '<span><a href="?mode=admin&amp;section=pages&amp;action=deletepage&amp;page='.urlencode($filename).'"><i class="icon icon_trash" title="Delete '.$title.'">&nbsp;</i></a></span>'."\n";
			    }
			$PB_output .= '</span>'."\n";
			
			$PB_output .= '<span class="menudiv-content">'.$content.'</span>';
			$PB_output .= '<span class="clear"></span><br style="clear:both;" />'."\n";
		$PB_output .= '</div>'."\n";
		
		if($echo) echo $PB_output;
		else return $PB_output;
    }
}
/* end of file Page.php */
/* system/class/Page.php*/