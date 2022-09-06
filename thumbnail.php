<?php
  include_once('config/db.php');
  include_once('config/config.php');
  include_once('svc/thumbnailServices.php');
  
  $img = imagecreatefrompng(getThumbnailPath($_COOKIE[$cookieName]));
  header("Content-Type: image/png");
  imagepng($img);
  imagedestroy($img);
  
?>