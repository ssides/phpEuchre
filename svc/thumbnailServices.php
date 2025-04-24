<?php

  function getUserProfileSummary($conn, $playerID) {
    $ret = [];
    $smt = mysqli_prepare($conn, "select `OriginalSavedPath`,`HOffset`,`VOffset`,`DisplayScale`,`OriginalScale`,`ThumbnailPath` from `UserProfile` where `PlayerID` = ?");
    mysqli_stmt_bind_param($smt, 's', $playerID);
    mysqli_stmt_execute($smt);
    $result = mysqli_stmt_get_result($smt);
    while($row = mysqli_fetch_assoc($result)) {
      $ret['originalSavedPath'] = $row['OriginalSavedPath'];
      $ret['hOffset'] = $row['HOffset'];
      $ret['vOffset'] = $row['VOffset'];
      $ret['displayScale'] = $row['DisplayScale'];
      $ret['originalScale'] = $row['OriginalScale'];
      $ret['thumbnailPath'] = $row['ThumbnailPath'];
    }
    
    return $ret;
  }

  function getThumbnailAsString($img, $scale, $width, $height) {
    $simg = imagescale($img, $scale * $width);
    ob_start();
    imagepng($simg);
    $imgAsString = ob_get_contents();
    ob_end_clean();
    return $imgAsString;
  }
  
  function getScale($width, $height) {
    global $thumbnailDim;
    if ($width / $height < 1) {
      return $thumbnailDim / $width;
    } else {
      return $thumbnailDim / $height;
    }
  }
  
  function deleteThumbnail($conn, $playerID) {
    $userProfile = getUserProfileSummary($conn, $playerID);
    unlink($userProfile['thumbnailPath']);
    $stmt = mysqli_prepare($conn, "update `UserProfile` set `ThumbnailPath` = '' where `PlayerID` = ?");
    mysqli_stmt_bind_param($stmt, "s", $playerID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
  }
  
  function deleteExistingImage($conn, $playerID) {
    $userProfile = getUserProfileSummary($conn, $playerID);
    if (!empty($userProfile['originalSavedPath'])) {
      unlink($userProfile['originalSavedPath']);
      deleteThumbnail($conn, $playerID);
      $smt = mysqli_prepare($conn, 'delete from `UserProfile` where `PlayerID` = ?');
      mysqli_stmt_bind_param($smt, 's', $playerID);
      mysqli_stmt_execute($smt);
      mysqli_stmt_close($smt);
    }
  }

  function getDestinationPath($ext) {
    global $uploadsDir;
    $guid = GUID();
    return $uploadsDir.$guid.$ext;
  }

  function updateThumbnail($conn, $playerID, $userProfile) {
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
    updateUserProfile($conn, $playerID, $userProfile);
  }
  
  function updateUserProfile($conn, $playerID, $userProfile) {
    $smt = mysqli_prepare($conn, 'update `UserProfile` set `ThumbnailPath` = ?, `HOffset` = ?,`VOffset` = ?, `DisplayScale` = ? where `PlayerID` = ?');
    mysqli_stmt_bind_param($smt, 'siids', $userProfile['thumbnailPath'], $userProfile['hOffset'], $userProfile['vOffset'], $userProfile['displayScale'], $playerID);
    mysqli_stmt_execute($smt);
    mysqli_stmt_close($smt);
  }

?>