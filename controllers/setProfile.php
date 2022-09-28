<?php  
    include_once('config/db.php');
    include_once('config/config.php');
    include_once('svc/services.php');
    include_once('svc/thumbnailServices.php');
    
    if (empty($_COOKIE[$cookieName])) {
      header('Location: index.php');
    } else {
      $userProfile = getUserProfileSummaryArray($_COOKIE[$cookieName]);
      $thumbnailPath = $userProfile['thumbnailPath'];
      if(isset($_POST['upload'])) {
        if ($_FILES['profileImage']['error'] == 0) {
          list($width, $height, $_gettype, $_getattr) = getimagesize($_FILES['profileImage']['tmp_name']);
          $size = $_FILES['profileImage']['size'];
          
          if ($width == 0 || $height == 0 || $size == 0) {
            $errorMsg = 'Could not get the dimensions of the image.';
          } else {
            $fileBytes = file_get_contents($_FILES['profileImage']['tmp_name']);
            $destPath = getDestinationPath('.image');
            if (move_uploaded_file($_FILES['profileImage']['tmp_name'], $destPath) !== true) {
              $errorMsg = 'Could not save the uploaded image.';
            } else {
              $img = imagecreatefromstring($fileBytes);
              if ($img === false) {
                $errorMsg = 'Could not create the thumbnail.';
              } else {
                $scale = getScale($width, $height);
                $th = getThumbnailAsString($img, $scale, $width, $height);
                $thumbnailPath = getDestinationPath('.png');
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
        $userProfile = getUserProfileSummaryArray($_COOKIE[$cookieName]);
        $fivepct = $userProfile['displayScale'] * 0.05;
        $userProfile['displayScale'] += $fivepct;
        updateThumbnail($_COOKIE[$cookieName], $userProfile);
      } else if(isset($_POST['zoomout'])) {
        $userProfile = getUserProfileSummaryArray($_COOKIE[$cookieName]);
        $fivepct = $userProfile['displayScale'] * 0.05;
        $userProfile['displayScale'] -= $fivepct;
        updateThumbnail($_COOKIE[$cookieName], $userProfile);
      }else if(isset($_POST['right'])) {
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
      }  else if(isset($_POST['close'])) {
        header('Location: dashboard.php');
      }
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