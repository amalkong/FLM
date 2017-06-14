            </section>
			<!-- End contentBody -->
			<div class="clear"></div>
		</div>
		<!-- End Container -->
		<div id="status_notification"></div>
		<a id="back-to-top" href="#" title="scroll to the top of the main content section"><i class="font-icon"></i></a>
		<div class="clear"></div>
        <!-- Begin Footer -->
        <div class="footer-wrapper">
			<footer id="footer" class="four">
		        <div id="first" class="widget-area">
		            <div class="widget widget_search">
				        <div class="widget-title">Search</div>
				        <form action="search.php" method="GET" class="searchform">
							<input type="hidden" name="action" value="SEARCH" />
			                <input type="text" name="keyword" value="Type Search Word And Hit Enter" onfocus="if (this.value == 'Type Search Word And Hit Enter') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Type Search Word And Hit Enter';}" />
						</form>
			        </div>
		        </div><!-- #first .widget-area -->
	
		        <div id="second" class="widget-area">
			        <div id="twitter-2" class="widget widget_twitter">
					    <div class="widget-title">Twitter</div>
					
					    <div id="twitter-wrapper">
					        <div id="twitter"></div>
					        <span class="username"><a href="http://twitter.com/elemisdesign">&rarr; Follow @elemisdesign</a></span>
				        </div>
		            </div>
		        </div><!-- #second .widget-area -->
	 
		        <div id="third" class="widget-area">
		            <div class="widget recent-articles">
		                <?php GetRecentEntryList($CFG->config['max_recent'],'date','DESC',true);?>
		            </div>
		        </div><!-- #third .widget-area -->
		
		        <div id="fourth" class="widget-area">
		            <div class="widget">
			            <div class="widget-title">Flickr</div>
			            <ul class="flickr-feed"></ul>
		            </div>
		        </div><!-- #fourth .widget-area -->
            </footer>
		</div>
			
	    <div class="site-generator-wrapper">
			<?php
			    echo '<div class="site-generator"><p><span id="copy">'.$CFG->config['site_title_full'].'&copy; 2014. All rights reserved</span>. <a href="'.SELF.'?mode=admin">Site Admin</a> | <A HREF="http://www.cj-design.com/?id=forum">Script Support</A> | <a href="http://www.cj-design.com/?id=donate">Donate</a></p>';
                echo'<div class="clear"></div>';
				echo '<div class="load-time left-1">'.$UTIL->timer_stop(1).'</div></div>';
			?>
        </div>
		<?php
			echo "<script type=\"text/javascript\" language=\"javascript\">\n";
	            echo "$(document).ready(function(){\n";
		            echo "$('nav.menu ul#tiny li.parent').removeClass('active');\n";
			        if( MODE == 'home' ) echo "$('nav.menu ul#tiny li.parent:eq(0)').addClass('active');\n";
			        elseif( MODE == 'profiles' ) echo "$('nav.menu ul#tiny li.parent:eq(1)').addClass('active');\n";
			        elseif( MODE == 'leagues' ) echo "$('nav.menu ul#tiny li.parent:eq(2)').addClass('active');\n";
			        elseif( MODE == 'gallery' ) echo "$('nav.menu ul#tiny li.parent:eq(3)').addClass('active');\n";
			        elseif( MODE == 'sitemap' ) echo "$('nav.menu ul#tiny li.parent:eq(4)').addClass('active');\n";
			        elseif( MODE == 'admin' ) echo "$('nav.menu ul#tiny li.parent:eq(5)').addClass('active');\n";
                    
					if(SECTION == 'setup' && ACTION == "meta_setup") echo "$('.link-container-top a:eq(0)').addClass('selected');";
                    else if(SECTION == 'setup' && ACTION == "file_setup" ) echo "$('.link-container-top a:eq(1)').addClass('selected');";
				
			        if(SECTION == 'manage' && ACTION == 'action_index') echo "$('.link-container-top a:eq(0)').addClass('active');\n";
			        else if(SECTION == 'manage' && ACTION == 'articles' ) echo "$('.link-container-top a:eq(1)').addClass('selected');\n";
                    else if(SECTION == 'manage' && ACTION == "pages" ) echo "$('.link-container-top a:eq(2)').addClass('selected');\n";
                    else if(SECTION == 'manage' && ACTION == "leagues" ) echo "$('.link-container-top a:eq(3)').addClass('selected');\n";
                    else if(SECTION == 'manage' && ACTION == 'images' ) echo "$('.link-container-top a:eq(4)').addClass('selected');\n";
                    else if(SECTION == 'manage' && ACTION == 'galleries' ) echo "$('.link-container-top a:eq(5)').addClass('selected');\n";
                    else if(SECTION == 'manage' && ACTION == 'categories' ) echo "$('.link-container-top a:eq(6)').addClass('selected');\n";
                    else if(SECTION == 'manage' && ACTION == 'edit' || SECTION == 'manage' && ACTION == 'delete' || ACTION == 'browser' ) echo "$('.link-container-top a:eq(7)').addClass('selected');\n";
				echo "});\n";
	        echo"</script>\n"; 
		?>
    </body> 
</html>