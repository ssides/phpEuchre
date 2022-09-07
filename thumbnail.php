<?php
  include_once('config/db.php');
  include_once('config/config.php');
  include_once('svc/thumbnailServices.php');
  
  $userProfile = getUserProfileSummaryArray($_COOKIE[$cookieName]);
  $img = imagecreatefrompng($userProfile['thumbnailPath']);
  header("Content-Type: image/png");
  imagepng($img);
  imagedestroy($img);
?>