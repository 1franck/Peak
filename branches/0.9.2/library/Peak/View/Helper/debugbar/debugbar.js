//load jquery function
load = function() {
    load.getScript("http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js");
    load.tryReady(0);
}

// dynamically load any javascript file.
load.getScript = function(filename) {
    var script = document.createElement('script');
    script.setAttribute("type","text/javascript");
    script.setAttribute("src", filename);
    if (typeof script!="undefined") document.getElementsByTagName("head")[0].appendChild(script);
}

load.tryReady = function(time_elapsed) {
    // Continually polls to see if jQuery is loaded.
    if (typeof $ == "undefined") { // if jQuery isn't loaded yet...
        if (time_elapsed <= 5000) { // and we havn't given up trying...
            setTimeout("load.tryReady(" + (time_elapsed + 200) + ")", 200); // set a timer to check again in 200 ms.
        } else {
            //alert("Timed out while loading jQuery.")
        }
    } else {
        $(function() {

            //resize debug bar window to browser window
            $(window).resize(function(){
              
                var bodyoffset = $('body').offset(),
                    dboffset = $('#pkdebugbar').offset(),
                    newheight = (dboffset.top - bodyoffset.top) - 40,
                    newwidth = $('body').width() - 60;
                
                $('.window.resizable').css('height',newheight);
                $('.window.resizable').css('width',newwidth);
                
            });
        });
    }
}

// start loading
load();


function pkdebugShow(id) {
	var target = "#" + id + "_window",
        bodyoffset = $('body').offset(),
        dboffset = $('#pkdebugbar').offset(),
        newheight = (dboffset.top - bodyoffset.top) - 40,
        newwidth = $('body').width() - 60;
    
    id = "#" + id;

	if($(id).hasClass("current")) {
		$(id).removeClass("current");
        $(target).fadeOut('fast');
	} else {
		pkdebugCloseAll();
		$(id).addClass("current");
        $(target).fadeIn('fast');
	}
    //fit window to screen
    if($(target).hasClass('resizable')) {
        $(target).css('height',newheight);
        $(target).css('width',newwidth);
    }
}

function pkdebugCloseAll() {
	$("#pkdebugbar .window").hide();
	$("#pkdebugbar .pkdb_tab").removeClass("current");	
}

function pkdebugToggle() {
	if($("#pkdebugbar li a#hideshow").hasClass("hidebar")) {
		pkdebugCloseAll();
        $("#pkdebugbar, #pkdebugbar .pkdbpanel").css({ width: '36px'});
		$("#pkdebugbar li").hide();
		$("#pkdebugbar li#togglebar").show();
        $("#pkdebugbar li a#hideshow").removeClass("hidebar").addClass("showbar");
	} else {
        $("#pkdebugbar, #pkdebugbar .pkdbpanel").css({ width: '100%'});
		$("#pkdebugbar li").show();
		$("#pkdebugbar li a#hideshow").removeClass("showbar").addClass("hidebar");
	}
}

function pkfullscreenToggle() {
    $("#pkdebugbar .window").addClass('fullscreen');
}