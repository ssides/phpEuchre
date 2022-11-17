<?php
  // no includes for now until I set it up to use $_SERVER['DOCUMENT_ROOT']

  function GUID()
  {
    if (function_exists('com_create_guid') === true)
    {
      return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
  }

  function cutoffDate() {
    $now = new DateTime(date("Y-m-d"));
    $now->sub(new DateInterval('P3D'));
    return $now->format('Y-m-d');
  }
  
?>