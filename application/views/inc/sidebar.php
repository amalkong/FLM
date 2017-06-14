        <!-- Begin Sidebar -->
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
	        	<div class="widget-title">Search</div>
				<form action="search.php" method="GET" class="searchform">
			        <input type="hidden" name="action" value="SEARCH" />
			        <input type="text" name="keyword" value="Type Search Word And Hit Enter" onfocus="if (this.value == 'Type Search Word And Hit Enter') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Type Search Word And Hit Enter';}" />
		        </form>
	        </div>
	
	        <div class="sidebox widget">
		        <div class="widget-title">Tags</div>
				<?php echo GetTags($meta_keywords); ?>
	        </div>
			
	        <div class="sidebox widget">
			    <?php 
				    //include(WIDGET_PATH.'recent_list.php');
				    GetRecentEntryList(2 ,'date','DESC',true);
				?>
	        </div>
			<?php 
			if(SECTION == 'pages'){ ?>
			    <div class="sidebox widget">
		            <div class="widget-title">Pages</div>
			        <?php include(VIEW_PATH.'inc/list_pages.php'); ?>
                </div>
			    <?php
			} 
			if(ACTION == 'video'){ ?>
	            <div class="sidebox widget">
		            <div class="widget-title">Videos</div>
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
			
			<div class="sidebox widget">
		        <div class="widget-title">Categories</div>
			    <?php echo GetCategories(false);?>
            </div>
        </div>
        <!--End Sidebar -->
        <div class="clear"></div>