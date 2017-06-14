<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    /**
     *
     * @param   string  $size_dir - size directory(small,large)
     * @param   string  $max_num_ads - The maximun amount of ads to display
     */
	echo'<div class="ads-banner-wrapper">';
		echo'<div class="title">Advertisements</div>';
	    adsRotator("small",20,"120px","120px");
	echo'</div>';
?>