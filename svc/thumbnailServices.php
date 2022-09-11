<?php
  include_once('config/db.php');
  include_once('config/config.php');
  
  function deleteThumbnail($playerID) {
    global $connection;
    $userProfile = getUserProfileSummaryArray($playerID);
    unlink($userProfile['thumbnailPath']);
    mysqli_query($connection, "update `UserProfile` set `ThumbnailPath` = '' where `PlayerID` = '{$playerID}'");
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
  }
  
  function getUserProfileSummaryArray($playerID) {
    global $connection;
    $result = mysqli_query($connection, "select `OriginalSavedPath`,`HOffset`,`VOffset`,`DisplayScale`,`OriginalScale`,`ThumbnailPath` from `UserProfile` where `PlayerID` = '{$playerID}'");
    while($row = mysqli_fetch_array($result)) {
      $originalSavedPath = $row['OriginalSavedPath'];
      $hOffset = $row['HOffset'];
      $vOffset = $row['VOffset'];
      $displayScale = $row['DisplayScale'];
      $originalScale = $row['OriginalScale'];
      $thumbnailPath = $row['ThumbnailPath'];
    }
    
    $ret = array();
    $ret['originalSavedPath'] = $originalSavedPath;
    $ret['hOffset'] = $hOffset;
    $ret['vOffset'] = $vOffset;
    $ret['displayScale'] = $displayScale;
    $ret['originalScale'] = $originalScale;
    $ret['thumbnailPath'] = $thumbnailPath;
    
    return $ret;
  }
  
  function deleteExistingImage($playerID) {
    global $connection;
    $userProfile = getUserProfileSummaryArray($playerID);
    unlink($userProfile['originalSavedPath']);
    deleteThumbnail($playerID);
    $smt = mysqli_prepare($connection, 'delete from `UserProfile` where `PlayerID` = ?');
    mysqli_stmt_bind_param($smt, 's', $playerID);
    if (!mysqli_stmt_execute($smt)){
      $sqlErr = mysqli_error($connection);
    }
    mysqli_stmt_close($smt);
  }
  
  function updateThumbnail($playerID, $userProfile) {
    global $thumbnailDim;
    unlink($userProfile['thumbnailPath']);
    $fileBytes = file_get_contents($userProfile['originalSavedPath']);
    $image = imagecreatefromstring($fileBytes);
    $width = imagesx($image);
    $height = imagesy($image);
    $simg = imagescale($image, $userProfile['displayScale'] * $width);
    $cimg = imagecrop($simg, ['x' => $userProfile['hOffset'], 'y' => $userProfile['vOffset'], 'width' => $thumbnailDim, 'height' => $thumbnailDim]);
    ob_start();
    imagepng($cimg);
    $imgAsString = ob_get_contents();
    ob_end_clean();
    $userProfile['thumbnailPath'] = getDestinationPath('.png');
    file_put_contents($userProfile['thumbnailPath'], $imgAsString);
    $thumbnailPath = $userProfile['thumbnailPath'];
    updateUserProfile($playerID, $userProfile);
  }
  
  function getDestinationPath($ext) {
    global $uploadsDir;
    $guid = GUID();
    return $uploadsDir.$guid.$ext;
  }

  function updateUserProfile($playerID, $userProfile) {
    global $connection;
    $smt = mysqli_prepare($connection, 'update `UserProfile` set `ThumbnailPath` = ?, `HOffset` = ?,`VOffset` = ?, `DisplayScale` = ? where `PlayerID` = ?');
    mysqli_stmt_bind_param($smt, 'siids', $userProfile['thumbnailPath'], $userProfile['hOffset'], $userProfile['vOffset'], $userProfile['displayScale'], $playerID);
    if (!mysqli_stmt_execute($smt)){
      $sqlErr = mysqli_error($connection);
    }
    mysqli_stmt_close($smt);
  }

?>