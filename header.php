<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    $UTIL->timer_start();
    
	$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
	if(isset($_GET['mode']))$pageTitle = cleanPageTitles($_GET['mode'] ).' Area';
	else if(isset($_GET['section']))$pageTitle = cleanPageTitles($_GET['section'] ).' Section';
	else if(isset($_GET['action']))$pageTitle = cleanPageTitles($_GET['action'] );
	else if(isset($_GET['page']))$pageTitle = cleanPageTitles($_GET['page'] ).' Page';
	else if(isset($_GET['album']))$pageTitle = cleanPageTitles($_GET['album'] ).' Album';
	else if(isset($_GET['league_table']))$pageTitle = cleanPageTitles($_GET['league_table'] ).' League';
	else $pageTitle = 'Home';
	
echo _DOCTYPE;
//echo'<!DOCTYPE html>';
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $CFG->config['language'] ;?>" lang="<?php echo $CFG->config['language'] ;?>">
    <head> 
	    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CFG->config['charset'] ;?>" />
        <meta http-equiv="Content-Style-Type" content="text/css" />
	    <meta http-equiv="Language" content="<?php echo $CFG->config['language'] ;?>" />
        <title>
		    <?php
			    if (isset($_GET['newsArticle'])) echo cleanPageTitles($_GET['newsArticle']).$separator;
		        else if (isset($_GET['league'])) echo cleanPageTitles($_GET['league']).'&nbsp;League'.$separator;
			    else if (isset($_GET['page'])) echo cleanPageTitles($_GET['page']).'&nbsp;Page'.$separator;
			    else if (isset($_GET['album'])) echo cleanPageTitles($_GET['album']).'&nbsp;Album'.$separator;
				
	            if (isset($_GET['action'])) echo cleanPageTitles($_GET['action']).$separator;
	            if (isset($_GET['section'])) echo cleanPageTitles($_GET['section']).'&nbsp;Section'.$separator;
				if (isset($_GET['mode'])) echo cleanPageTitles($_GET['mode']).'&nbsp;Area'.$separator;
		        echo $CFG->config['site_title_full'];
			?>
		</title>
		<!-- Mobile Specifics -->
        <meta name="HandheldFriendly" content="true">
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1,user-scalable=no">
        <!--<meta name="viewport" content="initial-scale = 0.6, width = device-width">-->
		<!-- Mobile Internet Explorer ClearType Technology -->
        <!--[if IEMobile]>  <meta http-equiv="cleartype" content="on">  <![endif]-->
		<meta name="Copyright" content="Copyright (c) 2014 PBB inc, all rights reserved." />
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
			
                $("#searchArea").css({'height': '0px','display':'none'});
				
	            $('.showHideSearch').on('click', function(e){
		            $target = $($(this).attr('href')).offset().top-50;
		            $('body, html').animate({scrollTop : $target}, {queue:false, duration: 750, easing: 'easeOutBounce'});
		            e.preventDefault();
					//return false;
	            });

	            $("a.showHideSearch").toggle( 
		            function () {
 			            $("#searchArea").animate({height: "100%"}, {queue:false, duration: 1700, easing: 'easeOutBounce'}) 
                        $("#searchArea").css({'display':'block'});
						$(".showHideSearch").addClass("selected");
					}, 
                    function () { 
			            $("#searchArea").animate({height: "0px"}, {queue:false, duration: 1700, easing: 'easeOutBounce'})  
		                $("#searchArea").css({'height': '0px','display':'none'});
						$(".showHideSearch").removeClass("selected");
					} 
	            );
                
				$('#side-toolbar').stop().animate({'margin-right':'-30px'},1000);
                $('#side-toolbar').mouseenter(function(e) {
					$('#side-toolbar').animate({'margin-right':'0px'},1000);
                }).mouseleave(function(e) {
					$('#side-toolbar').animate({'margin-right':'-30px'},1000);
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
			});
        </script>
		<link rel="icon" href="<?php echo BASE_URL.'favicon.ico'; ?>" type="image/x-icon" />
	    <link rel="shortcut icon" href="<?php echo BASE_URL.'favicon.ico'; ?>" type="image/x-icon" />
	    <link rel="apple-touch-icon" href="<?php echo BASE_URL.'apple-touch-icon.png'; ?>" />
		<link rel="stylesheet" href="<?php echo SKIN_FILE; ?>" type="text/css" />
	</head>
    <body <?php if(MODE == 'admin') echo'class="admin"';?>>
	    <a id="nextsection" href="#contentBody" title="scroll to the next section"><i class="font-icon">&nbsp;</i></a>
        <div id="side-toolbar"><a class="showHideSearch search-btn" href="#searchArea" title="toggle the search bar"><i class="icon icon_view" title="toggle the search bar">&nbsp;</i></a></div>
		
		<header id="header" class="head-section">
			<div class="logo-wrapper" align="center"><img src="<?php echo HEADER_URL.$CFG->config['header']?>" width="100%" height="140"></div>
 
			<hgroup class="header">
	            <h1><?php echo $pageTitle.$separator.$CFG->config['site_title_full']; ?></h1>
			    <h2>
                    <?php
                        if ($logged_in == 0) echo "Welcome Guest&nbsp;&nbsp;$message"; 
                        else echo $message.'&nbsp;&nbsp;'.ucwords($is_validUser['display_name']);
						echo',it is&nbsp;<span id="clock">00:00:00</span>';
					?>
			    </h2>
			</hgroup>
			<div id="menu-wrapper">
		        <nav id="menu" class="menu">
					<ul id="tiny">
						<li class="active parent"><a href="index.php?mode=home">Home</a>
							<ul class="menu-inner">
							    <li><a href="<?php echo BASE_URL;?>index.php?mode=viewArticle">View Article</a></li>
								<li><a href="<?php echo BASE_URL;?>index.php?mode=home&amp;section=pages">Pages</a>
							        <ul class="">
										<?php
										    
										    echo '<li><a href="'.BASE_URL.'index.php?mode=home&amp;section=pages&amp;action=page_list">Page List</a></li>';
											if(is_array($allPages) && count($allPages) > 0) {
											    foreach($allPages as $pagelink) {
												    $pagelink = removeFileExt($pagelink);
												    echo '<li><a href="'.BASE_URL.'index.php?mode=home&amp;section=pages&amp;page='.$pagelink.'">'.ucwords($pagelink).'</a></li>';
											    }
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
						<?php 
							if($logged_in == 1){
								echo '<li class="parent"><a href="'.BASE_URL.'index.php?mode=admin">Admin Panel</a>';
									require(VIEW_PATH.'inc/admin_menu.php');
			                        echo'<ul class="menu-inner">';
	        	                        foreach ($admin_menu as $link) if($link['permission'] <= $roleID) echo '<li><a href="'.$link['href'].'" title="'.$link['title'].'" target="'.$link['target'].'">'.$link['title'].'</a></li>'."\n";
	                                echo '</ul>'."\n";
								echo'</li>';
							}
						?>
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
		<hr/><a name="TOP">&nbsp;</a>
		<?php if($CFG->config['show_ads_banner_top'] == 'YES') include(VIEW_PATH.'inc/ads_top.php'); ?>
		<div id="container">
		    <?php
			echo'<div id="searchArea">';
                search_form(ACTION,$keyword, $limit_hits, $default_val,$max_chars);
			echo"</div>";	//<div class="hRule">&nbsp;</div>
		    ?>
		    <section id="contentBody">