<?php
/*
Plugin Name: NWP Slideshow
Plugin URI: http://blog.nephila.fr/nwp-slideshow-plugin-wordpress-en
Description: Display and customize a nice and smooth slideshow on your WordPress website.
Version:1.0.1
Author:Franck Kosellek
Author URI: http://www.nephila.fr
Licence: GPL
*/

$nwpsVersion = "1.1.1";

register_activation_hook(__FILE__, 'install');
require_once(dirname(__FILE__)."/funcs-backend.php");

function install() {
  global $wpdb;
  
  /* Add first slideshow */
  addSlideshow(array("label" => "Default", "id" => "1"));

  /* Create slides table   */
  if(!mysql_num_rows(mysql_query("SHOW TABLES LIKE 'nwp_slideshow'"))) {
    $q = "CREATE TABLE nwp_slideshow (id Int AUTO_INCREMENT, PRIMARY KEY(id), slideshow Int, position Int, text Text, url Text, image Varchar(64), textposx1 Int, textposy1 Int, textposx2 Int, textposy2 Int)";
    $wpdb->query($q) or die(mysql_error());
  }
}

function nwpSlideshowMenu() {
  add_options_page('NWP Slideshow Settings', 'NWP Slideshow', 'manage_options', 'nwp-slideshow', 'nwpSlideshowSettings');
}


function nwpAdminHead() {
  echo '<script src="' .plugins_url('js/backend.js', __FILE__). '" type="text/javascript"></script>';
  echo '<link rel="stylesheet" type="text/css" href="' .plugins_url('css/backend.css', __FILE__). '">';

  /* ImgAreaSelect jQuery Plugin */
  echo '<link rel="stylesheet" type="text/css" href="' .plugins_url('css/imgareaselect-default.css', __FILE__). '">';
  echo '<script src="' .plugins_url('js/jquery.imgareaselect.min.js', __FILE__). '" type="text/javascript"></script>';
}

function nwpHead() {

  $slideshows = get_option('nwp_slideshows_list');
  if (is_string($slideshows))
    $slideshows = unserialize($slideshows);

  echo '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>';

  /* echo "<style type='text/css'>.wrap, #nav, #nav ul, #slide-holder, #slide-runner, #slide-controls { width:".$nwpSlideshow['sizewidth']."px; } </style>"; */

  for ($i = 0; isset($slideshows[$i]['id']); $i++) {
    $nwpSlideshow = get_option("nwps_".$slideshows[$i]['id']);
    if (is_string($nwpSlideshow))
      $nwpSlideshow = unserialize($nwpSlideshow);
    echo "<style type='text/css'>\n";
    echo "#nwpsWindow-".$slideshows[$i]['id']." { width:".$nwpSlideshow['sizewidth']."px; height:".$nwpSlideshow['sizeheight']."px; }\n";
    echo "#nwpsWindow-".$slideshows[$i]['id']." .nwpsSlide { height:".$nwpSlideshow['sizeheight']."px; }\n";
    echo "#nwpsWindow-".$slideshows[$i]['id']." .nwpsSlide { width:".$nwpSlideshow['sizewidth']."px; }\n";
    echo "</style>\n";
  }

  echo '<script src="' .plugins_url('js/frontend.js', __FILE__). '" type="text/javascript"></script>';
  echo '<link rel="stylesheet" type="text/css" href="' .plugins_url('css/frontend.css', __FILE__). '">';
}

if (is_admin()) {
  /* Set current slideshow */
  if (get_option('curSlideshow') == '')
    update_option("curSlideshow", "1");

  /* Add NWP Slideshow submenu */
  add_action('admin_menu', 'nwpSlideshowMenu');
  add_action("admin_head", "nwpAdminHead");

  $plugin_dir = basename(dirname(__FILE__));
  load_plugin_textdomain("nwp-slideshow", false, $plugin_dir);

  /* Check if '/imagesslides' directory is writable. */
  if (!is_writable(dirname(__FILE__)."/images/slides") || !is_executable(dirname(__FILE__)."/images/slides"))  {
    $warning = '<div class="error">'.__("<p>Warning - The <b>/images/slides directory</b> is not writable by the server. Please, change directory permission.</p>", "nwp-slideshow").'</div>';
    $unwritable = __("<b class='red'>To upload/edit an image or add a slide, change /images/slides directory permissions</b>", "nwp-slideshow");
  } else {
    /* Check if on/off button are available for slideshow 1 */
    if (!file_exists(dirname(__FILE__).'/images/slides/onbutton-1.png')) {
      @copy(dirname(__FILE__)."/images/sources/onbutton.png", dirname(__FILE__)."/images/slides/onbutton-1.png");
      @copy(dirname(__FILE__)."/images/sources/offbutton.png", dirname(__FILE__)."/images/slides/offbutton-1.png");
    }
  }

  require_once(dirname(__FILE__)."/slideshow-backend.php");

} else {
  add_action("wp_head", "nwpHead");
  require_once(dirname(__FILE__)."/slideshow-frontend.php");
  add_shortcode( "nwp-slideshow", 'displaySlideshow' );
}

?>