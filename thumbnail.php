<?php
  include_once('config/db.php');
  include_once('config/config.php');
  include_once('controllers/isAuthenticated.php'); // for readAuthCookie()
  include('svc/thumbnailServices.php');

  if (!empty($_COOKIE[$cookieName])) {
    readAuthCookie();
    $userProfile = getUserProfileSummaryArray($$a['r']);
    $img = imagecreatefrompng($userProfile['thumbnailPath']);
    header("Content-Type: image/png");
    imagepng($img);
    imagedestroy($img);
  }
?>