<?php
  include_once('config/db.php');
  include_once('models/UserProfileSummary.php');
  
  function getThumbnailPath($playerID) {
    global $connection;
    $result = mysqli_query($connection, "select `ThumbnailPath`  from `UserProfile` where `PlayerID` = '{$playerID}'");
    while($row = mysqli_fetch_array($result)) {
      $p = $row['ThumbnailPath'];
    }
    return $p;
  }

  function deleteThumbnail($playerID) {
    global $connection;
    $tnpath = getThumbnailPath($playerID);
    unlink($tnpath);
    $result = mysqli_query($connection, "update `UserProfile` set `ThumbnailPath` = '' where `PlayerID` = '{$playerID}'");
  }

  function getUserProfileSummary($playerID, $smry) {
    global $connection;
    $result = mysqli_query($connection, "select `OriginalSavedPath`,`HOffset`,`VOffset`,`DisplayScale`,`OriginalScale`,`ThumbnailPath` from `UserProfile` where `PlayerID` = '{$playerID}'");
    while($row = mysqli_fetch_array($result)) {
      $smry->originalSavedPath = $row['OriginalSavedPath'];
      $smry->hOffset = $row['HOffset'];
      $smry->vOffset = $row['VOffset'];
      $smry->displayScale = $row['DisplayScale'];
      $smry->originalScale = $row['OriginalScale'];
      $smry->thumbnailPath = $row['ThumbnailPath'];
    }
    // $smry->$fileBytes = file_get_contents($smry->originalSavedPath);
    // $smry->$image = imagecreatefromstring($smry->$fileBytes);
    // list($width, $height, $_gettype, $_getattr) = getimagesize($smry->originalSavedPath);
    // $smry->originalWidth = $width;
    // $smry->originalHeight = $height;
  }
  
  function deleteExistingImage($playerID) {
    global $connection;
    $smry = new UserProfileSummary();
    getUserProfileSummary($playerID, $smry);
    unlink($smry->originalSavedPath);
    deleteThumbnail($playerID);
    $smt = mysqli_prepare($connection, 'delete from `UserProfile` where `PlayerID` = ?');
    mysqli_stmt_bind_param($smt, 's', $playerID);
    if (!mysqli_stmt_execute($smt)){
      $sqlErr = mysqli_error($connection);
    }
    mysqli_stmt_close($smt);
  }

?>