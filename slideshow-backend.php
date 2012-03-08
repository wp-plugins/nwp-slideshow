<?php


if (!empty($_POST['action']))
  if ($_POST['action'] == "editslideshow")
    editSlideshow($_POST, $_FILES);
  else if ($_POST['action'] == "resetslideshow")
    resetSlideshow();
  else if ($_POST['action'] == "editslide")
    editSlide($_POST, $_FILES);
  else if ($_POST['action'] == "deleteslide")
    deleteSlide($_POST);
  else if ($_POST['action'] == "addslide")
    addSlide($_POST, $_FILES);


/******************
 * ABOUT SECTION */

function nwpsAboutSettings($uri) { 
  global $nwpsVersion;

  /* About the plugin */

  echo "<h3>".__("About the plugin", "nwp-slideshow")."</h3>";
  echo "<div class='aboutP'>";

  _e("NWP Slideshow - Version : ");
  echo $nwpsVersion."<br /><br />";

  _e("NWP Slideshow allows you to display and customize a slideshow very easily on your WordPress.", "nwp-slideshow");
  echo "<br />";
  _e("Languages available are English and French. If you want to help on translation, please contact me =).", "nwp-slideshow");
  echo "<br /><br />";

  _e("The plugin works using jQuery and <a href='http://odyniec.net/projects/imgareaselect/' target='blank'>imgareaselect jQuery's plugin</a>.", "nwp-slideshow");
  echo "<br /><br />";

  _e("If you have any bug, any advice, any complain, please <a href='http://blog.nephila.fr/nwp-slideshow-plugin-wordpress-en'>check its blog's page</a> or contact me at <a href='mailto:franck@nephila.fr'>franck@nephila.fr</a>.", "nwp-slideshow");

  echo "</div>"; // .aboutP

  /* About Me */

  echo "<h3>".__("About me", "nwp-slideshow")."</h3>";
  echo "<div class='aboutP'>";
    echo "<img src='".plugins_url("/images/", __FILE__);
    _e("needFreelance-en.png", "nwp-slideshow");
    echo "' /><br />";
  _e("Hi. My name is Franck Kosellek, a freelance web developer and a huge WordPress fan !", "nwp-slideshow");
  echo "<br />";
  _e("I love this CMS that much because you almost can do any web project you want with it..", "nwp-slideshow");
  echo "<br /><br />";

  _e("If you need a freelance developer, SEO, contact me.", "nwp-slideshow");
  echo "<br />";

  _e("If you need an advice, or information about Wordpress, contact me.", "nwp-slideshow");
  echo "<br />";
  _e("If you need a graphist, well... contact me and I will redirect you to someone who really knows something about graphism ;).", "nwp-slideshow");
  echo "<br /><br />";

  echo "<a href='http://www.nephila.fr'>";
  _e("Check out my website ", "nwp-slideshow");
  echo "</a>";
  _e("(only in french for now =/ translation's in progress...)", "nwp-slideshow");
  echo "<br />";
  echo "<a href='mailto:franck@nephila.fr'>";
  _e("or e-mail me", "nwp-slideshow");
  echo "</a>.<br />";
  _e("See me on ", "nwp-slideshow");
  echo "<a href='https://plus.google.com/114926869349067736072/about'>Google+</a>,
<a href='https://www.facebook.com/profile.php?id=100000319169550'>Facebook</a>,
<a href='http://www.linkedin.com/in/franckkosellek'>Linkedin</a>,
<a href='http://ca.viadeo.com/fr/profile/kosellek.franck'>Viadeo</a>.";
echo "</div>"; // .aboutP


 }

/*******************
 * SLIDES SECTION */

