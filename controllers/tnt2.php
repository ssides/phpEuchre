<?php 
    include_once('config/db.php');
    include_once('config/config.php');

    header("Content-Type: image/jpeg");
    $img = imagecreatefromstring(getOriginalImageValue($_COOKIE[$cookieName]));
    imagepng($img);
    imagedestroy($img);
    
    function getOriginalImageValue($playerID) {
      global $connection;
      $qry = mysqli_query($connection, "select `OriginalImage` from `UserProfile` where `PlayerID` = '{$playerID}'");
      while($row = mysqli_fetch_array($qry)){
        $i = $row['OriginalImage'];
      }
      return $i;
    }

?>