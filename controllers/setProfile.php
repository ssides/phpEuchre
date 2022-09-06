<?php  
    include_once('config/db.php');
    include_once('config/config.php');
    include_once('svc/GUID.php');
    include_once('svc/thumbnailServices.php');
    
    if (empty($_COOKIE[$cookieName])) {
      header('Location: index.php');
    } else {
      $thumbnailPath = getThumbnailPath($_COOKIE[$cookieName]);
      if(isset($_POST['upload'])) {
        if ($_FILES['profileImage']['error'] == 0) {
          list($width, $height, $_gettype, $_getattr) = getimagesize($_FILES['profileImage']['tmp_name']);
          $size = $_FILES['profileImage']['size'];
          
          if ($width == 0 || $height == 0 || $size == 0) {
            $errorMsg = 'Could not get the dimensions of the image.';
          } else {
            $fileBytes = file_get_contents($_FILES['profileImage']['tmp_name']);
            $destPath = getDestinationPath();
            if (move_uploaded_file($_FILES['profileImage']['tmp_name'], $destPath) !== true) {
              $errorMsg = 'Could not save the uploaded image.';
            } else {
              $img = imagecreatefromstring($fileBytes);
              if ($img === false) {
                $errorMsg = 'Could not create the thumbnail.';
              } else {
                
                $scale = getScale($width, $height);
                $th = getThumbnailAsString($img, $scale, $width, $height);
                $thumbnailPath = getDestinationPath();
                file_put_contents($thumbnailPath, $th);
                
                $contentType = $_FILES['profileImage']['type'];
                
                $hofs = $vofs = 0;
                deleteExistingImage($_COOKIE[$cookieName]);
                $smt = mysqli_prepare($connection, 'insert into `UserProfile` (`ID` , `PlayerID` ,`OriginalName` , `OriginalSavedPath`,`OriginalContentType` ,`ThumbnailPath`, `OriginalFileSize`, `HOffset`,`VOffset`,`OriginalScale`,`DisplayScale`,`InsertDate` ) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())');
                mysqli_stmt_bind_param($smt, 'ssssssiiidd', GUID(), $_COOKIE[$cookieName], $_FILES['profileImage']['name'], $destPath, $contentType, $thumbnailPath, $size, $hofs, $vofs, $scale, $scale);
                if (!mysqli_stmt_execute($smt)){
                  $sqlErr = mysqli_error($connection);
                }
                mysqli_stmt_close($smt);
              }
            }
          }
        } else {
          $errorMsg = 'File could not be uploaded.';
        }
      } else if(isset($_POST['change'])) {
        deleteExistingImage($_COOKIE[$cookieName]);
        $thumbnailPath = '';
      } else if(isset($_POST['zoomin'])) {
        $playerID = $_COOKIE[$cookieName];
        $smry = new UserProfileSummary();
        getUserProfileSummary($playerID, $smry);
        unlink($smry->thumbnailPath);
        
        $fileBytes = file_get_contents($smry->originalSavedPath);
        $image = imagecreatefromstring($fileBytes);
        list($width, $height, $_gettype, $_getattr) = getimagesize($smry->originalSavedPath);

        $fivepct = $smry->displayScale * 0.1;
        $smry->displayScale = ($smry->displayScale + $fivepct);
        
        $errorMsg = 's: '.$smry->displayScale.' '.$fivepct.' '.(($image === false) ? ' no image' : ' got image');
        $th = getThumbnailAsString($image, $smry->displayScale, $width, $height);
        $thumbnailPath = getDestinationPath();
        file_put_contents($thumbnailPath, $th);
        updateUserProfile($playerID, $thumbnailPath, $smry);
      }
    }
    
    function updateUserProfile($playerID, $thumbnailPath, $smry) {
      global $connection;
      $smt = mysqli_prepare($connection, 'update `UserProfile` set `ThumbnailPath` = ?, `HOffset` = ?,`VOffset` = ?, `DisplayScale` = ? where `PlayerID` = ?');
      mysqli_stmt_bind_param($smt, 'siids', $thumbnailPath, $smry->hOffset, $smry->vOffset, $smry->displayScale, $playerID);
      if (!mysqli_stmt_execute($smt)){
        $sqlErr = mysqli_error($connection);
      }
      mysqli_stmt_close($smt);
    }
    
    function getDestinationPath() {
      global $uploadsDir;
      $guid = GUID();
      return $uploadsDir.$guid.'.image';
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

?>