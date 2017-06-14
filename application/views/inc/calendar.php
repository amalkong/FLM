<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');

$monthNames = Array("January", "February", "March", "April", "May", "June", "July","August", "September", "October", "November", "December");
//$monthNames = (date + strtotime(1-$month-$year) / date + mktime(0,0,0,$month,1,$year)).
/* -------------------------------------- 1 ----------------------------------------- */
if (!isset($_REQUEST["month"])) $_REQUEST["month"] = date("n");
if (!isset($_REQUEST["year"])) $_REQUEST["year"] = date("Y");
/* ------------------------------------- 2 ----------------------------------------- */
$cMonth = $_REQUEST["month"];
$cYear = $_REQUEST["year"];
$prev_year = $cYear;
$next_year = $cYear;
$prev_month = $cMonth-1;
$next_month = $cMonth+1;
 
if ($prev_month == 0 ) {
    $prev_month = 12;
    $prev_year = $cYear - 1;
}
if ($next_month == 13 ) {
    $next_month = 1;
    $next_year = $cYear + 1;
}
    $currentday = date("d");
	$currentmonth = $cMonth;
	$color = "style='color:#FF0000;'";
    $timestamp = mktime(0,0,0,$cMonth,1,$cYear);
    $maxday = date("t",$timestamp);
    $thismonth = getdate ($timestamp);
    $startday = $thismonth['wday'];
    $weekday = $thismonth['weekday'];
/* ------------------------------------ 3 ----------------------------------------- */
?>
<div class="calendar ">
    <table class="calendar-main-table">
        <tr class="calendar-nav">
           <td><a href="<?php echo $_SERVER["PHP_SELF"].'?';
				if(isset($_GET['mode'])) echo 'mode='.$_GET['mode'].'&amp;';
				if(isset($_GET['section'])) echo 'section='.$_GET['section'].'&amp;';
				if(isset($_GET['action'])) echo 'action='.$_GET['action'].'&amp;';
				if(isset($_GET['page'])) echo 'page='.$_GET['page'].'&amp;';
				if(isset($_GET['album'])) echo 'album='.$_GET['album'].'&amp;';
				if(isset($_GET['league'])) echo 'league='.$_GET['league'].'&amp;';
				if(isset($_GET['season'])) echo 'season='.$_GET['season'].'&amp;';
				if(isset($_GET['profile'])) echo 'profile='.$_GET['profile'].'&amp;';
				
				echo "month=". $prev_month . "&amp;year=" . $prev_year; ?>">Previous</a>
			</td>
            <td><a href="<?php echo $_SERVER["PHP_SELF"] .'?';
				if(isset($_GET['mode'])) echo 'mode='.$_GET['mode'].'&amp;';
				if(isset($_GET['section'])) echo 'section='.$_GET['section'].'&amp;';
				if(isset($_GET['action'])) echo 'action='.$_GET['action'].'&amp;';
				if(isset($_GET['page'])) echo 'page='.$_GET['page'].'&amp;';
				if(isset($_GET['album'])) echo 'album='.$_GET['album'].'&amp;';
				if(isset($_GET['league'])) echo 'league='.$_GET['league'].'&amp;';
				if(isset($_GET['season'])) echo 'season='.$_GET['season'].'&amp;';
				if(isset($_GET['profile'])) echo 'profile='.$_GET['profile'].'&amp;';
				echo"month=". $next_month . "&year=" . $next_year; ?>">Next</a>
			</td>
        </tr>
        <tr>
            <td align="center" colspan="2">
                <table class="calendar-days" width="100%">
                    <tr align="center" class="currentmonth"><td colspan="7"><strong><?php echo $monthNames[$cMonth-1].' '.$cYear; ?></strong></td></tr>
                    <tr class="days-th"><td align="center"><strong>S</strong></td><td align="center"><strong>M</strong></td><td align="center"><strong>T</strong></td><td align="center"><strong>W</strong></td><td align="center"><strong>T</strong></td><td align="center"><strong>F</strong></td><td align="center"><strong>S</strong></td></tr>
                    <?php
						for ($i=0; $i<($maxday+$startday); $i++) {
                            if(($i % 7) == 0 ) echo "<tr class=\"days\">\n";
                            if($i < $startday) echo "<td class=\"disabled\"></td>\n";
                            else {
							    if($currentday == ($i - $startday + 1)) {
                                    $class = "class='today'";
									 echo'<td '.$class.' align="center" valign="middle"> '.($i - $startday + 1).' </td>';
                                } else if($weekday == 'saturday'){
                                    $class="weekend";
									echo'<td '.$class.' align="center" valign="middle">'.($i - $startday + 1).' </td>';
                                } else {
                                    $class="";
									echo'<td '.$class.' align="center" valign="middle">'.($i - $startday + 1).' </td>';
                                }
							}
							if(($i % 7) == 6 ) echo "</tr>\n";
                        }             
                    ?>
                </table>
            </td>
        </tr>
    </table>
</div>