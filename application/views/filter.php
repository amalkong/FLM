<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    
$f_sortby = ( isset($_GET['f_sortby']) ? GetCommand("f_sortby") : "title" );
$f_ascdsc = ( isset($_GET['f_ascdsc']) ? GetCommand("f_ascdsc") : "DESC" );
$album = ( isset($_GET['album']) ? GetCommand("album") : "" );
$GLOBALS['f_sortby'] = $f_sortby;
$GLOBALS['f_ascdsc'] = $f_ascdsc;
	
$url_prefix = SELF.'?mode=filter&amp;filterby='.$_GET['filterby'];
	$CFG->config['itemsPerPage'] = 1;
if ( !empty( $_GET['author'] ) ){
	$author = $_GET['author'];
	$url_prefix .= '&amp;author='.$author;
	$posts = filterArticles($author ,'author' );
}

if ( !empty( $_GET['date']  ) ){
	$date = $_GET['date'];
	$url_prefix .= '&amp;date='.$date;
	$posts = filterArticles($date ,'date' );
}

if ( !empty( $_GET['category']  ) ){
	$category = $_GET['category'];
	$url_prefix .= '&amp;category='.$category;
	$posts = filterArticles($category ,'category' );
}

if ( !empty( $_GET['tag']  ) ){
	$tag = $_GET['tag'];
	$url_prefix .= '&amp;tag='.$tag;
	$posts = filterArticles($tag ,'tag' );
}

//$posts = $flat->paginate( $posts, isset( $_GET['page'] ) ? $_GET['page'] : 1 );
 ?>

<div class="content">
<section class="box">
<section class="block">
	<?php

	if ( !empty( $author ) || !empty( $date ) || !empty( $category )  || !empty( $tag ) ){
		echo 
		'<p>Displaying posts published
			' . ( !empty( $author ) ? 'by <span class="em i">' . $author . '</span>' : '' ) . '
			' . ( !empty( $category ) ? 'in the category <span class="em i">' . $category . '</span>' : '' ) . '
			' . ( !empty( $date ) ? 'on the <span class="em i">' . $date . '</span>' : '' ) . '
			' . ( !empty( $tag ) ? 'with the tag <span class="em i">' . $tag . '</span>' : '' ) . '
		</p>';
	} else {
		echo '<p>No filter specified, showing all posts. Return to <a href="index.php">home page</a>?</p>';
	}
	?>
</section>

<?php
    if( !empty( $posts ) ){
	    usort($posts,'sort_file');
		
        $numPages = ceil( count($posts) / $CFG->config['itemsPerPage'] );
        if(isset($_GET['p'])) {
	        $currentPage = $_GET['p'];
            if($currentPage > $numPages) $currentPage = $numPages;
        } else $currentPage=1;
        
        $start = ( $currentPage * $CFG->config['itemsPerPage'] ) - $CFG->config['itemsPerPage'];
        $total_posts = count($posts);
	    for( $i=$start; $i<$start + $CFG->config['itemsPerPage']; $i++ ) {
		    if( isset($posts[$i][0]) ) {
                $news_file = removeFileExt($posts[$i][0]);
                $news_id = $posts[$i][1];
			    $news_title = $posts[$i][2];
			    $news_author = $posts[$i][3];
			    $news_author_email = $posts[$i][4];
                $news_date = $posts[$i][5];
			    $news_moddate = $posts[$i][6];
                $news_category = $posts[$i][7];
                $news_keywords = $posts[$i][8];
			    $news_image = $posts[$i][9];
			    $cat_image = $posts[$i][10];
                $news_summary = $posts[$i][11];
                $news_details = $posts[$i][12];
			    $publish = $posts[$i][13];
        
			    echo showArticleBox($news_file,$news_id,$news_title,$news_author,$news_author_email,$news_date,$news_moddate,$news_category,$news_keywords,$news_image,$cat_image,$news_summary,$news_details,$publish);
	        } else {
		        if( isset($posts[$i][0]) ) $PB_output .= $posts[$i][0];
            }
	    }
	    //echo $flat->page_navigation();
		//-----------------------------------------
			$urlVars = $url_prefix."&amp;f_sortby=".$f_sortby."&amp;f_ascdsc=".$f_ascdsc;
			echo'<div class="clear"></div>';
			echo'<div class="paginate-wrapper" class="right- mini">There are : <strong>'.$total_posts.'</strong> post'.(($total_posts > 1) ? 's' :'').' | '. $UTIL->print_pagination($numPages,$urlVars,$currentPage).'</div>';
        
    } else {
	    echo '<section class="block">
	  	    <p>No posts found. Return to <a href="index.php">home page</a>?</p>
	    </section>';
    }
?>
</section></div>