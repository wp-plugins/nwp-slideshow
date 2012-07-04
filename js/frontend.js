var curSlide = 1;


function movesliding(id) {
    var slideNbr = jQuery(".nwpsWindow .nwpsSlide").length;
    
    dist = 0;
    cur = id;
    for (i = slideNbr; i > 0; i--) {
	if (cur > slideNbr) {
	    cur = 1;
	    dist = (-1)*width*(id-1);
	}
	
	jQuery('#nwpsSlide-'+cur).stop().animate({left:(dist)},speed,'swing');
	
	dist = dist + width;
	cur++;
    }
}

function moveresizing(id) {
    jQuery(".nwpsSlide:not(#nwpsSlide-"+curSlide+")").css("display", "none");
    jQuery(".nwpsSlide").slideUp();
    jQuery(".nwpsSlide").css("left", "0");
    jQuery("#nwpsSlide-"+id).slideDown(speed);
}

function movefading(id) {
    jQuery(".nwpsSlide:not(#nwpsSlide-"+curSlide+")").css("display", "none");
    jQuery(".nwpsSlide").css("left", "0");
    jQuery("#nwpsSlide-"+curSlide).fadeOut(speed, "swing", function() {});
    jQuery("#nwpsSlide-"+id).fadeIn(speed, "swing", function() {});
}

function movebasic(id) {
    jQuery(".nwpsSlide").css("left", "0");
    jQuery(".nwpsSlide").css("display", "none");
    jQuery("#nwpsSlide-"+id).css("display", "inline");
}

function moveNwps(id) {
    // Call move function
    eval('('+movement+')')(id);
    
    curSlide = parseInt(id);
    
    // Change nav buttons
    jQuery(".nwpsNav").attr("src", jQuery("#nwpsNav-"+curSlide).attr("src").replace("onbutton.png", "offbutton.png"));
    jQuery("#nwpsNav-"+curSlide).attr("src", jQuery("#nwpsNav-"+curSlide).attr("src").replace("offbutton.png", "onbutton.png"));
}

function autoMove() {
    tmpId = curSlide + 1;
    
    if (tmpId > jQuery(".nwpsWindow .nwpsImg").length)
	tmpId = 1;
    moveNwps(tmpId);
}

jQuery(document).ready(function() {

    jQuery("#nwpsNav-1").attr("src", jQuery("#nwpsNav-1").attr("src").replace("offbutton.png", "onbutton.png"));
    if (autoslide == "on")
	var intervalId = setInterval("autoMove()", interval*1000);

    jQuery(".nwpsNav").click(function(event) {
	id = event.target.id.replace("nwpsNav-", "");
	moveNwps(id);
    });



});