<?php
  // this is called from an api, so paths are relative to the api.
  // depends on: include_once('../config/config.php');
  // include_once('config/config.php');
  // converts this: C:/src/phpEuchre/images/E26DF607-3160-409B-B886-DF87ACF06306.png
  // to this:       http://localhost:8080/images/E26DF607-3160-409B-B886-DF87ACF06306.png
  // trusting that the thumbnail path includes an "images" folder in root.
  function getThumbnailURL($thumbnailPath) {
    global $appUrl;
    
    return $appUrl.substr($thumbnailPath, strpos($thumbnailPath,'images'));
  }

?>