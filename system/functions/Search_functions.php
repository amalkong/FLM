<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    function search_form($action,$keyword, $limit_hits, $default_val,$max_chars) {
	    $case=isset($_GET['case']) ? $_GET['case'] : 'false';
	    $limit= isset($_GET['limit']) ? $_GET['limit'] : 5;
		$j=count($limit_hits);
		if(isset($_GET['keyword'])) $getVar= str_replace("&amp;","&",htmlentities($_GET['keyword']));
	    else $getVar = "$default_val";
	    echo'<form action="search.php" method="GET" class="search-form">'."\n";
		    echo'<table class="search-table" border="0" cellspacing="1" cellpadding="1">'."\n";
	            echo'<tr align="center" valign="top">';
				    echo'<td>';
	                    echo'<input type="hidden" value="SEARCH" name="action" />';
	                    echo'<input type="text" name="keyword" class="text" size="25"  maxlength="'.$max_chars.'" value="'.$getVar.'" onkeyup="showResult(this.value)" onblur="if(this.value==\'\') this.value=\''.$getVar.'\';" onfocus="if(this.value==\''.$getVar.'\') this.value=\'\';" >';
	                echo '</td>';
	                echo '<td>';
					    if($j==1) echo '<input type="hidden" value="'.$limit_hits[0].'" name="limit">';
	                    elseif($j>1) {
		                    echo'<select name="limit" class="select">';
		                    for($i=0;$i<$j;$i++) {
							    if($limit==$limit_hits[$i]) $selected = "SELECTED";
								else $selected = '';
			                      
								echo '<option value="'.$limit_hits[$i].'" '.$selected.'>'.$limit_hits[$i].' Results</option>';
			                }
		                    echo '</select> ';
		                }
			    	echo'</td>';
		            echo'<td>';
	                    echo'<span class="checkbox">Match Case</span> <input type="checkbox" name="case" value="true" class="checkbox" ';
	                        if($case == 'true') echo " CHECKED";
                        echo' />';
				    echo'</td>';
                echo'</tr>'."\n";
				
		        echo'<tr align="center"><td colspan="3">';
					echo'<input type="submit" value="Search" class="button">';
	                echo'<br><a href="http://www.terraserver.de/" class="ts" target="_blank">Powered by terraserver.de/search</a>';
	            echo'</td></tr>'."\n";
		    echo'</table>'."\n";
		echo'</form>'."\n";
	}

    function search_headline($keyword,$action, $message_3) {
	    global $keyword,$action;
	    if(isset($_GET['keyword'])) $keyword=$_GET['keyword'];
	    if(isset($_GET['action'])) $action=$_GET['action'];
	    if($action == "SEARCH") // Volltextsuche
		    echo "<h1 class=\"result\">$message_3 '<i>".htmlentities(stripslashes($keyword))."</i>'</h1>\n";
    }

    function search_error($action, $min_chars, $max_chars, $message_1, $message_2, $limit_hits) {
	    global $action;
	    $keyword=$_GET['keyword'];
	    $action=$_GET['action'];
	    $limit= isset($_GET['limit']) ? $_GET['limit'] : 5;
	    if($action == "SEARCH") { 
		    if(strlen($keyword)<$min_chars||strlen($keyword)>$max_chars||!in_array ($limit, $limit_hits)) {
			    echo "<p class=\"result\"><b>$message_1</b><br>$message_2</p>\n";
			    $_GET['action'] = "ERROR";
		    }
	    }
    }

    function search_dir($action,$keyword,$url, $basepath, $s_dirs, $s_files, $s_skip, $message_1, $message_2, $no_title, $limit_extracts, $byte_size) {
	    global $count_hits,$keyword,$action,$PAGE,$UTIL,$tfxs,$pfxs;
	    $limit= isset($_GET['limit']) ? $_GET['limit'] : 5;;
	    $case=isset($_GET['case']) ? $_GET['case'] : 'false';
	    if($action == "SEARCH") {
		    foreach($s_dirs as $dir) {
			    $handle = @opendir($basepath.$dir);
		    	while($file = @readdir($handle)) {
				    if(in_array($file, $s_skip)) {
				    	continue;
					} elseif($count_hits>=$limit) {
					    break;
					} elseif(is_dir($basepath.$dir."/".$file)) {
					    $s_dirs = array("$dir/$file");
					    search_dir($action,$keyword,$url, $basepath, $s_dirs, $s_files, $s_skip, $message_1, $message_2, $no_title, $limit_extracts, $byte_size); // search_dir() rekursiv auf alle Unterverzeichnisse aufrufen
				    } else if(preg_match("/($s_files)$/i", $file)) {
					    $fd=fopen($basepath.$dir."/".$file,"r");
					    $text=fread($fd, $byte_size); // 50 KB
					    $keyword_html = htmlentities($keyword);
					    if($case) $do=strstr($text, $keyword)||strstr($text, $keyword_html);
					    else $do=stristr($text, $keyword)||stristr($text, $keyword_html);
					    
					    if($do)	{
						    $count_hits++; 
							if($UTIL->isValidExt($file,$pfxs)) {
							    $PAGE->GetFile($basepath.$dir."/$file");
						        if(isset($PAGE->file_metadata)) { // Generierung des Link-Textets aus <title>...</title>
								    $link_title=$PAGE->file_metadata['title'];
						        } else {
							         $link_title='Un-titled';
						        }
								$auszug = '';
								$page_section_count = count($PAGE->file_sections);
								if($page_section_count <= 1){
			                        for($i=0;$i<$page_section_count;$i++){
									    $auszug .= $PAGE->file_sections[$i]['content'];
				                    }
									//implode(',',$auszug);
		                        } else $auszug = $PAGE->file_sections[0]['content'];
			                    //echo $auszug;
								if(strpos($auszug, $keyword)) 
							        echo '<a href="'.$url.'index.php?mode=home&amp;section=pages&amp;page='.$file.'" target="_self" class="result">'.$count_hits.'. '.$link_title.'</a><br>'."\n"; // Ausgabe des Links
							    else {
								    $auszug = '';
								    $count_hits = 0;
								}
							} else if($UTIL->isValidExt($file,$tfxs)){
	                            $news_row = json_decode($text);
                                //$news_id = $news_row->id;
								//$news_author = $news_row->author;
		                        //$news_author_email = $news_row->email;
		                        $news_title = $news_row->title;
								$link_title = $news_title;
                                if($news_row->details == '') $auszug = $news_row->summary;
								else $auszug = $news_row->details;
							    if(strpos($auszug, $keyword)){
						            echo '<a href="'.$url.'index.php?mode=viewArticle&amp;newsArticle='.$file.'" target="_self" class="result">'.$count_hits.'. '.$link_title.'</a><br>'."\n"; // Ausgabe des Links
							    } else {
								    $auszug ='';
								    echo'<hr>False match found. Sorry for that, but it"s nothing to worry about. Just modify the search phrase a bit.';
								}
							} else {
							    //if(!$UTIL->isValidExt($file,$tfxs) || !$UTIL->isValidExt($file,$pfxs)) echo '<a href="'.$url.$dir.'/'.$file.'" target="_self" class="result">'.$count_hits.'. '.$link_title.'</a><br>';
							    if(preg_match_all("=<title[^>]*>(.*)</title>=siU", $text, $titel)) { // Generierung des Link-Textets aus <title>...</title>
							        if(!$titel[1][0]) $link_title='Un-titled'; // <title></title> ist leer...
							        else $link_title=$titel[1][0];
						        } elseif(preg_match_all("=<h1[^>]*>(.*)</h1>=siU", $text, $titel)) { // Generierung des Link-Textets aus <title>...</title>
							        if(!$titel[1][0]) $link_title='Un-titled'; // <title></title> ist leer...
							        else $link_title=$titel[1][0];
						        } elseif(preg_match_all("=<h2[^>]*>(.*)</h2>=siU", $text, $titel)) { // Generierung des Link-Textets aus <title>...</title>
							        if(!$titel[1][0]) $link_title='Un-titled'; // <title></title> ist leer...
							        else $link_title=$titel[1][0];
						        } else {
							        $link_title='Un-titled';
						        }
							    echo '<a href="'.$url.$dir.'/'.$file.'" target="_self" class="result">'.$count_hits.'. '.$link_title.'</a><br>'; 
							
							    $auszug = strip_tags($text);
							}
						    $keyword = preg_quote($keyword);
						    $keyword = str_replace("/","\/","$keyword");
							
						    $keyword_html = preg_quote($keyword_html);
						    $keyword_html = str_replace("/","\/","$keyword_html");
					    	echo "<span class=\"extract\">";
						        if(preg_match_all("/((\s\S*){0,3})($keyword|$keyword_html)((\s?\S*){0,3})/i", $auszug, $match, PREG_SET_ORDER)); {
							        if(!$limit_extracts) $number=count($match);
							        else $number=$limit_extracts;
								
							        for ($h=0;$h<$number;$h++) {
								        if (!empty($match[$h][3]))
									        printf("<i><b>..</b> %s<b>%s</b>%s <b>..</b></i>", $match[$h][1], $match[$h][3], $match[$h][4]);
							        }
						        }
						    echo "</span><br>\n";
						    flush();
						}
					    fclose($fd);
					}
				}
	  		    @closedir($handle);
			}
		}
	}

    function search_no_hits($HTTP_GET_VARS, $count_hits, $message_4) {
	    $action=$_GET['action'];
	    if($action == "SEARCH" && $count_hits<1) echo "<p class=\"result\">$message_4</p>\n";
	}
?>