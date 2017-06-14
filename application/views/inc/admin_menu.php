<?php
    $admin_menu = array(
		array('href' => BASE_URL."index.php?mode=admin&amp;section=updateadmin",'img'  => IMG_URL.'admin.png','desc' => 'update your admin profile by add/edit bio information, change password, change username or change avatar.','title' => 'Update Adminisration','target' => '','permission'=>5),
		array('href' => VIEW_URL."admin/register.php",'img' => IMG_URL.'unknown.png','desc' => 'Add a new user','title' => 'Add User','target' => '','permission'=>5),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=addnews",'img' => IMG_URL.'news.png','desc' => 'News Section, Manage news articles by Creating ,Editing or Delete any news article in the database','title' => 'Create/Add a new News Article','target' => '','permission'=>3),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=addleague",'img' => IMG_URL.'add_league.png','desc' => 'Create/Add a new league','title' => 'Create/Add New League','target' => '','permission'=>4),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=addseason",'img' => IMG_URL.'add_season.png','desc' => 'Add a new season to a registered league, replace the relegated teams with promoted ones.','title' => 'Add New Season','target' => '','permission'=>4),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=addcategory",'img' => IMG_URL.'add_category.png','desc' => 'Create/Add a new category','title' => 'Add Category','target' => '','permission'=>3),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=addfixture&amp;match_type=leagues",'img' => IMG_URL.'fixtures.png','desc' => 'Add the latest fixture list','title' => 'Add League Fixtures','target' => '','permission'=>4),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=updateresults",'img' => IMG_URL.'results.png','desc' => 'Update match results from all leagues','title' => 'Update Results','target' => '','permission'=>4),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=addpage",'img' => IMG_URL.'newpage.png','desc' => 'Create/Add a new page','title' => 'New Page','target' => '','permission'=>3),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=addgallery",'img' => IMG_URL.'gallery.png','desc' => 'Create/Add a new gallery','title' => 'New Gallery','target' => '','permission'=>3),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=addbanner",'img' => IMG_URL.'add_banner.png','desc' => 'Add a new advertisement banner','title' => 'New Advertisement Banner','target' => '','permission'=>4),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=upload",'img' => IMG_URL.'upload.png','desc' => 'Upload new avatars and background textures','title' => 'Uploads','target' => '_blank','permission'=>3),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=systeminfo",'img' => IMG_URL.'info1.png','desc' => 'Get a read out of the system and all database files','title' => 'System Info','target' => '_blank','permission'=>4),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=manage",'img' => IMG_URL.'filemanager.png','desc' => 'Manage files by editing, renaming and deleting news articles, blog posts,and images from the system.','title' => 'File Manager','target' => '_blank','permission'=>4),
		array('href' => BASE_URL."index.php?mode=admin&amp;section=setup",'img' => IMG_URL.'config.png','desc' => 'FLM Setup and Site Management Section, Manage the site&rsquo;s configurations setup, posts,photos by Creating ,Editing or Delete any of these options in the system','title' => 'Configurations','target' => '','permission'=>5)
	);
?>