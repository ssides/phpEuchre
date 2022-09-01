<?php 
    include_once('config/db.php');
    include_once('config/config.php');
    
    if (empty($_COOKIE[$cookieName])) {
      header('Location: index.php');
    } else {
      $smt = mysqli_prepare($connection, 'select `Thumbnail` from `UserProfile` where `PlayerID` = ?');
      mysqli_stmt_bind_param($smt, 's', $_COOKIE[$cookieName]);
      if (!mysqli_stmt_execute($smt)){
        $sqlErr2 = mysqli_error($connection);
      } else {
        mysqli_stmt_bind_result($smt, $th);
        mysqli_stmt_fetch($smt);
        if (!empty($th)) {
          imagepng(imagecreatefromstring($th));
        } else {
          imagepng(imagecreatetruecolor($thumbnailDim,$thumbnailDim));
        }
      }
      mysqli_stmt_close($smt);
    }
?>