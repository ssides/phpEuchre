<?php
  // make sure there is a final path separator in paths.
  $appUrl = 'http://localhost:8080/';
  $reportsUrl = $appUrl.'reports/';
  $cookieName = 'sidesLocalEuchre1';
  $thumbnailDim = 25;
  $positionDistance = 3;
  // the images folder must be called images and it must be in root.
  $uploadsDir = 'C:/src/phpEuchre/images/';
  $cardImageWidth = 50; // px
  $cardImageHeight = 70; // px
  $firstJackChoices = 500;
  $dealChoices = 12000;
  $gameControllerLog = true;
  $clearTableDelay = 2;
  $version = '1.7:10'; // displayed in the header. change in version forces the browser to get the latest styles.
                       // M.m:s   M - Major version number, m - minor version number, s - styles version number.
  $$a = array();
?>