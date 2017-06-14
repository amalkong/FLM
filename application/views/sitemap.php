<?php
 defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
?>
        <div class="content">
            <div class="box2">
		        <nav class="wrapper sitemap-nav">
					<ul id="">
						<li class="parent"><div class="label em i"><a href="index.php?mode=home">Home Section</a></div>
							<ul class="UL-list">
								<li><a href="index.php?mode=viewArticle">View Articles</a></li>
								<li><a href="index.php?mode=home&amp;section=pages">Pages</a>
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
								
			            <li class="parent"><div class="label em i"><a href="index.php?mode=profiles">Profiles Section</a></div>
                            <ul class="UL-list">
								<li><a href="index.php?mode=profiles&amp;section=teamprofile">Team Profile</a></li>
                                <li><a href="index.php?mode=profiles&amp;section=userprofile">User Profile</a></li>
							</ul>
						</li>
						<li class="parent"><div class="label em i"><a href="<?php echo BASE_URL;?>index.php?mode=leagues">Leagues Section</a></div>
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
												    echo'<ul class="UL-list">';
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
						<li class="parent"><div class="label em i"><a href="index.php?mode=viewGallery">View Galleries</a></div></li>
						<li class="parent"><div class="label em i"><a href="index.php?mode=sitemap">Sitemap</a></div></li>
						<?php 
							if($logged_in == 1){
							    require(VIEW_PATH.'inc/admin_menu.php');
							    echo'<li class="parent"><div class="label em i"><a href="index.php?mode=admin">Admin Section</a></div>';
			                        echo'<ul class="UL-list">';
	        	                        foreach ($admin_menu as $link) if($link['permission'] <= $roleID) echo '<li><a href="'.$link['href'].'" title="'.$link['title'].'" target="'.$link['target'].'">'.$link['title'].'</a></li>'."\n";
	                                echo '</ul>'."\n";
							    echo'</li>';
							}
						?>
				    </ul>
				</nav>
				<div class="clear">&nbsp;</div>
			</div>
	    </div>