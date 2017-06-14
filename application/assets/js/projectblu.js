var PBD = window.PBD || {};
	
    /* ==================================================
     	Scroll to Top
    ================================================== */
    PBD.scrollToTop = function(){
	    var windowWidth = $(window).width(),
	    didScroll = false;

	    var $arrow = $('#back-to-top');

	    $arrow.click(function(e) {
		    $('body,html').animate({ scrollTop: "0" }, 750, 'easeInOutBounce' );
		    e.preventDefault();
	    })

	    $(window).scroll(function() {
		    didScroll = true;
	    });

	    setInterval(function() {
		    if( didScroll ) {
			    didScroll = false;

			    if( $(window).scrollTop() > 200 ) $arrow.css('display', 'block');
			    else $arrow.css('display', 'none');
		    }
	    }, 250);
    }
	
/* ==================================================
   Next Section
================================================== */

PBD.goSection = function(){
	    var windowWidth = $(window).width(),didScroll = false;
		var $arrow = $('#nextsection');
		
	    $arrow.on('click', function(){
	    //$('#nextsection').on('click', function(){
		    $target = $($(this).attr('href')).offset().top-30;
		
		    $('body, html').animate({scrollTop : $target}, 750, 'easeOutExpo');
	    	return false;
	    });

	    $(window).scroll(function() {
		    didScroll = true;
	    });

	    setInterval(function() {
		    if( didScroll ) {
			    didScroll = false;

			    if( $(window).scrollTop() < 200 ) $arrow.css('display', 'block');
			    else $arrow.css('display', 'none');
		    }
	    }, 250);
    }
	
/* ==================================================
   GoUp
================================================== */

PBD.goUp = function(){
	$('#goUp').on('click', function(){
		$target = $($(this).attr('href')).offset().top-30;
		$('body, html').animate({scrollTop : $target}, 750, 'easeOutExpo');
		return false;
	});
}
 /*---------------------------------------------------------------------------- */
		function setOpacity(id, opacity){
			var element = document.getElementById(id).style;
			element.opacity = (opacity / 100);	// std
			element.MozOpacity = (opacity / 100);	// firefox
			element.filter = 'alpha(opacity=' + opacity + ')';	// IE
			element.KhtmlOpacity = (opacity / 100);	// Mac
		}

		function fadeOpacity(id, opacityStart, opacityEnd, msToFade){
			if (msToFade > 0){
				var frames = Math.round((msToFade / 1000) * 30);
				var msPerFrame = Math.round(msToFade / frames);
				var opacityPerFrame = (opacityEnd - opacityStart) / frames;
				var opacity = opacityStart;
			
				for (frame = 1; frame <= frames; frame++){
					setTimeout('setOpacity(\'' + id + '\',' + opacity + ')',(frame * msPerFrame));
					opacity += opacityPerFrame;
				}
				if (opacityEnd == 0) setTimeout('document.getElementById(\'' + id + '\').style.visibility=\'hidden\'',((frames+1) * msPerFrame));
				else setTimeout('setOpacity(\'' + id + '\',' + opacityEnd + ')',((frames+1) * msPerFrame));
				
			} else {
				setOpacity(id, opacityEnd);
				if (opacityEnd == 0) document.getElementById(id).style.visibility='hidden';
			}
		}
		
        function addLoadListener(fn){
            if (typeof window.addEventListener != 'undefined') window.addEventListener('load', fn, false);
            else if (typeof document.addEventListener != 'undefined') document.addEventListener('load', fn, false);
            else if (typeof window.attachEvent != 'undefined') window.attachEvent('onload', fn);
            else {
                var oldfn = window.onload;
                if (typeof window.onload != 'function') window.onload = fn;
                else {
                    window.onload = function(){
                        oldfn();
                        fn();
                    };
                }
            }
        }

        function update_time() {
            var rightnow = new Date(); // The current date and time
            var hours = rightnow.getHours(); // Capture the hours as a string, "00" thru "23"
			if (hours < 10) var hourstring = "0" + hours.toString();
            else var hourstring = hours.toString();
                
            var minutes = rightnow.getMinutes(); // Capture the minutes as a string, "00" thru "59"
            if (minutes < 10) var minutestring = "0" + minutes.toString();
            else var minutestring = minutes.toString();
               
            var seconds = rightnow.getSeconds(); // Capture the seconds as a string, "00" thru "59"
            if (seconds < 10) var secondstring = "0" + seconds.toString();
            else var secondstring = seconds.toString();
               
            var timestring = hourstring + ":" + minutestring + ":" + secondstring; // Put it all together, "00:00:00"
            var timeplace = document.getElementById("clock"); // Manipulate the DOM, display it to the screen!
            timeplace.childNodes[0].nodeValue = timestring;
            setTimeout('update_time()', 1000);
        }
		function update_time2() {
            var rightnow = new Date(); // The current date and time
			//var date = rightnow.getDate();
            var timeplace = document.getElementById("clock"); // Manipulate the DOM, display it to the screen!
            timeplace.childNodes[0].nodeValue = rightnow;
            setTimeout('update_time2()', 1000);
        }
        /*
		var myVideo=document.getElementById("highlight-video"); 
        function playPause(){if (myVideo.paused) myVideo.play();else myVideo.pause();}
        function enlarge(){myVideo.width=650,myVideo.height=280}
        function reduce(){myVideo.width=200;}
        function Normal(){myVideo.width=300;}
		*/
/* ---------------------------------------------------------------- */
        function kadabra(zap) {
	        if (document.getElementById) {
		        var abra = document.getElementById(zap).style;
		        if (abra.display == 'none') abra.display = 'block';
		        else abra.display = 'none';
				
				fadeOpacity(abra, 90, 0, 3900);
				
		        return false;
	        } else return true;
        }

/* -------------------------------------------------------------------------- */		
		function limitText(limitField, limitCount, limitNum) {
	        if (limitField.value.length > limitNum) limitField.value = limitField.value.substring(0, limitNum);
	        else limitCount.value = limitNum - limitField.value.length;
        } 
	
        function cnt(w,x){
            var y=w.value;
            var r = 0;
            a=y.replace(/\s/g,' ');
            a=a.split(',');
            for (z=0; z<a.length; z++) {if (a[z].length > 0) r++;}
            x.value=r;
        }

// *********   **************************************************
		function confirmation(message) {return confirm(message);}
		
		function showNotify( str ) {
		    var elem = document.getElementById("status_notification");
		    elem.style.display = "block";
		    elem.style.visibility = "visible";

		    if ( elem.currentStyle && elem.currentStyle.position == "absolute" ) {
			    elem.style.top = '0';
		    }
		    elem.innerHTML = str;
	    }

    	function hideNotify() {
		    var elem = document.getElementById("status_notification");
		    elem.style.display = "none";
		    elem.style.visibility = "hidden";
	    }
	//addLoadListener(update_time);
	addLoadListener(update_time2);