function nwpsSlidesSettings($uri) {
  global $wpdb;
  global $unwritable;

  /* Retrieve Slideshow Options */
  $slideshow = get_option("nwp_slideshow");
  if (is_string($slideshow))
    $slideshow = unserialize($slideshow);

  /* retrieve slides */
  $q = "SELECT * FROM nwp_slideshow ORDER BY position";
  $slides = $wpdb->get_results($q);

  /* Get current slide ID */
  $slideId = 0;
  if (!empty($_GET['slide']))
    $slideId = $_GET['slide'];

  /* Slides numbering */
  echo "<div id='slideLst'>Slides : ";
  for ($i = 0; isset($slides[$i]); $i++) {
    if ($i == $slideId) {
      $curSlide = $slides[$i];
      echo "<b>".($i+1)."</b> ";
    } else
      echo "<a href='".$uri[0]."&section=slides&slide=".$i."'>".($i+1)."</a> ";
  }
  echo "&nbsp; &nbsp; &nbsp; ";
  if (!empty($unwritable))
    echo $unwritable;
  else
    echo "<a href='javascript:void(0);' id='addSlideButton'>+ ".__("NEW SLIDE", "nwp-slideshow")."</a>";
  echo "</div>";

  if (file_exists(dirname(__FILE__)."/images/slides/".$curSlide->image))
    $imageUrl = plugins_url("images/slides/".$curSlide->image, __FILE__);
  else
    $imageUrl = plugins_url("images/noimage.png", __FILE__);


  ?>

  <form id="addSlide" method="post" enctype="multipart/form-data" action="<?php echo $uri[0]; ?>&section=slides">
    <h3><?php echo _e("Add a new slide", "nwp-slideshow"); ?></h3>
  <input type="hidden" name="action" value="addslide" />
  <div class="row"><label class="label" for="positionAdd"><?php echo _e("Position", "nwp-slideshow"); ?></label> <input type="text" name="position" id="positionAdd" /> </div>
  <div class="row"><label class="label" for="urlAdd"><?php echo _e("Url", "nwp-slideshow"); ?></label> <input type="text" name="url" id="urlAdd" /> </div>
  <div class="row"><label class="label" for="imageAdd"><?php echo _e("Image", "nwp-slideshow"); ?></label> <input type="file" name="image" id="imageAdd" /> </div>
  <br />
  <input type="submit" value="<?php echo _e("Add the new slide", "nwp-slideshow"); ?>">
  <span class="spacement"></span>
  <a href='javascript:void(0);' id='cancel'><?php _e("cancel", "nwp-slideshow"); ?></a>
  </form>

<?php
  /* Check if there is slides in Database */
  if (!$slides) {
    echo "<br /><br /><br />".__("There is no slide.", "nwp-slideshow");
    return ;
  }
?>

  <br />
  <form method="post" id="deleteSlideForm" action="<?php echo $uri[0]; ?>&section=slides">
  <input type="hidden" name="action" value="deleteslide" />
  <input type="hidden" name="id" value="<?php echo $curSlide->id; ?>" />
<input type="hidden" name="curImage" value="<?php echo $curSlide->image; ?>" />
</form>
<form method="post" id="editSlide" enctype="multipart/form-data">
<input type="hidden" name="action" value="editslide" />
<input type="hidden" name="id" value="<?php echo $curSlide->id; ?>" />
<input type="hidden" name="curImage" value="<?php echo $curSlide->image; ?>" />

<input type="hidden" name="textposx1" id="textposx1" value="<?php echo $curSlide->textposx1; ?>" />
<input type="hidden" name="textposx2" id="textposx2" value="<?php echo $curSlide->textposx2; ?>" />
<input type="hidden" name="textposy1" id="textposy1" value="<?php echo $curSlide->textposy1; ?>" />
<input type="hidden" name="textposy2" id="textposy2" value="<?php echo $curSlide->textposy2; ?>" />

<label for="position"><?php echo _e("Position", "nwp-slideshow"); ?></label> <input type="text" name="position" id="position" value="<?php echo $curSlide->position; ?>" />
<span class="spacement"></span>
<label for="url"><?php echo _e("Url", "nwp-slideshow"); ?></label> <input type="text" name="url" id="url" value="<?php echo $curSlide->url; ?>" /> <br />

<br />
<h3><?php echo _e("Image", "nwp-slideshow"); ?></h3>

<div id="slideImgFrame" style='width:<?php echo $slideshow["sizewidth"]; ?>px; height:<?php echo $slideshow["sizeheight"]; ?>px;'>
  <img class="slideImg" src="<?php echo $imageUrl; ?>" alt="<?php echo $curSlide->image; ?>" />
</div>

<?php if (!empty($unwritable)) { echo $unwritable; } else { ?>
<label for="image"><?php echo _e("Change image", "nwp-slideshow"); ?></label><input type="file" name="image" id="image" />
    <?php } ?>    
    <br /><br />
    <h3><?php echo _e("Content (Selection in front of the image indicates where content will be displayed)", "nwp-slideshow"); ?></h3>
    
    <?php the_editor(str_replace("\\", "", $curSlide->text), "text"); ?>
    
    <br /><br />

    <input type="submit" value="<?php echo _e("Edit the slide", "nwp-slideshow"); ?>" />
    <span class="spacement"></span>
    <a href="javascript:void(0);" class="delete"><?php echo _e("Delete this slide", "nwp-slideshow"); ?></a>
    
  </form>
  <?php }
    
    
    
    /********************
     * OPTIONS SECTION */
    
    function nwpsOptionsSettings($uri) {
     global $unwritable;
     
  $slideshow = get_option("nwp_slideshow");
  if (is_string($slideshow))
    $slideshow = unserialize($slideshow);
  $categories = get_categories();


  ?>

<form method="post" id="resetForm">
  <input type="hidden" name="action" value="resetslideshow" />
</form>
<form method="post" enctype="multipart/form-data">
<input type="hidden" name="action" value="editslideshow" />
  <div class="row">
    <div class="label"><?php _e("Content", "nwp-slideshow"); ?></div>
    <div class="type">
      <input type="radio" name="type" id="customType" value="custom" <?php if ($slideshow['type'] == "custom") { echo "checked"; } ?> />
      <label for="customType"><?php _e("Custom", "nwp-slideshow"); ?></label> (<?php _e("Each slide is set by yourself", "nwp-slideshow"); ?>)
      <br />
      <input type="radio" name="type" id="lastType" value="last" <?php if ($slideshow['type'] == "last") { echo "checked"; } ?> />
      <label for="lastType"><?php _e("Last posts", "nwp-slideshow"); ?></label> (<?php _e("Automatically show the x last posts", "nwp-slideshow"); ?>)
      <br />
      <input type="radio" name="type" id="categoryType" value="category" <?php if ($slideshow['type'] == "category") { echo "checked"; } ?> />
      <label for="categoryType"><?php _e("From category", "nwp-slideshow"); ?></label> (<?php _e("Posts from a specific category", "nwp-slideshow"); ?>)
    </div>
  </div>
  <div class="row">
    <label class="label" for="movement"><?php _e("Movement", "nwp-slideshow"); ?></label>
    <select name="movement" id="movement">
      <option value="movesliding" <?php if ($slideshow['movement'] == "movesliding") { echo "selected"; } ?>>Slide</option>
      <option value="movefading" <?php if ($slideshow['movement'] == "movefading") { echo "selected"; } ?>>Fade in / Fade out</option>
      <option value="moveresizing" <?php if ($slideshow['movement'] == "moveresizing") { echo "selected"; } ?>>Resize</option>
      <option value="movebasic" <?php if ($slideshow['movement'] == "movebasic") { echo "selected"; } ?>>Basic</option>
    </select>
  </div>
  <div class="row">
    <div class="label"><?php _e("Slideshow size (px)", "nwp-slideshow"); ?></div>
    <label for="sizewidth">Width</label> <input type="text" name="sizewidth" id="sizewidth" value="<?php echo $slideshow['sizewidth']; ?>" class="nbrStyle" />
    <span class="spacement"></span>
    <label for="sizeheight">Height</label> <input type="text" name="sizeheight" id="sizeheight" value="<?php echo $slideshow['sizeheight']; ?>" class="nbrStyle" />
  </div>
  <div class="row">
    <label class="label" for="postsnbr"><?php _e("Shown posts number", "nwp-slideshow"); ?></label>
    <input type="text" name="postsnbr" id="postsnbr" value="<?php echo $slideshow['postsnbr']; ?>" class="nbrStyle" />
    <span class="clue"><b><?php _e("Only for 'last posts' or 'From category' type", "nwp-slideshow"); ?> : </b> <?php _e("How many posts are displayed ?", "nwp-slideshow"); ?> </span>
  </div>

  <div class="row">
    <label class="label" for="category"><?php _e("Category's posts", "nwp-slideshow"); ?></label>
    <select name="category" id="category">
      <?php
	foreach ($categories as $category) {
	  echo "<option value='".$category->term_id."' ";
	  if ($category->term_id == $slideshow['category'])
	    echo "selected";
	  echo " >".$category->name."</option>";
	}
      ?>
    </select>
    <span class="clue"><b><?php _e("Only for 'From category' type", "nwp-slideshow"); ?> : </b> <?php _e("From which category posts are displayed ?", "nwp-slideshow"); ?> </span>
  </div>

<br />
<a href="javascript:void(0);" id="advancedButton" ><?php _e("+ Advanced options", "nwp-slideshow"); ?></a>

<div id="advancedOptions">

  <div class="row">
    <label class="label" for="shortcode"><?php _e("Shortcode", "nwp-slideshow"); ?></label>
    <input type="text" name="shortcode" id="shortcode" value="<?php echo $slideshow['shortcode']; ?>" />
    <span class="clue"><?php _e("To put the slideshow into a post/page. insert [shortcode] anywhere in the content.", "nwp-slideshow"); ?></span>
  </div>
  <div class="row">
    <label class="label" for="interval"><?php _e("Time interval (seconds)", "nwp-slideshow"); ?></label>
    <input type="text" name="interval" id="interval" value="<?php echo $slideshow['interval']; ?>" class="nbrStyle" />
    <span class="clue"><?php _e("Time elapsed between 2 slides.", "nwp-slideshow"); ?></span>
  </div>
  <div class="row">
    <label class="label" for="speed"><?php _e("Speed (ms)", "nwp-slideshow"); ?></label>
    <input type="text" name="speed" id="speed" value="<?php echo $slideshow['speed']; ?>" class="nbrStyle" />
    <span class="clue"><?php _e("Speed of the transition.", "nwp-slideshow"); ?></span>
  </div>
  <div class="row">
    <label class="label" for="autoslide"><?php _e("Auto slide", "nwp-slideshow"); ?></label>
    <input type="checkbox" name="autoslide" id="autoslide" <?php if ($slideshow['autoslide'] == "on") { echo "checked"; } ?> />
  </div>

  <div class="row">
    <label class="label" for="onbuttonimg"><?php _e("On button's image", "nwp-slideshow"); ?></label>
    <?php if (!empty($unwritable)) { echo $unwritable; } else { ?>
    <input type="file" name="onbuttonimg" id="onbuttonimg" />
    <span class="clue"><?php _e("Preview : ", "nwp-slideshow"); ?> <img src="<?php echo plugins_url("/images/slides/onbutton.png", __FILE__); ?>" alt="off button img" /> </span>
    <?php } ?>
  </div>
  <div class="row">
    <label class="label" for="offbuttonimg"><?php _e("Off button's image", "nwp-slideshow"); ?></label>
    <?php if (!empty($unwritable)) { echo $unwritable; } else { ?>
    <input type="file" name="offbuttonimg" id="offbuttonimg" />
    <span class="clue"><?php _e("Preview : ", "nwp-slideshow"); ?> <img src="<?php echo plugins_url("/images/slides/offbutton.png", __FILE__); ?>" alt="off button img" /> </span>
    <?php } ?>
  </div>
  <div class="row">
    <div class="label"><?php _e("Buttons position (px)", "nwp-slideshow"); ?></div>
    <label for="buttonposx">X</label> <input type="text" name="buttonposx" id="buttonposx" value="<?php echo $slideshow['buttonposx']; ?>" class="nbrStyle" />
    <span class="spacement"></span>
    <label for="buttonposy">Y</label> <input type="text" name="buttonposy" id="buttonposy" value="<?php echo $slideshow['buttonposy']; ?>" class="nbrStyle" />
  <span class="clue"><?php _e("Where are located slides buttons.", "nwp-slideshow"); ?></span>
  </div>

  </div> <!-- #advancedOptions -->

<br /><br />

<input type="submit" value="<?php _e("Edit Slideshow", "nwp-slideshow"); ?>" />
<span class="spacement"> </span>
	<a href="javascript:void(0);" id="reset"><?php _e("reset", "nwp-slideshow"); ?></a>

</form>

<?php
  echo "<div id='help'>";
  echo "<h3>".__("HOW TO PUT THE SLIDESHOW INTO A POST/PAGE ?", "nwp-slideshow")."</h3>";
  _e("Simply insert the following shortcode anywhere you want into the post : ", "nwp-slideshow");
  echo " <i>[".$slideshow['shortcode']."]</i>";
  echo "</div>";
}

