<?php




function getSlides_last($nwpSlideshow, $id) {
  $args = Array(
		"numberposts" => $nwpSlideshow['postsnbr'],
		"orderby" => "post_date",
		"order" => "DESC",
		"post_status" => "publish",
		"post_type" => "post"
		);
  if ($nwpSlideshow['type'] == "category") {
    $args['category'] = $nwpSlideshow['category'];
  }
  $posts = get_posts($args);

  /* print_r($posts); */
  for ($i = 0; isset($posts[$i]); $i++) {
    $text = "<div class='lastPostText'>".$posts[$i]->post_title."</div>";
    $res[$i]['url'] = $posts[$i]->guid;
    if( has_post_thumbnail($posts[$i]->ID) ) {
      $image = wp_get_attachment_image_src( get_post_thumbnail_id( $posts[$i]->ID ), 'single-post-thumbnail' );
      $res[$i]['image'] = $image[0];      
    } else
      $res[$i]['image'] = plugins_url("/images/noimage.png", __FILE__);
    $res[$i]['text'] = $text;
    $res[$i]['textposx1'] = 0;
    $res[$i]['textposx2'] = $nwpSlideshow['sizewidth'];
    $res[$i]['textposy1'] = $nwpSlideshow['sizeheight']-50;
    $res[$i]['textposy2'] = 0;
  }

  /* print_r($posts); */

  return $res;
}

function getSlides_category($nwpSlideshow, $id) {
  return getSlides_last($nwpSlideshow, $id);
}

function getSlides_custom($nwpSlideshow, $id) {
  global $wpdb;
  $q = "SELECT * FROM nwp_slideshow WHERE slideshow='".$id."' ORDER BY position ASC";
  $slides = $wpdb->get_results($q);

  $uri = explode("&", $_SERVER['REQUEST_URI']);
  for ($i = 0; isset($slides[$i]); $i++) {
    $res[$i]['url'] = $slides[$i]->url;
    $res[$i]['image'] = plugins_url("/images/slides/".$slides[$i]->image, __FILE__);
    $res[$i]['text'] = $slides[$i]->text;
    $res[$i]['textposx1'] = $slides[$i]->textposx1;
    $res[$i]['textposx2'] = $slides[$i]->textposx2;
    $res[$i]['textposy1'] = $slides[$i]->textposy1;
    $res[$i]['textposy2'] = $slides[$i]->textposy2;
  }

  return $res;
}

function displaySlideshow($param) {

  $id = $param['id'];

  $nwpSlideshow = get_option("nwps_".$id);
  if (is_string($nwpSlideshow))
    $nwpSlideshow = unserialize($nwpSlideshow);

  /* Insert JS variables */
  echo '<script type="text/javascript">';
  echo 'var width = '.$nwpSlideshow['sizewidth'].';';
  echo 'var interval = '.$nwpSlideshow['interval'].';';
  echo 'var movement = "'.$nwpSlideshow['movement'].'";';
  echo 'var speed = '.$nwpSlideshow['speed'].';';
  echo 'var autoslide = "'.$nwpSlideshow['autoslide'].'";';
  echo '</script>';


  /* What is the type of the Slideshow ? */
  $getSlides = "getSlides_".$nwpSlideshow['type'];
  $slides = $getSlides($nwpSlideshow, $id);


  $s = '';

  $s .= '<div id="nwpsWindow-'.$id.'" class="nwpsWindow">';

  $float = 0;
  for ($i = 0; isset($slides[$i]); $i++) {
    $width = $slides[$i]['textposx2'] - $slides[$i]['textposx1'];
    $height = $slides[$i]['textposy2'] - $slides[$i]['textposy1'];
    $top = $slides[$i]['textposy1'];
    $left = $slides[$i]['textposx1'];
    /* $style = "left:".$float."px; padding-left:".$left."px;  top:".$top."px; width:".$width."px; height:".$height."px;"; */
    $style = "left:".$left."px;  top:".$top."px; width:".$width."px; height:".$height."px;";
    if (!empty($slides[$i]['url']))
      $s .= '<a href="'.$slides[$i]['url'].'" ';
    else
      $s .= '<div ';
    $s .= 'class="nwpsSlide" id="nwpsSlide-'.($i+1).'" style="left:'.$float.'px;" >';
    $s .= '<img id="nwpsImg-'.($i+1).'" class="nwpsImg" src="'.$slides[$i]['image'].'" alt="slide image"  >';
    $s .= '<div id="nwpsText-'.($i+1).'" class="nwpsText" style="'.$style.' ">'.str_replace("\\", "", $slides[$i]['text']).'</div>'; 
    if (!empty($slides[$i]['url']))
      $s .= '</a>';
    else
      $s .= '</div>';
    $float += $nwpSlideshow['sizewidth'];
  }


  /* Controls */
  $s .= '<p id="nwpsControls" style="left:'.$nwpSlideshow['buttonposx'].'px; top:'.$nwpSlideshow['buttonposy'].'px;">';
  for ($i = 0; isset($slides[$i]); $i++) {
    $s .= '<img src="'.plugins_url("/images/slides/offbutton.png", __FILE__).'" class="nwpsNav" id="nwpsNav-'.($i+1).'" />';
  }
  $s .= '</p>';

  $s .= '</div>'; // #nwpsWindow


  return $s;
  
}





?>