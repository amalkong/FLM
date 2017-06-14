<?php defined('PBD_FLM') or exit('Hacking Attempt detected .... Go Fuck Yourself, ACCESS DENIED!');
    $debug = false; // show debug information
	$categoriesenabled = 'no'; // enable/disable categories
	
	$rename_upload_file = TRUE; // if this option is true, the title of the article willbe used as the upload file name or certain characters will be removed from the file.
	$Users_DB_File = DATABASE_PATH.'users.txt';
    $News_Archive_DB_File = DATABASE_PATH.'news_articles_archive.txt';
	$Category_DB_File = DATABASE_PATH.'categories.txt';
	$League_DB_File = DATABASE_PATH.'leagues.txt';
	$Ads_DB_File = DATABASE_PATH.'adBanners.txt';
	$Gallery_DB_File = DATABASE_PATH.'galleries.txt';
    $separator = '&nbsp;&raquo;&nbsp;';
	$next_file = '&nbsp;&gt;&gt;&gt;&nbsp;';
	$previous_file = '&nbsp;&lt;&lt;&lt;&nbsp;';
	
	// Set allowed file types, lowercase without period (.)
    $allowed_file_types = array("jpg","gif","png");

	// Set maximum file upload size in kilobytes.
    $max_size_in_kb = 1020000;
	$max_upload_size = "10000000"; //e.g.: "30000000" (about 30MB)
	$max_upload_video_size = "100000000"; //e.g.: "10000000" (about 100MB)

	$ifxs = array(".jpg",".jpeg",".png",".gif",".JPG",".JPEG",".PNG",".GIF");
	$tfxs = array(".txt",".xml",".md",".json");
	$pfxs = array(".php",".htm",".html",".phtml");
	$afxs = array(".mp3",".ogg",".webma");
	$vfxs = array(".mp4",".ogv",".webm",".flv");
	
	$ignore  = array('.', '..', 'thumbs','Thumbs.db');
	
	$default_stopwords = "able,about,above,according,accordingly,across,actually,after,afterwards,
	              again,against,ain't,all,allow,allows,almost,alone,along,already,also,
				  although,always,am,among,amongst,an,and,another,any,anybody,anyhow,
				  anyone,anything,anyway,anyways,anywhere,apart,appear,appreciate,
				  appropriate,are,aren't,around,as,aside,ask,asking,associated,at,available,
				  away,awfully,be,became,because,become,becomes,becoming,been,before,
				  beforehand,behind,being,believe,below,beside,besides,best,better,
				  between,beyond,both,brief,but,by,c'mon,c's,came,can,can't,cannot,cant,
				  cause,causes,certain,certainly,changes,clearly,co,com,come,comes,concerning,
				  consequently,consider,considering,contain,containing,contains,corresponding,
				  could,couldn't,course,currently,definitely,described,despite,did,didn't,
				  different,do,does,doesn't,doing,don't,done,down,downwards,during,each,
				  edu,eg,eight,either,else,elsewhere,enough,entirely,especially,et,etc,even,ever,every,everybody,everyone,everything,everywhere,ex,exactly,example,except,far,few,fifth,first,five,followed,following,follows,for,former,formerly,forth,four,from,further,furthermore,get,gets,getting,given,gives,go,goes,going,gone,got,gotten,greetings,had,hadn't,happens,hardly,has,hasn't,have,haven't,having,he,he's,hello,help,hence,her,here,here's,hereafter,hereby,herein,hereupon,hers,herself,hi,him,himself,his,hither,hopefully,how,howbeit,however,i'd,i'll,i'm,i've,ie,if,ignored,immediate,in,inasmuch,inc,indeed,indicate,indicated,indicates,inner,insofar,instead,into,inward,is,isn't,it,it'd,it'll,it's,its,itself,just,keep,keeps,kept,know,knows,known,last,lately,later,latter,latterly,least,less,lest,let,let's,like,liked,likely,little,look,looking,looks,ltd,mainly,many,may,maybe,me,mean,meanwhile,merely,might,more,moreover,most,mostly,much,must,my,myself,name,namely,nd,near,nearly,necessary,need,needs,neither,never,nevertheless,new,next,nine,no,nobody,non,none,noone,nor,normally,not,nothing,novel,now,nowhere,obviously,of,off,often,oh,ok,okay,old,on,once,one,ones,only,onto,or,other,others,otherwise,ought,our,ours,ourselves,out,outside,over,overall,own,particular,particularly,per,perhaps,placed,please,plus,possible,presumably,probably,provides,que,quite,qv,rather,rd,re,really,reasonably,regarding,regardless,regards,relatively,respectively,right,said,same,saw,say,saying,says,second,secondly,see,seeing,seem,seemed,seeming,seems,seen,self,selves,sensible,sent,serious,seriously,seven,several,shall,she,should,shouldn't,since,six,so,some,somebody,somehow,someone,something,sometime,sometimes,somewhat,somewhere,soon,sorry,specified,specify,specifying,still,sub,such,sup,sure,t's,take,taken,tell,tends,th,than,thank,thanks,thanx,that,that's,thats,the,their,theirs,them,themselves,then,thence,there,there's,thereafter,thereby,therefore,therein,theres,thereupon,these,they,they'd,they'll,they're,they've,think,third,this,thorough,thoroughly,those,though,three,through,throughout,thru,thus,to,together,too,took,toward,towards,tried,tries,truly,try,trying,twice,two,un,under,unfortunately,unless,unlikely,until,unto,up,upon,us,use,used,useful,uses,using,usually,value,various,very,via,viz,vs,want,wants,was,wasn't,way,we,we'd,we'll,we're,we've,welcome,well,went,were,weren't,what,what's,whatever,when,whence,whenever,where,where's,whereafter,whereas,whereby,wherein,whereupon,wherever,whether,which,while,whither,who,who's,whoever,whole,whom,whose,why,will,willing,wish,with,within,without,won't,wonder,would,would've,wouldn't,yes,yet,you,you'd,you'll,you're,you've,your,yours,yourself,yourselves,zero,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z";
    
	/* --------------- Search Configurations -----------------------------*/
    //$s_dirs = array("application/data/leagues/".LEAGUE."/".SEASON."/fixtures","application/data/leagues/".LEAGUE."/".SEASON."/results","application/data/news_articles","application/data/pages"); // Which directories should be searched ("/dir1","/dir2","/dir1/subdir2","/Verzeichniss2/Unterverzeichniss2")? --> $s_dirs = array(""); searches the entire server
    //$s_skip = array("..",".","subdir2"); // Which files/dirs do you like to skip?
    $s_files = "html|htm|HTM|HTML|php3|php4|php|txt"; // Which files types should be searched? Example: "html$|htm$|php4$"
    $min_chars = "3"; // Min. chars that must be entered to perform the search
    $max_chars = "30"; // Max. chars that can be submited to perform the search
    $default_val = "Search..."; // Default value in searchfield
    $limit_hits = array("5","10","25","50","100"); // How many hits should be displayed, to suppress the select-menu simply use one value in the array --> array("100")
    $message_1 = "Invalid Searchterm!"; // Invalid searchterm
    $message_2 = "Please enter at least '$min_chars', highest '$max_chars' characters."; // Invalid searchterm long ($min_chars/$max_chars)
    $message_3= "Your searchresult for:"; // Headline searchresults
    $message_4 = "Sorry, no hits."; // No hits
    $message_5 = "results"; // Hits
    $message_6 = "Match case"; // Match case
    $no_title = "Untiteled"; // This should be displayed if no title or empty title is found in file
    $limit_extracts = ""; // How many extratcts per file do you like to display. Default: "" --> every extract, alternative: 'integer' e.g. "3"
    $byte_size = "51200"; // How many bytes per file should be searched? Reduce to increase speed
    
/* End of file config.php */
/* Location: ./application/config/config.php */