<?php
/*
Plugin Name: NWP Slideshow
Plugin URI: http://blog.nephila.fr/nwp-slideshow-plugin-wordpress-en
Description: Display and customize a nice and smooth slideshow on your WordPress website.
Version:1.0
Author:Franck Kosellek
Author URI: http://www.nephila.fr
Licence: GPL
*/

$nwpsVersion = "1.0";

register_activation_hook(__FILE__, 'install');
require_once(dirname(__FILE__)."/funcs-backend.php");

function install() {
  global $wpdb;
  
  /* Reset Slideshow Options */
  $nwpSlideshow = get_option("nwp_slideshow");
  if (!$nwpSlideshow)
    resetSlideshow();

  /* Create slides table   */
  if(!mysql_num_rows(mysql_query("SHOW TABLES LIKE 'nwp_slideshow'"))) {
    $q = "CREATE TABLE nwp_slideshow (id Int AUTO_INCREMENT, PRIMARY KEY(id), position Int, text Text, url Text, image Varchar(64), textposx1 Int, textposy1 Int, textposx2 Int, textposy2 Int)";
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
  global $nwpSlideshow;

  echo '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>';

  /* echo "<style type='text/css'>.wrap, #nav, #nav ul, #slide-holder, #slide-runner, #slide-controls { width:".$nwpSlideshow['sizewidth']."px; } </style>"; */

  echo "<style type='text/css'>";
  echo "#nwpsWindow { width:".$nwpSlideshow['sizewidth']."px; height:".$nwpSlideshow['sizeheight']."px; }";
  echo "#nwpsWindow .nwpsSlide { height:".$nwpSlideshow['sizeheight']."px; }";
  echo "#nwpsWindow .nwpsSlide { width:".$nwpSlideshow['sizewidth']."px; }";
  echo "</style>";

  echo '<script type="text/javascript">';
  echo 'var width = '.$nwpSlideshow['sizewidth'].';';
  echo 'var interval = '.$nwpSlideshow['interval'].';';
  echo 'var movement = "'.$nwpSlideshow['movement'].'";';
  echo 'var speed = '.$nwpSlideshow['speed'].';';
  echo 'var autoslide = "'.$nwpSlideshow['autoslide'].'";';
  echo '</script>';

  echo '<script src="' .plugins_url('js/frontend.js', __FILE__). '" type="text/javascript"></script>';
  echo '<link rel="stylesheet" type="text/css" href="' .plugins_url('css/frontend.css', __FILE__). '">';
}

if (is_admin()) {

  /* Add NWP Slideshow submenu */
  add_action('admin_menu', 'nwpSlideshowMenu');
  add_action("admin_head", "nwpAdminHead");

  $plugin_dir = basename(dirname(__FILE__));
  load_plugin_textdomain("nwp-slideshow", false, $plugin_dir);

  /* Check if '/imagesslides' directory is writable. */
  if (!is_writable(dirname(__FILE__)."/images/slides") || !is_executable(dirname(__FILE__)."/images/slides"))  {
    echo '<div class="error">'.__("<p>Warning - The <b>/images/slides directory</b> is not writable by the server. Please, change directory permission.</p>", "nwp-slideshow").'</div>';
    $unwritable = __("<b class='red'>To upload/edit an image or add a slide, change /images/slides directory permissions</b>", "nwp-slideshow");
  }


  require_once(dirname(__FILE__)."/slideshow-backend.php");



} else {
  add_action("wp_head", "nwpHead");

  $nwpSlideshow = get_option("nwp_slideshow");
  if (is_string($nwpSlideshow))
    $nwpSlideshow = unserialize($nwpSlideshow);

  require_once(dirname(__FILE__)."/slideshow-frontend.php");
  add_shortcode( $nwpSlideshow['shortcode'], 'displaySlideshow' );
}

?>