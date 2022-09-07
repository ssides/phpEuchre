<?php 
    include_once('config/db.php');
    include_once('config/config.php');
    include_once('svc/GUID.php');
    include_once('svc/thumbnailServices.php');
    
    if (empty($_COOKIE[$cookieName])) {
      header('Location: index.php');
    } else {
      $userProfile = getUserProfileSummaryArray($_COOKIE[$cookieName]);
      $thumbnailPath = $userProfile['thumbnailPath'];
      if(isset($_POST['zoomin'])) {
        $userProfile = getUserProfileSummaryArray($_COOKIE[$cookieName]);
        $fivepct = $userProfile['displayScale'] * 0.05;
        $userProfile['displayScale'] += $fivepct;
        updateThumbnail($_COOKIE[$cookieName], $userProfile);
      } else if(isset($_POST['right'])) {
        $userProfile = getUserProfileSummaryArray($_COOKIE[$cookieName]);
        $userProfile['hOffset'] -= $positionDistance;
        updateThumbnail($_COOKIE[$cookieName], $userProfile);
      } else if(isset($_POST['left'])) {
        $userProfile = getUserProfileSummaryArray($_COOKIE[$cookieName]);
        $userProfile['hOffset'] += $positionDistance;
        updateThumbnail($_COOKIE[$cookieName], $userProfile);
      } else if(isset($_POST['up'])) {
        $userProfile = getUserProfileSummaryArray($_COOKIE[$cookieName]);
        $userProfile['vOffset'] += $positionDistance;
        updateThumbnail($_COOKIE[$cookieName], $userProfile);
      } else if(isset($_POST['down'])) {
        $userProfile = getUserProfileSummaryArray($_COOKIE[$cookieName]);
        $userProfile['vOffset'] -= $positionDistance;
        updateThumbnail($_COOKIE[$cookieName], $userProfile);
      } else if(isset($_POST['zoomout'])) {
        $userProfile = getUserProfileSummaryArray($_COOKIE[$cookieName]);
        $fivepct = $userProfile['displayScale'] * 0.05;
        $userProfile['displayScale'] -= $fivepct;
        updateThumbnail($_COOKIE[$cookieName], $userProfile);
      }
    }

?>