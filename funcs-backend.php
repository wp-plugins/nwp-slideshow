<?php


function secureArray($arr) {

  foreach ($arr as $key => $value)
    $arr[$key] = addslashes($value);

  return $arr;
}

function toAscii($str) {
  $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
  $clean = strtolower(trim($clean, '-'));
  $clean = preg_replace("/[\/_|+ -]+/", '-', $clean);

  return $clean;
}

function getSlideshowName($id) {
  $slideshows = get_option('nwp_slideshows_list');

  if (is_string($slideshows))
    $slideshows = unserialize($slideshows);

  for ($i = 0; isset($slideshows[$i]['label']); $i++)
    if ($slideshows[$i]['id'] == $id)
      return $slideshows[$i]['label'];

}

function editOptions($_POST) {
  update_option("nwps_options", serialize($_POST));
}

function delSlideshow($_POST) {
  global $wpdb;

  $id = $_POST['id'];

  if ($id == 1)
    return ;

  /* Delete slides */
  $q = "DELETE FROM nwp_slideshow WHERE slideshow='".$_POST."'";
  $wpdb->query($q);

  /* Delete slideshow */
  delete_option('nwps_'.$id);

  /* Delete slideshow from list */
  $slideshows = get_option('nwp_slideshows_list');
  if (is_string($slideshows))
    $slideshows = unserialize($slideshows);
  for ($i = 0; isset($slideshows[$i]); $i++) {
    if ($slideshows[$i]['id'] == $id)
      $row = $i;
  }
  for ($j = $row; isset($slideshows[$j+1]['id']); $j++)
    $slideshows[$j] = $slideshows[$j+1];
  unset($slideshows[$j]);
  update_option("nwp_slideshows_list", serialize($slideshows));

  /* Change current slideshow */
  update_option("curSlideshow", "1");
}

function addSlideshow($_POST) {
  global $wpdb;

  /* Edit Slideshows list */
  $slideshows = get_option('nwp_slideshows_list');

  $_POST = secureArray($_POST);

  if (is_string($slideshows))
    $slideshows = unserialize($slideshows);
  
  $label = $_POST['label'];
  if ($_POST['id'] == '1')
    $id = '1';
  else
    $id = date("U");

  $slideshows[] = Array("label" => $label, "id" => $id);

  $slideshows = serialize($slideshows);
  update_option("nwp_slideshows_list", $slideshows);

  /* Add Slideshow options */
  $array = Array(
		 "interval" => "10",
		 "sizewidth" => "950",
		 "sizeheight" => "400",
		 "buttonposx" => "20",
		 "buttonposy" => "20",
		 "postsnbr" => "5",
		 "category" => "1",
		 "speed" => "1000",
		 "autoslide" => "on",
		 "movement" => "movesliding",
		 "type" => "custom"
		 );
  update_option("nwps_".$id, serialize($array));
  update_option("curSlideshow", $id);
  @copy(dirname(__FILE__)."/images/sources/onbutton.png", dirname(__FILE__)."/images/slides/onbutton-".$id.".png");
  @copy(dirname(__FILE__)."/images/sources/offbutton.png", dirname(__FILE__)."/images/slides/offbutton-".$id.".png");
}

function changeSlideshow($_POST) {
  update_option('curSlideshow', $_POST['changeSlideshow']);
}

function editSlideshow($_POST, $_FILES) {
  update_option("nwps_".get_option('curSlideshow'), serialize($_POST));
  if (isset($_FILES['onbuttonimg']))
    move_uploaded_file($_FILES['onbuttonimg']['tmp_name'], dirname(__FILE__).'/images/slides/onbutton-'.get_option('curSlideshow').'.png');
  if (isset($_FILES['offbuttonimg']))
    move_uploaded_file($_FILES['offbuttonimg']['tmp_name'], dirname(__FILE__).'/images/slides/offbutton-'.get_option('curSlideshow').'.png');
}

function deleteSlide($_POST) {
  global $wpdb;

  $q = "DELETE FROM nwp_slideshow WHERE id='".$_POST['id']."'";

  $wpdb->query($q);
  @unlink(dirname(__FILE__)."/images/slides/".$_POST['curImage']);
}

function addSlide($_POST, $_FILES) {
  global $wpdb;

  $_POST = secureArray($_POST);

  $image = '';
  if (isset($_FILES['image'])) {
    move_uploaded_file($_FILES['image']['tmp_name'], dirname(__FILE__).'/images/slides/'.$_FILES['image']['name']);
    $image = $_FILES['image']['name'];
  }  

  $q = "INSERT INTO nwp_slideshow VALUES(";
  $q .= "'', "; // ID
  $q .= "'".get_option('curSlideshow')."', " ;
  $q .= "'".$_POST['position']."', "; // POSITION
  $q .= "'', "; // TEXT
  $q .= "'".$_POST['url']."', "; // URL
  $q .= "'".$image."', "; // IMAGE
  $q .= "'0', "; // TEXTPOSX1
  $q .= "'0', "; // TEXTPOSy1
  $q .= "'50', "; // TEXTPOSX2
  $q .= "'50' "; // TEXTPOSy2
  $q .= ")";

  $wpdb->query($q);
}

function editSlide($_POST, $_FILES) {

  global $wpdb;

  $_POST = secureArray($_POST);

  $q = "UPDATE nwp_slideshow SET ";
  $q .= " position='".$_POST['position']."', ";
  $q .= " text='".$_POST['text']."', ";
  $q .= " url='".$_POST['url']."', ";
  if (isset($_FILES['image']) && $_FILES['image']['error'] ==0) {
    @unlink(dirname(__FILE__)."/images/slides/".$_POST['curImage']);
    move_uploaded_file($_FILES['image']['tmp_name'], dirname(__FILE__).'/images/slides/'.$_FILES['image']['name']);
    $q .= " image='".$_FILES['image']['name']."', ";
  }
  $q .= " textposx1='".$_POST['textposx1']."', ";
  $q .= " textposy1='".$_POST['textposy1']."', ";
  $q .= " textposx2='".$_POST['textposx2']."', ";
  $q .= " textposy2='".$_POST['textposy2']."' ";
  $q .= "WHERE id='".$_POST['id']."'";

  
  $wpdb->query($q);
}


?>