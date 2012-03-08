<?php


function secureArray($arr) {

  foreach ($arr as $key => $value)
    $arr[$key] = addslashes($value);

  return $arr;
}



function editSlideshow($_POST, $_FILES) {
  update_option("nwp_slideshow", serialize($_POST));
  if (isset($_FILES['onbuttonimg']))
    move_uploaded_file($_FILES['onbuttonimg']['tmp_name'], dirname(__FILE__).'/images/slides/onbutton.png');
  if (isset($_FILES['offbuttonimg']))
    move_uploaded_file($_FILES['offbuttonimg']['tmp_name'], dirname(__FILE__).'/images/slides/offbutton.png');

}


function resetSlideshow() {
  $array = Array(
		 "shortcode" => "nwp-slideshow",
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
  update_option("nwp_slideshow", serialize($array));
  @copy(dirname(__FILE__)."/images/sources/onbutton.png", dirname(__FILE__)."/images/slides/onbutton.png");
  @copy(dirname(__FILE__)."/images/sources/offbutton.png", dirname(__FILE__)."/images/slides/offbutton.png");
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