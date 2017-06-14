<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
	$PB_output =NULL;
	$article_content_list = GetDirContents(ARTICLES_PATH,'files');
	$page_content_list = GetDirContents(PAGE_PATH,'files');
	$total_articles = count($article_content_list);
	$total_pages = count($page_content_list);
	
	if(isset($CFG->config['gui_thumb_width']) && $CFG->config['gui_thumb_width'] !=null ) $width = $CFG->config['gui_thumb_width']; 
    else $width = '100';
    if(isset($CFG->config['gui_thumb_height']) && $CFG->config['gui_thumb_height'] !=null ) $height = $CFG->config['gui_thumb_height']; 
	else $height = '100';
	
    $GUI_menu = array(
		array('href' => BASE_URL."index.php?mode=admin&amp;section=updateadmin",'img'  => IMG_URL.'admin.png','desc' => 'update your admin profile by add/edit bio information, change password, change username or change avatar.','title' => 'Update Adminisration','target' => '','permission'=>5),
		array('href' => VIEW_URL."admin/register.php",'img' => IMG_URL.'unknown.png','desc' => 'Add a new user','title' => 'Add User','target' => '','permission'=>5),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=addnews",'img' => IMG_URL.'news.png','desc' => 'News Section, Manage news articles by Creating ,Editing or Delete any news article in the database','title' => 'Create/Add a new News Article','target' => '','permission'=>3),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=addleague",'img' => IMG_URL.'add_league.png','desc' => 'Create/Add a new league','title' => 'Create/Add New League','target' => '','permission'=>4),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=addseason",'img' => IMG_URL.'add_season.png','desc' => 'Add a new season to a registered league, replace the relegated teams with promoted ones.','title' => 'Add New Season','target' => '','permission'=>4),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=addcategory",'img' => IMG_URL.'add_category.png','desc' => 'Create/Add a new category','title' => 'Add Category','target' => '','permission'=>3),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=addfixture&amp;match_type=leagues",'img' => IMG_URL.'fixtures.png','desc' => 'Add the latest fixture list','title' => 'Add League Fixtures','target' => '','permission'=>4),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=updateresults",'img' => IMG_URL.'results.png','desc' => 'Update match results from all leagues','title' => 'Update Results','target' => '','permission'=>4),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=addpage",'img' => IMG_URL.'newpage.png','desc' => 'Create/Add a new page','title' => 'New Page','target' => '','permission'=>3),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=addgallery",'img' => IMG_URL.'gallery.png','desc' => 'Create a new gallery which can be viewed by itself or "injected" into a page or an article','title' => 'New Gallery','target' => '','permission'=>3),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=addbanner",'img' => IMG_URL.'add_banner.png','desc' => 'Add a new advertisement banner','title' => 'New Advertisement Banner','target' => '','permission'=>4),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=upload",'img' => IMG_URL.'upload.png','desc' => 'Upload new avatars and background textures','title' => 'Uploads','target' => '_blank','permission'=>3),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=systeminfo",'img' => IMG_URL.'info1.png','desc' => 'Get a read out of the system and all database files','title' => 'System Info','target' => '_blank','permission'=>4),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=manage",'img' => IMG_URL.'filemanager.png','desc' => 'Manage files by editing, renaming and deleting news articles, blog posts,and images from the system.','title' => 'File Manager','target' => '_blank','permission'=>4),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=setup",'img' => IMG_URL.'config.png','desc' => 'FLM Setup and Site Management Section, Manage the site&rsquo;s configurations setup, posts,photos by Creating ,Editing or Delete any of these options in the system','title' => 'Configurations','target' => '','permission'=>5)
	);
	$PB_output .='<div class="content">';
	    $PB_output .='<div class="box">';
            $PB_output .='<div class="header"><h2>Administration Dashboard</h2></div>';
			
            $PB_output .='<div class="wrapper">';
                $PB_output .='<div class="box2 grid-3 left-1">';
			        $PB_output .='<p>Welcome to the <span class="em i">'.$PB_CONFIG['site_title_full'].'</span> admin area,  you are logged in as';
                        if ($logged_in == 1 && $CFG->config['admin_username'] == $_SESSION['userName'] && $_SESSION['userRole'] == 'superadmin') {
						    $PB_output .= '<strong class="em i">Super Admin'.$separator.ucfirst($is_validUser['user_name']).'</strong>';
                            $PB_output .='&nbsp;Your user role/rights allows full access to all adminitrative options such as add/edit/publish/delete news articles, add/edit/delete pages, add/edit/delete leagues. You also have access to the hidden page'.$separator.'<a href="'.SELF.'?mode=admin&amp;section=manage&amp;action=browser" target="_blank"><span class="em i">browser.php</a></span>';
						} else {
						    $PB_output .= '<strong class="em i">'.ucfirst($_SESSION['userName']).', your role'.$separator.$_SESSION['userRole'].'</strong>&nbsp;';
			                if($is_validUser['user_role'] == 'admin') $PB_output .='Your user role/rights allows almost full access to all adminitrative options such as add/edit/publish/delete news articles, add/edit/delete pages, add/edit/delete leagues.';
			                if($is_validUser['user_role'] == 'editor') $PB_output .='Your user role/rights allows you to add/edit/delete/publish news articles, add new page.';
			                if($is_validUser['user_role'] == 'user') $PB_output .='Your user role/rights allows you access some articles and pages that were inaccessible bofore.';
						}
					$PB_output .='</p>';
		    	    if($roleID >= 3) {
					    $PB_output .='<p>To start, you may want to post a <a href="'.SELF.'?mode=admin&amp;section=addnews">new article</a>, <a href="'.SELF.'?mode=admin&amp;section=addpage">create a new page</a>, or choose a different task from the navigation. 
			                Below are the admin option to change the appearance and the output displayed by the site, 
				            mange your recent articles, and get some general statistics about the site.
			            </p>';
					    $PB_output .= '<p class="title">A total of <strong>'.$total_articles.' News Articles</strong> were found '.(($CFG->config['display_dirsize'] == 'YES') ? ', totalling '.$UTIL->size('dir',ARTICLES_PATH).' in size' : '' ).'. <a href="'.SELF.'?mode=admin&amp;section=manage&amp;action=articles">Manage Articles</a></p>';
			            $PB_output .= '<p class="title">A total of <strong>'.$total_pages.' Pages</strong> were found '.(($CFG->config['display_dirsize'] == 'YES') ? ', totalling '.$UTIL->size('dir',PAGE_PATH).' in size' : '' ).'. <a href="'.SELF.'?mode=admin&amp;section=manage&amp;action=pages">Manage Pages</a></p>';
			        } 
				$PB_output .='</div>';
				
				$PB_output .='<div class="grid-1 right-1">';
			        if($is_validUser['show_avatar'] == 'YES' && file_exists(AVATAR_PATH.$is_validUser['user_avatar'])){
					    $PB_output .='<figure><img src="'.AVATAR_URL.$is_validUser['user_avatar'].'" alt="'.$is_validUser['user_avatar'].'" />
		                    <figcaption>'.$is_validUser['display_name'].'</figcaption>
		                </figure>';
				    } else $PB_output .='<img src="'.AVATAR_URL.'no_avatar.jpg" alt="no avatar" /><br/><span>'.$is_validUser['display_name'].'</span>';
				    if($CFG->config['admin_username'] == $_SESSION['userName'] && $_SESSION['userRole'] == 'superadmin') $PB_output .= '<div class="panel">'.$CFG->config['admin_bio'].'</div>';
				$PB_output .='</div>';
				
			$PB_output .='</div>';
			$PB_output .='<h3>Choose an Option</h3>';
            $PB_output .='<div class="GUI-nav" align="center">';
          	    $PB_output .='<ul>';
	        	foreach ($GUI_menu as $link) {
				    if($link['permission'] <= $roleID){
		        	    $PB_output .= '<li><div class="item">';
		        	        $PB_output .= '<div class="title">'.$link['title'].'</div>';
			                $PB_output .= '<img src="'.$link['img'].'" alt="'.basename($link['img']).'"  height="'.$height.'" width="'.$width.'"/>';
				            $PB_output .= '<div class="description"><a href="'.$link['href'].'" target="'.$link['target'].'" title="'.(($link['target'] == '_blank') ? 'this page will be opened in a new/blank tab' : 'switch to this page').' - '.$link['title'].'">'.$link['title'].'</a>';
				            $PB_output .='<p>'.$link['desc'].'</p></div>';
			            $PB_output .='</div></li>'."\n";
		            }
		        }
	            $PB_output .='</ul>'."\n";
	        $PB_output .='</div>';

	        $PB_output .= "<script type=\"text/javascript\">
            $(document).ready(function(){
	            var move = -1
                zoom = 1.1;
                $('.item').hover(function() {
                    width = $('.item').width() * zoom;
                    height = $('.item').height() * zoom;
                    $(this).find('img').stop(false,true).animate({'width':width, 'height':height, 'top':move, 'left':move}, {duration:500});
                    $(this).find('div.title').stop(false,true).slideUp(1000);
					$(this).find('div.description').stop(false,true).slideDown(1200);
                },
                function() {
                    //$(this).find('img').stop(false,true).animate({'width':$('.item').width(), 'height':$('.item').height(), 'top':'10px', 'left':'10px'}, {duration:300});    
                    $(this).find('div.title').stop(false,true).slideDown(1200);
					$(this).find('div.description').stop(false,true).slideUp(500);
					$(this).find('img').stop(false,true).animate({'width':'".$width."px', 'height':'".$height."px', 'top':'35px', 'left':'35px'}, {duration:1200});
                });
			});
            </script>";
			
	    $PB_output .= '</div>';
	$PB_output .= '</div>';
	echo $PB_output;
?>