function nwpSlideshowSettings() {

  /* Javascript Messages (hidden) */
  echo "<div class='jsmess' id='jsmess-reset'>".__("Confirm you want to reset slideshow's settings (do not alter slides)", "nwp-slideshow")."</div>";
  echo "<div class='jsmess' id='jsmess-delete'>".__("Confirm you want to delete this slide (data + image)", "nwp-slideshow")."</div>";

  echo "<div class='wrap'>";
  echo '<div id="icon-options-general" class="icon32"><br></div>';
  echo "<h2>NWP Slideshow Settings</h2> <br />";
  
  $section = "options";
  if (isset($_GET['section']))
    $section = $_GET['section'];

  $uri = explode("&", $_SERVER['REQUEST_URI']); 

  /* Display the menu */
  $menu = Array(
		__("OPTIONS", "nwp-slideshow") => "options",
		__("SLIDES (custom content)", "nwp-slideshow") => "slides",
	        __("ABOUT", "nwp-slideshow") => "about"
		);
  echo "<div id='nwpsMenu'>";
  foreach ($menu as $key => $value) {
    if ($value == $section)
      echo "<b>".$key."</b>";
    else
      echo "<a href='".$uri[0]."&section=".$value."'>".__($key, "nwp-slideshow")."</a>";
  }
  echo "</div>";

  /* Call page's function */
  $tocall = "nwps".ucwords($section)."Settings";
  echo "<div id='nwpsSettings'>";
  $tocall($uri);
  echo "</div>";

  /* Footer */
  echo "<div class='nwpFooter'>";
  echo "Franck Kosellek - ".__("Freelance web development / integration / SEO", "nwp-slideshow")."<br />";
  echo " <a href='http://www.nephila.fr'>www.nephila.fr</a> <span class='spacement'></span> <a href='mailto:franck@nephila.fr'>franck@nephila.fr</a> ";
  echo "</div>";

  echo "</div>"; // .wrap
 } ?>