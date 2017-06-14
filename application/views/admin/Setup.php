<?php  defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    if (($roleID < 5))  {
       echo'<div class="message error"><p>Access Denied!,You are not authorised to access this section, your user role does not permit you to re-configure site settings.</p></div>';
	   include(BASE_PATH.'footer.php');
	   exit;
    }
    //include(CONFIG_PATH."config.php");
    require(CONFIG_PATH."settings.php");
	require(SYSTEM_PATH.'functions/setup_functions.php');
	
	if (isset($_POST['action'])) $action = $_POST['action'];
    else if (isset($_GET['action'])) $action = $_GET['action'];
    else $action = 'meta_setup';
	$url_prefix = SELF.'?mode='.MODE.'&amp;section='.SECTION;
	$PB_output='';
	$PB_output.='<div class="content" style="margin-top:0 !important;padding-top:0 !important;">';
	$PB_output .='<div class="link-container-top">
	    <center>
			<a href="'.$url_prefix.'&amp;action=meta_setup">Meta Setup</a> | 
			<a href="'.$url_prefix.'&amp;action=file_setup">File Setup</a>
		</center>
	</div>';
	$PB_output.='<div class="box">';
	$action = strtolower($action);
	switch($action){
		case 'meta_setup':
	        if (!isset($_POST['save'])) {
                $PB_output.= meta_setup();
            } else if(isset($_POST['save'])){
		        if(!$fp=fopen(CONFIG_PATH."settings_meta.php","w")) die ("ERROR: unable to open .$fp.");
		        fwrite($fp,"<?php\n");	
				    fwrite($fp,'$PB_CONFIG[\'site_title_full\'] = "'.$_POST['site_title_full']."\";\n");
				    fwrite($fp,'$PB_CONFIG[\'short_name\'] = "'.$_POST['short_name']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'subtitle\'] = "'.$_POST['subtitle']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'keywords\'] = "'.$_POST['keywords']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'description\'] = "'.$_POST['description']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'author\'] = "'.$_POST['author']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'charset\'] = "'.$_POST['charset']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'language\'] = "'.$_POST['language']."\";\n");
			    fwrite($fp,"?>\n");
		        fclose($fp);
		        unset($_GET['save']);
                $PB_output.= meta_setup();
            }
		break;
		
		case 'file_setup':
			if (!isset($_POST['save'])) {
                $PB_output.= file_setup();
            } else if(isset($_POST['save'])){
		        if(!$fp=fopen(CONFIG_PATH."settings_file.php","w")) die ("ERROR: unable to open .$fp.");
		        fwrite($fp,"<?php\n");
				    fwrite($fp,'$PB_CONFIG[\'itemsPerPage\'] = "'.$_POST['itemsPerPage']."\";\n");
				    fwrite($fp,'$PB_CONFIG[\'max_recent\'] = "'.$_POST['max_recent']."\";\n");
				    fwrite($fp,'$PB_CONFIG[\'max_number_teams\'] = "'.$_POST['max_number_teams']."\";\n");
				    fwrite($fp,'$PB_CONFIG[\'min_number_teams\'] = "'.$_POST['min_number_teams']."\";\n");
				    fwrite($fp,'$PB_CONFIG[\'max_number_uploads\'] = "'.$_POST['max_number_uploads']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'log_threshold\'] = "'.$_POST['log_threshold']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'log_date_format\'] = "'.$_POST['log_date_format']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'display_dirsize\'] = "'.$_POST['display_dirsize']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'allow_create_file\'] = "'.$_POST['allow_create_file']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'allow_delete_file\'] = "'.$_POST['allow_delete_file']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'allow_edit_file\'] = "'.$_POST['allow_edit_file']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'use_js_editor\'] = "'.$_POST['use_js_editor']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'show_ads_banner_top\'] = "'.$_POST['show_ads_banner_top']."\";\n");
			        fwrite($fp,'$PB_CONFIG[\'show_ads_banner_side\'] = "'.$_POST['show_ads_banner_side']."\";\n");
			    fwrite($fp,"?>\n");
		        fclose($fp);
		        unset($_GET['action']);
                $PB_output.= file_setup(); 
            }
		break;
    }
	
	$PB_output.= '</div>';
	$PB_output.= '</div>';
	
	echo $PB_output;
?>