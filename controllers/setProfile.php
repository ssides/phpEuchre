<?php  
  include_once('svc/services.php');
  include_once('svc/thumbnailServices.php');
  include_once('controllers/isAuthenticated.php'); // for readAuthCookie()
  
  if (!empty($_COOKIE[$cookieName])) {
    readAuthCookie();
    try {
      mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
      $conn = mysqli_connect($hostname, $username, $password, $dbname);
      
      mysqli_begin_transaction($conn);
      
      if(isset($_POST['upload'])) {
        if ($_FILES['profileImage']['error'] == 0) {
          list($width, $height, $_gettype, $_getattr) = getimagesize($_FILES['profileImage']['tmp_name']);
          $size = $_FILES['profileImage']['size'];
          
          if ($width == 0 || $height == 0 || $size == 0) {
            throw new Exception('Could not get the dimensions of the image.');
          } else {
            $fileBytes = file_get_contents($_FILES['profileImage']['tmp_name']);
            $destPath = getDestinationPath('.image');
            if (move_uploaded_file($_FILES['profileImage']['tmp_name'], $destPath) !== true) {
              throw new Exception('Could not save the uploaded image.');
            } else {
              $img = imagecreatefromstring($fileBytes);
              if ($img === false) {
                throw new Exception('Could not create the thumbnail.');
              } else {
                $scale = getScale($width, $height);
                $th = getThumbnailAsString($img, $scale, $width, $height);
                $thumbnailPath = getDestinationPath('.png');
                file_put_contents($thumbnailPath, $th);
                
                $contentType = $_FILES['profileImage']['type'];
                
                $hofs = $vofs = 0;
                
                deleteExistingImage($conn, $$a['r']);
                
                $smt = mysqli_prepare($conn, 'insert into `UserProfile` (`ID` , `PlayerID` ,`OriginalName` , `OriginalSavedPath`,`OriginalContentType` ,`ThumbnailPath`, `OriginalFileSize`, `HOffset`,`VOffset`,`OriginalScale`,`DisplayScale`,`InsertDate` ) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())');
                mysqli_stmt_bind_param($smt, 'ssssssiiidd', GUID(), $$a['r'], $_FILES['profileImage']['name'], $destPath, $contentType, $thumbnailPath, $size, $hofs, $vofs, $scale, $scale);
                mysqli_stmt_execute($smt);
                mysqli_stmt_close($smt);
              }
            }
          }
        } else {
          throw new Exception('File could not be uploaded.');
        }
      } 
    
      mysqli_commit($conn);
      mysqli_close($conn);

    } catch (Exception $e) {
      if (isset($conn) && $conn) { 
        mysqli_rollback($conn);
        mysqli_close($conn); 
      }
      trigger_error($e->getMessage() . "\nStack trace: " . $e->getTraceAsString(), E_USER_ERROR);
      $controllerError .= 'Internal Server Error';
    }
  }

?>