<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');

//Define dir constant
define('TINYMCE_DIR', PLUGIN_PATH.'tinymce/lib');
define('TINYMCE_URL', PLUGIN_URL.'tinymce/lib');
//Define constant to add tinymce-class to textareas
define('WYSIWYG_TEXTAREA_CLASS', 'tinymce');

function tinymce_info() {
	global $lang;
	return array(
		'name'          => $lang['tinymce']['module_name'],
		'intro'         => $lang['tinymce']['module_intro'],
		'version'       => '3.4.4',
		'author'        => $lang['general']['pluck_dev_team'],
		'website'       => 'http://www.pluck-cms.org',
		'icon'          => 'images/tinymce.png',
		'compatibility' => '4.7'
	);
}

    function load_tinymce_editor() {
	    //Display main code.
	    tinymce_display_code(); ?>
	    <script type="text/javascript">
	        <!--
	        function insert_page_link() {
	            var id = document.getElementById('insert_pages');
	            var page = id.selectedIndex;
	            var file = id.options[page].value;
	            var title = id.options[page].text;

	            //Remove indent space. //@fixme Not the best way to do it, but it works.
	            title = escape(title);
	            title = title.replace(/%u2003/g, '');
	            title = unescape(title);

	            tinyMCE.execCommand('mceInsertContent', false, '<a href="?mode=home&section=pages&page=' + file + '" title="' + title + '">' + title + '<\/a>');
	        }

	        function insert_image_link(dir) {
	            var id = document.getElementById('insert_images');
	            var image = id.selectedIndex;
	            var file = id.options[image].text;

	            tinyMCE.execCommand('mceInsertContent', false, '<img src="' + dir + '/' + file + '" alt="" \/>');
	        }

	        function insert_gallery(dir) {
	            var id = document.getElementById('insert_gallery');
	            var module = id.selectedIndex;
	            var code = id.options[module].value;

	            tinyMCE.execCommand('mceInsertContent', false, '{ show_gallery(' + code + ')}');
	        }
	    </script>
	    <?php
    }

    function tinymce_display_code() { ?>
    	<script type="text/javascript" src="<?php echo TINYMCE_URL.'/tiny_mce.js'; ?>"></script>
	    <script type="text/javascript">
	        //<![CDATA[
	        tinyMCE.init({
		        mode : "textareas",
		        editor_selector : "tinymce",
		        entity_encoding : "raw",
		        <?php
		            //Set the language
		            //if (file_exists(TINYMCE_DIR.'/langs/'.LANG.'.js'))
		            //	echo 'language : "'.LANG.'",'."\n";
		            //else
		       	    echo 'language : "en",'."\n";
		        ?>
		        theme : "advanced",
		        width : "600px",
		        plugins : "table,media,paste,safari",
		        <?php
		            $buttons = array(
		             	'bold', 'italic', 'underline', 'strikethrough', 
		             	'separator', 'justifyleft', 'justifycenter', 'justifyright', 'justifyfull',
		              	'separator', 'formatselect', 'fontsizeselect'
	            	);
	             	$number = count($buttons);
		        ?>
		        theme_advanced_buttons1 : "<?php foreach ($buttons as $key => $button) {echo $button; if (($number - 1) != $key) echo ',';}?>",
		        <?php
	            	$buttons = array(
	              		'cut', 'copy', 'paste', 'pastetext', 'pasteword',
	             		'separator', 'undo', 'redo',
	            		'separator', 'bullist', 'numlist', 'outdent', 'indent',
		            	'separator', 'link', 'unlink', 'anchor', 'image', 'media',
		            	'separator', 'table', 'hr', 'forecolor', 'backcolor',
		            	'separator', 'code', 'cleanup'
		            );
		            $number = count($buttons);
		        ?>
		        theme_advanced_buttons2 : "<?php foreach ($buttons as $key => $button) {echo $button; if (($number - 1) != $key) echo ',';}?>",
		        <?php
		            $buttons = array();
		            $number = count($buttons);
		        ?>
		        theme_advanced_buttons3 : "<?php foreach ($buttons as $key => $button) {echo $button; if (($number - 1) != $key) echo ',';}?>",
		        theme_advanced_toolbar_location : "top",
		        theme_advanced_toolbar_align : "left",
		        theme_advanced_path_location : "bottom",
		        theme_advanced_resizing : true,
		        theme_advanced_resize_horizontal : false
	        })
	        //]]>
	    </script>
	    <?php
    }

    load_tinymce_editor();
?>