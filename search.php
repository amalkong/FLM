<?php
	define('PBD_FLM', TRUE);
	define('PATH', str_replace('\\','/',__dir__).'/');
    $hint = '';
	
//----------------------------------------------------------------------------------------
	$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : isset($_POST['keyword']) ? $_POST['keyword'] : NULL;
     define('KEYWORD' , $keyword);
	if(file_exists(PATH.'system/Engine.php')) require(PATH.'system/Engine.php');
	else require('./system/Engine.php');
	
	$UTIL->timer_start();
	$PAGE =& load_class('Page','library');
	/* --------------- Search Configurations -----------------------------*/
    $s_dirs = array("application/data/leagues/".LEAGUE."/".SEASON."/fixtures","application/data/leagues/".LEAGUE."/".SEASON."/results","application/data/news","application/data/pages"); // Which directories should be searched ("/dir1","/dir2","/dir1/subdir2","/Verzeichniss2/Unterverzeichniss2")? --> $s_dirs = array(""); searches the entire server
    $s_skip = array("..",".","application/data/leagues/".LEAGUE."/".SEASON."/icons"); // Which files/dirs do you like to skip?
    
	echo _DOCTYPE;
//echo'<!DOCTYPE html>';
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $CFG->config['language'] ;?>" lang="<?php echo $CFG->config['language'] ;?>">
    <head> 
	    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CFG->config['charset'] ;?>" />
        <meta http-equiv="imagetoolbar" content="no">
        <meta http-equiv="Content-Script-Type" content="text/javascript" />
        <meta http-equiv="Content-Style-Type" content="text/css" />
	    <meta http-equiv="Language" content="<?php echo $CFG->config['language'] ;?>" />
		
		<meta name="robots" content="index, follow"> 
		<title><?php echo $CFG->config['site_title_full'].' Search';?></title>
        <!-- Mobile Specifics -->
        <meta name="HandheldFriendly" content="true">
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1,user-scalable=no">
        <!--<meta name="viewport" content="initial-scale = 0.6, width = device-width">-->
		<!-- Mobile Internet Explorer ClearType Technology -->
        <!--[if IEMobile]>  <meta http-equiv="cleartype" content="on">  <![endif]-->
		<meta name="Copyright" content="Copyright (c) 2012 ProjectBlu-media, all rights reserved." />
		<meta name="title" content="<?php echo $meta_title; ?>"> 
		<meta name="description" content="<?php echo $meta_description; ?>"> 
		<meta name="keywords" content="<?php echo $meta_keywords; ?>"> 
		<meta name="author" content="<?php echo $meta_author; ?>">  
        <?php echo $required_js_scripts;?>
        <script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
			    <?php if($CFG->config['skin'] == 'default' || $CFG->config['skin'] == '') echo '$.backstretch("'.TEXTURES_URL.$CFG->config['texture'].'");'?>
			   
			    /*-----------------------------------------------------------------------------------*/
                /*	IMAGE HOVER
                /*-----------------------------------------------------------------------------------*/		
		
		        $('.quick-flickr-item').addClass("frame");
                $('.frame a').prepend('<span class="more"></span>');
                //----------------------------------------------
                $('.frame').mouseenter(function(e) {
                    $(this).children('a').children('span').fadeIn(300);
                }).mouseleave(function(e) {
                    $(this).children('a').children('span').fadeOut(200);
                });
				
				$('.full-table tbody tr').mouseenter(function(e) {
                    $(this).children('td.position').css({'background':'#aaa','color':'#444'});
                }).mouseleave(function(e) {
                    $(this).children('td.position').css({'background':'none','color':'inherit'});
                });
			
		        PBD.scrollToTop();
				PBD.goSection();
			    /*-----------------------------------------------------------------------------------*/
                /*	MENU
                /*-----------------------------------------------------------------------------------*/
                ddsmoothmenu.init({
	                mainmenuid: "menu",
	                orientation: 'h',
	                classname: 'menu',
	                contentsource: "markup"
                });
                $("#searchArea").css('height', '0px');
	            $("a.showHideSearch").toggle( 
		            function () { 
 			            $("#searchArea").animate({height: "100px"}, {queue:false, duration: 1700, easing: 'easeOutBounce'}) 
                        $(".showHideSearch").addClass("selected");
					}, 
                    function () { 
			            $("#searchArea").animate({height: "0px"}, {queue:false, duration: 1700, easing: 'easeOutBounce'})  
		                $(".showHideSearch").removeClass("selected");
					} 
	            );
			});
        </script>
		<link rel="icon" type="image/x-icon" href="favicon.ico" />
	    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	    <link rel="apple-touch-icon" href="apple-touch-icon.png" />
		<link rel="stylesheet" href="<?php echo SKIN_FILE; ?>" type="text/css" />
	</head>
    <body> 
	    <a id="nextsection" href="#contentBody" title="scroll to the next section"><i class="font-icon">&nbsp;</i></a>
	    <header id="header" class="head-section">
			<div class="logo-wrapper" align="center"><img src="<?php echo HEADER_URL.$CFG->config['header']?>" width="100%" height="140"></div>
            <hgroup class="header pbd-black_gray-bkg">
	            <h1><?php echo 'Search Page'.$separator.$CFG->config['site_title_full']; ?></h1>
			    <h2>
                    <?php
					    echo'<p><strong>';
                            if ($logged_in == 0) echo "Welcome Guest&nbsp;&nbsp;$message"; 
                            else echo $message.'&nbsp;&nbsp;'.ucfirst($_SESSION['userName']);
						echo'</strong>,it is <span id="clock">00:00:00</span></p>';
					?>
			    </h2>
			</hgroup> 
			<div id="menu-wrapper">
		        <nav id="menu" class="menu">
					<ul id="tiny">
						<li class="active parent"><a href="index.php?mode=home">Home</a>
							<ul class="menu-inner">
								<li><a href="<?php echo BASE_URL;?>index.php?mode=home&amp;section=pages">Pages</a>
							        <ul class="">
										<?php
										    echo '<li><a href="'.BASE_URL.'index.php?mode=home&amp;section=pages&amp;action=page_list">Page List</a></li>';
											foreach($pageList as $pagelink) {
												$pagelink = removeFileExt($pagelink);
												echo '<li><a href="'.BASE_URL.'index.php?mode=home&amp;section=pages&amp;page='.$pagelink.'">'.ucwords($pagelink).'</a></li>';
											}
										?>
							        </ul>
								</li>
						    </ul>
						</li>
								
			            <li class="parent"><a href="<?php echo BASE_URL;?>index.php?mode=profiles">Profile</a>
                            <ul class="menu-inner">
                                <li><a href="<?php echo BASE_URL;?>index.php?mode=profiles&amp;section=userprofile">User Profile</a></li>
								<li><a href="<?php echo BASE_URL;?>index.php?mode=profiles&amp;section=teamprofile">Team Profile</a></li>
							</ul>
						</li>
						
			            <li class="parent"><a href="<?php echo BASE_URL;?>index.php?mode=leagues">Leagues</a>
                            <?php
								$league_files = GetLeagues();
								if( count($league_files) > 0 ){
			                        $total_leagues = count($league_files);
									echo'<ul class="menu-inner">';
									    for( $i=0; $i<$total_leagues; $i++ ) {
					                        if( isset($league_files[$i][3]) ) {
								                $league_title = $league_files[$i][2];
								                $league_name = $league_files[$i][3];
								                $league_image = $league_files[$i][9];
												if($league_image != NULL && file_exists(LOGO_PATH.$league_image)) $league_logo = LOGO_URL.$league_image;
								                else $league_logo = LOGO_URL.'flm.png';
							                     $img_alt = strlen($league_title) > 18 ? substr($league_title,0,16)."..." : $league_title;
			                        
												$this_league_season_path = $UTIL->Check_For_Slash(LEAGUE_PATH.$league_name,true);
									            $this_league_seasons = GetDirContents($this_league_season_path,'dirs');
									            echo'<li><img class="left-1" src="'.$league_logo.'" alt="'.$img_alt.'" width="30px" height="30px" /><a href="'.BASE_URL.'index.php?mode=leagues&amp;section=leagueTable&amp;league='.$league_name.'&amp;season='.((count($this_league_seasons) > 1 ) ? end($this_league_seasons) : $this_league_seasons[0]).'">'.cleanPageTitles($league_name).'</a>';
												    echo'<ul class="">';
								                        echo'<li class="table-view-link"><a href="'.SELF.'?mode=leagues&amp;section=leagueTable&amp;league='.$league_name.'&amp;season='.((count($this_league_seasons) > 1 ) ? end($this_league_seasons) : $this_league_seasons[0]).'">League Table</a></li>';
                                                        echo'<li class="table-view-link"><a href="'.SELF.'?mode=leagues&amp;section=fixtures&amp;league='.$league_name.'&amp;season='.((count($this_league_seasons) > 1 ) ? end($this_league_seasons) : $this_league_seasons[0]).'">Fixtures</a></li>';
                                                        echo'<li class="table-view-link"><a href="'.SELF.'?mode=leagues&amp;section=results&amp;league='.$league_name.'&amp;season='.((count($this_league_seasons) > 1 ) ? end($this_league_seasons) : $this_league_seasons[0]).'">Results</a></li>';
													echo'</ul>';
												echo'</li>';
									        }
								    	}
									echo'</ul>';	
								}
							?>
						</li>
						<li class="parent"><a href="<?php echo BASE_URL;?>index.php?mode=viewGallery">Gallery</a></li>
						<li class="parent"><a href="<?php echo BASE_URL;?>index.php?mode=sitemap">Sitemap</a></li>
						<li class="parent"><a href="<?php echo BASE_URL;?>index.php?mode=admin">Admin Screen</a>
							<?php 
								if(MODE == 'admin' || $logged_in == 1){
									require(VIEW_PATH.'inc/admin_menu.php');
			                        echo'<ul class="menu-inner">';
	        	                        foreach ($admin_menu as $link) if($link['permission'] <= $roleID) echo '<li><a href="'.$link['href'].'" title="'.$link['title'].'" target="'.$link['target'].'">'.$link['title'].'</a></li>'."\n";
	                                echo '</ul>'."\n";
								}
							?>
						</li>
						<li>
                            <?php 
								if($logged_in ==0) echo'<a href="'.VIEW_URL.'admin/login.php">Login</a>';
								else echo'<a href="'.VIEW_URL.'admin/logout.php">Logout</a>';
                            ?>
						</li>
				    </ul>
				</nav>
				<div class="clear">&nbsp;</div>
			</div>
		</header> 
		<a name="TOP">&nbsp;</a>
		<?php if($CFG->config['show_ads_banner_top'] == 'YES') include(VIEW_PATH.'inc/ads_top.php'); ?>
		<div class="hRule">&nbsp;</div>
		<div id="container">
		<section id="contentBody">
			<div class="content box">
            <?php
			echo'<div id="search-container">'."\n";
			    echo'<div class="caption">'.$CFG->config['site_title_full'].$separator.' SEARCH RESULTS</div>'."\n";
	            echo'<div class="hRule">&nbsp;</div>'."\n";
	            echo"<center>";
                    search_form(ACTION,KEYWORD, $limit_hits, $default_val,$max_chars);
                echo"</center>\n";
                echo'<div class="hRule">&nbsp;</div>'."\n";
                if(isset($_GET['keyword'])){
				    echo"<div class=\"panel no-float\">\n";
                        search_headline(ACTION,KEYWORD, $message_3);
                        search_error(ACTION, $min_chars, $max_chars, $message_1, $message_2, $limit_hits);
                        search_dir(ACTION,KEYWORD,BASE_URI, BASE_PATH, $s_dirs, $s_files, $s_skip, $message_1, $message_2, $no_title, $limit_extracts, $byte_size);
                        search_no_hits(ACTION, $count_hits, $message_4);
			        echo"</div>\n";
			    }
		    	echo"</div>\n";
                ?>
            </div>
		    <div class="sidebar box">
			    <?php 
			    if ((isset($_SESSION['validUser'])) && ($_SESSION['validUser'] == true)){
	                echo'<div class="sidebox widget">';
	        	        echo'<div class="widget-title">User</div>';
			            if($is_validUser['show_avatar'] == 'YES' && file_exists(AVATAR_PATH.$is_validUser['user_avatar'])){
					        $avatar_image = AVATAR_URL.$is_validUser['user_avatar'];
						    $avatar_alt= $is_validUser['display_name'].' avatar';
					    } else {
						    $avatar_image = AVATAR_URL.'no_avatar.jpg';
						    $avatar_alt="no avatar";
						}
						echo '<figure><img src="'.$avatar_image.'" alt="'.$avatar_alt.'" />
		                    <figcaption>'.$is_validUser['display_name'].'</figcaption>
		                </figure>';
				        
			        echo'</div>';
			    }
			    ?>
	            <div class="sidebox widget">
		            <h3 class="widget-title">Tags</h3>
				    <?php echo getTags($meta_keywords); ?>
	             </div>
			
		    	<?php 
			        if(ACTION == 'youtube'){ ?>
	                <div class="sidebox widget">
		                <h3 class="widget-title">Videos</h3>
			            <?php 
                            include LIB_PATH."YouTubeParser.php";
                            # where is the feed located? 
                            $url = "http://gdata.youtube.com/feeds/api/videos?q=skateboarding+dog&start-index=21&max-results=10&v=2"; 
                            # create object to hold data and display output 
                            $parser = new YouTubeParser($url); 
                            $output = $parser->getOutput(); 
                            # returns string containing HTML 
                            echo $output;
				        ?>
                    </div>
			    <?php 
			} ?>
			<div class="sidebox widget">
			    <?php
				    echo'<div class="push-4">';
			            include(VIEW_PATH.'inc/Calendar.php');
			        echo'</div>';
			        if($PB_CONFIG['show_ads_banner_side'] == 'YES'){
				        echo'<hr/>';
				        echo'<div class="grid-auto panel">';
					        echo'<div class="grid-4">';
	                            include(VIEW_PATH.'inc/ads_side.php');
					        echo'</div>';
			            echo'</div>';
				    }
				?>
            </div> 
			
        </div>
        <!--End Sidebar -->
        <div class="clear"></div>
		<?php include(BASE_PATH.'footer.php'); ?>