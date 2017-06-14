<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
	function meta_setup($title='Meta Data'){
	    include(CONFIG_PATH.'settings_meta.php');
	    include(CONFIG_PATH.'charsets.php');
	    include(CONFIG_PATH.'languages.php');
		$out = "";
	    $out.='<form method="post" action="" id="setupform">
		    <div class="header"><h2>'.$title.'</h2></div>
		    <table width="100%" align="center">
		        <tr><td align="right">Site Title:</td><td><input type="text" name="site_title_full" value="'.$PB_CONFIG['site_title_full'].'" /></td></tr>
		        <tr><td align="right">Short Site Title:</td><td><input type="text" name="short_name" value="'.$PB_CONFIG['short_name'].'" /></td></tr>
	            <tr><td align="right">Subtitle:</td><td><input type="text" name="subtitle" value="'.$PB_CONFIG['subtitle'].'" /></td></tr>
		        <tr><td align="right">keywords:</td><td><textarea name="keywords">'.$PB_CONFIG['keywords'].'</textarea></td></tr>
	            <tr><td align="right">Site Description:</td><td><textarea name="description">'.$PB_CONFIG['description'].'</textarea></td></tr>
	            <tr><td align="right">Author:</td><td><input type="text" name="author" value="'.$PB_CONFIG['author'].'" /></td></tr>';
		        $out.='<tr><td align="right">Charset:</td><td><select name="charset">';
			        foreach( $_charsets as $charset) $out.='<option value="'.$charset.'" '.(($charset == $PB_CONFIG['charset']) ? 'SELECTED' : '' ).'>'.$charset.'&nbsp;</option>';
		        $out.='</select></td></tr>';
		
		        $out.='<tr><td align="right">Default Language:</td><td><select name="language">';
		            foreach( $_languages as $language => $key) $out.='<option value="'.$language.'" '.(($language == $PB_CONFIG['language']) ? 'SELECTED' : '' ).'>'.$key.'</option>';
		        $out.=' </select></td></tr>';
		       $out.='<tr><td colspan="2"><center><input class="save" type="submit" name="save" value="Save Setup" /></center></td></tr>';
		    $out.='</table>';
		$out.='</form>';
		return $out;
	}
	function file_setup($title='File Configurations'){
	    include(CONFIG_PATH.'settings_file.php');
	    include(CONFIG_PATH.'formats_date.php');
		$out = "";
	    $out.='<form method="post" action="" id="setupform">
		    <div class="header"><h2>'.$title.'</h2></div>
		    <table width="100%" align="center">';
	         	$out.='<tr><td align="right">Items Per Page:</td><td><input type="text" name="itemsPerPage" value="'.$PB_CONFIG['itemsPerPage'].'" /></td></tr>';
		        $out.='<tr><td align="right">Maximum Recent Articles:</td><td><input type="text" name="max_recent" value="'.$PB_CONFIG['max_recent'].'" /></td></tr>';
		        $out.='<tr><td align="right">Maximum Teams Per League:</td><td><input type="text" name="max_number_teams" value="'.$PB_CONFIG['max_number_teams'].'" /></td></tr>';
		        $out.='<tr><td align="right">Minimum Teams Per League:</td><td><input type="text" name="min_number_teams" value="'.$PB_CONFIG['min_number_teams'].'" /></td></tr>';
		        $out.='<tr><td align="right">Maximum Number Uploads:</td><td><input type="text" name="max_number_uploads" value="'.$PB_CONFIG['max_number_uploads'].'" /></td></tr>';
	            
		        $out.='<tr><td align="right">Log Date Format:</td><td><select name="log_date_format">';
		            foreach( $_date_formats as $format) $out.='<option value="'.$format.'" '.(($format == $PB_CONFIG['log_date_format']) ? 'SELECTED' : '' ).'>'.$format.'</option>';
		        $out.=' </select></td></tr>';
		        $out.='<tr><td align="right">Log Treshold:</td><td><select name="log_threshold">';
		        $out.='<option value="1" '.(($PB_CONFIG['log_threshold'] == '1') ? "selected" : '' ).'>1</option>';
                $out.='<option value="0" '.(($PB_CONFIG['log_threshold'] == '0') ? "selected" : '' ).'>0</option></select></td></tr>';
		
	    	    $out.='<tr><td align="right">Show Directory Size:</td><td><select name="display_dirsize">';
		        $out.='<option value="YES" '.(($PB_CONFIG['display_dirsize'] == 'YES') ? "selected" : '').'>Yes</option>';
                $out.='<option value="NO" '.(($PB_CONFIG['display_dirsize'] == 'NO') ? "selected" : '' ).'>No</option></select> current value - ['.$PB_CONFIG['display_dirsize'].'] Show Directory Size</td></tr>';
		
		        $out.='<tr><td align="right">Allow Create File:</td><td><select name="allow_create_file">';
		        $out.='<option value="YES" '.(($PB_CONFIG['allow_create_file'] == 'YES') ? "selected" : '' ).'>Yes</option>';
                $out.='<option value="NO" '.(($PB_CONFIG['allow_create_file'] == 'NO') ? "selected" : '' ).'>No</option></select> current value - ['.$PB_CONFIG['allow_create_file'].'] allow create file</td></tr>';
		
		        $out.='<tr><td align="right">Allow Delete File:</td><td><select name="allow_delete_file">';
	        	$out.='<option value="YES" '.(($PB_CONFIG['allow_delete_file'] == 'YES') ? "selected" : '' ).'>Yes</option>';
                $out.='<option value="NO"' .(($PB_CONFIG['allow_delete_file'] == 'NO') ? "selected" : '' ).'>No</option></select> current value - ['.$PB_CONFIG['allow_delete_file'].'] allow delete file</td></tr>';
		
		        $out.='<tr><td align="right">Allow Edit File:</td><td><select name="allow_edit_file">';
		         $out.='<option value="YES" '.(($PB_CONFIG['allow_edit_file'] == 'YES') ? "selected" : '' ).'>Yes</option>';
                $out.='<option value="NO" '.(($PB_CONFIG['allow_edit_file'] == 'NO') ? "selected" : '').'>No</option></select> current value - ['.$PB_CONFIG['allow_edit_file'].'] allow edit file</td></tr>';
		
		        $out.='<tr><td align="right">Use JS Editor:</td><td><select name="use_js_editor">';
		        $out.='<option value="YES" '.(($PB_CONFIG['use_js_editor'] == 'YES') ? "selected" : '').'>Yes</option>';
                $out.='<option value="NO" '.(($PB_CONFIG['use_js_editor'] == 'NO') ? "selected" : '' ).'>No</option></select></td></tr>';
				
		        $out.='<tr><td align="right">Show Top Ad Banners:</td><td><select name="show_ads_banner_top">';
		        $out.='<option value="YES" '.(($PB_CONFIG['show_ads_banner_top'] == 'YES') ? "selected" : '').'>Yes</option>';
                $out.='<option value="NO" '.(($PB_CONFIG['show_ads_banner_top'] == 'NO') ? "selected" : '' ).'>No</option></select></td></tr>';
				
		        $out.='<tr><td align="right">Show Side Ads Banners:</td><td><select name="show_ads_banner_side">';
		        $out.='<option value="YES" '.(($PB_CONFIG['show_ads_banner_side'] == 'YES') ? "selected" : '' ).'>Yes</option>';
                $out.='<option value="NO" '.(($PB_CONFIG['show_ads_banner_side'] == 'NO') ? "selected" : '' ).'>No</option></select></td></tr>';
				
		        $out.='<tr><td colspan="2"><center><input class="save" type="submit" name="save" value="Save Setup" /></center></td></tr>';
		    $out.='</table>';
		$out.='</form>';
		return $out;
	}
?>