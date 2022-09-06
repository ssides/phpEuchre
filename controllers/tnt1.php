<?php 
  $img = imagecreatefromjpeg('srs.jpg');
  header("Content-Type: image/png");
  imagepng($img);
  imagedestroy($img);
?>