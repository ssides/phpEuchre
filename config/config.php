<?php
  // make sure there is a final path separator in paths.
  $appUrl = 'http://192.168.254.148:8080/';
  $reportsUrl = $appUrl.'reports/';
  $cookieName = 'sidesLocalEuchre1';
  $thumbnailDim = 25;
  $positionDistance = 3;
  // the images folder must be called images and it must be in root.
  $uploadsDir = 'C:/src/phpEuchre/images/';
  $audioDir = 'audio/';
  $cardFaces = "1"; // can be any number 1 to 5.  Selects different card faces.
  $firstJackChoices = 500;
  $dealChoices = 12000;
  $gameControllerLog = true;
  $clearTableDelay = 2;
  $version = '1.7:76'; // displayed in the header. change in version forces the browser to get the latest styles.
                       // M.m:s   M - Major version number, m - minor version number, s - styles version number.
  $$a = array();
?>