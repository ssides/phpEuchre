<?php
  include_once('config/db.php'); 
  include_once('config/config.php'); 
  include_once('svc/thumbnailServices.php'); 

  echo 'playerID: '.$_COOKIE[$cookieName].'<br>';
  
  $ret = getUserProfileSummaryArray($_COOKIE[$cookieName]);
  
  echo 'thumbnailPath: '.$ret['thumbnailPath'].'<br>';
  echo 'displayScale: '.$ret['displayScale'].'<br>';
  
  $ret = changeSomething($_COOKIE[$cookieName]);
  echo 'displayScale: '.$ret['displayScale'].'<br>';
  
  function changeSomething($playerID) {
    $ret = getUserProfileSummaryArray($playerID);
    $a = $ret['displayScale'] * 0.05;
    $ret['displayScale'] += $a;
    return $ret;
  }
?>
