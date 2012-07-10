function delSlideshow() {
    if (confirm(jQuery("#jsmess-delslideshow").html()) == true)
	jQuery("#delslideshowForm").submit();
}

function deleteSlide() {
    if (confirm(jQuery("#jsmess-delete").html()) == true)
	jQuery("#deleteSlideForm").submit();
}

function osc(img, selection) {
    jQuery("#textposy1").val(selection.y1);
    jQuery("#textposy2").val(selection.y2);
    jQuery("#textposx1").val(selection.x1);
    jQuery("#textposx2").val(selection.x2);
}

function addSlide() {
    if (jQuery("#addSlide").css("display") == "none")
	jQuery("#addSlide").slideDown();
    else
	jQuery("#addSlide").slideUp();
}

function addSlideshow() {
    if (jQuery("#addSlideshow").css("display") == "none")
	jQuery("#addSlideshow").slideDown();
    else
	jQuery("#addSlideshow").slideUp();	
}

function advancedOptions() {
    if (jQuery("#advancedOptions").css("display") == "none")
	jQuery("#advancedOptions").slideDown();
    else
	jQuery("#advancedOptions").slideUp();
}

jQuery(document).ready(function() {

    if (jQuery("#delslideshowForm").length > 0) {
	jQuery("#delSlideshow").click(function() { delSlideshow(); });
	jQuery("#advancedButton").click(function() { advancedOptions(); });
    }
    else if (jQuery("#slideLst").length > 0) {
	if (jQuery("#editSlide").length > 0) {
	    var selection = jQuery('#slideImgFrame').imgAreaSelect({
		handles: true,
		x1:jQuery("#textposx1").val(),
		y1:jQuery("#textposy1").val(),
		x2:jQuery("#textposx2").val(),
		y2:jQuery("#textposy2").val(),
		persistent:true,
		onSelectEnd: osc
	    });
	    jQuery(".delete").click(function() { deleteSlide(); });
	}
	if (jQuery("#addSlideButton").length > 0) {
	    jQuery("#addSlideButton").click(function() { addSlide(); });
	    jQuery("#cancel").click(function() { addSlide(); });
	}
    }

    jQuery("#addSlideshowButton").click(function() { addSlideshow(); });
    jQuery("#changeSlideshowButton").change(function() { jQuery("#changeSlideshow").submit() });

});
