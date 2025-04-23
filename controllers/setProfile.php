<?php  
    include_once('svc/services.php');
    include_once('svc/thumbnailServices.php');
    include_once('controllers/isAuthenticated.php'); // for readAuthCookie()
    
    if (!empty($_COOKIE[$cookieName])) {
      readAuthCookie();
      $userProfile = getUserProfileSummaryArray($$a['r']);
      $thumbnailPath = $userProfile['thumbnailPath'];
      if(isset($_POST['upload'])) {
        $controllerError = is_null($connection) ? "No connection. " : "";
        if ($_FILES['profileImage']['error'] == 0) {
          list($width, $height, $_gettype, $_getattr) = getimagesize($_FILES['profileImage']['tmp_name']);
          $size = $_FILES['profileImage']['size'];
          
          if ($width == 0 || $height == 0 || $size == 0) {
            $controllerError .= 'Could not get the dimensions of the image.';
          } else {
            $fileBytes = file_get_contents($_FILES['profileImage']['tmp_name']);
            $destPath = getDestinationPath('.image');
            if (move_uploaded_file($_FILES['profileImage']['tmp_name'], $destPath) !== true) {
              $controllerError .= 'Could not save the uploaded image.';
            } else {
              $img = imagecreatefromstring($fileBytes);
              if ($img === false) {
                $controllerError .= 'Could not create the thumbnail.';
              } else {
                $scale = getScale($width, $height);
                $th = getThumbnailAsString($img, $scale, $width, $height);
                $thumbnailPath = getDestinationPath('.png');
                file_put_contents($thumbnailPath, $th);
                
                $contentType = $_FILES['profileImage']['type'];
                
                $hofs = $vofs = 0;
                deleteExistingImage($$a['r']);
                $smt = mysqli_prepare($connection, 'insert into `UserProfile` (`ID` , `PlayerID` ,`OriginalName` , `OriginalSavedPath`,`OriginalContentType` ,`ThumbnailPath`, `OriginalFileSize`, `HOffset`,`VOffset`,`OriginalScale`,`DisplayScale`,`InsertDate` ) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())');
                mysqli_stmt_bind_param($smt, 'ssssssiiidd', GUID(), $$a['r'], $_FILES['profileImage']['name'], $destPath, $contentType, $thumbnailPath, $size, $hofs, $vofs, $scale, $scale);
                if (!mysqli_stmt_execute($smt)){
                  $controllerError .= mysqli_error($connection);
                }
                mysqli_stmt_close($smt);
              }
            }
          }
        } else {
          $controllerError .= 'File could not be uploaded.';
        }
      } else if(isset($_POST['change'])) {
        deleteExistingImage($$a['r']);
        $thumbnailPath = '';
      } else if(isset($_POST['zoomin'])) {
        $userProfile = getUserProfileSummaryArray($$a['r']);
        $fivepct = $userProfile['displayScale'] * 0.05;
        $userProfile['displayScale'] += $fivepct;
        updateThumbnail($$a['r'], $userProfile);
      } else if(isset($_POST['zoomout'])) {
        $userProfile = getUserProfileSummaryArray($$a['r']);
        $fivepct = $userProfile['displayScale'] * 0.05;
        $userProfile['displayScale'] -= $fivepct;
        updateThumbnail($$a['r'], $userProfile);
      }else if(isset($_POST['right'])) {
        $userProfile = getUserProfileSummaryArray($$a['r']);
        $userProfile['hOffset'] -= $positionDistance;
        updateThumbnail($$a['r'], $userProfile);
      } else if(isset($_POST['left'])) {
        $userProfile = getUserProfileSummaryArray($$a['r']);
        $userProfile['hOffset'] += $positionDistance;
        updateThumbnail($$a['r'], $userProfile);
      } else if(isset($_POST['up'])) {
        $userProfile = getUserProfileSummaryArray($$a['r']);
        $userProfile['vOffset'] += $positionDistance;
        updateThumbnail($$a['r'], $userProfile);
      } else if(isset($_POST['down'])) {
        $userProfile = getUserProfileSummaryArray($$a['r']);
        $userProfile['vOffset'] -= $positionDistance;
        updateThumbnail($$a['r'], $userProfile);
      } else if(isset($_POST['close'])) {
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