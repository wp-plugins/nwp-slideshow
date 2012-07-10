<?php

function formatSlideText($str) {
  $str = str_replace("\\", "", $str);
  $str = str_replace("\n", "<br />", $str);

  return $str;
}

?>