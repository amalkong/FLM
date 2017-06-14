<?php
 defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
?>
        <div class="ads-banner-wrapper top" class="left-1">
	        <ul id="ticker" class="grid-66 ticker left-1">
			    <?php
				$bannerArrayTop = array();
				$max_num_ads = 6;
				$size_dir = 'large';
			    $bannerWidth = '600';
			    $bannerHeight = '60';
		        $is_random = false;
		        $ads_folder_path = DATA_PATH.'ads/';
		        $ads_folder_url = DATA_URL.'ads/';
		        if(file_exists($Ads_DB_File)){
					$fp = @fopen($Ads_DB_File, 'r');
					//$ads_array = file($Ads_DB_File);
		            $ads_array = explode("\n", fread($fp, filesize($Ads_DB_File))); 
		             //$PB_output .= '<ol>';
		         	for($x=0;$x<sizeof($ads_array);$x++) {	// start loop, each line of file
		                $temp = explode(":",$ads_array[$x]); // explode the line and assign to temp
			            $ad_files = array();
			            $ad_files['ad_id'] = $temp[0];
			            $ad_files['ad_pic'] = $temp[1];
			            $ad_files['ad_pic_alt'] = $temp[2];
						$ad_files['ad_link'] = $temp[3];
			            $ad_files['ad_text'] = $temp[4];
			            $ad_files['ad_size'] = $temp[5];
			            $ad_files['show_ad'] = $temp[6];
			            array_push( $bannerArrayTop,$ad_files);
	                }
					
					if(!$is_random) shuffle($bannerArrayTop);
					$recent_count = 0;
			        $total_ads_count = count($bannerArrayTop);
			        
		            if ($total_ads_count > 0) {
			            for($i=0;$i<$total_ads_count;$i++){
					        if ( $recent_count < $max_num_ads && $bannerArrayTop[$i]['ad_size'] == $size_dir) {
					            if($is_random){
								    srand ((double) microtime () * 1000000);
					                $randomAd = rand (($i), count ($bannerArrayTop)-1);
						            //$randomAd = intval(rand($i,count($bannerArrayTop)-1));
					                $ad_id = $bannerArrayTop[$randomAd]['ad_id'];
					                $ad_pic = $bannerArrayTop[$randomAd]['ad_pic'];
					                $ad_pic_alt = $bannerArrayTop[$randomAd]['ad_pic_alt'];
					                $ad_text = $bannerArrayTop[$randomAd]['ad_text'];
					                $ads_url = $bannerArrayTop[$randomAd]['ad_link'];
					                $ad_size = $bannerArrayTop[$randomAd]['ad_size'];
					                $show_ad = $bannerArrayTop[$randomAd]['show_ad'];
								} else{
								    $ad_id = $bannerArrayTop[$i]['ad_id'];
					                $ad_pic = $bannerArrayTop[$i]['ad_pic'];
					                $ad_pic_alt = $bannerArrayTop[$i]['ad_pic_alt'];
					                $ad_text = $bannerArrayTop[$i]['ad_text'];
					                $ads_url = $bannerArrayTop[$i]['ad_link'];
					                $ad_size = $bannerArrayTop[$i]['ad_size'];
					                $show_ad = $bannerArrayTop[$i]['show_ad'];
								}
					            if(file_exists($ads_folder_path.$size_dir.'/'.$ad_pic)) {
					                if($show_ad == 'YES' && $ad_size == $size_dir) echo '<li><a href="http://'.$ads_url.'" target="_blank" title="'.$ad_text.'"><img src="'.$ads_folder_url.$size_dir.'/'.$ad_pic . '" alt="'.$ad_pic_alt.'" border="0" height="'.$bannerHeight.'" width="'.$bannerWidth.'" /></a><span class="text">'.$ad_text.'</span></li>';
							    }
							    
								if(!file_exists($ads_folder_path.$size_dir.'/'.$ad_pic)) echo '<li><span class="text">For excellent deals <a href="'.SELF.'?mode=home&amp;section=pages&amp;page=contact" target="_blank" title="advertise here">Advertise Here</a></span></li>';
								$recent_count++;
						    }
				        }
		            } else echo '<li><div class="message error"><b>Empty directory,No ad found</b></div></li>';
		        } else echo '<li><div class="message error">Unable to open <b>adBannersTop.txt</b>, either the file does not exists or ads directory was not found</div></li>';
			    ?>
	        </ul>
		    <div class="grid-30 right-1 push-1"><?php adsRotator("small",1,"240px","100px",false); ?></div>
	        <div class="clear">&nbsp;</div>
		</div>
		
        <script type="text/javascript">
	        function tick3(){
		        $('#ticker li:first').animate({'opacity':0}, 350, function () {
			        $(this).appendTo($('#ticker')).css('opacity', 1);
				});
	        }
	        setInterval(function(){ tick3 () }, 7500);
        </script>